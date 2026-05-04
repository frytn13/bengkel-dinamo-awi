@extends('layouts.main')

@section('title', 'Proses Penerimaan Barang (RO)')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="mb-0"><i class="fas fa-box-open me-2 text-success"></i>Form Penerimaan Barang (Berdasarkan
                {{ $po->po_number }})</h5>
        </div>

        <form action="/receive-orders" method="POST">
            @csrf
            <div class="card-body p-3 p-md-4">

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">

                <div class="row mb-4 bg-light p-3 rounded mx-0 border">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">No. Receive Order</label>
                        <input type="text" name="ro_number" class="form-control bg-white" value="{{ $autoRoNumber }}"
                            readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Tgl. Diterima <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Vendor/Pemasok</label>
                        <input type="text" class="form-control bg-white text-muted" value="{{ $po->vendor->name }}"
                            readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Metode Pembelian <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select border-primary" required>
                            <option value="" disabled selected>-- Pilih Metode --</option>
                            <option value="Tunai">Tunai (Kas)</option>
                            <option value="Kredit">Kredit (Hutang)</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-info border-0 py-2 d-flex align-items-center">
                    <i class="fas fa-info-circle fs-4 me-3 d-none d-sm-block"></i>
                    <div class="small">Sistem telah menarik daftar barang dari PO. Silakan pastikan kuantitas fisik sesuai
                        dan tentukan <b>Lokasi Gudang</b> untuk masing-masing barang.</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mt-3 text-nowrap">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="35%" style="min-width: 200px;">Nama Barang Diterima</th>
                                <th width="20%" style="min-width: 150px;">Kuantitas Diterima</th>
                                <th width="40%" style="min-width: 250px;">Lokasi Penyimpanan <span
                                        class="text-danger">*</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($po->details as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-medium">
                                        {{ $detail->product->name }}
                                        <input type="hidden" name="items[{{$index}}][product_id]"
                                            value="{{ $detail->product_id }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="items[{{$index}}][qty]"
                                                class="form-control bg-light text-center" value="{{ $detail->qty }}" readonly>
                                            <span class="input-group-text">{{ $detail->product->unit->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="items[{{$index}}][location_id]" class="form-select" required>
                                            <option value="" disabled selected>-- Tentukan Gudang --</option>
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->id }}"><i class="fas fa-map-marker-alt"></i>
                                                    {{ $loc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="card-footer bg-transparent text-end py-3">
                <a href="/purchase-orders" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-box-open me-2"></i>Selesai & Masukkan ke
                    Stok</button>
            </div>
        </form>
    </div>
@endsection
