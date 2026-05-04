<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceiveOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });

    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);

    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('categories', CategoryController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('units', UnitController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('products', ProductController::class);

    Route::get('/get-vendor-products/{vendor_id}', [PurchaseOrderController::class, 'getVendorProducts']);
    Route::get('/purchase-orders/{id}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    Route::post('/purchase-orders/{id}/detail', [PurchaseOrderController::class, 'storeDetail'])->name('purchase-orders.storeDetail');
    Route::post('/purchase-orders/{id}/force-complete', [PurchaseOrderController::class, 'forceComplete'])->name('purchase-orders.forceComplete');
    Route::post('/purchase-orders/{id}/close', [PurchaseOrderController::class, 'forceClose'])->name('purchase-orders.close');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::get('/receive-orders/get-po-details/{id}', [ReceiveOrderController::class, 'getPoDetails'])->name('receive-orders.getPoDetails');
    Route::get('/receive-orders/get-po-remaining/{id}', [ReceiveOrderController::class, 'getPoRemainingDetails']);
    Route::post('/receive-orders/{id}/mark-paid', [ReceiveOrderController::class, 'markPaid'])->name('receive-orders.mark-paid');
    Route::resource('receive-orders', ReceiveOrderController::class);

    Route::get('/get-product-locations/{product_id}', [UsageController::class, 'getProductLocations']);
    Route::resource('usages', UsageController::class);

    Route::get('/sales/get-product-locations/{product_id}', [SaleController::class, 'getProductLocations']);
    Route::post('/sales/{id}/mark-paid', [SaleController::class, 'markPaid'])->name('sales.mark-paid');
    Route::resource('sales', SaleController::class);

    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
