@extends('layouts.main')

@section('title', 'Purchase Order (PO)')

@section('content')
    <style>
        .swal2-popup {
            border-radius: 20px !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }
        .swal2-title, .swal2-html-container { color: #0f172a !important; }

        [data-bs-theme="dark"] .swal2-popup {
            background: rgba(30, 41, 59, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
        }
        [data-bs-theme="dark"] .swal2-title,
        [data-bs-theme="dark"] .swal2-html-container { color: #f8fafc !important; }

        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.02); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.15); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.25); }
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); }
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.25); }

        .table-responsive.custom-scrollbar thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background-color: var(--bs-tertiary-bg);
        }
        .pagination { margin-bottom: 0; }
    </style>

    <div class="container-fluid py-2">

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i> Pesanan Pembelian (PO)</h5>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form action="{{ route('purchase-orders.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center me-lg-3">
                    <div class="input-group shadow-sm" style="width: auto;">
                        <span class="input-group-text bg-body border-secondary-subtle text-body-secondary"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" class="form-control text-body bg-body border-secondary-subtle" name="start_date" value="{{ $startDate }}">
                    </div>
                    <span class="text-body-secondary fw-bold">s/d</span>
                    <div class="input-group shadow-sm" style="width: auto;">
                        <span class="input-group-text bg-body border-secondary-subtle text-body-secondary"><i class="fas fa-calendar-check"></i></span>
                        <input type="date" class="form-control text-body bg-body border-secondary-subtle" name="end_date" value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary shadow-sm" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                </form>

                <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addPoModal">
                    <i class="fas fa-plus me-2"></i> Buat PO Baru
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0 overflow-hidden">
                <div class="table-responsive custom-scrollbar" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="border-bottom border-secondary-subtle">
                            <tr>
                                <th width="5%" class="text-center py-3 ps-4 text-uppercase small fw-bold text-body-secondary">No</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Nomor PO / Tanggal</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Vendor</th>
                                <th class="text-center py-3 text-uppercase small fw-bold text-body-secondary">Status</th>
                                <th class="text-end py-3 text-uppercase small fw-bold text-body-secondary">Grand Total</th>
                                <th width="15%" class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $index => $po)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">{{ ($purchaseOrders->currentPage() - 1) * $purchaseOrders->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-body">{{ $po->po_number }}</div>
                                        <div class="small text-secondary">
                                            {{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}</div>
                                    </td>
                                    <td class="fw-bold text-body">{{ $po->vendor->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($po->status == 'Pending')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                                        @elseif($po->status == 'Diterima Sebagian' || $po->status == 'Parsial')
                                            <span class="badge bg-info text-white rounded-pill px-3 py-2">Parsial (Sebagian)</span>
                                        @elseif($po->status == 'Ditutup')
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">Ditutup Paksa</span>
                                        @else
                                            <span class="badge bg-success rounded-pill px-3 py-2">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-body">
                                        Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <button type="button"
                                            class="btn btn-sm btn-info text-white rounded-pill px-3 me-1 shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#detailPoModal{{ $po->id }}"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if(in_array($po->status, ['Pending', 'Parsial', 'Diterima Sebagian']))
                                            <form action="{{ route('purchase-orders.close', $po->id) }}" method="POST"
                                                class="d-inline form-tutup">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm btn-tutup-po me-1"
                                                    title="Tutup PO (Batalkan sisa barang)">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($po->status == 'Pending')
                                            <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST"
                                                class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus"
                                                    title="Hapus PO">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Belum Ada Transaksi PO</h6>
                                        <p class="text-body-secondary small mb-0">Klik tombol "Buat PO Baru" untuk memesan
                                            barang ke Vendor.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($purchaseOrders->hasPages())
                    <div class="card-footer bg-transparent border-top border-secondary-subtle p-3 d-flex justify-content-center">
                        {{ $purchaseOrders->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @foreach($purchaseOrders as $po)
        <div class="modal fade" id="detailPoModal{{ $po->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                        <h5 class="modal-title fw-bold text-body"><i class="fas fa-file-invoice-dollar me-2 text-info"></i>
                            Detail Pesanan Pembelian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="bg-info bg-opacity-10 border-bottom border-info-subtle p-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold"
                                        style="letter-spacing: 0.5px;">Nomor PO</p>
                                    <h5 class="fw-bold text-body mb-0">{{ $po->po_number }}</h5>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold"
                                        style="letter-spacing: 0.5px;">Tanggal Pesan</p>
                                    <h5 class="fw-bold text-body mb-0">{{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}
                                    </h5>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold"
                                        style="letter-spacing: 0.5px;">Vendor / Supplier</p>
                                    <h5 class="fw-bold text-body mb-0 text-truncate"><i
                                            class="fas fa-building me-2 text-info"></i>{{ $po->vendor->name ?? '-' }}</h5>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold"
                                        style="letter-spacing: 0.5px;">Status</p>
                                    @if($po->status == 'Pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 w-100">Pending</span>
                                    @elseif($po->status == 'Diterima Sebagian' || $po->status == 'Parsial')
                                        <span class="badge bg-info text-white rounded-pill px-3 py-2 w-100">Parsial</span>
                                    @elseif($po->status == 'Ditutup')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 w-100">Ditutup</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3 py-2 w-100">Selesai</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <h6 class="fw-bold text-body mb-3"><i class="fas fa-box-open me-2 text-secondary"></i>Item yang
                                Dipesan</h6>
                            <div class="table-responsive border border-secondary-subtle" style="border-radius: 12px;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-body-tertiary border-bottom border-secondary-subtle">
                                        <tr>
                                            <th width="5%" class="small text-uppercase fw-bold text-body-secondary py-3 ps-4">No
                                            </th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3">Produk / Sparepart
                                            </th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3 text-end">Harga
                                                Beli</th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3 text-center">Dipesan
                                            </th>
                                            <th class="small text-uppercase fw-bold text-success py-3 text-center">Diterima
                                            </th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3 text-end pe-4">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($po->details ?? [] as $idx => $detail)
                                            <tr>
                                                <td class="ps-4 text-body-secondary">{{ $idx + 1 }}</td>
                                                <td class="fw-bold text-body">{{ $detail->product->name ?? 'Produk Dihapus' }}</td>
                                                <td class="text-end text-body">Rp {{ number_format($detail->price, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center fw-bold text-body">
                                                    <span
                                                        class="badge bg-secondary bg-opacity-25 text-body rounded-pill px-3">{{ $detail->qty }}</span>
                                                </td>
                                                <td class="text-center fw-bold text-success">
                                                    <span
                                                        class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">{{ $detail->received_qty ?? 0 }}</span>
                                                </td>
                                                <td class="text-end fw-bold text-body pe-4">Rp
                                                    {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-danger fw-bold">Tidak ada rincian
                                                    barang ditemukan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary px-4 py-3 shadow-sm d-flex align-items-center"
                                    style="border-radius: 16px;">
                                    <div class="me-4 text-end">
                                        <span class="d-block fw-bold text-uppercase small opacity-75"
                                            style="letter-spacing: 1px;">Grand Total PO</span>
                                    </div>
                                    <h2 class="fw-bold mb-0">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3"
                        style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="addPoModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                    <h5 class="modal-title fw-bold text-body"><i class="fas fa-plus-circle me-2 text-primary"></i> Form Buat
                        PO Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('purchase-orders.store') }}" method="POST" id="formPO">
                    @csrf
                    <div class="modal-body p-4">

                        <div class="bg-body-tertiary border border-secondary-subtle p-4 mb-4" style="border-radius: 16px;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-body fw-bold small text-uppercase"
                                        style="letter-spacing: 0.5px;">Vendor / Pemasok <span
                                            class="text-danger">*</span></label>
                                    <select
                                        class="form-select form-select-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm"
                                        name="vendor_id" id="vendorSelect" required>
                                        <option value="" disabled selected>-- Pilih Vendor Terlebih Dahulu --</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-body fw-bold small text-uppercase"
                                        style="letter-spacing: 0.5px;">Tanggal Pemesanan <span
                                            class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control form-control-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm"
                                        name="order_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-body mb-0"><i class="fas fa-list-ul me-2 text-secondary"></i>Daftar
                                Barang Pesanan</h6>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm d-none"
                                id="addRowBtn">
                                <i class="fas fa-plus me-1"></i> Tambah Item
                            </button>
                        </div>

                        <div class="table-responsive border border-secondary-subtle" style="border-radius: 12px; max-height: 400px; overflow-y: auto;">
                            <table class="table table-borderless align-middle mb-0" id="poTable">
                                <thead class="bg-body-tertiary border-bottom border-secondary-subtle" style="position: sticky; top: 0; z-index: 2;">
                                    <tr>
                                        <th width="35%" class="small text-uppercase fw-bold text-body-secondary py-3 ps-4">
                                            Produk / Sparepart</th>
                                        <th width="25%" class="small text-uppercase fw-bold text-body-secondary py-3">Harga
                                            Beli</th>
                                        <th width="15%"
                                            class="small text-uppercase fw-bold text-body-secondary py-3 text-center">Qty
                                        </th>
                                        <th width="20%"
                                            class="small text-uppercase fw-bold text-body-secondary py-3 text-end pe-2">
                                            Subtotal</th>
                                        <th width="5%"
                                            class="small text-uppercase fw-bold text-body-secondary py-3 text-center pe-4">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="poTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-info-circle text-secondary fs-3 mb-2 d-block opacity-50"></i>
                                            <span class="text-body-secondary small">Silakan pilih Vendor di atas untuk
                                                memunculkan daftar produk terkait.</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary px-4 py-3 shadow-sm d-flex align-items-center"
                                style="border-radius: 16px;">
                                <div class="me-4 text-end">
                                    <span class="d-block fw-bold text-uppercase small opacity-75"
                                        style="letter-spacing: 1px;">Estimasi Total</span>
                                </div>
                                <h2 class="fw-bold mb-0" id="grandTotalDisplay">Rp 0</h2>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3"
                        style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="btnSubmitPO"
                            disabled>
                            <i class="fas fa-paper-plane me-2"></i> Buat Dokumen PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let currentVendorProducts = [];
            const vendorSelect = document.getElementById('vendorSelect');
            const tableBody = document.getElementById('poTableBody');
            const addRowBtn = document.getElementById('addRowBtn');
            const grandTotalDisplay = document.getElementById('grandTotalDisplay');
            const btnSubmitPO = document.getElementById('btnSubmitPO');
            const formPO = document.getElementById('formPO');

            vendorSelect.addEventListener('change', async function () {
                const vendorId = this.value;

                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i><br><span class="text-secondary small">Menarik data dari gudang...</span></td></tr>`;
                btnSubmitPO.disabled = true;
                addRowBtn.classList.add('d-none');

                try {
                    const response = await fetch(`{{ url('/get-vendor-products') }}/${vendorId}`);
                    currentVendorProducts = await response.json();

                    tableBody.innerHTML = '';

                    if (currentVendorProducts.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-danger fw-bold">Vendor ini tidak memiliki stok barang yang terdaftar di master data.</td></tr>`;
                    } else {
                        addDynamicRow();
                        addRowBtn.classList.remove('d-none');
                    }
                    calculateTotal();
                } catch (error) {
                    console.error(error);
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-danger fw-bold">Koneksi terputus. Gagal mengambil data.</td></tr>`;
                }
            });

            function addDynamicRow() {
                const tr = document.createElement('tr');
                let optionsHtml = '<option value="" disabled selected>Pilih Produk...</option>';

                currentVendorProducts.forEach(p => {
                    optionsHtml += `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (Stok: ${p.stock})</option>`;
                });

                tr.innerHTML = `
                    <td class="ps-4 py-3">
                        <select name="product_id[]" class="form-select text-body bg-body border-secondary-subtle product-select fw-semibold" required>
                            ${optionsHtml}
                        </select>
                    </td>
                    <td class="py-3">
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-secondary-subtle text-body-secondary fw-bold">Rp</span>
                            <input type="number" name="price[]" class="form-control text-body bg-body border-secondary-subtle price-input fw-semibold" required min="0" placeholder="0">
                        </div>
                    </td>
                    <td class="py-3 text-center">
                        <input type="number" name="qty[]" class="form-control text-center text-body bg-body border-secondary-subtle qty-input fw-bold" required min="1" value="1">
                    </td>
                    <td class="py-3 text-end pe-2">
                        <div class="d-flex align-items-center justify-content-end">
                            <span class="text-body-secondary me-2 fw-bold">Rp</span>
                            <input type="text" class="form-control bg-transparent border-0 text-body fw-bold fs-6 p-0 text-end subtotal-display" readonly value="0" style="width: 100px;">
                        </div>
                    </td>
                    <td class="text-center pe-4 py-3">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle remove-row-btn" style="width: 32px; height: 32px;" title="Hapus Baris">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
                tr.style.opacity = '0';
                setTimeout(() => tr.style.opacity = '1', 50);
            }

            addRowBtn.addEventListener('click', addDynamicRow);

            function calculateTotal() {
                let grandTotal = 0;
                const rows = tableBody.querySelectorAll('tr');
                let hasValidProduct = false;

                rows.forEach(row => {
                    const priceInput = row.querySelector('.price-input');
                    const qtyInput = row.querySelector('.qty-input');
                    const subtotalInput = row.querySelector('.subtotal-display');

                    if (priceInput && qtyInput && subtotalInput) {
                        hasValidProduct = true;
                        const price = parseFloat(priceInput.value) || 0;
                        const qty = parseInt(qtyInput.value) || 0;
                        const subtotal = price * qty;

                        subtotalInput.value = new Intl.NumberFormat('id-ID').format(subtotal);
                        grandTotal += subtotal;
                    }
                });

                grandTotalDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
                btnSubmitPO.disabled = !hasValidProduct;
            }

            tableBody.addEventListener('input', calculateTotal);

            tableBody.addEventListener('change', function (e) {
                if (e.target.classList.contains('product-select')) {
                    const price = e.target.options[e.target.selectedIndex].dataset.price;
                    e.target.closest('tr').querySelector('.price-input').value = Math.round(price);
                    calculateTotal();
                }
            });

            tableBody.addEventListener('click', (e) => {
                if (e.target.closest('.remove-row-btn')) {
                    e.target.closest('tr').remove();
                    calculateTotal();

                    if (tableBody.querySelectorAll('tr').length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-secondary"><i class="fas fa-shopping-basket fa-2x mb-3 opacity-50"></i><br>Keranjang kosong. Klik "Tambah Item" untuk memasukkan barang.</td></tr>`;
                        btnSubmitPO.disabled = true;
                    }
                }
            });

            formPO.addEventListener('submit', function () {
                btnSubmitPO.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                btnSubmitPO.disabled = true;
            });

            const deleteButtons = document.querySelectorAll('.btn-hapus');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Batalkan PO?',
                        text: "Dokumen ini akan dibatalkan secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                            this.classList.add('disabled');
                            form.submit();
                        }
                    });
                });
            });

            const closeButtons = document.querySelectorAll('.btn-tutup-po');
            closeButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Tutup PO Paksa?',
                        text: "Sisa barang yang belum datang akan dibatalkan permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: '<i class="fas fa-power-off me-2"></i>Ya, Tutup PO',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                            this.classList.add('disabled');
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>
@endpush
