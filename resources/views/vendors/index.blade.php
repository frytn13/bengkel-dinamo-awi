@extends('layouts.main')

@section('title', 'Master Vendor')

@section('content')
    <div class="container-fluid py-2">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-truck-moving me-2 text-primary"></i> Master Vendor /
                Supplier</h5>
            <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal"
                data-bs-target="#addVendorModal">
                <i class="fas fa-plus me-2"></i> Tambah Vendor
            </button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="border-bottom border-secondary-subtle">
                            <tr>
                                <th width="5%"
                                    class="text-center py-3 ps-4 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">No</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">Nama Vendor</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">Kontak / Telp</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">Alamat</th>
                                <th width="15%"
                                    class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $index => $vendor)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-body">{{ $vendor->name }}</div>
                                        <div class="small text-secondary">ID:
                                            VND-{{ str_pad($vendor->id, 3, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="text-body">
                                        <i class="fas fa-phone-alt small me-1 text-primary"></i> {{ $vendor->phone ?? '-' }}
                                    </td>
                                    <td class="text-body text-truncate" style="max-width: 200px;">
                                        {{ $vendor->address ?? '-' }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3 me-1 shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#editVendorModal{{ $vendor->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="/vendors/{{ $vendor->id }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editVendorModal{{ $vendor->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom border-secondary-subtle">
                                                <h5 class="modal-title fw-bold text-body">Edit Data Vendor</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="/vendors/{{ $vendor->id }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label text-body fw-semibold">Nama Vendor <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ $vendor->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-body fw-semibold">Nomor Telepon /
                                                            WhatsApp</label>
                                                        <input type="text" class="form-control" name="phone"
                                                            value="{{ $vendor->phone }}">
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label text-body fw-semibold">Alamat
                                                            Kantor/Gudang</label>
                                                        <textarea class="form-control" name="address"
                                                            rows="3">{{ $vendor->address }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top border-secondary-subtle">
                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
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
                                    <td colspan="5" class="text-center py-5">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-handshake fa-2x text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Belum Ada Vendor</h6>
                                        <p class="text-body-secondary small mb-0">Daftar supplier belum terisi. Tambahkan vendor
                                            untuk memulai transaksi PO.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom border-secondary-subtle">
                    <h5 class="modal-title fw-bold text-body">Tambah Vendor Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/vendors" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-body fw-semibold">Nama Vendor <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required
                                placeholder="Contoh: PT. Sumber Jaya Makmur">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-body fw-semibold">Nomor Telepon / WhatsApp</label>
                            <input type="text" class="form-control" name="phone" placeholder="0812xxxx">
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-body fw-semibold">Alamat Kantor/Gudang</label>
                            <textarea class="form-control" name="address" rows="3"
                                placeholder="Jl. Raya Utama No. 123..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary-subtle">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i
                                class="fas fa-save me-2"></i> Simpan Vendor</button>
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
                        title: 'Hapus Vendor?',
                        text: "Data vendor dan riwayat transaksinya akan terpengaruh!",
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
