@extends('layouts.main')

@section('title', 'Penerimaan Barang (RO)')

@section('content')
    <style>
        .swal2-popup { border-radius: 20px !important; background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(24px) saturate(200%); border: 1px solid rgba(255, 255, 255, 0.9) !important; color: #0f172a !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important; }
        .swal2-title, .swal2-html-container { color: #0f172a !important; }
        [data-bs-theme="dark"] .swal2-popup { background: rgba(30, 41, 59, 0.95) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; color: #f8fafc !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important; }
        [data-bs-theme="dark"] .swal2-title, [data-bs-theme="dark"] .swal2-html-container { color: #f8fafc !important; }

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
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-truck-loading me-2 text-success"></i> Penerimaan Barang (RO)</h5>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form action="{{ route('receive-orders.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center me-lg-3">
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
                    <a href="{{ route('receive-orders.index') }}" class="btn btn-secondary shadow-sm" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                </form>

                <button class="btn btn-success fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addRoModal">
                    <i class="fas fa-plus me-2"></i> Catat Barang Masuk
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
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Nomor RO / Tanggal</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Referensi PO & Vendor</th>
                                <th class="text-center py-3 text-uppercase small fw-bold text-body-secondary">Pembayaran</th>
                                <th class="text-end py-3 text-uppercase small fw-bold text-body-secondary">Estimasi Total</th>
                                <th width="15%" class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receiveOrders as $index => $ro)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">{{ ($receiveOrders->currentPage() - 1) * $receiveOrders->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-body">{{ $ro->ro_number }}</div>
                                        <div class="small text-secondary">
                                            {{ \Carbon\Carbon::parse($ro->date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-body">{{ $ro->purchaseOrder->po_number ?? '-' }}</div>
                                        <div class="small text-info fw-bold"><i class="fas fa-building me-1"></i>
                                            {{ $ro->purchaseOrder->vendor->name ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($ro->payment_method == 'Tempo')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 d-block w-100">Tempo / Hutang</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 d-block w-100">Lunas / Cash</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-body">
                                        Rp {{ number_format($ro->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3 me-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#detailRoModal{{ $ro->id }}" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($ro->payment_method == 'Tempo')
                                            <form action="{{ route('receive-orders.mark-paid', $ro->id) }}" method="POST" class="d-inline form-lunasi">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 me-1 shadow-sm btn-lunasi" title="Lunasi Hutang">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('receive-orders.destroy', $ro->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus" title="Batalkan & Kurangi Stok">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-boxes fa-2x text-success"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Belum Ada Penerimaan Barang</h6>
                                        <p class="text-body-secondary small mb-0">Klik "Catat Barang Masuk" jika barang pesanan sudah tiba.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($receiveOrders->hasPages())
                    <div class="card-footer bg-transparent border-top border-secondary-subtle p-3 d-flex justify-content-center">
                        {{ $receiveOrders->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @foreach($receiveOrders as $ro)
        <div class="modal fade" id="detailRoModal{{ $ro->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                        <h5 class="modal-title fw-bold text-body"><i class="fas fa-clipboard-check me-2 text-success"></i>
                            Detail Penerimaan (RO)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="bg-success bg-opacity-10 border-bottom border-success-subtle p-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Nomor RO</p>
                                    <h5 class="fw-bold text-body mb-0">{{ $ro->ro_number }}</h5>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Tanggal Diterima</p>
                                    <h5 class="fw-bold text-body mb-0">{{ \Carbon\Carbon::parse($ro->date)->format('d M Y') }}</h5>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Referensi PO & Vendor</p>
                                    <h5 class="fw-bold text-body mb-0 text-truncate">{{ $ro->purchaseOrder->po_number ?? '-' }}
                                        <span class="fs-6 text-secondary">({{ $ro->purchaseOrder->vendor->name ?? '-' }})</span>
                                    </h5>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <p class="small text-body-secondary mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Pembayaran</p>
                                    <span class="badge {{ $ro->payment_method == 'Tempo' ? 'bg-danger text-white' : 'bg-success' }} rounded-pill px-3 py-2 w-100">
                                        {{ $ro->payment_method }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <h6 class="fw-bold text-body mb-3"><i class="fas fa-box-open me-2 text-secondary"></i>Fisik Barang yang Masuk</h6>
                            <div class="table-responsive border border-secondary-subtle" style="border-radius: 12px;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-body-tertiary border-bottom border-secondary-subtle">
                                        <tr>
                                            <th width="5%" class="small text-uppercase fw-bold text-body-secondary py-3 ps-4">No</th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3">Produk / Sparepart</th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3">Rak / Lokasi</th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3 text-center">Qty Masuk</th>
                                            <th class="small text-uppercase fw-bold text-body-secondary py-3 text-end pe-4">Estimasi Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ro->details as $idx => $detail)
                                            <tr>
                                                <td class="ps-4 text-body-secondary">{{ $idx + 1 }}</td>
                                                <td class="fw-bold text-body">{{ $detail->product->name ?? '-' }}</td>
                                                <td class="text-body"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $detail->location->name ?? '-' }}</td>
                                                <td class="text-center fw-bold text-body"><span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">{{ $detail->qty }}</span></td>
                                                <td class="text-end fw-bold text-body pe-4">Rp {{ number_format($detail->qty * ($detail->price ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <div class="bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-4 py-3 shadow-sm d-flex align-items-center" style="border-radius: 16px;">
                                    <div class="me-4 text-end">
                                        <span class="d-block fw-bold text-uppercase small opacity-75" style="letter-spacing: 1px;">Estimasi Total Tagihan</span>
                                    </div>
                                    <h2 class="fw-bold mb-0">Rp {{ number_format($ro->total_amount, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3" style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="addRoModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                    <h5 class="modal-title fw-bold text-body"><i class="fas fa-truck-loading me-2 text-success"></i> Validasi Barang Masuk (RO)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('receive-orders.store') }}" method="POST" id="formRO">
                    @csrf
                    <div class="modal-body p-4">

                        <div class="bg-body-tertiary border border-secondary-subtle p-4 mb-4" style="border-radius: 16px;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-body fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Referensi PO <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm" name="purchase_order_id" id="poSelect" required>
                                        <option value="" disabled selected>-- Pilih PO Menunggu --</option>
                                        @foreach($purchaseOrders ?? [] as $po)
                                            <option value="{{ $po->id }}">{{ $po->po_number }} ({{ $po->vendor->name ?? '' }}) - {{ $po->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-body fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Pembayaran Ke Vendor <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm" name="payment_method" required>
                                        <option value="Cash">Cash / Lunas</option>
                                        <option value="Tempo">Tempo / Hutang</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-body fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Tanggal Diterima <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm" name="date" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-body mb-3"><i class="fas fa-clipboard-check me-2 text-secondary"></i>Fisik Barang yang Diterima & Penempatan</h6>
                        <div class="table-responsive border border-secondary-subtle" style="border-radius: 12px; max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-body-tertiary border-bottom border-secondary-subtle shadow-sm" style="position: sticky; top: 0; z-index: 2;">
                                    <tr>
                                        <th width="30%" class="small text-uppercase fw-bold text-body-secondary py-3 ps-4">Produk (Dari PO)</th>
                                        <th width="10%" class="text-center small text-uppercase fw-bold text-body-secondary py-3">Pesanan</th>
                                        <th width="10%" class="text-center small text-uppercase fw-bold text-info py-3">Sisa Menunggu</th>
                                        <th width="20%" class="small text-uppercase fw-bold text-body-secondary py-3">Lokasi Simpan (Rak)</th>
                                        <th width="15%" class="small text-uppercase fw-bold text-success py-3 text-center">Qty Fisik Masuk</th>
                                        <th width="15%" class="small text-uppercase fw-bold text-body-secondary py-3 text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="roTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-search fs-3 text-secondary mb-2 opacity-50 d-block"></i>
                                            <span class="text-body-secondary small">Silakan pilih <b>Referensi PO</b> di atas untuk menarik data pesanan secara otomatis.</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="small text-danger mt-2 fw-bold d-none" id="errorWarning">
                            <i class="fas fa-exclamation-triangle me-1"></i> Terdapat input Qty yang melebihi batas "Sisa Menunggu"!
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <div class="bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-4 py-3 shadow-sm d-flex align-items-center" style="border-radius: 16px;">
                                <div class="me-4 text-end">
                                    <span class="d-block fw-bold text-uppercase small opacity-75" style="letter-spacing: 1px;">Estimasi Tagihan</span>
                                </div>
                                <h2 class="fw-bold mb-0" id="roGrandTotalDisplay">Rp 0</h2>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3" style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" id="btnSubmitRO" disabled>
                            <i class="fas fa-save me-2"></i> Simpan & Update Stok
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
            const poSelect = document.getElementById('poSelect');
            const roTableBody = document.getElementById('roTableBody');
            const roGrandTotalDisplay = document.getElementById('roGrandTotalDisplay');
            const btnSubmitRO = document.getElementById('btnSubmitRO');
            const errorWarning = document.getElementById('errorWarning');

            let locations = [];
            try {
                locations = {!! json_encode($locations ?? []) !!};
            } catch (e) {
                console.error("Gagal memuat data lokasi");
            }

            let locationOptions = '<option value="" disabled selected>Pilih Rak...</option>';
            if (Array.isArray(locations) && locations.length > 0) {
                locations.forEach(loc => { locationOptions += `<option value="${loc.id}">${loc.name}</option>`; });
            } else {
                locationOptions += `<option value="1">Gudang Utama</option>`;
            }

            function formatRupiah(number) { return new Intl.NumberFormat('id-ID').format(number); }

            function calculateRoTotal() {
                let grandTotal = 0;
                let isValid = true;
                let hasIncomingItem = false;

                roTableBody.querySelectorAll('tr.item-row').forEach(row => {
                    const price = parseFloat(row.querySelector('.ro-price-input').value) || 0;
                    const qtyInput = row.querySelector('.ro-qty-input');
                    const qty = parseInt(qtyInput.value) || 0;
                    const maxSisa = parseInt(qtyInput.getAttribute('max')) || 0;
                    const subtotal = price * qty;

                    const subtotalInput = row.querySelector('.ro-subtotal-display');
                    if (subtotalInput) subtotalInput.value = formatRupiah(subtotal);

                    grandTotal += subtotal;

                    if (qty > 0) hasIncomingItem = true;

                    if (qty > maxSisa || qty < 0) {
                        isValid = false;
                        qtyInput.classList.add('is-invalid', 'bg-danger', 'bg-opacity-10');
                    } else {
                        qtyInput.classList.remove('is-invalid', 'bg-danger', 'bg-opacity-10');
                    }
                });

                roGrandTotalDisplay.innerText = 'Rp ' + formatRupiah(grandTotal);

                if (!isValid) {
                    errorWarning.classList.remove('d-none');
                    btnSubmitRO.disabled = true;
                } else if (!hasIncomingItem) {
                    errorWarning.classList.add('d-none');
                    btnSubmitRO.disabled = true;
                } else {
                    errorWarning.classList.add('d-none');
                    btnSubmitRO.disabled = false;
                }
            }

            poSelect.addEventListener('change', async function () {
                const poId = this.value;
                roTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-success mb-2"></i><br><span class="text-secondary small">Menarik rincian dari PO...</span></td></tr>`;
                btnSubmitRO.disabled = true;

                try {
                    const response = await fetch(`{{ url('/receive-orders/get-po-details') }}/${poId}`);
                    if (!response.ok) throw new Error('Terjadi kesalahan pada Server');

                    const data = await response.json();
                    roTableBody.innerHTML = '';

                    const items = data.details || [];

                    if (items.length === 0) {
                        roTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-success fw-bold"><i class="fas fa-check-circle me-2"></i>Semua barang pada PO ini sudah diterima/ditutup.</td></tr>`;
                    } else {
                        items.forEach((item, index) => {
                            const tr = document.createElement('tr');
                            tr.className = 'item-row';

                            tr.innerHTML = `
                                    <td class="ps-4 py-3">
                                        <input type="hidden" name="product_id[]" value="${item.id}">
                                        <input type="hidden" name="price[]" class="ro-price-input" value="${item.price}">
                                        <div class="fw-bold text-body">${item.name}</div>
                                        <div class="small text-secondary">Hrg Beli: Rp ${formatRupiah(item.price)}</div>
                                    </td>
                                    <td class="text-center py-3"><span class="badge bg-secondary bg-opacity-25 text-body rounded-pill px-3">${item.po_qty}</span></td>
                                    <td class="text-center py-3"><span class="badge bg-info text-white rounded-pill px-3 fs-6">${item.sisa_qty}</span></td>
                                    <td class="py-3">
                                        <select class="form-select text-body bg-body border-secondary-subtle fw-semibold" name="location_id[]" required>
                                            ${locationOptions}
                                        </select>
                                    </td>
                                    <td class="py-3 text-center">
                                        <input type="number" name="qty[]" class="form-control text-center text-success bg-body border-success ro-qty-input fw-bold" required min="0" max="${item.sisa_qty}" value="${item.sisa_qty}">
                                    </td>
                                    <td class="py-3 text-end pe-4">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span class="text-body-secondary me-2 fw-bold">Rp</span>
                                            <input type="text" class="form-control bg-transparent border-0 text-body fw-bold fs-6 p-0 text-end ro-subtotal-display" readonly value="0" style="width: 100px;">
                                        </div>
                                    </td>
                                `;
                            roTableBody.appendChild(tr);
                            tr.style.opacity = '0';
                            setTimeout(() => tr.style.opacity = '1', 50);
                        });

                        calculateRoTotal();
                    }
                } catch (error) {
                    console.error(error);
                    roTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger fw-bold"><i class="fas fa-exclamation-triangle fs-3 mb-2"></i><br>Gagal menarik data PO.</td></tr>`;
                }
            });

            roTableBody.addEventListener('input', calculateRoTotal);

            const setupAlert = (selector, title, text, icon, confirmText) => {
                document.querySelectorAll(selector).forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault(); const form = this.closest('form');

                        Swal.fire({
                            title: title,
                            text: text,
                            icon: icon,
                            showCancelButton: true,
                            confirmButtonText: confirmText,
                            cancelButtonText: 'Batal'
                        }).then((res) => {
                            if (res.isConfirmed) {
                                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                                this.classList.add('disabled');
                                form.submit();
                            }
                        });
                    });
                });
            };

            setupAlert('.btn-hapus', 'Hapus RO & Kurangi Stok?', 'Stok fisik di gudang akan dikurangi kembali dan status PO akan di-reset menjadi Pending!', 'warning', '<i class="fas fa-trash me-2"></i>Ya, Hapus RO');
            setupAlert('.btn-lunasi', 'Lunasi Hutang RO?', 'Tagihan ini akan ditandai Lunas (Cash).', 'question', '<i class="fas fa-check-double me-2"></i>Ya, Lunasi');

            document.getElementById('formRO').addEventListener('submit', () => {
                btnSubmitRO.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses Stok...';
                btnSubmitRO.disabled = true;
            });
        });
    </script>
@endpush
