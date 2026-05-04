@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

    <style>
        @keyframes float-icon {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .icon-floating {
            animation: float-icon 3s ease-in-out infinite;
            display: inline-block;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            z-index: 10;
        }

        .icon-box {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            flex-shrink: 0;
        }

        .cta-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
            border: none !important;
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .cta-btn i {
            color: #ffffff !important;
        }

        .cta-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4) !important;
        }
    </style>

    <div class="container-fluid py-2">

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row align-items-center">
                            <div class="col-lg-5 mb-4 mb-lg-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="fas fa-user-shield fs-3 text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold mb-1 text-body-emphasis">Selamat Datang,
                                            {{ auth()->check() ? auth()->user()->name : 'Administrator' }}!
                                        </h3>
                                        <p class="mb-0 text-body-secondary fs-6">Pusat Kendali Operasional & Keuangan
                                            Bengkel Awi.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                    <a href="/sales"
                                        class="btn fw-bold rounded-pill px-4 py-2 card-hover d-flex align-items-center cta-btn">
                                        <i class="fas fa-cash-register me-2 fs-6"></i> Buka Kasir
                                    </a>
                                    <a href="/purchase-orders"
                                        class="btn fw-bold rounded-pill px-4 py-2 card-hover d-flex align-items-center cta-btn">
                                        <i class="fas fa-shopping-cart me-2 fs-6"></i> Order Barang
                                    </a>
                                    <a href="/receive-orders"
                                        class="btn fw-bold rounded-pill px-4 py-2 card-hover d-flex align-items-center cta-btn">
                                        <i class="fas fa-truck-loading me-2 fs-6"></i> Terima RO
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-body-secondary text-uppercase mb-3"><i class="fas fa-wallet me-2"></i>Ringkasan Keuangan
        </h6>
        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Omzet
                                Hari Ini</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">Rp
                                {{ number_format($salesToday ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="fas fa-calendar-day fs-3 text-success icon-floating"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Omzet
                                Bulan Ini</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">Rp
                                {{ number_format($salesThisMonth ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="fas fa-chart-line fs-3 text-primary icon-floating" style="animation-delay: 0.2s;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Piutang
                                Klien</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">Rp
                                {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10">
                            <i class="fas fa-hand-holding-usd fs-3 text-info icon-floating"
                                style="animation-delay: 0.4s;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Hutang
                                Vendor</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">Rp
                                {{ number_format($totalHutang ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="icon-box bg-danger bg-opacity-10">
                            <i class="fas fa-file-invoice-dollar fs-3 text-danger icon-floating"
                                style="animation-delay: 0.6s;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-body-secondary text-uppercase mb-3"><i class="fas fa-boxes me-2"></i>Kapasitas & Logistik
        </h6>
        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Macam
                                Produk</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">{{ $totalProducts ?? 0 }}</h4>
                        </div>
                        <div class="icon-box bg-secondary bg-opacity-10">
                            <i class="fas fa-tags fs-3 text-secondary icon-floating" style="animation-delay: 0.1s;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Total
                                Stok Fisik</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">{{ $totalStock ?? 0 }}</h4>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10">
                            <i class="fas fa-cubes fs-3 text-info icon-floating" style="animation-delay: 0.3s;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">Total
                                Vendor</h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">{{ $totalVendors ?? 0 }}</h4>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="fas fa-building fs-3 text-success icon-floating" style="animation-delay: 0.5s;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-body-secondary mb-2" style="font-size: 0.75rem;">PO
                                Menunggu <span class="badge bg-warning text-dark border border-warning-subtle ms-1"
                                    style="font-size: 0.55rem; padding: 2px 6px;">+Parsial</span></h6>
                            <h4 class="fw-bold text-body-emphasis mb-0">{{ $poPending ?? 0 }} <small
                                    class="fs-6 fw-normal text-body-secondary">Antrian</small></h4>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="fas fa-truck-loading fs-3 text-warning-emphasis icon-floating"
                                style="animation-delay: 0.7s;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                    <div
                        class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-body-emphasis"><i
                                class="fas fa-chart-area me-2 text-primary"></i>Grafik Pendapatan (7 Hari Terakhir)</h6>

                        <a href="/reports"
                            class="btn btn-sm bg-primary bg-opacity-10 text-primary border-0 rounded-pill shadow-sm small fw-bold px-3 py-1">
                            Buka Laporan <i class="fas fa-arrow-right ms-1 small"></i>
                        </a>

                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center p-4">
                        <canvas id="salesChart" style="width: 100%; max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-flex flex-column gap-4">

                <div class="card shadow-sm border-0 h-50" style="border-radius: 15px;">
                    <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger border-opacity-25 py-3">
                        <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Peringatan Stok
                            Menipis (<= 5)</h6>
                    </div>
                    <div class="card-body p-0 overflow-auto" style="max-height: 200px;">
                        <ul class="list-group list-group-flush border-0">
                            @forelse($lowStocks ?? [] as $item)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 bg-transparent border-bottom border-secondary-subtle">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-body-emphasis fs-6">{{ $item->name }}</h6>
                                        <small class="text-body-secondary">{{ $item->item_code ?? 'Tanpa Kode' }}</small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill fs-6 shadow-sm px-3">{{ $item->stock }}
                                        Sisa</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-4 text-body-secondary bg-transparent border-0">
                                    <i class="fas fa-check-circle text-success fs-3 mb-2 d-block"></i>
                                    Hore! Semua stok barang dalam kondisi aman.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    @if(($lowStocks ?? collect())->count() > 0)
                        <div class="card-footer bg-transparent text-center py-2 border-top border-secondary-subtle">
                            <a href="/products" class="text-decoration-none small fw-bold text-danger">Kelola Produk <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    @endif
                </div>

                <div class="card shadow-sm border-0 h-50" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-body-emphasis"><i class="fas fa-history me-2 text-primary"></i>5
                            Transaksi Terakhir</h6>
                    </div>
                    <div class="card-body p-0 overflow-auto" style="max-height: 200px;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 text-nowrap align-middle">
                                <thead class="bg-body-tertiary border-bottom border-secondary-subtle">
                                    <tr>
                                        <th class="ps-4 small text-uppercase text-body-secondary fw-bold py-2">Pelanggan
                                        </th>
                                        <th class="text-end pe-4 small text-uppercase text-body-secondary fw-bold py-2">
                                            Nominal (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSales ?? [] as $sale)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <a href="/sales"
                                                    class="text-decoration-none fw-bold text-primary">{{ $sale->invoice_number }}</a><br>
                                                <small class="text-body-secondary">{{ $sale->customer_name ?? 'Umum' }}</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-success py-3">
                                                {{ number_format($sale->total_amount, 0, ',', '.') }}<br>
                                                <small
                                                    class="badge {{ $sale->status == 'Selesai' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $sale->status == 'Selesai' ? 'success' : 'danger' }} border border-{{ $sale->status == 'Selesai' ? 'success' : 'danger' }} p-1"
                                                    style="font-size: 10px;">{{ $sale->status }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-body-secondary py-4">Belum ada transaksi
                                                penjualan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($chartDates ?? []);
        const dataTotals = @json($chartTotals ?? []);

        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const textColor = isDarkMode ? '#cbd5e1' : '#475569';
        const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

        const ctx = document.getElementById('salesChart').getContext('2d');

        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: dataTotals,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                        bodyFont: { size: 14, family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                let value = context.raw || 0;
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { family: "'Plus Jakarta Sans', sans-serif" } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        ticks: {
                            color: textColor,
                            font: { family: "'Plus Jakarta Sans', sans-serif" },
                            callback: function (value) {
                                if (value >= 1000000) return (value / 1000000) + ' Jt';
                                if (value >= 1000) return (value / 1000) + ' Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
