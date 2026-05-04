@extends('layouts.main')

@section('title', 'Master Produk')

@section('content')
    <div class="container-fluid py-2">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-box-open me-2 text-primary"></i> Master Produk</h5>
            <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal"
                data-bs-target="#addProductModal">
                <i class="fas fa-plus me-2"></i> Tambah Produk
            </button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="border-bottom border-secondary-subtle">
                            <tr>
                                <th width="5%"
                                    class="text-center py-3 ps-4 text-uppercase small fw-bold text-body-secondary">No</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Kode & Produk</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary">Kategori & Vendor</th>
                                <th class="text-center py-3 text-uppercase small fw-bold text-body-secondary">Stok</th>
                                <th class="text-end py-3 text-uppercase small fw-bold text-body-secondary">Harga Jual</th>
                                <th width="15%"
                                    class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $product)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-body">{{ $product->name }}</div>
                                        <div class="small text-secondary"><i class="fas fa-barcode me-1"></i>
                                            {{ $product->code }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-secondary bg-opacity-25 text-body border border-secondary-subtle rounded-pill px-3 py-1 mb-1 d-inline-block">
                                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                                        </span>
                                        <div class="small text-info fw-bold"><i class="fas fa-building me-1"></i>
                                            {{ $product->vendor->name ?? 'Vendor Umum' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($product->stock <= 5)
                                            <span class="badge bg-danger rounded-pill px-3 py-2"><i
                                                    class="fas fa-exclamation-triangle me-1"></i> {{ $product->stock }}
                                                {{ $product->unit->name ?? '' }}</span>
                                        @else
                                            <span
                                                class="badge bg-success bg-opacity-25 text-success border border-success-subtle rounded-pill px-3 py-2">{{ $product->stock }}
                                                {{ $product->unit->name ?? '' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-body">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3 me-1 shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="/products/{{ $product->id }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom border-secondary-subtle">
                                                <h5 class="modal-title fw-bold text-body"><i
                                                        class="fas fa-edit me-2 text-info"></i> Edit Data Produk</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="/products/{{ $product->id }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Kode SKU <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control text-body bg-body border-secondary-subtle"
                                                                name="code" value="{{ $product->code }}" required>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label class="form-label text-body fw-semibold">Nama Produk /
                                                                Sparepart <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control text-body bg-body border-secondary-subtle"
                                                                name="name" value="{{ $product->name }}" required>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Kategori <span
                                                                    class="text-danger">*</span></label>
                                                            <select
                                                                class="form-select text-body bg-body border-secondary-subtle"
                                                                name="category_id" required>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                        {{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Satuan <span
                                                                    class="text-danger">*</span></label>
                                                            <select
                                                                class="form-select text-body bg-body border-secondary-subtle"
                                                                name="unit_id" required>
                                                                @foreach($units as $unit)
                                                                    <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Vendor Pemasok <span
                                                                    class="text-danger">*</span></label>
                                                            <select
                                                                class="form-select text-body bg-body border-secondary-subtle fw-bold"
                                                                name="vendor_id" required>
                                                                <option value="" disabled>-- Pilih Vendor --</option>
                                                                @foreach($vendors as $vendor)
                                                                    <option value="{{ $vendor->id }}" {{ $product->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                                        {{ $vendor->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Stok Gudang</label>
                                                            <input type="number"
                                                                class="form-control text-body bg-body border-secondary-subtle"
                                                                name="stock" value="{{ $product->stock }}" required min="0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Harga Beli
                                                                (Modal)</label>
                                                            <div class="input-group">
                                                                <span
                                                                    class="input-group-text bg-body-tertiary border-secondary-subtle">Rp</span>
                                                                <input type="number"
                                                                    class="form-control text-body bg-body border-secondary-subtle"
                                                                    name="purchase_price" value="{{ $product->purchase_price }}"
                                                                    required min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-body fw-semibold">Harga Jual</label>
                                                            <div class="input-group">
                                                                <span
                                                                    class="input-group-text bg-body-tertiary border-secondary-subtle">Rp</span>
                                                                <input type="number"
                                                                    class="form-control text-body bg-body border-secondary-subtle"
                                                                    name="selling_price" value="{{ $product->selling_price }}"
                                                                    required min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i
                                                            class="fas fa-save me-2"></i> Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-box-open fa-2x text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Gudang Masih Kosong</h6>
                                        <p class="text-body-secondary small mb-0">Belum ada data produk/sparepart. Pastikan Anda
                                            sudah membuat Kategori dan Satuan terlebih dahulu.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-body-tertiary border-bottom border-secondary-subtle px-4 py-3">
                    <h5 class="modal-title fw-bold text-body"><i class="fas fa-plus-circle me-2 text-primary"></i> Tambah
                        Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/products" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Kode SKU <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control text-body bg-body border-secondary-subtle"
                                    name="code" required placeholder="Contoh: OLI-001">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-body fw-semibold">Nama Produk / Sparepart <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control text-body bg-body border-secondary-subtle"
                                    name="name" required placeholder="Masukkan nama barang...">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Kategori <span
                                        class="text-danger">*</span></label>
                                <select class="form-select text-body bg-body border-secondary-subtle" name="category_id"
                                    required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Satuan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select text-body bg-body border-secondary-subtle" name="unit_id"
                                    required>
                                    <option value="" disabled selected>-- Pilih Satuan --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Vendor Pemasok <span
                                        class="text-danger">*</span></label>
                                <select class="form-select text-body bg-body border-secondary-subtle fw-bold"
                                    name="vendor_id" required>
                                    <option value="" disabled selected>-- Pilih Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Stok Awal</label>
                                <input type="number" class="form-control text-body bg-body border-secondary-subtle"
                                    name="stock" value="0" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Harga Beli (Modal)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-secondary-subtle">Rp</span>
                                    <input type="number" class="form-control text-body bg-body border-secondary-subtle"
                                        name="purchase_price" required min="0" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-body fw-semibold">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-secondary-subtle">Rp</span>
                                    <input type="number" class="form-control text-body bg-body border-secondary-subtle"
                                        name="selling_price" required min="0" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top border-secondary-subtle px-4 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i
                                class="fas fa-save me-2"></i> Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-hapus');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

                    Swal.fire({
                        title: 'Hapus Produk?',
                        text: "Data ini tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: isDarkMode ? '#475569' : '#94a3b8',
                        confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus',
                        cancelButtonText: 'Batal',
                        background: isDarkMode ? 'rgba(30, 41, 59, 0.98)' : '#ffffff',
                        color: isDarkMode ? '#f8fafc' : '#0f172a',
                        borderRadius: '16px'
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
