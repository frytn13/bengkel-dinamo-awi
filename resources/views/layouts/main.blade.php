@php
    $notifAnomali = 0;
    $notifHutang = 0;
    $notifPoMenunggu = 0;
    $notifPiutang = 0;
    $lowStocks = collect();

    if (\Illuminate\Support\Facades\Schema::hasTable('receive_orders')) {
        $notifHutang = \App\Models\ReceiveOrder::where('payment_method', 'Tempo')->count();
    }
    if (\Illuminate\Support\Facades\Schema::hasTable('sales')) {
        $notifPiutang = \App\Models\Sale::where('payment_method', 'Tempo')->count();
    }
    if (\Illuminate\Support\Facades\Schema::hasTable('purchase_orders')) {
        $notifPoMenunggu = \App\Models\PurchaseOrder::whereIn('status', ['Pending', 'Parsial', 'Diterima Sebagian'])->count();
    }
    if (\Illuminate\Support\Facades\Schema::hasTable('usages')) {
        $notifAnomali = \App\Models\Usage::where('purpose', 'Barang Hilang / Rusak (Write-off)')
            ->whereDate('date', \Carbon\Carbon::today())->count();
    }
    if (\Illuminate\Support\Facades\Schema::hasTable('products')) {
        $lowStocks = \App\Models\Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->get();
    }

    $countLowStock = $lowStocks->count();
    $totalNotif = $notifAnomali + $notifHutang + $notifPoMenunggu + $notifPiutang + $countLowStock;
