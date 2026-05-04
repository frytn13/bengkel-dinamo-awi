@extends('layouts.main')

@section('title', 'Pemakaian Barang (Internal)')

@section('content')
    <style>
        .swal2-popup {
            border-radius: 20px !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .swal2-title,
        .swal2-html-container {
            color: #0f172a !important;
        }

        [data-bs-theme="dark"] .swal2-popup {
            background: rgba(30, 41, 59, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
        }

        [data-bs-theme="dark"] .swal2-title,
        [data-bs-theme="dark"] .swal2-html-container {
            color: #f8fafc !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.25);
        }

        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }

        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }

        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .table-responsive.custom-scrollbar thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-clipboard-list me-2 text-warning"></i> Pemakaian Barang
                (Internal)</h5>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form action="{{ route('usages.index') }}" method="GET"
                    class="d-flex flex-wrap gap-2 align-items-center me-lg-3">
                    <div class="input-group shadow-sm" style="width: auto;">
                        <span class="input-group-text bg-body border-secondary-subtle text-body-secondary"><i
                                class="fas fa-calendar-alt"></i></span>
                        <input type="date" class="form-control text-body bg-body border-secondary-subtle" name="start_date"
                            value="{{ $startDate }}">
                    </div>
                    <span class="text-body-secondary fw-bold">s/d</span>
                    <div class="input-group shadow-sm" style="width: auto;">
                        <span class="input-group-text bg-body border-secondary-subtle text-body-secondary"><i
                                class="fas fa-calendar-check"></i></span>
                        <input type="date" class="form-control text-body bg-body border-secondary-subtle" name="end_date"
                            value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('usages.index') }}" class="btn btn-secondary shadow-sm" title="Reset Filter"><i
                            class="fas fa-sync-alt"></i></a>
                </form>

                <button class="btn btn-warning text-dark fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal"
                    data-bs-target="#addUsageModal">
                    <i class="fas fa-plus me-2"></i> Ambil Barang
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0 overflow-hidden">
                <div class="table-responsive custom-scrollbar" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-body-tertiary border-bottom border-secondary-subtle">
                            <tr>
                                <th width="5%"
                                    class="text-center py-3 ps-4 text-uppercase small fw-bold text-body-secondary">No</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Tanggal</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Produk & Lokasi Rak</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Tujuan / Keperluan</th>
                                <th class="text-center py-3 text-uppercase small fw-bold text-body-secondary">Qty Keluar
                                </th>
                                <th width="10%"
                                    class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usages ?? [] as $index => $usage)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">
                                        {{ ($usages->currentPage() - 1) * $usages->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-body">
                                            {{ \Carbon\Carbon::parse($usage->date)->format('d M Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $usage->product->name ?? '-' }}</div>
                                        <div class="small text-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ $usage->location->name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div
                                            class="fw-bold {{ str_contains($usage->purpose, 'Hilang') || str_contains($usage->purpose, 'Rusak') ? 'text-danger' : 'text-body' }}">
                                            @if(str_contains($usage->purpose, 'Hilang') || str_contains($usage->purpose, 'Rusak'))
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                            @endif
                                            {{ $usage->purpose }}
                                        </div>
                                        <div class="small text-secondary text-wrap" style="max-width: 250px;">
                                            {{ $usage->notes ?? '-' }}</div>
                                    </td>
                                    <td class="text-center fw-bold text-body">
                                        <span class="badge bg-warning text-dark rounded-pill px-3">{{ $usage->qty }}</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <form action="{{ route('usages.destroy', $usage->id) }}" method="POST"
                                            class="d-inline form-delete">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus"
                                                title="Batalkan & Kembalikan Stok">
                                                <i class="fas fa-undo me-1"></i> Batal
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-clipboard-list fa-2x text-secondary"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Belum Ada Catatan</h6>
                                        <p class="text-body-secondary small">Coba ubah rentang tanggal atau catat pemakaian
                                            baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($usages->hasPages())
                    <div
                        class="card-footer bg-transparent border-top border-secondary-subtle p-3 d-flex justify-content-center">
                        {{ $usages->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUsageModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                    <h5 class="modal-title fw-bold text-body"><i class="fas fa-box-open me-2 text-warning"></i> Form
                        Pengeluaran Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('usages.store') }}" method="POST" id="formUsage">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Tanggal <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control text-body bg-body border-secondary-subtle"
                                    name="date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Barang / Sparepart <span
                                        class="text-danger">*</span></label>
                                <select class="form-select text-body bg-body border-secondary-subtle fw-bold"
                                    name="product_id" id="productSelect" required>
                                    <option value="" disabled selected>-- Pilih Barang --</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                            {{ $product->name }} (Sisa: {{ $product->stock }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Lokasi Rak <span
                                        class="text-danger">*</span></label>
                                <select class="form-select text-body bg-body border-secondary-subtle" name="location_id"
                                    id="locationSelect" required disabled>
                                    <option value="" disabled selected>-- Pilih Produk Dulu --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Qty Keluar <span
                                        class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control text-body bg-body border-warning text-warning-emphasis fw-bold"
                                    name="qty" id="qtyInput" min="1" placeholder="0" required disabled>
                                <div class="small text-danger mt-1 d-none" id="stockWarning"><i
                                        class="fas fa-exclamation-triangle"></i> Melebihi stok gudang!</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Tujuan Pemakaian <span
                                        class="text-danger">*</span></label>
                                <select class="form-select text-body bg-body border-secondary-subtle fw-semibold"
                                    name="purpose" id="purposeSelect" required>
                                    <option value="" disabled selected>-- Pilih Tujuan --</option>
                                    <option value="Pemakaian Mekanik Internal">Pemakaian Mekanik Internal</option>
                                    <option value="Barang Hilang / Rusak">Barang Hilang / Rusak</option>
                                    <option value="Lain-lain">Lain-lain (Ketik Sendiri)</option>
                                </select>
                            </div>

                            <div class="col-md-6 d-none" id="purposeOtherContainer">
                                <label class="form-label fw-bold small text-uppercase text-info">Ketik Tujuan Lainnya <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control border-info bg-body text-body" name="purpose_other"
                                    id="purposeOtherInput" placeholder="Cth: Dipinjam Cabang B...">
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label fw-bold small text-uppercase">Catatan Tambahan</label>
                                <textarea class="form-control text-body bg-body border-secondary-subtle" name="notes"
                                    rows="2" placeholder="Contoh: Cacat pabrik dan tidak layak pakai..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3"
                        style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark rounded-pill px-5 fw-bold shadow-sm"
                            id="btnSubmitUsage" disabled>
                            <i class="fas fa-save me-2"></i> Simpan & Kurangi Stok
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
            const productSelect = document.getElementById('productSelect');
            const locationSelect = document.getElementById('locationSelect');
            const qtyInput = document.getElementById('qtyInput');
            const stockWarning = document.getElementById('stockWarning');
            const btnSubmitUsage = document.getElementById('btnSubmitUsage');

            const purposeSelect = document.getElementById('purposeSelect');
            const purposeOtherContainer = document.getElementById('purposeOtherContainer');
            const purposeOtherInput = document.getElementById('purposeOtherInput');

            let maxStock = 0;

            productSelect.addEventListener('change', async function () {
                maxStock = parseInt(this.options[this.selectedIndex].getAttribute('data-stock')) || 0;
                qtyInput.max = maxStock;
                qtyInput.value = '';
                qtyInput.disabled = false;
                checkStock();

                const productId = this.value;
                locationSelect.innerHTML = '<option value="" disabled selected>Mencari Rak...</option>';
                locationSelect.disabled = true;

                try {
                    const response = await fetch(`{{ url('/get-product-locations') }}/${productId}`);
                    const locations = await response.json();

                    locationSelect.innerHTML = '<option value="" disabled selected>-- Ditemukan di Rak Berikut --</option>';
                    locations.forEach(loc => {
                        locationSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                    });
                    locationSelect.disabled = false;
                } catch (err) {
                    console.error("Gagal melacak lokasi rak:", err);
                    locationSelect.innerHTML = '<option value="" disabled selected>Gagal memuat lokasi</option>';
                }
            });

            qtyInput.addEventListener('input', checkStock);

            function checkStock() {
                const qty = parseInt(qtyInput.value) || 0;
                if (qty > maxStock) {
                    stockWarning.classList.remove('d-none');
                    qtyInput.classList.add('is-invalid', 'bg-danger', 'bg-opacity-10');
                    btnSubmitUsage.disabled = true;
                } else if (qty > 0 && qty <= maxStock && locationSelect.value !== "") {
                    stockWarning.classList.add('d-none');
                    qtyInput.classList.remove('is-invalid', 'bg-danger', 'bg-opacity-10');
                    btnSubmitUsage.disabled = false;
                } else {
                    stockWarning.classList.add('d-none');
                    qtyInput.classList.remove('is-invalid', 'bg-danger', 'bg-opacity-10');
                    btnSubmitUsage.disabled = true;
                }
            }

            locationSelect.addEventListener('change', checkStock);

            purposeSelect.addEventListener('change', function () {
                if (this.value === 'Lain-lain') {
                    purposeOtherContainer.classList.remove('d-none');
                    purposeOtherInput.required = true;
                } else {
                    purposeOtherContainer.classList.add('d-none');
                    purposeOtherInput.required = false;
                    purposeOtherInput.value = '';
                }
            });

            const deleteButtons = document.querySelectorAll('.btn-hapus');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Batalkan Pemakaian?',
                        text: "Stok fisik barang ini akan dikembalikan secara otomatis ke gudang!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: '<i class="fas fa-undo me-2"></i>Ya, Kembalikan Stok',
                        cancelButtonText: 'Tutup'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                            this.classList.add('disabled');
                            form.submit();
                        }
                    });
                });
            });

            document.getElementById('formUsage').addEventListener('submit', function () {
                btnSubmitUsage.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                btnSubmitUsage.disabled = true;
            });
        });
    </script>
@endpush
