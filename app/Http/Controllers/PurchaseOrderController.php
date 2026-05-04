<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $purchaseOrders = PurchaseOrder::with(['vendor', 'details.product'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->latest()
            ->paginate(50);

        $vendors = Vendor::all();
        $products = Product::all();

        return view('purchase_orders.index', compact('purchaseOrders', 'vendors', 'products', 'startDate', 'endDate'));
    }

    public function getVendorProducts($vendor_id)
    {
        $products = Product::where('vendor_id', $vendor_id)->orWhereNull('vendor_id')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required',
            'order_date' => 'required|date',
            'product_id' => 'required|array',
            'qty' => 'required|array',
            'price' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $today = Carbon::parse($request->order_date)->format('Ymd');
            $count = PurchaseOrder::whereDate('date', $request->order_date)->count();
            $poNumber = 'PO-' . $today . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'date' => $request->order_date,
                'vendor_id' => $request->vendor_id,
                'status' => 'Pending',
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $key => $productId) {
                $qty = $request->qty[$key];
                $price = $request->price[$key];
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                    'received_qty' => 0,
                    'price' => $price,
                    'subtotal' => $subtotal
                ]);
            }

            $po->total_amount = $totalAmount;
            $po->save();

            DB::commit();
            return redirect()->back()->with('success', 'Dokumen Purchase Order (PO) berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);
            if ($po->status !== 'Pending') {
                return redirect()->back()->with('error', 'PO yang sudah diproses tidak bisa dihapus.');
            }
            $po->details()->delete();
            $po->delete();

            return redirect()->back()->with('success', 'Dokumen PO berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus PO: ' . $e->getMessage());
        }
    }

    public function forceClose($id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);
            $po->status = 'Ditutup';
            $po->save();

            return redirect()->back()->with('success', 'PO berhasil ditutup paksa. Sisa barang dianggap batal/hangus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menutup PO: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $po = PurchaseOrder::with(['vendor', 'details.product'])->findOrFail($id);
        return view('purchase_orders.print', compact('po'));
    }
}
