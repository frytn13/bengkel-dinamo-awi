<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ReceiveOrder;
use App\Models\Sale;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $totalNotif = 0;
            $notifHutang = collect();
            $notifPiutang = collect();

            if (\Schema::hasTable('receive_orders')) {
                $notifHutang = ReceiveOrder::where('payment_method', 'Tempo')
                    ->with('purchaseOrder.vendor')
                    ->latest()->get();
                $totalNotif += $notifHutang->count();
            }

            if (\Schema::hasTable('sales')) {
                $notifPiutang = Sale::where('payment_method', 'Tempo')->latest()->get();
                $totalNotif += $notifPiutang->count();
            }

            $view->with('notifHutang', $notifHutang);
            $view->with('notifPiutang', $notifPiutang);
            $view->with('totalNotif', $totalNotif);
        });
    }
}
