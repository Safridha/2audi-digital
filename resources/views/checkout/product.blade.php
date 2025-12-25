@extends('layouts.main')

@section('title', 'Detail Cetakan')

@section('content')
<div class="container mt-1 mb-5">
    <h4 class="fw-bold text-black text-center mb-3" style="font-size: 22px;">
        Detail Cetakan Produk
    </h4>

    <div class="row g-4">

        {{-- KARTU PRODUK --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}"
                         class="card-img-top"
                         alt="{{ $product->name }}"
                         style="height:200px; object-fit:cover;">
                @endif

                <div class="card-body">
                    <h5 class="fw-bold mb-2">{{ $product->name }}</h5>

                    <p class="mb-0 fw-semibold text-blue-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                        <span class="small text-muted"> / m²</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- FORM DETAIL CETAKAN --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4">

                {{-- 1 FORM UNTUK 2 AKSI (KERANJANG & PESAN SEKARANG) --}}
                <form id="detail-form"
                      action="{{ route('cart.add', $product) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- konsisten dengan CartController@add() --}}
                    <input type="hidden" name="action" id="action_field" value="cart">

                    {{-- harga & finishing rate untuk JS --}}
                    <input type="hidden" id="unit_price" value="{{ $product->price }}">
                    <input type="hidden" id="finishing_rate" value="500">

                    <h5 class="fw-bold mb-3">Detail Cetakan</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Panjang (m)</label>
                            <input type="number" step="0.01" min="0.01"
                                   class="form-control"
                                   name="length" id="length" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Lebar (m)</label>
                            <input type="number" step="0.01" min="0.01"
                                   class="form-control"
                                   name="width" id="width" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah</label>
                            <input type="number" min="1"
                                   class="form-control"
                                   name="quantity" id="quantity" value="1" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Finishing</label>
                            <select name="finishing" id="finishing" class="form-select">
                                <option value="finishing">Finishing</option>
                                <option value="tanpa">Tanpa Finishing</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Catatan</label>
                            <textarea name="note"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Contoh: dominan warna biru, dipasang di depan toko."></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Desain <span class="text-danger">*</span></label>
                        <input type="file"
                               name="design_file"
                               id="design_file"
                               class="form-control @error('design_file') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.pdf,.ai,.cdr"
                               required>
                        <div class="form-text">
                            Format: JPG, PNG, PDF, AI, CDR — max 10MB
                        </div>
                        @error('design_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ESTIMASI HARGA (muncul setelah upload gambar) --}}
                    <div id="estimate-box" class="alert alert-info mt-3" style="display:none;">
                        <strong>Estimasi Harga</strong><br>
                        <span id="est-area">Luas: -</span><br>
                        <span id="est-product">Harga Produk: -</span><br>
                        <span id="est-finishing">Harga Finishing: -</span><br>
                        <span id="est-total">Total Harga: -</span>
                        <div class="small text-muted mt-1">
                            Estimasi ini belum termasuk ongkir (jika dikirim).
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <div class="d-flex gap-2">
                            {{-- MASUKKAN KERANJANG --}}
                            <button type="button"
                                    class="btn btn-warning text-white"
                                    onclick="submitAsCart()">
                                Masukkan Keranjang
                            </button>

                            {{-- PESAN SEKARANG (langsung ke checkout single) --}}
                            <button type="button"
                                    class="btn btn-primary"
                                    onclick="submitAsBuyNow()">
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
    function hitungEstimasi() {
        const price         = parseFloat(document.getElementById('unit_price').value || 0);
        const finishingRate = parseFloat(document.getElementById('finishing_rate').value || 0);

        const length    = parseFloat(document.getElementById('length').value || 0);
        const width     = parseFloat(document.getElementById('width').value || 0);
        const qty       = parseInt(document.getElementById('quantity').value || 0);
        const finishing = document.getElementById('finishing').value;
        const design    = document.getElementById('design_file');

        const box       = document.getElementById('estimate-box');
        const txtArea   = document.getElementById('est-area');
        const txtProd   = document.getElementById('est-product');
        const txtFin    = document.getElementById('est-finishing');
        const txtTotal  = document.getElementById('est-total');

        if (!design || !design.value || !length || !width || !qty) {
            box.style.display = 'none';
            return;
        }

        const areaPerItem   = length * width;
        const totalArea     = areaPerItem * qty;
        const productCost   = price * totalArea;
        const finishingCost = (finishing === 'finishing') ? finishingRate * totalArea : 0;
        const total         = productCost + finishingCost;

        box.style.display = 'block';

        txtArea.innerText  = `Luas: ${areaPerItem.toFixed(2)} m² / lembar (Total ${totalArea.toFixed(2)} m²)`;
        txtProd.innerText  = `Harga Produk: Rp ${productCost.toLocaleString('id-ID')}`;
        txtFin.innerText   = `Harga Finishing: Rp ${finishingCost.toLocaleString('id-ID')}`;
        txtTotal.innerText = `Total Harga: Rp ${total.toLocaleString('id-ID')}`;
    }

    function submitAsCart() {
        const form = document.getElementById('detail-form');
        const actionField = document.getElementById('action_field');

        form.action = "{{ route('cart.add', $product) }}";
        if (actionField) actionField.value = 'cart';

        if (form.requestSubmit) form.requestSubmit();
        else form.submit();
    }

    function submitAsBuyNow() {
        const form = document.getElementById('detail-form');
        const actionField = document.getElementById('action_field');

        form.action = "{{ route('product.single.start', $product) }}";
        if (actionField) actionField.value = 'buy_now';

        if (form.requestSubmit) form.requestSubmit();
        else form.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        ['length', 'width', 'quantity', 'finishing', 'design_file'].forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;

            const eventName = (el.tagName === 'SELECT' || el.type === 'file') ? 'change' : 'input';
            el.addEventListener(eventName, hitungEstimasi);
        });
    });
</script>
@endsection
