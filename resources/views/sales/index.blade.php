@extends('layouts.main')

@section('title', 'Penjualan (Kasir)')

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
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .pagination { margin-bottom: 0; }
    </style>

    <div class="container-fluid py-2">
        @if(isset($notifPiutang) && $notifPiutang->count() > 0)
            <div
                class="alert bg-warning bg-opacity-10 border border-warning border-opacity-50 rounded-4 d-flex align-items-center p-4 mb-4 shadow-sm">
                <div class="bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center me-4 shadow-sm"
                    style="width: 60px; height: 60px; flex-shrink: 0;">
                    <i class="fas fa-hand-holding-usd fa-2x"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-warning-emphasis mb-1" style="letter-spacing: 0.5px;">Terdapat Tagihan Pelanggan
                        (Piutang)</h5>
                    <p class="text-body-secondary mb-0">Ada <b>{{ $notifPiutang->count() }} transaksi</b> senilai <b
                            class="text-warning-emphasis">Rp
                            {{ number_format($notifPiutang->sum('total_amount'), 0, ',', '.') }}</b> yang belum dibayar oleh
                        pelanggan. Klik tombol <span class="badge bg-success"><i class="fas fa-check-double"></i> Lunasi</span>
                        di tabel bawah jika pelanggan sudah membayar.</p>
                </div>
            </div>
        @endif

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-shopping-bag me-2 text-primary"></i> Pencatatan Penjualan &
                Servis</h5>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form action="{{ route('sales.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center me-lg-3">
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
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary shadow-sm" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                </form>

                <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addSaleModal">
                    <i class="fas fa-plus me-2"></i> Buat Transaksi
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0 overflow-hidden">
                <div class="table-responsive custom-scrollbar" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-body-tertiary border-bottom border-secondary-subtle">
                            <tr>
                                <th width="5%" class="text-center py-3 ps-4 text-uppercase small fw-bold text-body-secondary">No</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">No. Invoice & Tanggal</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Pelanggan & Kategori</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary text-center">Pembayaran</th>
                                <th class="text-end py-3 text-uppercase small fw-bold text-body-secondary">Total Transaksi</th>
                                <th width="15%" class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $index => $sale)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $sale->invoice_number }}</div>
                                        <div class="small text-secondary">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-body"><i class="fas fa-user-circle me-1 text-secondary"></i> {{ $sale->customer_name ?? 'Umum' }}</div>
                                        <div class="small text-info fw-bold mt-1"><i class="fas fa-wrench me-1 opacity-75"></i> {{ $sale->saleType->name ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $sale->payment_method == 'Tempo' ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-1">{{ $sale->payment_method }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-body fs-6">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3 me-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#detailSaleModal{{ $sale->id }}" title="Detail / Nota">
                                            <i class="fas fa-receipt"></i>
                                        </button>

                                        @if($sale->payment_method == 'Tempo')
                                            <form action="{{ route('sales.mark-paid', $sale->id) }}" method="POST" class="d-inline form-lunasi">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 me-1 shadow-sm btn-lunasi" title="Tandai Sudah Dibayar (Lunas)">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus" title="Void Transaksi">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px;">
                                            <i class="fas fa-shopping-bag fa-2x text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Belum Ada Transaksi</h6>
                                        <p class="text-body-secondary small">Coba ubah filter tanggal atau tambahkan transaksi baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="card-footer bg-transparent border-top border-secondary-subtle p-3 d-flex justify-content-center">
                        {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @foreach($sales as $sale)
        <div class="modal fade" id="detailSaleModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                        <h5 class="modal-title fw-bold text-body"><i class="fas fa-receipt me-2 text-primary"></i> Detail Transaksi / Nota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row mb-4">
                            <div class="col-6">
                                <small class="text-secondary fw-bold d-block">NO. INVOICE</small>
                                <h5 class="fw-bold text-primary">{{ $sale->invoice_number }}</h5>
                                <small class="text-secondary fw-bold d-block mt-2">PELANGGAN</small>
                                <span class="fw-bold text-body">{{ $sale->customer_name ?? 'Umum' }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-secondary fw-bold d-block">KATEGORI</small>
                                <span class="fw-bold text-body">{{ $sale->saleType->name ?? '-' }}</span>
                                <small class="text-secondary fw-bold d-block mt-2">METODE</small>
                                <span class="badge {{ $sale->payment_method == 'Tempo' ? 'bg-danger' : 'bg-success' }} fs-6">{{ $sale->payment_method }}</span>
                            </div>
                        </div>

                        @if($sale->service_fee > 0)
                            <div class="alert alert-info border-info border-opacity-25 rounded-3 mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="fas fa-wrench me-2"></i>Biaya Jasa / Servis</h6>
                                    <small>{{ $sale->service_name }}</small>
                                </div>
                                <h5 class="fw-bold mb-0">Rp {{ number_format($sale->service_fee, 0, ',', '.') }}</h5>
                            </div>
                        @endif

                        @if($sale->details->count() > 0)
                            <h6 class="fw-bold text-body mb-2"><i class="fas fa-box-open me-2 text-secondary"></i>Rincian Sparepart/Oli</h6>
                            <div class="table-responsive border border-secondary-subtle rounded-3 mb-4">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-body-tertiary">
                                        <tr>
                                            <th class="small text-secondary py-2 ps-3">Item Barang</th>
                                            <th class="small text-secondary py-2 text-end">Harga</th>
                                            <th class="small text-secondary py-2 text-center">Qty</th>
                                            <th class="small text-secondary py-2 text-end pe-3">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->details as $detail)
                                            <tr>
                                                <td class="ps-3 fw-bold text-body">{{ $detail->item_name }}</td>
                                                <td class="text-end text-body">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                                <td class="text-center fw-bold text-body">{{ $detail->qty }}</td>
                                                <td class="text-end fw-bold text-body pe-3">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="d-flex justify-content-end">
                            <div class="bg-body-tertiary p-3 rounded-3 border border-secondary-subtle text-end" style="min-width: 250px;">
                                <span class="text-secondary fw-bold d-block small">GRAND TOTAL</span>
                                <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="addSaleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable mt-4 mb-4">
            <form action="{{ route('sales.store') }}" method="POST" id="formSale" class="modal-content border-0 shadow-lg"
                style="border-radius: 20px;">
                @csrf
                <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                    <h5 class="modal-title fw-bold text-body"><i class="fas fa-shopping-bag me-2 text-primary"></i> Pencatatan Transaksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-body">
                    <div class="bg-body-tertiary border border-secondary-subtle p-4 mb-4" style="border-radius: 16px;">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label text-body fw-bold small text-uppercase">Nama Pelanggan</label>
                                <input type="text" class="form-control form-control-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm" name="customer_name" placeholder="Umum">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-body fw-bold small text-uppercase">Tipe Transaksi <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg text-body bg-primary text-white border-primary fw-bold shadow-sm" name="sale_type" id="saleTypeSelect" required>
                                    <option value="" disabled selected>-- Pilih Tipe --</option>
                                    <option value="Jasa / Servis">Hanya Jasa / Servis</option>
                                    <option value="Pembelian Sparepart">Hanya Beli Sparepart</option>
                                    <option value="Servis & Pembelian Sparepart">Servis + Beli Sparepart</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-body fw-bold small text-uppercase">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm" name="payment_method" required>
                                    <option value="Cash">Tunai / Transfer (Lunas)</option>
                                    <option value="Tempo">Hutang / Tempo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-body fw-bold small text-uppercase">Waktu Transaksi</label>
                                <input type="datetime-local" class="form-control form-control-lg text-body bg-body border-secondary-subtle fw-semibold shadow-sm" name="date" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                    </div>

                    <div id="sectionJasa" class="d-none mb-4">
                        <h6 class="fw-bold text-body mb-3"><i class="fas fa-wrench me-2 text-info"></i>Rincian Biaya Jasa / Servis</h6>
                        <div class="row g-3 p-4 border border-info border-opacity-50 rounded-4 bg-info bg-opacity-10">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-uppercase">Keterangan Servis</label>
                                <input type="text" class="form-control text-body bg-body border-secondary-subtle fw-semibold" name="service_name" placeholder="Cth: Servis Rutin, Ganti Kampas Rem">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Biaya Jasa</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-secondary-subtle fw-bold">Rp</span>
                                    <input type="number" class="form-control text-body bg-body border-secondary-subtle fw-bold text-end" name="service_fee" id="serviceFee" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sectionProduk" class="d-none mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-body mb-0"><i class="fas fa-box-open me-2 text-secondary"></i>Keranjang Sparepart/Oli</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 shadow-sm" id="addRowBtn">
                                <i class="fas fa-plus me-1"></i> Tambah Item
                            </button>
                        </div>
                        <div class="table-responsive border border-secondary-subtle" style="border-radius: 12px; max-height: 350px; overflow-y: auto;">
                            <table class="table table-borderless align-middle mb-0" id="saleTable">
                                <thead class="bg-body-tertiary border-bottom border-secondary-subtle shadow-sm" style="position: sticky; top: 0; z-index: 2;">
                                    <tr>
                                        <th width="35%" class="small text-uppercase fw-bold text-secondary py-3 ps-4">Pilih Produk</th>
                                        <th width="20%" class="small text-uppercase fw-bold text-secondary py-3">Rak Pengambilan</th>
                                        <th width="20%" class="small text-uppercase fw-bold text-secondary py-3 text-end">Harga Jual</th>
                                        <th width="10%" class="small text-uppercase fw-bold text-secondary py-3 text-center">Qty</th>
                                        <th width="10%" class="small text-uppercase fw-bold text-secondary py-3 text-end">Subtotal</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="saleTableBody">
                                    <tr class="item-row border-bottom border-secondary-subtle">
                                        <td class="ps-4 py-3">
                                            <select name="product_id[]" class="form-select text-body bg-body border-secondary-subtle product-select fw-semibold">
                                                <option value="" selected>Pilih Produk...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">{{ $product->name }} (Sisa: {{ $product->stock }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-3">
                                            <select class="form-select text-body bg-body border-secondary-subtle fw-medium location-select" name="location_id[]" disabled>
                                                <option value="" selected>Tunggu...</option>
                                            </select>
                                        </td>
                                        <td class="py-3 text-end">
                                            <input type="number" name="price[]" class="form-control text-end fw-semibold price-input border-secondary-subtle" min="0" placeholder="0">
                                        </td>
                                        <td class="py-3 text-center">
                                            <input type="number" name="qty[]" class="form-control text-center fw-bold qty-input border-secondary-subtle" min="1" value="1">
                                        </td>
                                        <td class="py-3 text-end">
                                            <span class="fw-bold subtotal-text text-body">Rp 0</span>
                                        </td>
                                        <td class="text-center py-3 pe-4">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn d-none" title="Hapus Baris"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-4 p-4 text-end shadow-sm" style="min-width: 300px;">
                            <p class="text-primary fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">Grand Total Tagihan</p>
                            <h2 class="fw-bold text-body mb-0" id="grandTotalDisplay">Rp 0</h2>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3"
                    style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="btnSubmitSale" disabled>
                        <i class="fas fa-save me-2"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const saleTypeSelect = document.getElementById('saleTypeSelect');
            const sectionJasa = document.getElementById('sectionJasa');
            const sectionProduk = document.getElementById('sectionProduk');
            const serviceFeeInput = document.getElementById('serviceFee');
            const tableBody = document.getElementById('saleTableBody');
            const addRowBtn = document.getElementById('addRowBtn');
            const btnSubmitSale = document.getElementById('btnSubmitSale');
            const grandTotalDisplay = document.getElementById('grandTotalDisplay');
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            function formatRupiah(number) { return new Intl.NumberFormat('id-ID').format(number); }

            saleTypeSelect.addEventListener('change', function () {
                const val = this.value;
                if (val === 'Jasa / Servis') {
                    sectionJasa.classList.remove('d-none');
                    sectionProduk.classList.add('d-none');
                } else if (val === 'Pembelian Sparepart') {
                    sectionJasa.classList.add('d-none');
                    sectionProduk.classList.remove('d-none');
                    serviceFeeInput.value = 0;            } else {
                    sectionJasa.classList.remove('d-none');
                    sectionProduk.classList.remove('d-none');
                }
                calculateAll();
            });

            addRowBtn.addEventListener('click', function () {
                const firstRow = tableBody.querySelector('.item-row');
                if (!firstRow) return;
                const newRow = firstRow.cloneNode(true);
                newRow.querySelector('.product-select').selectedIndex = 0;
                newRow.querySelector('.price-input').value = '';
                newRow.querySelector('.qty-input').value = '1';
                newRow.querySelector('.subtotal-text').innerText = 'Rp 0';
                const locSelect = newRow.querySelector('.location-select');
                locSelect.innerHTML = '<option value="" selected>Pilih Rak...</option>';
                locSelect.disabled = true;
                newRow.querySelector('.qty-input').classList.remove('is-invalid');
                const removeBtn = newRow.querySelector('.remove-row-btn');
                removeBtn.classList.remove('d-none');
                newRow.style.opacity = '0';
                tableBody.appendChild(newRow);
                setTimeout(() => newRow.style.opacity = '1', 50);
                calculateAll();
            });

            tableBody.addEventListener('click', function (e) {
                if (e.target.closest('.remove-row-btn')) {
                    e.target.closest('tr').remove();
                    calculateAll();
                }
            });

            tableBody.addEventListener('change', async function (e) {
                if (e.target.classList.contains('product-select')) {
                    const select = e.target;
                    const row = select.closest('tr');
                    const priceInput = row.querySelector('.price-input');
                    const locSelect = row.querySelector('.location-select');
                    const productId = select.value;
                    if (!productId) return;

                    const rawPrice = select.options[select.selectedIndex].getAttribute('data-price');
                    priceInput.value = Math.round(parseFloat(rawPrice));
                    locSelect.innerHTML = '<option value="" disabled selected>Mencari...</option>';
                    locSelect.disabled = true;

                    try {
                        const response = await fetch(`{{ url('/sales/get-product-locations') }}/${productId}`);
                        const locations = await response.json();
                        locSelect.innerHTML = '';
                        if (locations.length === 0) {
                            locSelect.innerHTML = '<option value="" disabled selected>Tidak Ditemukan</option>';
                        } else {
                            locations.forEach(loc => { locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`; });
                            locSelect.disabled = false;
                        }
                    } catch (error) {
                        locSelect.innerHTML = '<option value="" disabled selected>Gagal Memuat</option>';
                    }
                    calculateAll();
                }
            });

            function calculateAll() {
                let currentGrandTotal = 0;
                let isReadyToSubmit = false;
                let isStockValid = true;

                if (!sectionJasa.classList.contains('d-none')) {
                    const fee = parseFloat(serviceFeeInput.value) || 0;
                    currentGrandTotal += fee;
                    if (fee > 0) isReadyToSubmit = true;
                }

                if (!sectionProduk.classList.contains('d-none')) {
                    let hasValidProduct = false;
                    tableBody.querySelectorAll('.item-row').forEach(row => {
                        const select = row.querySelector('.product-select');
                        const priceInput = row.querySelector('.price-input');
                        const qtyInput = row.querySelector('.qty-input');
                        const subtotalText = row.querySelector('.subtotal-text');
                        if (select.value) {
                            hasValidProduct = true;
                            const maxStock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock')) || 0;
                            const price = parseFloat(priceInput.value) || 0;
                            const qty = parseInt(qtyInput.value) || 0;
                            if (qty > maxStock) {
                                qtyInput.classList.add('is-invalid', 'border-danger');
                                isStockValid = false;
                            } else {
                                qtyInput.classList.remove('is-invalid', 'border-danger');
                            }
                            const subtotal = price * qty;
                            subtotalText.innerText = 'Rp ' + formatRupiah(subtotal);
                            currentGrandTotal += subtotal;
                        }
                    });
                    if (hasValidProduct) isReadyToSubmit = true;
                }
                grandTotalDisplay.innerText = 'Rp ' + formatRupiah(currentGrandTotal);
                btnSubmitSale.disabled = !isReadyToSubmit || !isStockValid;
            }

            tableBody.addEventListener('input', calculateAll);
            serviceFeeInput.addEventListener('input', calculateAll);

            document.getElementById('formSale').addEventListener('submit', function (e) {
                const tipe = saleTypeSelect.value;
                if (tipe === 'Jasa / Servis' && parseFloat(serviceFeeInput.value) <= 0) {
                    e.preventDefault();
                    alert('Silakan masukkan nominal Biaya Jasa terlebih dahulu!');
                    return;
                }
                btnSubmitSale.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
                btnSubmitSale.disabled = true;
            });

            document.querySelectorAll('.btn-hapus').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault(); const form = this.closest('form');
                    Swal.fire({
                        title: 'Batal Transaksi?', text: "Data akan dihapus permanen!", icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#ef4444', cancelButtonColor: isDarkMode ? '#475569' : '#94a3b8', cancelButtonText: 'Tutup', confirmButtonText: 'Ya, Void'
                    }).then((res) => { if (res.isConfirmed) { this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; form.submit(); } });
                });
            });

            document.querySelectorAll('.btn-lunasi').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault(); const form = this.closest('form');
                    Swal.fire({
                        title: 'Tandai Lunas?', text: "Tagihan pelanggan ini akan dicatat sebagai uang masuk (Cash).", icon: 'question', showCancelButton: true,
                        confirmButtonColor: '#10b981', cancelButtonColor: isDarkMode ? '#475569' : '#94a3b8', cancelButtonText: 'Batal', confirmButtonText: '<i class="fas fa-check-double me-2"></i> Ya, Lunas'
                    }).then((res) => { if (res.isConfirmed) { this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; form.submit(); } });
                });
            });
        });
    </script>
@endpush
