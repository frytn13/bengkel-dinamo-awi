@extends('layouts.main')

@section('title', 'Master Satuan')

@section('content')
    <div class="container-fluid py-2">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-body mb-0"><i class="fas fa-balance-scale me-2 text-warning"></i> Master Satuan</h5>
            <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal"
                data-bs-target="#addUnitModal">
                <i class="fas fa-plus me-2"></i> Tambah Satuan
            </button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="border-bottom border-secondary-subtle">
                            <tr>
                                <th width="10%"
                                    class="text-center py-3 ps-4 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">No</th>
                                <th class="py-3 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">Nama Satuan</th>
                                <th width="20%"
                                    class="text-center py-3 pe-4 text-uppercase small fw-bold text-body-secondary"
                                    style="letter-spacing: 0.5px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $index => $unit)
                                <tr>
                                    <td class="text-center text-body-secondary ps-4">{{ $index + 1 }}</td>
                                    <td class="fw-bold text-body">{{ $unit->name }}</td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3 me-1 shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $unit->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="/units/{{ $unit->id }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm btn-hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom border-secondary-subtle">
                                                <h5 class="modal-title fw-bold text-body">Edit Satuan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="/units/{{ $unit->id }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label text-body fw-semibold">Nama Satuan <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ $unit->name }}" required
                                                            placeholder="Contoh: Pcs, Set, Liter, Box...">
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
                                    <td colspan="3" class="text-center py-5">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-ruler-combined fa-2x text-warning"></i>
                                        </div>
                                        <h6 class="fw-bold text-body mb-1">Satuan Belum Tersedia</h6>
                                        <p class="text-body-secondary small mb-0">Belum ada data satuan produk. Klik tombol di
                                            atas untuk menambah.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom border-secondary-subtle">
                    <h5 class="modal-title fw-bold text-body">Tambah Satuan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/units" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-body fw-semibold">Nama Satuan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required
                                placeholder="Contoh: Pcs, Set, Liter, Box...">
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary-subtle">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i
                                class="fas fa-save me-2"></i> Simpan Data</button>
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
                        title: 'Hapus Satuan?',
                        text: "Data ini mungkin terhubung dengan produk Anda!",
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
