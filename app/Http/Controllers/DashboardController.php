<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ReceiveOrder;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $salesToday = Sale::whereDate('date', $today)->sum('total_amount');
        $salesThisMonth = Sale::whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->sum('total_amount');
        $totalPiutang = Sale::where('payment_method', 'Tempo')->sum('total_amount');
        $totalHutang = ReceiveOrder::where('payment_method', 'Tempo')->get()->sum('total_amount');

        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalVendors = Vendor::count();
        $poPending = \App\Models\PurchaseOrder::whereIn('status', ['Pending', 'Parsial'])->count();

        $lowStocks = Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->get();

        $recentSales = Sale::latest()->take(5)->get();

        $chartDates = [];
        $chartTotals = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartDates[] = $date->format('d M');
            $totalPerDay = Sale::whereDate('date', $date)->sum('total_amount');
            $chartTotals[] = $totalPerDay;
        }

        return view('dashboard.index', compact(
            'salesToday',
            'salesThisMonth',
            'totalPiutang',
            'totalHutang',
            'totalProducts',
            'totalStock',
            'totalVendors',
            'poPending',
            'lowStocks',
            'recentSales',
            'chartDates',
            'chartTotals'
        ));
    }
}
