<?php

namespace App\Http\Controllers;

use App\Models\Usage;
use App\Models\Product;
use App\Models\Location;
use App\Models\ReceiveOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsageController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $usages = Usage::with(['product', 'location'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->latest()
            ->paginate(50);

        $products = Product::where('stock', '>', 0)->get();

        return view('usages.index', compact('usages', 'products', 'startDate', 'endDate'));
    }

    public function getProductLocations($product_id)
    {
        $locationIds = ReceiveOrderDetail::where('product_id', $product_id)
            ->pluck('location_id')
            ->unique();

        if ($locationIds->isNotEmpty()) {
            $locations = Location::whereIn('id', $locationIds)->get();
        } else {
            $locations = Location::all();
        }

        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'qty' => 'required|integer|min:1',
            'purpose' => 'required|string',
            'purpose_other' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            if ($product->stock < $request->qty) {
                return redirect()->back()->with('error', 'Gagal! Sisa stok ' . $product->name . ' di gudang tidak mencukupi (Sisa: ' . $product->stock . ').');
            }

            $finalPurpose = $request->purpose;
            if ($finalPurpose === 'Lain-lain' && !empty($request->purpose_other)) {
                $finalPurpose = $request->purpose_other;
            }

            Usage::create([
                'date' => $request->date,
                'product_id' => $request->product_id,
                'location_id' => $request->location_id,
                'qty' => $request->qty,
                'purpose' => $finalPurpose,
                'notes' => $request->notes,
            ]);

            $product->stock -= $request->qty;
            $product->save();

            DB::commit();
            return redirect()->back()->with('success', 'Pemakaian internal berhasil dicatat dan stok gudang telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pengeluaran barang: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $usage = Usage::findOrFail($id);
            $product = Product::find($usage->product_id);

            if ($product) {
                $product->stock += $usage->qty;
                $product->save();
            }

            $usage->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Catatan pemakaian dibatalkan. Stok barang telah dikembalikan utuh ke gudang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan pemakaian: ' . $e->getMessage());
        }
    }
}
