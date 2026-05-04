@extends('layouts.main')

@section('title', 'Buat Purchase Order (PO)')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="mb-0"><i class="fas fa-cart-plus me-2 text-primary"></i>Form Pemesanan Barang (PO)</h5>
        </div>

        <form action="/purchase-orders" method="POST">
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

                <div class="row mb-4 bg-light p-3 rounded mx-0 border">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">No. Purchase Order</label>
                        <input type="text" name="po_number" class="form-control bg-white" value="{{ $autoPoNumber }}"
                            readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tanggal Pemesanan <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Pilih Vendor/Pemasok <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Rincian Barang yang Dipesan:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-nowrap" id="poTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="35%" style="min-width: 250px;">Pilih Produk</th>
                                <th width="15%" style="min-width: 120px;">Kuantitas (Qty)</th>
                                <th width="25%" style="min-width: 200px;">Harga Modal Satuan (Rp)</th>
                                <th width="20%" style="min-width: 200px;">Subtotal (Rp)</th>
                                <th width="5%" class="text-center"><i class="fas fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-select product-select" required
                                        onchange="setPrice(this)">
                                        <option value="" disabled selected>-- Pilih Produk --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ round($product->purchase_price) }}">
                                                {{ $product->code ?? '' }} - {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                    <input type="text" class="form-control subtotal-input bg-light text-end" readonly
                                        placeholder="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm" onclick="addRow()"><i
                                            class="fas fa-plus"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end align-middle fs-5">Total Pembayaran :</th>
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
                <a href="/purchase-orders" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan PO</button>
            </div>
        </form>
    </div>

    <table style="display: none;">
        <tbody id="rowTemplate">
            <tr>
                <td>
                    <select name="product_id[]" class="form-select product-select" required onchange="setPrice(this)">
                        <option value="" disabled selected>-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ round($product->purchase_price) }}">
                                {{ $product->code ?? '' }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="qty[]" class="form-control qty-input text-center" min="1" value="1" required
                        onkeyup="calculateRow(this)" onchange="calculateRow(this)"></td>
                <td><input type="text" name="price[]" class="form-control price-input text-end" required
                        onkeyup="formatRupiah(this); calculateRow(this)" placeholder="0"></td>
                <td><input type="text" class="form-control subtotal-input bg-light text-end" readonly placeholder="0"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i
                            class="fas fa-trash"></i></button></td>
            </tr>
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        function setPrice(selectElement) {
            let row = selectElement.closest('tr');
            let priceInput = row.querySelector('.price-input');
            let selectedOption = selectElement.options[selectElement.selectedIndex];

            let price = selectedOption.getAttribute('data-price') || "0";
            let format = price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            priceInput.value = format;

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
            let subtotals = document.querySelectorAll('#poTable tbody .subtotal-input');
            let grandTotal = 0;

            subtotals.forEach(function (input) {
                let val = parseInt(input.value.replace(/\./g, '')) || 0;
                grandTotal += val;
            });

            document.getElementById('grandTotal').value = grandTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function addRow() {
            let tbody = document.querySelector('#poTable tbody');
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
