<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ReceiveOrder;
use App\Models\PurchaseOrder;
use App\Models\Usage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $kasMasuk = Sale::where('payment_method', 'Cash')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_amount');

        $kasKeluar = ReceiveOrder::with('details')
            ->where('payment_method', 'Cash')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->sum(function ($ro) {
                return $ro->total_amount ?? $ro->details->sum('subtotal');
            });

        $salesPiutang = Sale::where('payment_method', 'Tempo')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        $totalPiutang = $salesPiutang->sum('total_amount');

        $roHutang = ReceiveOrder::with(['purchaseOrder.vendor', 'details'])
            ->where('payment_method', 'Tempo')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        $totalHutang = $roHutang->sum(function ($ro) {
            return $ro->total_amount ?? $ro->details->sum('subtotal');
        });

        $salesData = Sale::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'desc')->get();

        $poPending = PurchaseOrder::with(['vendor', 'details.product'])
            ->whereIn('status', ['Pending', 'Parsial', 'Diterima Sebagian'])
            ->get();

        $poClosed = PurchaseOrder::with(['vendor', 'details.product'])
            ->where('status', 'Ditutup')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $allPOs = PurchaseOrder::with(['vendor'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $allROs = ReceiveOrder::with(['purchaseOrder.vendor', 'details'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $usagesData = Usage::with('product')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $nilaiPemakaian = 0;
        foreach ($usagesData as $usage) {
            $nilaiPemakaian += ($usage->qty * ($usage->product->purchase_price ?? 0));
        }

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'kasMasuk',
            'kasKeluar',
            'salesData',
            'salesPiutang',
            'totalPiutang',
            'roHutang',
            'totalHutang',
            'poPending',
            'poClosed',
            'allPOs',
            'allROs',
            'usagesData',
            'nilaiPemakaian'
        ));
    }

    public function print(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $kasMasuk = Sale::where('payment_method', 'Cash')->whereBetween('date', [$startDate, $endDate])->sum('total_amount');

        $kasKeluar = ReceiveOrder::with('details')->where('payment_method', 'Cash')->whereBetween('date', [$startDate, $endDate])
            ->get()->sum(function ($ro) {
                return $ro->total_amount ?? $ro->details->sum('subtotal');
            });

        $salesData = Sale::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
        $salesPiutang = Sale::where('payment_method', 'Tempo')->whereBetween('date', [$startDate, $endDate])->get();
        $totalPiutang = $salesPiutang->sum('total_amount');

        $roHutang = ReceiveOrder::with(['purchaseOrder.vendor', 'details'])->where('payment_method', 'Tempo')->whereBetween('date', [$startDate, $endDate])->get();
        $totalHutang = $roHutang->sum(function ($ro) {
            return $ro->total_amount ?? $ro->details->sum('subtotal');
        });

        $poPending = PurchaseOrder::with(['vendor', 'details.product'])->whereIn('status', ['Pending', 'Parsial', 'Diterima Sebagian'])->get();
        $poClosed = PurchaseOrder::with(['vendor', 'details.product'])->where('status', 'Ditutup')->whereBetween('date', [$startDate, $endDate])->get();

        $allPOs = PurchaseOrder::with(['vendor'])->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
        $allROs = ReceiveOrder::with(['purchaseOrder.vendor', 'details'])->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();

        $usagesData = Usage::with('product')->whereBetween('date', [$startDate, $endDate])->get();
        $nilaiPemakaian = 0;
        foreach ($usagesData as $usage) {
            $nilaiPemakaian += ($usage->qty * ($usage->product->purchase_price ?? 0));
        }

        return view('reports.print', compact(
            'startDate',
            'endDate',
            'kasMasuk',
            'kasKeluar',
            'salesData',
            'salesPiutang',
            'totalPiutang',
            'roHutang',
            'totalHutang',
            'poPending',
            'poClosed',
            'allPOs',
            'allROs',
            'usagesData',
            'nilaiPemakaian'
        ));
    }
}
