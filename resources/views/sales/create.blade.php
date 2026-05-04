@extends('layouts.main')

@section('title', 'Aplikasi Kasir Bengkel')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0"><i class="fas fa-desktop me-2"></i>Formulir Nota Penjualan & Service</h5>
        </div>

        <form action="/sales" method="POST">
            @csrf
            <div class="card-body p-3 p-md-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row mb-4 bg-light p-3 rounded mx-0 border">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">No. Invoice</label>
                        <input type="text" name="invoice_number" class="form-control bg-white" value="{{ $autoInvoice }}"
                            readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Nama Pelanggan</label>
                        <input type="text" name="customer_name" class="form-control"
                            placeholder="Opsional (Isi jika kredit)">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Metode Bayar <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select border-success" required>
                            <option value="Tunai">Tunai (Lunas)</option>
                            <option value="Kredit">Kredit (Catat Piutang)</option>
                        </select>
                    </div>
                    <input type="hidden" name="sale_type_id"
                        value="{{ $saleTypes->where('name', 'Barang & Jasa')->first()->id ?? 2 }}">
                </div>

                <h6 class="fw-bold mb-3"><i class="fas fa-shopping-cart me-2"></i>Daftar Barang & Jasa:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-nowrap" id="salesTable">
                        <thead class="table-dark text-center">
                            <tr>
                                <th width="15%" style="min-width: 150px;">Jenis Item</th>
                                <th width="40%" style="min-width: 300px;">Deskripsi Barang / Jasa</th>
                                <th width="10%" style="min-width: 100px;">Qty</th>
                                <th width="15%" style="min-width: 180px;">Harga/Tarif (Rp)</th>
                                <th width="15%" style="min-width: 180px;">Subtotal (Rp)</th>
                                <th width="5%"><i class="fas fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="type[]" class="form-select type-select bg-light"
                                        onchange="toggleItemType(this)">
                                        <option value="Barang">Barang Fisik</option>
                                        <option value="Jasa">Jasa / Servis</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="barang-inputs">
                                        <select name="product_id[]" class="form-select mb-2 product-select" required
                                            onchange="handleProductChange(this)">
                                            <option value="" disabled selected>-- Pilih Produk --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ round($product->selling_price ?? 0) }}">
                                                    {{ $product->code ?? '' }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <select name="location_id[]" class="form-select location-select" required>
                                            <option value="" disabled selected>-- Pilih Produk Dahulu --</option>
                                        </select>
                                    </div>
                                    <div class="jasa-inputs" style="display: none;">
                                        <input type="text" name="jasa_name[]" class="form-control border-primary"
                                            placeholder="Ketik nama jasa (Misal: Ganti Oli)">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" name="qty[]" class="form-control qty-input text-center" min="1"
                                        value="1" required onkeyup="calculateRow(this)" onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="text" name="price[]" class="form-control price-input text-end" required
                                        onkeyup="formatRupiah(this); calculateRow(this)" placeholder="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal-input bg-light text-end fw-bold"
                                        readonly placeholder="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm" onclick="addRow()"><i
                                            class="fas fa-plus"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end align-middle fs-5">GRAND TOTAL :</th>
                                <th>
                                    <input type="text" name="total_amount" id="grandTotal"
                                        class="form-control fw-bold bg-warning-subtle text-dark fs-5 text-end" readonly
                                        value="0">
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
            <div class="card-footer bg-transparent text-end py-3">
                <a href="/sales" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg shadow-sm"><i
                        class="fas fa-check-circle me-2"></i>Selesaikan Transaksi</button>
            </div>
        </form>
    </div>

    <table style="display: none;">
        <tbody id="rowTemplate">
            <tr>
                <td>
                    <select name="type[]" class="form-select type-select bg-light" onchange="toggleItemType(this)">
                        <option value="Barang">Barang Fisik</option>
                        <option value="Jasa">Jasa / Servis</option>
                    </select>
                </td>
                <td>
                    <div class="barang-inputs">
                        <select name="product_id[]" class="form-select mb-2 product-select" required
                            onchange="handleProductChange(this)">
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ round($product->selling_price ?? 0) }}">
                                    {{ $product->code ?? '' }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="location_id[]" class="form-select location-select" required>
                            <option value="" disabled selected>-- Pilih Produk Dahulu --</option>
                        </select>
                    </div>
                    <div class="jasa-inputs" style="display: none;">
                        <input type="text" name="jasa_name[]" class="form-control border-primary"
                            placeholder="Ketik nama jasa (Misal: Ganti Oli)">
                    </div>
                </td>
                <td><input type="number" name="qty[]" class="form-control qty-input text-center" min="1" value="1" required
                        onkeyup="calculateRow(this)" onchange="calculateRow(this)"></td>
                <td><input type="text" name="price[]" class="form-control price-input text-end" required
                        onkeyup="formatRupiah(this); calculateRow(this)" placeholder="0"></td>
                <td><input type="text" class="form-control subtotal-input bg-light text-end fw-bold" readonly
                        placeholder="0"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i
                            class="fas fa-trash"></i></button></td>
            </tr>
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        const productsData = @json($products);

        function toggleItemType(selectElement) {
            let row = selectElement.closest('tr');
            let type = selectElement.value;
            let barangInputs = row.querySelector('.barang-inputs');
            let jasaInputs = row.querySelector('.jasa-inputs');
            let productSelect = row.querySelector('.product-select');
            let locationSelect = row.querySelector('.location-select');
            let jasaNameInput = row.querySelector('input[name="jasa_name[]"]');

            if (type === 'Barang') {
                barangInputs.style.display = 'block';
                jasaInputs.style.display = 'none';
                productSelect.setAttribute('required', 'required');
                locationSelect.setAttribute('required', 'required');
                jasaNameInput.removeAttribute('required');
            } else {
                barangInputs.style.display = 'none';
                jasaInputs.style.display = 'block';
                productSelect.removeAttribute('required');
                locationSelect.removeAttribute('required');
                jasaNameInput.setAttribute('required', 'required');

                row.querySelector('.price-input').value = '';
                locationSelect.innerHTML = '<option value="" disabled selected>-- Tidak Perlu Lokasi --</option>';
                calculateRow(row.querySelector('.price-input'));
            }
        }

        function handleProductChange(selectElement) {
            let row = selectElement.closest('tr');
            let priceInput = row.querySelector('.price-input');
            let locationSelect = row.querySelector('.location-select');
            let selectedOption = selectElement.options[selectElement.selectedIndex];

            let price = selectedOption.getAttribute('data-price') || "0";
            let format = price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            priceInput.value = format;

            let productId = selectElement.value;
            let product = productsData.find(p => p.id == productId);

            locationSelect.innerHTML = '<option value="" disabled selected>-- Sedang mencari stok... --</option>';

            if (product && product.locations) {
                let hasStock = false;
                let optionsHtml = '<option value="" disabled selected>-- Pilih Pengambilan Stok --</option>';

                product.locations.forEach(loc => {
                    if (loc.pivot.stock > 0) {
                        optionsHtml += `<option value="${loc.id}">${loc.name} (Tersedia: ${loc.pivot.stock})</option>`;
                        hasStock = true;
                    }
                });

                if (!hasStock) {
                    locationSelect.innerHTML = '<option value="" disabled selected>-- STOK HABIS DI SEMUA GUDANG --</option>';
                } else {
                    locationSelect.innerHTML = optionsHtml;
                }
            }

            calculateRow(priceInput);
        }

        function calculateRow(element) {
            let row = element.closest('tr');

            let qtyVal = row.querySelector('.qty-input').value;
            let qty = parseInt(qtyVal) || 0;

            let priceStr = row.querySelector('.price-input').value.replace(/\./g, '');
            let price = parseInt(priceStr) || 0;

            let subtotal = qty * price;
            row.querySelector('.subtotal-input').value = subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let subtotals = document.querySelectorAll('#salesTable tbody .subtotal-input');
            let grandTotal = 0;

            subtotals.forEach(function (input) {
                let val = parseInt(input.value.replace(/\./g, '')) || 0;
                grandTotal += val;
            });

            document.getElementById('grandTotal').value = grandTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function addRow() {
            let tbody = document.querySelector('#salesTable tbody');
            let template = document.querySelector('#rowTemplate').innerHTML;
            tbody.insertAdjacentHTML('beforeend', template);
        }

        function removeRow(button) {
            let row = button.closest('tr');
            row.remove();
            calculateGrandTotal();
        }
    </script>
@endpush
