<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\ReceiveOrder;
use App\Models\PurchaseOrder;
use App\Models\Usage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetch()
    {
        $today = Carbon::today()->format('Y-m-d');
        $threeDaysAgo = Carbon::today()->subDays(3)->format('Y-m-d');

        $lowStocks = Product::where('stock', '<=', 5)->select('name', 'stock')->orderBy('stock', 'asc')->take(4)->get();
        $countLowStock = Product::where('stock', '<=', 5)->count();

        $piutangCount = Sale::where('status', 'Belum Lunas')->count();

        $hutangCount = ReceiveOrder::where('status', 'Belum Lunas')->count();

        $poTelatCount = PurchaseOrder::whereIn('status', ['Pending', 'Diterima Sebagian'])
            ->whereDate('date', '<=', $threeDaysAgo)
            ->count();

        $anomaliCount = Usage::whereIn('purpose', ['Lainnya', 'Barang Rusak / Hilang', 'Rusak'])
            ->whereDate('date', $today)
            ->count();

        $totalNotif = $countLowStock + $piutangCount + $hutangCount + $poTelatCount + $anomaliCount;

        return response()->json([
            'total'           => $totalNotif,
            'low_stocks'      => $lowStocks,
            'count_low_stock' => $countLowStock,
            'piutang'         => $piutangCount,
            'hutang'          => $hutangCount,
            'po_telat'        => $poTelatCount,
            'anomali'         => $anomaliCount
        ]);
    }
}
