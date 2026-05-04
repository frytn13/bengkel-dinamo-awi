<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleType;
use App\Models\Product;
use App\Models\Location;
use App\Models\ReceiveOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $sales = Sale::with(['saleType', 'details.product', 'details.location'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->latest()
            ->paginate(50);

        $notifPiutang = Sale::where('payment_method', 'Tempo')->get();

        $products = Product::where('stock', '>', 0)->get();
        $locations = Location::all();
        $saleTypes = SaleType::all();

        return view('sales.index', compact('sales', 'products', 'locations', 'saleTypes', 'startDate', 'endDate', 'notifPiutang'));
    }

    public function getProductLocations($product_id)
    {
        $locationIds = ReceiveOrderDetail::where('product_id', $product_id)->pluck('location_id')->unique();
        $locations = Location::whereIn('id', $locationIds)->get();

        if ($locations->isEmpty()) {
            $locations = Location::all();
        }
        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'sale_type' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $saleType = SaleType::firstOrCreate(['name' => $request->sale_type]);

            $totalProduk = 0;
            $serviceFee = 0;

            if (in_array($request->sale_type, ['Jasa / Servis', 'Servis & Pembelian Sparepart'])) {
                $serviceFee = $request->service_fee ? intval($request->service_fee) : 0;
            }

            if (in_array($request->sale_type, ['Pembelian Sparepart', 'Servis & Pembelian Sparepart']) && $request->has('product_id')) {
                foreach ($request->product_id as $key => $productId) {
                    $totalProduk += ($request->qty[$key] * $request->price[$key]);
                }
            }

            $grandTotal = $totalProduk + $serviceFee;

            $today = Carbon::now()->format('Ymd');
            $count = Sale::whereDate('created_at', Carbon::today())->count();
            $invoiceNumber = 'INV-' . $today . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'date' => $request->date,
                'customer_name' => $request->customer_name ?? 'Pelanggan Umum',
                'service_name' => in_array($request->sale_type, ['Jasa / Servis', 'Servis & Pembelian Sparepart']) ? $request->service_name : null,
                'service_fee' => $serviceFee,
                'sale_type_id' => $saleType->id,
                'payment_method' => $request->payment_method,
                'status' => 'Selesai',
                'total_amount' => $grandTotal,
            ]);

            if (in_array($request->sale_type, ['Pembelian Sparepart', 'Servis & Pembelian Sparepart']) && $request->has('product_id')) {
                foreach ($request->product_id as $key => $productId) {
                    $qty = $request->qty[$key];
                    $price = $request->price[$key];
                    $product = Product::find($productId);

                    if (!$product || $product->stock < $qty) {
                        throw new \Exception("Stok {$product->name} kurang!");
                    }

                    $product->stock -= $qty;
                    $product->save();

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'location_id' => $request->location_id[$key],
                        'item_name' => $product->name,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $qty * $price,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $sale = Sale::with('details')->findOrFail($id);

            foreach ($sale->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->stock += $detail->qty;
                    $product->save();
                }
            }

            $sale->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Transaksi di-void. Stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    public function markPaid($id)
    {
        try {
            $sale = Sale::findOrFail($id);
            $sale->payment_method = 'Cash';
            $sale->save();

            return redirect()->back()->with('success', 'Tagihan pelanggan berhasil dilunasi. Omzet telah masuk ke Kas!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melunasi transaksi: ' . $e->getMessage());
        }
    }
}
