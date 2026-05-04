<?php

namespace App\Http\Controllers;

use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderDetail;
use App\Models\PurchaseOrder;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceiveOrderController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $receiveOrders = ReceiveOrder::with('purchaseOrder.vendor', 'details.product')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->latest()
            ->paginate(50);

        $purchaseOrders = PurchaseOrder::with('vendor')
            ->whereIn('status', ['Pending', 'Parsial'])
            ->get();

        $locations = Location::all();

        return view('receive_orders.index', compact('receiveOrders', 'purchaseOrders', 'locations', 'startDate', 'endDate'));
    }

    public function getPoDetails($id)
    {
        $po = PurchaseOrder::with('details.product')->findOrFail($id);

        $details = $po->details->map(function ($detail) {
            $sisaBarang = $detail->qty - $detail->received_qty;

            return [
                'id' => $detail->product->id,
                'name' => $detail->product->name,
                'po_qty' => $detail->qty,
                'received_qty' => $detail->received_qty,
                'sisa_qty' => $sisaBarang,
                'price' => $detail->price,
            ];
        });

        $filteredDetails = $details->filter(function ($detail) {
            return $detail['sisa_qty'] > 0;
        })->values();

        return response()->json([
            'po' => $po,
            'details' => $filteredDetails
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'date' => 'required|date',
            'payment_method' => 'required|string',
            'product_id' => 'required|array',
            'location_id' => 'required|array',
            'qty' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $po = PurchaseOrder::findOrFail($request->purchase_order_id);

            $today = Carbon::now()->format('Ymd');
            $count = ReceiveOrder::whereDate('created_at', Carbon::today())->count();
            $roNumber = 'RO-' . $today . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $ro = ReceiveOrder::create([
                'ro_number' => $roNumber,
                'date' => $request->date,
                'purchase_order_id' => $po->id,
                'payment_method' => $request->payment_method,
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $key => $productId) {
                $qtyDatang = $request->qty[$key];
                if ($qtyDatang <= 0) continue;

                $poDetail = $po->details()->where('product_id', $productId)->first();

                if (!$poDetail) {
                    throw new \Exception("Produk ID $productId tidak ada dalam dokumen PO ini.");
                }

                $sisaBolehDiterima = $poDetail->qty - $poDetail->received_qty;

                if ($qtyDatang > $sisaBolehDiterima) {
                    throw new \Exception("Gagal! Jumlah {$poDetail->product->name} yang diterima ($qtyDatang) melebihi sisa pesanan PO ($sisaBolehDiterima).");
                }

                $subtotal = $qtyDatang * $poDetail->price;
                $totalAmount += $subtotal;

                ReceiveOrderDetail::create([
                    'receive_order_id' => $ro->id,
                    'product_id' => $productId,
                    'location_id' => $request->location_id[$key],
                    'qty' => $qtyDatang,
                    'price' => $poDetail->price,
                    'subtotal' => $subtotal,
                ]);

                $product = Product::find($productId);
                $product->stock += $qtyDatang;
                $product->save();

                $poDetail->received_qty += $qtyDatang;
                $poDetail->save();
            }

            if ($totalAmount == 0) {
                throw new \Exception("Harap isi setidaknya satu kuantitas barang yang diterima.");
            }

            $po->checkAndUpdateStatus();

            DB::commit();
            return redirect()->back()->with('success', 'Barang berhasil diterima dan masuk ke Rak Gudang! Status PO telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $ro = ReceiveOrder::with('details', 'purchaseOrder')->findOrFail($id);
            $po = $ro->purchaseOrder;

            foreach ($ro->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->stock -= $detail->qty;
                    $product->save();
                }

                $poDetail = $po->details()->where('product_id', $detail->product_id)->first();
                if ($poDetail) {
                    $poDetail->received_qty -= $detail->qty;
                    $poDetail->save();
                }
            }

            $ro->delete();

            if ($po) {
                $po->checkAndUpdateStatus();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Penerimaan (RO) dibatalkan. Stok gudang dan riwayat PO telah dikembalikan utuh.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan RO: ' . $e->getMessage());
        }
    }

    public function markPaid($id)
    {
        try {
            $ro = ReceiveOrder::findOrFail($id);

            if ($ro->payment_method === 'Cash') {
                return redirect()->back()->with('error', 'Dokumen RO ini sudah berstatus Lunas.');
            }

            $ro->payment_method = 'Cash';
            $ro->save();

            return redirect()->back()->with('success', 'Status tagihan RO berhasil diubah menjadi LUNAS (Cash)!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melunasi tagihan RO: ' . $e->getMessage());
        }
    }
}
