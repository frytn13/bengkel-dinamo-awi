@extends('layouts.main')

@section('title', 'Laporan Keuangan & Audit')

@section('content')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.02); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.15); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.25); }

        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); }
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.25); }

        .table-responsive.custom-scrollbar thead th {
            position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .table-responsive.custom-scrollbar tfoot td {
            position: sticky; bottom: -1px; z-index: 10; box-shadow: 0 -2px 4px rgba(0,0,0,0.05);
        }
    </style>

    @php
        $grandTotalPenjualan = $salesData->sum('total_amount');
        $grandTotalRO = $allROs->sum(function($ro) { return $ro->total_amount ?? $ro->details->sum('subtotal'); });
    @endphp

    <div class="container-fluid py-2">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Laporan Keuangan & Audit</h5>

            <form action="{{ route('reports.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group shadow-sm" style="width: auto;">
                    <span class="input-group-text bg-body border-secondary-subtle text-body-secondary"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" class="form-control text-body bg-body border-secondary-subtle" name="start_date" value="{{ $startDate }}">
                </div>
                <span class="text-body-secondary fw-bold">s/d</span>
                <div class="input-group shadow-sm" style="width: auto;">
                    <span class="input-group-text bg-body border-secondary-subtle text-body-secondary"><i class="fas fa-calendar-check"></i></span>
                    <input type="date" class="form-control text-body bg-body border-secondary-subtle" name="end_date" value="{{ $endDate }}">
                </div>
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-filter"></i> Terapkan</button>

                <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="btn btn-danger fw-bold shadow-sm ms-md-2">
                    <i class="fas fa-print me-2"></i> Cetak Laporan
                </a>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10 border border-success-subtle" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-uppercase small fw-bold text-success mb-1">Kas Masuk (Lunas)</p><h4 class="fw-bold text-success mb-0">Rp {{ number_format($kasMasuk ?? 0, 0, ',', '.') }}</h4></div>
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;"><i class="fas fa-hand-holding-usd fs-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-danger bg-opacity-10 border border-danger-subtle" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-uppercase small fw-bold text-danger mb-1">Kas Keluar (Lunas)</p><h4 class="fw-bold text-danger mb-0">Rp {{ number_format($kasKeluar ?? 0, 0, ',', '.') }}</h4></div>
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;"><i class="fas fa-money-bill-wave fs-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10 border border-info-subtle" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-uppercase small fw-bold text-info mb-1">Total Piutang</p><h4 class="fw-bold text-info mb-0">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</h4></div>
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;"><i class="fas fa-users fs-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10 border border-warning-subtle" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-uppercase small fw-bold text-warning mb-1">Hutang Vendor</p><h4 class="fw-bold text-warning mb-0">Rp {{ number_format($totalHutang ?? 0, 0, ',', '.') }}</h4></div>
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;"><i class="fas fa-boxes fs-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom border-secondary-subtle p-3 overflow-auto">
                <ul class="nav nav-pills flex-nowrap text-nowrap" id="reportTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-penjualan"><i class="fas fa-cash-register me-2"></i> Omzet Kasir</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-all-po"><i class="fas fa-shopping-cart me-2"></i> Buku PO</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-all-ro"><i class="fas fa-truck-loading me-2"></i> Buku RO</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-piutang"><i class="fas fa-user-clock me-2"></i> Piutang</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-hutang"><i class="fas fa-file-invoice-dollar me-2"></i> Hutang</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-menyusul"><i class="fas fa-box-open me-2"></i> PO Menggantung</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3 me-1" data-bs-toggle="pill" data-bs-target="#tab-batal"><i class="fas fa-ban me-2"></i> PO Batal</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-3" data-bs-toggle="pill" data-bs-target="#tab-aset"><i class="fas fa-tools me-2"></i> Pemakaian</button></li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="reportTabsContent">

                    <div class="tab-pane fade show active" id="tab-penjualan">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL</th>
                                        <th class="py-3 small fw-bold text-body-secondary">INVOICE & PELANGGAN</th>
                                        <th class="py-3 text-center small fw-bold text-body-secondary">PEMBAYARAN</th>
                                        <th class="py-3 text-end pe-4 small fw-bold text-body-secondary">TOTAL NILAI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesData ?? [] as $sale)
                                        <tr>
                                            <td class="ps-4 text-body-secondary">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</td>
                                            <td><div class="fw-bold text-primary">{{ $sale->invoice_number }}</div><div class="small text-body">{{ $sale->customer_name ?? 'Pelanggan Umum' }}</div></td>
                                            <td class="text-center"><span class="badge {{ $sale->payment_method == 'Cash' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-1">{{ $sale->payment_method }}</span></td>
                                            <td class="text-end fw-bold text-body pe-4">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Tidak ada data penjualan pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-body-tertiary border-top border-2">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-uppercase">Grand Total Penjualan (Omzet)</td>
                                        <td class="text-end fw-bold py-3 pe-4 text-primary fs-6">Rp {{ number_format($grandTotalPenjualan, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-all-po">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL</th>
                                        <th class="py-3 small fw-bold text-body-secondary">NOMOR PO</th>
                                        <th class="py-3 small fw-bold text-body-secondary">VENDOR</th>
                                        <th class="py-3 text-center small fw-bold text-body-secondary">STATUS</th>
                                        <th class="py-3 text-end pe-4 small fw-bold text-body-secondary">GRAND TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allPOs as $po)
                                        <tr>
                                            <td class="ps-4 text-body-secondary">{{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}</td>
                                            <td class="fw-bold text-primary">{{ $po->po_number }}</td>
                                            <td>{{ $po->vendor->name ?? '-' }}</td>
                                            <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-body border">{{ $po->status }}</span></td>
                                            <td class="text-end fw-bold text-body pe-4">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-5 text-secondary">Tidak ada riwayat Purchase Order.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-all-ro">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL MASUK</th>
                                        <th class="py-3 small fw-bold text-body-secondary">NOMOR RO & PO</th>
                                        <th class="py-3 text-center small fw-bold text-body-secondary">PEMBAYARAN</th>
                                        <th class="py-3 text-end pe-4 small fw-bold text-body-secondary">TOTAL TAGIHAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allROs as $ro)
                                        <tr>
                                            <td class="ps-4 text-body-secondary">{{ \Carbon\Carbon::parse($ro->date)->format('d M Y') }}</td>
                                            <td><div class="fw-bold text-success">{{ $ro->ro_number }}</div><div class="small text-body-secondary">Ref: {{ $ro->purchaseOrder->po_number ?? '-' }}</div></td>
                                            <td class="text-center"><span class="badge {{ $ro->payment_method == 'Cash' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-1">{{ $ro->payment_method }}</span></td>
                                            <td class="text-end fw-bold text-body pe-4">Rp {{ number_format($ro->total_amount ?? $ro->details->sum('subtotal'), 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Tidak ada riwayat Penerimaan Barang.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-body-tertiary border-top border-2">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-uppercase">Grand Total Seluruh Tagihan RO</td>
                                        <td class="text-end fw-bold py-3 pe-4 text-success fs-6">Rp {{ number_format($grandTotalRO, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-piutang">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL</th>
                                        <th class="py-3 small fw-bold text-body-secondary">INVOICE</th>
                                        <th class="py-3 small fw-bold text-body-secondary">PELANGGAN</th>
                                        <th class="py-3 text-end pe-4 small fw-bold text-body-secondary">NOMINAL PIUTANG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesPiutang as $piutang)
                                        <tr>
                                            <td class="ps-4 text-body-secondary">{{ \Carbon\Carbon::parse($piutang->date)->format('d M Y') }}</td>
                                            <td class="fw-bold text-primary">{{ $piutang->invoice_number }}</td>
                                            <td class="text-body">{{ $piutang->customer_name ?? 'Umum' }}</td>
                                            <td class="text-end fw-bold text-danger pe-4">Rp {{ number_format($piutang->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Tidak ada tagihan piutang.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-body-tertiary border-top border-2">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-uppercase">Total Akumulasi Piutang</td>
                                        <td class="text-end fw-bold py-3 pe-4 text-danger fs-6">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-hutang">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="py-3 ps-4 small fw-bold text-body-secondary">TGL MASUK</th>
                                        <th class="py-3 small fw-bold text-body-secondary">NOMOR RO</th>
                                        <th class="py-3 small fw-bold text-body-secondary">VENDOR</th>
                                        <th class="py-3 text-end pe-4 small fw-bold text-body-secondary">NOMINAL HUTANG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roHutang as $hutang)
                                        <tr>
                                            <td class="ps-4 text-body-secondary">{{ \Carbon\Carbon::parse($hutang->date)->format('d M Y') }}</td>
                                            <td class="fw-bold text-primary">{{ $hutang->ro_number }}</td>
                                            <td class="text-body">{{ $hutang->purchaseOrder->vendor->name ?? '-' }}</td>
                                            <td class="text-end fw-bold text-warning-emphasis pe-4">Rp {{ number_format($hutang->total_amount ?? $hutang->details->sum('subtotal'), 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Tidak ada tagihan hutang.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-body-tertiary border-top border-2">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-uppercase">Total Akumulasi Hutang</td>
                                        <td class="text-end fw-bold py-3 pe-4 text-warning-emphasis fs-6">Rp {{ number_format($totalHutang, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-menyusul">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th width="15%" class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL PO</th>
                                        <th width="25%" class="py-3 small fw-bold text-body-secondary">PO & VENDOR</th>
                                        <th width="45%" class="py-3 small fw-bold text-body-secondary">SISA BARANG BELUM DATANG</th>
                                        <th width="15%" class="py-3 text-center pe-4 small fw-bold text-body-secondary">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($poPending as $po)
                                        <tr>
                                            <td class="ps-4 text-body-secondary align-top pt-3">{{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}</td>
                                            <td class="align-top pt-3"><div class="fw-bold text-primary">{{ $po->po_number }}</div><div class="small text-body-secondary"><i class="fas fa-building me-1"></i> {{ $po->vendor->name ?? '-' }}</div></td>
                                            <td class="align-top pt-3">
                                                <ul class="list-unstyled mb-0 small">
                                                    @foreach($po->details ?? [] as $detail)
                                                        @php $sisa = $detail->qty - ($detail->received_qty ?? 0); @endphp
                                                        @if($sisa > 0)
                                                            <li class="mb-1 border-bottom border-secondary-subtle pb-1"><i class="fas fa-box-open text-warning me-2"></i> <span class="text-body fw-semibold">{{ $detail->product->name ?? 'Produk' }}</span> <span class="ms-2 badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">Sisa: {{ $sisa }}</span></li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="text-center pe-4 align-top pt-3"><span class="badge bg-info text-white rounded-pill px-3 py-1">{{ $po->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Semua PO sudah diterima.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-batal">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th width="15%" class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL PO</th>
                                        <th width="25%" class="py-3 small fw-bold text-body-secondary">PO & VENDOR</th>
                                        <th width="45%" class="py-3 small fw-bold text-body-secondary">RINCIAN BARANG YANG HANGUS</th>
                                        <th width="15%" class="py-3 text-center pe-4 small fw-bold text-body-secondary">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($poClosed as $po)
                                        <tr>
                                            <td class="ps-4 text-body-secondary align-top pt-3">{{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}</td>
                                            <td class="align-top pt-3"><div class="fw-bold text-secondary text-decoration-line-through">{{ $po->po_number }}</div><div class="small text-body-secondary"><i class="fas fa-building me-1"></i> {{ $po->vendor->name ?? '-' }}</div></td>
                                            <td class="align-top pt-3">
                                                <ul class="list-unstyled mb-0 small">
                                                    @foreach($po->details ?? [] as $detail)
                                                        @php $sisa = $detail->qty - ($detail->received_qty ?? 0); @endphp
                                                        @if($sisa > 0)
                                                            <li class="mb-1 border-bottom border-secondary-subtle pb-1"><i class="fas fa-times-circle text-danger me-2"></i> <span class="text-body fw-semibold text-decoration-line-through">{{ $detail->product->name ?? 'Produk' }}</span> <span class="ms-2 badge bg-secondary text-white">Batal: {{ $sisa }}</span></li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="text-center pe-4 align-top pt-3"><span class="badge bg-secondary text-white rounded-pill px-3 py-1">Ditutup Paksa</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Tidak ada PO yang ditutup paksa.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-aset">
                        <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="py-3 ps-4 small fw-bold text-body-secondary">TANGGAL</th>
                                        <th class="py-3 small fw-bold text-body-secondary">BARANG</th>
                                        <th class="py-3 text-center small fw-bold text-body-secondary">QTY</th>
                                        <th class="py-3 small fw-bold text-body-secondary pe-4">KEPERLUAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usagesData as $usage)
                                        <tr>
                                            <td class="ps-4 text-body-secondary">{{ \Carbon\Carbon::parse($usage->date)->format('d M Y') }}</td>
                                            <td class="fw-bold text-body">{{ $usage->product->name ?? '-' }}</td>
                                            <td class="text-center text-body fw-bold">{{ $usage->qty }}</td>
                                            <td class="pe-4"><span class="{{ $usage->purpose == 'Barang Hilang / Rusak (Write-off)' ? 'text-danger fw-bold' : 'text-body' }}">{{ $usage->purpose }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-secondary">Tidak ada pemakaian.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-body-tertiary border-top border-2">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-uppercase">Estimasi Nilai Keluar</td>
                                        <td class="fw-bold py-3 pe-4 text-body fs-6">Rp {{ number_format($nilaiPemakaian, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
