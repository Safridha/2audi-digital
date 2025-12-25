@extends('layouts.main')

@section('title', 'Checkout')

@section('content')
<div class="container mt-5 mb-5">
    <h4 class="fw-bold black text-center mb-3" style="font-size: 22px;">
        Checkout
    </h4>

    <div class="row g-4">

        {{-- RINGKASAN PESANAN --}}
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>

                    @foreach($items as $item)
                        @php
                            $hargaPerM2  = $item->product->price;
                            $luasPerItem = $item->length * $item->width;

                            $finishingRate  = $item->finishing !== 'tanpa' ? 500 : 0;
                            $finishingTotal = $finishingRate * $luasPerItem * $item->quantity;

                            $totalItem = ($luasPerItem * $item->quantity * $hargaPerM2) + $finishingTotal;
                        @endphp

                        <div class="mb-3 pb-2 border-bottom">
                            <div class="fw-semibold">{{ $item->product->name }}</div>
                            <div class="small text-muted">
                                Ukuran: {{ $item->length }} x {{ $item->width }} m<br>
                                Qty: {{ $item->quantity }}<br>
                                Finishing: {{ ucfirst($item->finishing) }} {{ $item->finishing !== 'tanpa' ? '(+Rp 500/m²)' : '' }}
                            </div>
                            <div class="mt-1 text-end fw-semibold">
                                Rp {{ number_format($totalItem, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between fw-bold mt-3">
                        <span>Subtotal</span>
                        <span id="subtotalText">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Ongkir</span>
                        <span id="ongkirText">Rp 0</span>
                    </div>

                    <div class="d-flex justify-content-between fw-bold mt-2">
                        <span>Total</span>
                        <span id="totalText">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="small text-muted mt-2">
                        * Total akan berubah setelah pilih layanan pengiriman.
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <div class="col-md-7">
            <div class="card shadow-sm border-0 p-4">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                    @csrf

                    {{-- kirim item yang sedang di-checkout ke checkout.store --}}
                    @foreach($items as $it)
                        <input type="hidden" name="items[]" value="{{ $it->id }}">
                    @endforeach

                    <h5 class="fw-bold mb-3">Data Pemesan</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="customer_name" class="form-control"
                                   value="{{ old('customer_name', auth()->user()->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-control"
                                   value="{{ old('customer_email', auth()->user()->email) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. HP / WhatsApp</label>
                        <input type="text" name="customer_phone" class="form-control"
                               value="{{ old('customer_phone') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                    </div>

                    {{-- Dropdown alamat ala Shopee --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Provinsi</label>
                            <select class="form-select" id="provinceSelect" name="province">
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kota / Kab</label>
                            <select class="form-select" id="citySelect" name="city" disabled required>
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kecamatan</label>
                            <select class="form-select" id="districtSelect" name="district" disabled required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control"
                               value="{{ old('postal_code') }}" required>
                    </div>

                    <input type="hidden" name="destination_id" id="destination_id" value="{{ old('destination_id','') }}">

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">Pengiriman</h5>

                    <div class="mb-3">
                        <label class="form-label">Opsi Pengiriman</label>
                        <select name="shipping_option" id="shipping_option" class="form-select" required>
                            <option value="ambil" {{ old('shipping_option')==='ambil'?'selected':'' }}>Ambil di Toko</option>
                            <option value="kirim" {{ old('shipping_option')==='kirim'?'selected':'' }}>Dikirim ke Alamat</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3 d-none" id="courier-wrapper">
                        <div class="col-md-6">
                            <label class="form-label">Kurir</label>
                            <select name="shipping_courier" id="shipping_courier" class="form-select">
                                <option value="">Pilih Kurir</option>
                                <option value="jne"  {{ old('shipping_courier')==='jne'?'selected':'' }}>JNE</option>
                                <option value="tiki" {{ old('shipping_courier')==='tiki'?'selected':'' }}>TIKI</option>
                                <option value="pos"  {{ old('shipping_courier')==='pos'?'selected':'' }}>POS Indonesia</option>
                                <option value="jnt"  {{ old('shipping_courier')==='jnt'?'selected':'' }}>J&T Express</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Layanan</label>
                            <select name="shipping_service" id="shipping_service" class="form-select">
                                <option value="">Pilih Layanan</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small d-none" id="shippingInfo"></div>

                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost',0) }}">
                    <input type="hidden" name="shipping_etd" id="shipping_etd" value="{{ old('shipping_etd','') }}">

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">Pembayaran</h5>

                    <div class="mb-4">
                        <label class="form-label">Opsi Pembayaran</label>
                        <select name="payment_option" class="form-select" required>
                            <option value="transfer" {{ old('payment_option')==='transfer'?'selected':'' }}>Transfer / Payment Gateway</option>
                            <option value="tunai" {{ old('payment_option')==='tunai'?'selected':'' }}>Bayar di Toko</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                            &laquo; Kembali ke Keranjang
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Buat Pesanan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    const subtotal = {{ (int) $subtotal }};

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const provinceSelect = document.getElementById('provinceSelect');
    const citySelect = document.getElementById('citySelect');
    const districtSelect = document.getElementById('districtSelect');
    const destinationIdInput = document.getElementById('destination_id');

    const shippingSelect = document.getElementById('shipping_option');
    const courierWrapper = document.getElementById('courier-wrapper');
    const courierSelect  = document.getElementById('shipping_courier');
    const serviceSelect  = document.getElementById('shipping_service');

    const shippingInfo   = document.getElementById('shippingInfo');
    const ongkirText     = document.getElementById('ongkirText');
    const totalText      = document.getElementById('totalText');

    const etdInput       = document.getElementById('shipping_etd');
    const costInput      = document.getElementById('shipping_cost');

    function rupiah(n){ return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }

    function resetShippingUI() {
        serviceSelect.innerHTML = `<option value="">Pilih Layanan</option>`;
        etdInput.value = '';
        costInput.value = 0;
        ongkirText.textContent = rupiah(0);
        totalText.textContent  = rupiah(subtotal);
        shippingInfo.classList.add('d-none');
        shippingInfo.textContent = '';
    }

    function toggleCourier() {
        if (shippingSelect.value === 'kirim') {
            courierWrapper.classList.remove('d-none');

            courierSelect.setAttribute('required', 'required');
            serviceSelect.setAttribute('required', 'required');

        } else {
            courierWrapper.classList.add('d-none');
            courierSelect.value = '';

            courierSelect.removeAttribute('required');
            serviceSelect.removeAttribute('required');

            resetShippingUI();
        }
    }

    async function loadProvinces(){
        const res = await fetch(`{{ route('regions.provinces') }}`, {
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();

        provinceSelect.innerHTML = `<option value="">Pilih Provinsi</option>`;
        (json.data || []).forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name;
            provinceSelect.appendChild(opt);
        });
    }

    async function loadCities(provinceId){
        citySelect.disabled = true;
        districtSelect.disabled = true;
        citySelect.innerHTML = `<option value="">Loading...</option>`;
        districtSelect.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        destinationIdInput.value = '';
        resetShippingUI();

        const res = await fetch(`{{ url('/regions/cities') }}/${provinceId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();

        citySelect.innerHTML = `<option value="">Pilih Kota</option>`;
        (json.data || []).forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.name;
            opt.dataset.id = c.id;
            opt.textContent = c.name;
            citySelect.appendChild(opt);
        });

        citySelect.disabled = false;
    }

    async function loadDistricts(cityId){
        districtSelect.disabled = true;
        districtSelect.innerHTML = `<option value="">Loading...</option>`;
        destinationIdInput.value = '';
        resetShippingUI();

        const res = await fetch(`{{ url('/regions/districts') }}/${cityId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();

        districtSelect.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        (json.data || []).forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.name;
            opt.dataset.id = d.id;
            opt.textContent = d.name;
            districtSelect.appendChild(opt);
        });

        districtSelect.disabled = false;
    }

    async function loadServices() {
        const courier = courierSelect.value;
        const destination = destinationIdInput.value.trim();

        if (!courier || !destination) {
            resetShippingUI();
            return;
        }

        const weight = 1000; 
        serviceSelect.innerHTML = `<option value="">Loading...</option>`;
        shippingInfo.classList.add('d-none');

        const res = await fetch(`{{ route('shipping.cek') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ courier, destination, weight })
        });

        const json = await res.json();

        if (!json.success) {
            serviceSelect.innerHTML = `<option value="">Pilih Layanan</option>`;
            shippingInfo.textContent = json.message || 'Gagal mengambil ongkir.';
            shippingInfo.classList.remove('d-none');
            resetShippingUI();
            return;
        }

        const costs = json.costs || [];
        serviceSelect.innerHTML = `<option value="">Pilih Layanan</option>`;

        costs.forEach(item => {
            const harga = item?.cost?.[0]?.value ?? 0;
            const etd   = item?.cost?.[0]?.etd ?? '';
            const svc   = item?.service ?? 'SERVICE';
            const desc  = item?.description ?? '';

            const opt = document.createElement('option');
            opt.value = svc;
            opt.dataset.harga = harga;
            opt.dataset.etd   = etd;
            opt.dataset.desc  = desc;
            opt.textContent = `${svc} - ${rupiah(harga)}${etd ? ` (${etd} hari)` : ''}`;

            serviceSelect.appendChild(opt);
        });
    }

    function onServiceChange() {
        const opt = serviceSelect.selectedOptions[0];
        const harga = parseInt(opt?.dataset?.harga || 0);
        const etd   = opt?.dataset?.etd || '';
        const desc  = opt?.dataset?.desc || '';

        costInput.value = harga;
        etdInput.value  = etd;

        ongkirText.textContent = rupiah(harga);
        totalText.textContent  = rupiah(subtotal + harga);

        if (opt && opt.value) {
            shippingInfo.textContent = `${desc}${etd ? ` (Estimasi: ${etd} hari)` : ''}`;
            shippingInfo.classList.remove('d-none');
        } else {
            shippingInfo.classList.add('d-none');
            shippingInfo.textContent = '';
        }
    }

    provinceSelect.addEventListener('change', e => {
        const id = e.target.value;
        citySelect.disabled = true;
        districtSelect.disabled = true;
        citySelect.innerHTML = `<option value="">Pilih Kota</option>`;
        districtSelect.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        destinationIdInput.value = '';
        resetShippingUI();
        if (!id) return;
        loadCities(id);
    });

    citySelect.addEventListener('change', e => {
        const cityApiId = e.target.selectedOptions[0]?.dataset?.id || '';
        districtSelect.disabled = true;
        districtSelect.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        destinationIdInput.value = '';
        resetShippingUI();
        if (!cityApiId) return;
        loadDistricts(cityApiId);
    });

    districtSelect.addEventListener('change', e => {
        const districtApiId = e.target.selectedOptions[0]?.dataset?.id || '';
        destinationIdInput.value = districtApiId;
        loadServices();
    });

    shippingSelect.addEventListener('change', toggleCourier);
    courierSelect.addEventListener('change', loadServices);
    serviceSelect.addEventListener('change', onServiceChange);

    toggleCourier();
    await loadProvinces();
});
</script>
@endsection
