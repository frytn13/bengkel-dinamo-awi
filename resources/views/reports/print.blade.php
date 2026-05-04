<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Bengkel Awi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            background-color: #e2e8f0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .action-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #1e293b;
            padding: 15px 20px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .a4-wrapper {
            max-width: 900px;
            margin: 90px auto 40px auto;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            transition: all 0.1s ease;
        }

        #report-content {
            width: 100%;
            padding: 40px;
            background-color: #ffffff;
            box-sizing: border-box;
        }

        .report-header {
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .report-title {
            font-weight: 800;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
        }

        .company-name {
            font-size: 16px;
            color: #555;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            margin-top: 35px;
            margin-bottom: 15px;
            text-transform: uppercase;
            color: #000;
            page-break-after: avoid;
            break-after: avoid;
        }

        .table {
            font-size: 11px;
            margin-bottom: 25px;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table th {
            background-color: #f1f5f9 !important;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .table td {
            padding: 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .table tfoot td {
            background-color: #f8fafc;
            border-top: 2px solid #000;
        }

        .list-sisa {
            margin: 0;
            padding-left: 15px;
        }

        .list-sisa li {
            margin-bottom: 3px;
        }

        tr,
        .ttd-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body {
                background-color: #fff;
                margin: 0;
            }

            .action-toolbar {
                display: none !important;
            }

            .a4-wrapper {
                margin: 0;
                padding: 0;
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
            }

            #report-content {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    @php
        $grandTotalPenjualan = collect($salesData)->sum('total_amount');
        $grandTotalRO = collect($allROs)->sum(function ($ro) {
            return $ro->total_amount ?? collect($ro->details)->sum('subtotal'); });
    @endphp

    <div class="action-toolbar d-print-none">
        <div class="text-white">
            <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i> Pratinjau Laporan Final</h6>
            <small class="opacity-75">Dokumen lengkap siap unduh</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary fw-bold px-4 shadow-sm"><i
                    class="fas fa-print me-2"></i> Cetak Fisik</button>
            <button onclick="downloadPDF()" class="btn btn-danger fw-bold px-4 shadow-sm" id="btn-download"><i
                    class="fas fa-file-pdf me-2"></i> Download PDF</button>
            <button onclick="window.close()" class="btn btn-secondary fw-bold px-4 shadow-sm">Tutup</button>
        </div>
    </div>

    <div class="a4-wrapper" id="pdf-wrapper">
        <div id="report-content">

            <div class="report-header d-flex justify-content-between align-items-end">
                <div>
                    <div class="report-title">LAPORAN KEUANGAN & AUDIT KAS</div>
                    <div class="company-name mt-1 fw-bold">Sistem Informasi Bengkel Awi</div>
                </div>
                <div class="text-end" style="font-size: 12px; color: #444;">
                    <p class="mb-1"><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                        s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    <p class="mb-0"><strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</p>
                </div>
            </div>

            <table
                style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin: 0 -10px 30px -10px; page-break-inside: avoid;">
                <tr>
                    <td
                        style="width: 25%; border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; text-align: center; background-color: #f8fafc; vertical-align: middle;">
                        <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">Kas
                            Masuk (Lunas)</div>
                        <div style="font-size: 16px; color: #16a34a; font-weight: bold; margin-top: 8px;">Rp
                            {{ number_format($kasMasuk, 0, ',', '.') }}</div>
                    </td>
                    <td
                        style="width: 25%; border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; text-align: center; background-color: #f8fafc; vertical-align: middle;">
                        <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">Kas
                            Keluar (Lunas)</div>
                        <div style="font-size: 16px; color: #dc2626; font-weight: bold; margin-top: 8px;">Rp
                            {{ number_format($kasKeluar, 0, ',', '.') }}</div>
                    </td>
                    <td
                        style="width: 25%; border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; text-align: center; background-color: #f8fafc; vertical-align: middle;">
                        <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">
                            Total Piutang</div>
                        <div style="font-size: 16px; color: #0284c7; font-weight: bold; margin-top: 8px;">Rp
                            {{ number_format($totalPiutang, 0, ',', '.') }}</div>
                    </td>
                    <td
                        style="width: 25%; border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; text-align: center; background-color: #f8fafc; vertical-align: middle;">
                        <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">
                            Hutang Vendor</div>
                        <div style="font-size: 16px; color: #d97706; font-weight: bold; margin-top: 8px;">Rp
                            {{ number_format($totalHutang, 0, ',', '.') }}</div>
                    </td>
                </tr>
            </table>

            <div class="section-title">1. Buku Penjualan & Omzet Kasir</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 25%;">No. Invoice</th>
                        <th style="width: 30%;">Pelanggan</th>
                        <th style="width: 15%; text-align: center;">Tipe</th>
                        <th style="width: 15%; text-align: right;">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesData as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $sale->invoice_number }}</strong></td>
                            <td>{{ $sale->customer_name ?? 'Umum' }}</td>
                            <td class="text-center">{{ strtoupper($sale->payment_method) }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-3">Tidak ada data penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">GRAND TOTAL PENJUALAN (OMZET)</td>
                        <td class="text-end fw-bold" style="font-size: 13px;">Rp
                            {{ number_format($grandTotalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="section-title">2. Buku Pesanan Pembelian (Purchase Order / PO)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 25%;">No. PO</th>
                        <th style="width: 30%;">Vendor</th>
                        <th style="width: 15%; text-align: center;">Status</th>
                        <th style="width: 15%; text-align: right;">Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allPOs as $po)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $po->po_number }}</strong></td>
                            <td>{{ $po->vendor->name ?? '-' }}</td>
                            <td class="text-center">{{ strtoupper($po->status) }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-3">Tidak ada catatan pemesanan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="section-title">3. Buku Penerimaan Barang (Receive Order / RO)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tgl Masuk</th>
                        <th style="width: 25%;">No. RO (Referensi PO)</th>
                        <th style="width: 30%;">Vendor</th>
                        <th style="width: 15%; text-align: center;">Pembayaran</th>
                        <th style="width: 15%; text-align: right;">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allROs as $ro)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($ro->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $ro->ro_number }}</strong><br><span style="color: #666; font-size: 10px;">Ref:
                                    {{ $ro->purchaseOrder->po_number ?? '-' }}</span></td>
                            <td>{{ $ro->purchaseOrder->vendor->name ?? '-' }}</td>
                            <td class="text-center">{{ strtoupper($ro->payment_method) }}</td>
                            <td class="text-end fw-bold">Rp
                                {{ number_format($ro->total_amount ?? collect($ro->details)->sum('subtotal'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-3">Tidak ada catatan penerimaan barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">GRAND TOTAL SELURUH TAGIHAN RO</td>
                        <td class="text-end fw-bold" style="font-size: 13px;">Rp
                            {{ number_format($grandTotalRO, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="section-title">4. Tagihan Piutang (Pelanggan Menunggak)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 25%;">No. Invoice</th>
                        <th style="width: 40%;">Nama Pelanggan</th>
                        <th style="width: 20%; text-align: right;">Nominal Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesPiutang as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $p->invoice_number }}</strong></td>
                            <td>{{ $p->customer_name ?? 'Pelanggan Umum' }}</td>
                            <td class="text-end fw-bold" style="color: red;">Rp
                                {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-3">Tidak ada catatan piutang pada periode
                                ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAL AKUMULASI PIUTANG</td>
                        <td class="text-end fw-bold" style="color: red; font-size: 13px;">Rp
                            {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="section-title">5. Tagihan Hutang (Belum Dibayar ke Vendor)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tgl Masuk</th>
                        <th style="width: 25%;">No. RO</th>
                        <th style="width: 40%;">Nama Vendor</th>
                        <th style="width: 20%; text-align: right;">Nominal Hutang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roHutang as $h)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($h->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $h->ro_number }}</strong></td>
                            <td>{{ $h->purchaseOrder->vendor->name ?? '-' }}</td>
                            <td class="text-end fw-bold" style="color: #d97706;">Rp
                                {{ number_format($h->total_amount ?? collect($h->details)->sum('subtotal'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-3">Tidak ada catatan hutang pada periode
                                ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAL AKUMULASI HUTANG</td>
                        <td class="text-end fw-bold" style="color: #d97706; font-size: 13px;">Rp
                            {{ number_format($totalHutang, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="section-title">6. Audit Barang Menyusul (Sisa PO Menggantung)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tgl PO</th>
                        <th style="width: 25%;">Nomor PO & Vendor</th>
                        <th style="width: 45%;">Rincian Barang yang Belum Datang</th>
                        <th style="width: 15%; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poPending as $po)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $po->po_number }}</strong><br><span
                                    style="color: #555;">{{ $po->vendor->name ?? '-' }}</span></td>
                            <td>
                                <ul class="list-sisa">
                                    @php $hasMissing = false; @endphp
                                    @foreach($po->details ?? [] as $detail)
                                        @php $sisa = $detail->qty - ($detail->received_qty ?? 0); @endphp
                                        @if($sisa > 0)
                                            @php $hasMissing = true; @endphp
                                            <li>{{ $detail->product->name ?? 'Produk' }} (Sisa: <strong>{{ $sisa }}</strong>)</li>
                                        @endif
                                    @endforeach
                                    @if(!$hasMissing)
                                    <li style="color: #777;">-</li> @endif
                                </ul>
                            </td>
                            <td class="text-center">
                                <strong>{{ strtoupper($po->status == 'Diterima Sebagian' ? 'PARSIAL' : $po->status) }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-3">Semua pesanan (PO) sudah diterima/tidak
                                ada yang menggantung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="section-title">7. Audit Pembatalan Order (PO Ditutup Paksa)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tgl PO</th>
                        <th style="width: 25%;">Nomor PO & Vendor</th>
                        <th style="width: 45%;">Rincian Barang yang Batal/Hangus</th>
                        <th style="width: 15%; text-align: center;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poClosed as $po)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td><strong
                                    style="text-decoration: line-through; color: #555;">{{ $po->po_number }}</strong><br><span
                                    style="color: #555;">{{ $po->vendor->name ?? '-' }}</span></td>
                            <td>
                                <ul class="list-sisa" style="color: #555;">
                                    @foreach($po->details ?? [] as $detail)
                                        @php $sisa = $detail->qty - ($detail->received_qty ?? 0); @endphp
                                        @if($sisa > 0)
                                            <li><span
                                                    style="text-decoration: line-through;">{{ $detail->product->name ?? 'Produk' }}</span>
                                                <span style="color: red;">(Batal: {{ $sisa }})</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center"><strong style="color: red;">DITUTUP PAKSA</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-3">Tidak ada catatan PO yang ditutup paksa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="section-title">
                <div class="d-flex justify-content-between align-items-end">
                    <span>8. Audit Pemakaian Internal & Aset</span>
                    <span style="font-size: 11px; text-transform: none; color: #555; font-weight: normal;">Estimasi
                        Nilai Keluar: <strong>Rp {{ number_format($nilaiPemakaian, 0, ',', '.') }}</strong></span>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 35%;">Nama Barang / Sparepart</th>
                        <th style="width: 10%; text-align: center;">Qty</th>
                        <th style="width: 40%;">Keterangan Keperluan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usagesData as $usage)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($usage->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $usage->product->name ?? '-' }}</strong></td>
                            <td class="text-center">{{ $usage->qty }}</td>
                            <td>
                                @if($usage->purpose == 'Barang Hilang / Rusak (Write-off)')
                                    <span style="color: red; font-weight: bold;">{{ $usage->purpose }}</span>
                                @else
                                    {{ $usage->purpose }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-3">Tidak ada catatan pengeluaran aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="ttd-section" style="margin-top: 60px;">
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="width: 70%; border: none;"></td>
                        <td style="width: 30%; text-align: center; border: none; font-size: 12px;">
                            <p style="margin-bottom: 70px;">Palembang,
                                {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Pemilik / Penanggung Jawab,</p>
                            <p style="margin-bottom: 0; font-weight: bold; text-decoration: underline;">
                                {{ auth()->check() ? auth()->user()->name : 'Nug (Yulianus Febry T.N)' }}</p>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

    <script>
        function downloadPDF() {
            const btn = document.getElementById('btn-download');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses PDF...';
            btn.disabled = true;

            const wrapper = document.getElementById('pdf-wrapper');
            const element = document.getElementById('report-content');

            const oriMargin = wrapper.style.margin;
            const oriMaxWidth = wrapper.style.maxWidth;
            const oriBoxShadow = wrapper.style.boxShadow;

            window.scrollTo(0, 0);
            wrapper.style.margin = '0';
            wrapper.style.maxWidth = '1000px';
            wrapper.style.boxShadow = 'none';

            const opt = {
                margin: 15,
                filename: 'Laporan_Keuangan_BengkelAwi_{{ \Carbon\Carbon::now()->format("Ymd") }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0, scrollX: 0 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'], avoid: ['tr', '.ttd-section'] }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                wrapper.style.margin = oriMargin;
                wrapper.style.maxWidth = oriMaxWidth;
                wrapper.style.boxShadow = oriBoxShadow;
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error('PDF Error: ', err);
                wrapper.style.margin = oriMargin;
                wrapper.style.maxWidth = oriMaxWidth;
                wrapper.style.boxShadow = oriBoxShadow;
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Terjadi kesalahan saat memproses PDF. Silakan coba lagi.');
            });
        }
    </script>
</body>

</html>