@endphp

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Bengkel Dinamo Awi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            overflow: hidden;
            height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            z-index: -1;
            pointer-events: none;
        }

        body::before {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: rgba(56, 189, 248, 0.4);
        }

        body::after {
            bottom: -10%;
            right: -10%;
            width: 40vw;
            height: 40vw;
            background: rgba(167, 139, 250, 0.4);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade {
            animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .modal-backdrop {
            z-index: 1060 !important;
        }

        .modal {
            z-index: 1070 !important;
        }

        .sidebar::-webkit-scrollbar,
        main::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track,
        main::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb,
        main::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        main::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        [data-bs-theme="dark"] .sidebar::-webkit-scrollbar-thumb,
        [data-bs-theme="dark"] main::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }

        [data-bs-theme="dark"] .sidebar::-webkit-scrollbar-thumb:hover,
        [data-bs-theme="dark"] main::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .dropdown-menu-notif {
            width: 340px;
            padding: 0;
            border-radius: 16px;
            overflow: hidden;
        }

        .notif-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.5);
        }

        .notif-item {
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            text-decoration: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: background-color 0.2s ease;
            white-space: normal;
            color: inherit;
        }

        .notif-item:hover {
            background-color: rgba(59, 130, 246, 0.05);
            color: inherit;
        }

        .notif-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(24px) saturate(180%);
            border-right: 1px solid rgba(255, 255, 255, 0.8) !important;
        }

        .sidebar a.nav-link-custom {
            color: #475569;
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-radius: 10px;
            margin: 6px 12px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .sidebar a.nav-link-custom:hover {
            background-color: rgba(15, 23, 42, 0.05);
            color: #0f172a;
            transform: translateX(6px);
        }

        .sidebar a.active {
            background-color: #3b82f6 !important;
            color: white !important;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
            transform: scale(1.02);
        }

        .card,
        .modal-content {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.03) !important;
            border-radius: 16px;
            color: #0f172a !important;
        }

        .dropdown-menu {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(30px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 1) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
            border-radius: 16px;
        }

        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 1040 !important;
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.6) !important;
        }

        .glass-btn {
            background: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            color: #0f172a !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .glass-btn:hover {
            background: rgba(255, 255, 255, 1) !important;
        }

        .theme-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            color: #475569;
        }

        .theme-btn:hover {
            background: rgba(255, 255, 255, 1);
            color: #0f172a;
        }

        [data-bs-theme="dark"] body {
            background-color: #020617;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] body::before {
            background: rgba(56, 189, 248, 0.12);
        }

        [data-bs-theme="dark"] body::after {
            background: rgba(167, 139, 250, 0.12);
        }

        [data-bs-theme="dark"] .sidebar {
            background: rgba(15, 23, 42, 0.7) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .sidebar a.nav-link-custom {
            color: #94a3b8;
        }

        [data-bs-theme="dark"] .sidebar a.nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .sidebar a.active {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.15) 0%, transparent 100%) !important;
            border-left: 3px solid #38bdf8 !important;
            border-radius: 0 10px 10px 0 !important;
            color: #38bdf8 !important;
            box-shadow: none !important;
        }

        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .modal-content {
            background: rgba(30, 41, 59, 0.75) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background: rgba(15, 23, 42, 0.98) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.9) !important;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        [data-bs-theme="dark"] .notif-header {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        [data-bs-theme="dark"] .notif-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
        }

        [data-bs-theme="dark"] .notif-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .bg-light-subtle-custom {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .top-navbar {
            background: rgba(15, 23, 42, 0.7) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .glass-btn {
            background: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .glass-btn:hover {
            background: rgba(51, 65, 85, 1) !important;
        }

        [data-bs-theme="dark"] .theme-btn {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        [data-bs-theme="dark"] .theme-btn:hover {
            background: rgba(51, 65, 85, 1);
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .table,
        [data-bs-theme="dark"] .table td,
        [data-bs-theme="dark"] .table th {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] h1,
        [data-bs-theme="dark"] h2,
        [data-bs-theme="dark"] h3,
        [data-bs-theme="dark"] h4,
        [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] h6 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background: rgba(15, 23, 42, 0.6) !important;
            color: #f8fafc !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid vh-100 overflow-hidden">
        <div class="row h-100 flex-nowrap">

            <div class="col-auto col-md-3 col-lg-2 px-0 offcanvas-md offcanvas-start sidebar overflow-y-auto"
                tabindex="-1" id="sidebarMenu">

                <div class="offcanvas-header d-md-none border-bottom border-secondary">
                    <a href="/dashboard" class="text-decoration-none">
                        <h5 class="offcanvas-title fw-bold text-body"><i class="fas fa-tools me-2 text-primary"></i>
                            Bengkel Awi</h5>
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>

                <div class="offcanvas-body d-md-flex flex-column p-2 pt-lg-3 w-100">
                    <a href="/dashboard"
                        class="text-decoration-none text-center d-none d-md-block mt-2 mb-3 transition-all"
                        style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'">
                        <h5 class="fw-bold fs-4 text-body m-0"><i class="fas fa-cogs text-primary me-2"></i>Bengkel Awi
                        </h5>
                    </a>
                    <hr class="border-2 opacity-10 d-none d-md-block mx-3 border-secondary mt-0">

                    <a href="/dashboard" class="nav-link-custom {{ Request::is('dashboard') ? 'active' : '' }} mt-2"><i
                            class="fas fa-home me-3 width-20 text-center"></i> Dashboard</a>

                    <p class="small ms-3 mt-4 mb-2 fw-bold text-uppercase opacity-50 text-body">Master Data</p>
                    <a href="/categories" class="nav-link-custom {{ Request::is('categories*') ? 'active' : '' }}"><i
                            class="fas fa-tags me-3 text-center"></i> Kategori</a>
                    <a href="/locations" class="nav-link-custom {{ Request::is('locations*') ? 'active' : '' }}"><i
                            class="fas fa-map-marker-alt me-3 text-center"></i> Lokasi Gudang</a>
                    <a href="/units" class="nav-link-custom {{ Request::is('units*') ? 'active' : '' }}"><i
                            class="fas fa-balance-scale me-3 text-center"></i> Satuan</a>
                    <a href="/vendors" class="nav-link-custom {{ Request::is('vendors*') ? 'active' : '' }}"><i
                            class="fas fa-building me-3 text-center"></i> Vendor</a>
                    <a href="/products" class="nav-link-custom {{ Request::is('products*') ? 'active' : '' }}"><i
                            class="fas fa-box-open me-3 text-center"></i> Produk</a>

                    <p class="small ms-3 mt-4 mb-2 fw-bold text-uppercase opacity-50 text-body">Transaksi</p>
                    <a href="/purchase-orders"
                        class="nav-link-custom {{ Request::is('purchase-orders*') ? 'active' : '' }}"><i
                            class="fas fa-shopping-cart me-3 text-center"></i> Purchase Order (PO)</a>
                    <a href="/receive-orders"
                        class="nav-link-custom {{ Request::is('receive-orders*') ? 'active' : '' }}"><i
                            class="fas fa-truck-loading me-3 text-center"></i> Penerimaan (RO)</a>
                    <a href="/usages" class="nav-link-custom {{ Request::is('usages*') ? 'active' : '' }}"><i
                            class="fas fa-clipboard-list me-3 text-center"></i> Pemakaian Barang</a>
                    <a href="/sales" class="nav-link-custom {{ Request::is('sales*') ? 'active' : '' }}"><i
                            class="fas fa-cash-register me-3 text-center"></i> Penjualan (Kasir)</a>

                    <p class="small ms-3 mt-4 mb-2 fw-bold text-uppercase opacity-50 text-body">Analisa</p>
                    <a href="/reports" class="nav-link-custom {{ Request::is('reports*') ? 'active' : '' }}"><i
                            class="fas fa-chart-pie me-3 text-center"></i> Laporan Keuangan</a>
                </div>
            </div>

            <main class="col px-0 w-100 h-100 overflow-y-auto" style="overflow-x: hidden;">

                <nav
                    class="navbar top-navbar px-3 px-md-4 py-3 d-flex justify-content-between align-items-center flex-nowrap">

                    <div class="d-flex align-items-center flex-grow-1 overflow-hidden me-2">
                        <button class="btn btn-outline-secondary d-md-none me-2 border-0 flex-shrink-0"
                            style="padding: 0.25rem 0.5rem;" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebarMenu">
                            <i class="fas fa-bars fs-5"></i>
                        </button>
                        <h4 class="mb-0 fw-bold text-truncate text-body">@yield('title')</h4>
                    </div>

                    <div class="d-flex align-items-center gap-1 gap-md-2 flex-shrink-0">
                        <div class="dropdown">
                            <a href="#" class="theme-btn text-decoration-none position-relative no-caret"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fs-5"></i>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light {{ $totalNotif > 0 ? '' : 'd-none' }}"
                                    style="font-size: 0.65rem;">
                                    {{ $totalNotif > 99 ? '99+' : $totalNotif }}
                                </span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-notif animate-fade">
                                <li>
                                    <div class="notif-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-body"><i
                                                class="fas fa-bolt text-warning me-2"></i>Pusat Tindakan</h6>
                                        @if($totalNotif > 0)
                                            <span class="badge bg-primary rounded-pill shadow-sm">{{ $totalNotif }}
                                                Baru</span>
                                        @endif
                                    </div>
                                </li>
                                <div class="overflow-y-auto" style="max-height: 400px;">
                                    @if($totalNotif == 0)
                                        <li>
                                            <div class="text-center py-5">
                                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-check text-success fs-2"></i>
                                                </div>
                                                <h6 class="fw-bold text-body mb-1">Semua Lancar!</h6>
                                                <p class="text-body-secondary small mb-0">Tidak ada tindakan mendesak saat
                                                    ini.</p>
                                            </div>
                                        </li>
                                    @else
                                        @if($notifAnomali > 0)
                                            <li><a class="notif-item" href="/usages">
                                                    <div class="notif-icon-box bg-danger text-white"><i
                                                            class="fas fa-exclamation-triangle fs-5"></i></div>
                                                    <div>
                                                        <h6 class="fw-bold text-body mb-1">Barang Rusak / Hilang</h6>
                                                        <p class="text-body-secondary mb-0 small">Ada <b>{{ $notifAnomali }}</b>
                                                            laporan anomali dicatat hari ini.</p>
                                                    </div>
                                                </a></li>
                                        @endif
                                        @if($notifHutang > 0)
                                            <li><a class="notif-item" href="/receive-orders">
                                                    <div class="notif-icon-box bg-danger text-white"><i
                                                            class="fas fa-file-invoice-dollar fs-5"></i></div>
                                                    <div>
                                                        <h6 class="fw-bold text-body mb-1">Hutang ke Vendor</h6>
                                                        <p class="text-body-secondary mb-0 small"><span
                                                                class="fw-bold text-body">{{ $notifHutang }} tagihan</span> RO
                                                            belum lunas.</p>
                                                    </div>
                                                </a></li>
                                        @endif
                                        @if($notifPoMenunggu > 0)
                                            <li><a class="notif-item" href="/purchase-orders">
                                                    <div class="notif-icon-box bg-warning text-dark"><i
                                                            class="fas fa-truck-loading fs-5"></i></div>
                                                    <div>
                                                        <h6 class="fw-bold text-body mb-1">PO / Parsial Menunggu</h6>
                                                        <p class="text-body-secondary mb-0 small"><span
                                                                class="fw-bold text-body">{{ $notifPoMenunggu }} pesanan</span>
                                                            menunggu barang datang.</p>
                                                    </div>
                                                </a></li>
                                        @endif
                                        @if($notifPiutang > 0)
                                            <li><a class="notif-item" href="/sales">
                                                    <div class="notif-icon-box bg-info text-white"><i
                                                            class="fas fa-hand-holding-usd fs-5"></i></div>
                                                    <div>
                                                        <h6 class="fw-bold text-body mb-1">Piutang Pelanggan</h6>
                                                        <p class="text-body-secondary mb-0 small"><span
                                                                class="fw-bold text-body">{{ $notifPiutang }} nota</span>
                                                            menunggu dilunasi.</p>
                                                    </div>
                                                </a></li>
                                        @endif
                                        @if($countLowStock > 0)
                                            <li class="bg-light-subtle-custom bg-light border-bottom border-secondary-subtle">
                                                <div class="px-3 py-2 text-danger fw-bold small text-uppercase"
                                                    style="letter-spacing: 0.5px;"><i class="fas fa-box-open me-1"></i> Stok
                                                    Menipis ({{ $countLowStock }})</div>
                                            </li>
                                            @foreach($lowStocks->take(4) as $item)
                                                <li><a class="notif-item align-items-center justify-content-between py-2 px-3"
                                                        href="/products">
                                                        <div class="d-flex flex-column text-truncate pe-2"><span
                                                                class="fw-bold text-body"
                                                                style="font-size: 0.85rem;">{{ $item->name }}</span></div><span
                                                            class="badge bg-danger rounded-pill px-2 py-1">{{ $item->stock }}
                                                            sisa</span>
                                                    </a></li>
                                            @endforeach
                                            @if($countLowStock > 4)
                                                <li><a href="/products"
                                                        class="dropdown-item text-center py-2 text-primary fw-bold small bg-light bg-light-subtle-custom">Lihat
                                                        semua stok menipis...</a></li>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </ul>
                        </div>

                        <a href="javascript:void(0)" id="themeToggle" class="theme-btn text-decoration-none"><i
                                class="fas fa-moon fs-5"></i></a>

                        <div class="dropdown ms-1 ms-md-2">
                            <button
                                class="btn dropdown-toggle d-flex align-items-center px-2 px-md-3 py-1 py-md-2 rounded-pill glass-btn shadow-sm"
                                type="button" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->check() ? urlencode(auth()->user()->name) : 'A' }}&background=0D8ABC&color=fff&rounded=true"
                                    alt="Avatar" width="28" height="28" class="me-1 me-md-2 rounded-circle">
                                <span class="d-none d-sm-inline fw-bold text-body"
                                    style="font-size: 0.9rem;">{{ auth()->check() ? auth()->user()->name : 'Nug' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 mt-3 animate-fade">
                                <li>
                                    <form action="/logout" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold py-2"><i
                                                class="fas fa-sign-out-alt me-2"></i> Keluar Sistem</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="p-3 p-md-4 animate-fade">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const themeIcon = themeToggle.querySelector('i');
        const savedTheme = localStorage.getItem('theme') || 'light';

        html.setAttribute('data-bs-theme', savedTheme);
        updateIcon(savedTheme);

        themeToggle.addEventListener('click', () => {
            const newTheme = html.getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light';
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.replace('fa-moon', 'fa-sun');
                themeIcon.classList.add('text-warning');
            } else {
                themeIcon.classList.replace('fa-sun', 'fa-moon');
                themeIcon.classList.remove('text-warning');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => { document.body.appendChild(modal); });

            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#3b82f6', background: html.getAttribute('data-bs-theme') === 'dark' ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)', color: html.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#0f172a', borderRadius: '16px', backdrop: `rgba(0,0,0,0.5)` });
            @endif
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ session('error') }}", confirmButtonColor: '#ef4444', background: html.getAttribute('data-bs-theme') === 'dark' ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)', color: html.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#0f172a', borderRadius: '16px', backdrop: `rgba(0,0,0,0.5)` });
            @endif

            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (!this.hasAttribute('onsubmit') && !this.action.includes('logout') && !this.action.includes('delete') && !this.querySelector('.btn-hapus-baris')) {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) { submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...'; submitBtn.classList.add('disabled'); submitBtn.style.pointerEvents = 'none'; }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
