@extends('layouts.main')

@section('title', 'Checkout - 1 Produk')

@section('content')
<div class="container mt-1 mb-5">
    {{-- ✅ samakan style judul dengan halaman detail produk --}}
    <h4 class="fw-bold text-blue-900 text-center mb-3 mt-n2" style="font-size: 22px;">
        Checkout 1 Produk
    </h4>

    <div class="row g-4">

        {{-- RINGKASAN CETAKAN --}}
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <h5 class="fw-bold mb-2">{{ $product->name }}</h5>

                    <p class="mb-1">
                        Harga satuan:
                        <strong>Rp {{ number_format($product->price, 0, ',', '.') }} / m²</strong>
                    </p>

                    <hr>

                    <h6 class="fw-bold">Detail Cetakan</h6>

                    <p class="mb-1">
                        Ukuran:
                        <strong>{{ $detail['length'] }} m × {{ $detail['width'] }} m</strong>
                        <span class="text-muted">
                            ({{ number_format($area, 2, ',', '.') }} m² per lembar)
                        </span>
                    </p>

                    <p class="mb-1">
                        Jumlah:
                        <strong>{{ $detail['quantity'] }} lembar</strong>
                    </p>

                    <p class="mb-1">
                        Harga Produk:
                        <strong>Rp {{ number_format($productTotal, 0, ',', '.') }}</strong>
                    </p>

                    <p class="mb-1">
                        Finishing:
                        <strong>{{ ucfirst($detail['finishing']) }}</strong>
                        @if($finishingRate > 0)
                            <span class="text-muted">
                                (Rp {{ number_format($finishingRate, 0, ',', '.') }} / m²)
                            </span>
                        @else
                            <span class="text-muted">(tanpa finishing)</span>
                        @endif
                    </p>

                    <p class="mb-1">
                        Total Finishing:
                        <strong>Rp {{ number_format($finishingTotal, 0, ',', '.') }}</strong>
                    </p>

                    @if(!empty($detail['note']))
                        <p class="mb-1 mt-2">
                            Catatan:<br>
                            <span class="small text-muted">{{ $detail['note'] }}</span>
                        </p>
                    @endif

                    <hr>

                    <p class="mb-1">
                        Subtotal:
                        <strong id="subtotalText">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </p>

                    <p class="small text-muted mb-0">
                        Estimasi harga sebelum ongkir (jika dikirim).
                    </p>
                </div>
            </div>
        </div>

        {{-- FORM DATA PEMESAN --}}
        <div class="col-md-7">
            <div class="card shadow-sm border-0 p-4">

                <form action="{{ route('checkout.single.store') }}" method="POST" id="checkoutForm">
                    @csrf

                    <h5 class="fw-bold mb-3">Data Pemesan</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text"
                                   name="customer_name"
                                   class="form-control"
                                   value="{{ old('customer_name', auth()->user()->name) }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="customer_email"
                                   class="form-control"
                                   value="{{ old('customer_email', auth()->user()->email) }}"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. HP / WhatsApp</label>
                        <input type="text"
                               name="customer_phone"
                               class="form-control"
                               value="{{ old('customer_phone') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address"
                                  class="form-control"
                                  rows="2"
                                  required>{{ old('address') }}</textarea>
                    </div>

                    {{-- DROPDOWN ALA SHOPEE --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Provinsi</label>
                            <select class="form-select" id="provinceSelect">
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
                        <input type="text"
                               name="postal_code"
                               class="form-control"
                               value="{{ old('postal_code') }}"
                               required>
                    </div>

                    {{-- RajaOngkir destination (district id) --}}
                    <input type="hidden" name="destination_id" id="destination_id" value="{{ old('destination_id','') }}">

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">Pengiriman & Pembayaran</h5>

                    <div class="mb-3">
                        <label class="form-label">Opsi Pengiriman</label>
                        <select name="shipping_option" id="shipping_option" class="form-select" required>
                            <option value="ambil" {{ old('shipping_option') === 'ambil' ? 'selected' : '' }}>Ambil di Toko</option>
                            <option value="kirim" {{ old('shipping_option') === 'kirim' ? 'selected' : '' }}>Dikirim ke Alamat</option>
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

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_option" class="form-select" required>
                            <option value="transfer" {{ old('payment_option') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="tunai" {{ old('payment_option') === 'tunai' ? 'selected' : '' }}>Bayar di Toko</option>
                        </select>
                    </div>

                    <input type="hidden" name="shipping_etd" id="shipping_etd" value="{{ old('shipping_etd','') }}">
                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost',0) }}">

                    <hr class="my-4">

                    <div class="mb-3">
                        <h5 class="fw-bold">Ringkasan Pembayaran</h5>

                        <p class="mb-1">
                            Subtotal:
                            <strong id="subtotalRingkas">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </p>

                        <p class="mb-1">
                            Ongkir:
                            <strong id="ongkirRingkas">Rp 0</strong>
                        </p>

                        <p class="mt-2 h5 text-blue-900">
                            Total Bayar:
                            <strong id="totalRingkas">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Lanjut ke Pembayaran
                    </button>
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
    const ongkirText     = document.getElementById('ongkirRingkas');
    const totalText      = document.getElementById('totalRingkas');

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
        } else {
            courierWrapper.classList.add('d-none');
            courierSelect.value = '';
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

        const weight = 1000; // TODO: bikin dinamis
        serviceSelect.innerHTML = `<option value="">Loading...</option>`;
        shippingInfo.classList.add('d-none');
        shippingInfo.textContent = '';

        const res = await fetch(`{{ route('shipping.cek') }}`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ courier, destination, weight })
        });

        const text = await res.text();
        let json = {};
        try { json = JSON.parse(text); } catch(e) {}

        if (!res.ok || !json.success) {
            serviceSelect.innerHTML = `<option value="">Pilih Layanan</option>`;
            etdInput.value = '';
            costInput.value = 0;
            ongkirText.textContent = rupiah(0);
            totalText.textContent  = rupiah(subtotal);

            shippingInfo.textContent = json.message || `Gagal ambil ongkir. (HTTP ${res.status})`;
            shippingInfo.classList.remove('d-none');
            return;
        }

        const costs = json.costs || [];
        serviceSelect.innerHTML = `<option value="">Pilih Layanan</option>`;

        if (!costs.length) {
            shippingInfo.textContent = 'Ongkir kosong dari API. Cek RAJAONGKIR_KEY / ORIGIN / endpoint.';
            shippingInfo.classList.remove('d-none');
            resetShippingUI();
            return;
        }

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
