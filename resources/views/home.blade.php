@extends('layouts.main')

@section('title', 'Beranda - 2 Audi Digital')

@section('content')

@php
    use Illuminate\Support\Str;
@endphp

<style>
/* PROMO BESAR – TANPA KOTAK DALAM */
.promo-wrap{
    position: relative;
    width: 100%;
    height: 320px;              /* ⬅️ BESARIN DI SINI */
    overflow: hidden;
}

.promo-wrap img{
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;        /* banner utuh */
    opacity: 0;
    transition: opacity .6s ease;
}

.promo-wrap img.active{
    opacity: 1;
}

@media (max-width: 992px){
    .promo-wrap{ height: 260px; }
}

@media (max-width: 576px){
    .promo-wrap{ height: 220px; }
}
</style>

{{-- HERO + PROMO (digabung agar kategori lebih luas) --}}
<section class="container mt-4">
    <div class="card-soft p-4 p-md-5 section-surface">
        <div class="row align-items-center g-4">

            {{-- HERO KIRI --}}
            <div class="col-lg-7">
                <h1 class="fw-bold" style="font-size: clamp(22px, 3vw, 36px); line-height: 1.15;">
                    Cetak Banner, Spanduk & Kebutuhan Promosi
                </h1>
                <p class="text-muted mt-2 mb-4" style="font-size: 15px;">
                    Full color • Bisa custom ukuran • Produksi cepat • Hasil tajam dan rapi.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="#kategori-produk" class="btn-brand text-decoration-none">
                        Lihat Kategori
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <span class="badge rounded-pill text-bg-light border">Mulai Rp 16.000/m*</span>
                    <span class="badge rounded-pill text-bg-light border">One Day Service*</span>
                    <span class="badge rounded-pill text-bg-light border">Kirim Seluruh Indonesia</span>
                </div>
            </div>

            {{-- PROMO KANAN (sidebar) --}}
            <div class="col-lg-5">
                    <div class="promo-wrap mb-3" id="promoSlider">
                        <img src="{{ asset('images/carousel/hero-1.jpg') }}" class="active" alt="Promo 1">
                        <img src="{{ asset('images/carousel/hero-2.jpg') }}" alt="Promo 2">
                        <img src="{{ asset('images/carousel/hero-3.jpg') }}" alt="Promo 3">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Script promo slider (tetap) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('#promoSlider img');
    if (!slides.length) return;
    let idx = 0;
    setInterval(() => {
        slides[idx].classList.remove('active');
        idx = (idx + 1) % slides.length;
        slides[idx].classList.add('active');
    }, 3500);
});
</script>

{{-- KATEGORI (dibuat FULL WIDTH agar lebih luas) --}}
<section class="container mt-4" id="kategori-produk">
    <div class="card-soft p-4 section-surface">
        <h5 class="fw-bold text-center mb-4">KATEGORI PRODUK</h5>

        <div class="row g-3">
            @forelse ($categories as $category)
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="{{ route('catalog.products', $category->id) }}" class="text-decoration-none text-reset">
                        <div class="card-soft p-3 h-100 hover:shadow-lg transition-all duration-300">

                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="img-fluid rounded mb-2"
                                     style="height:150px; width:100%; object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded mb-2"
                                     style="height:150px;">
                                    <span class="text-muted small">No Image</span>
                                </div>
                            @endif

                            <div class="d-flex align-items-center justify-content-between">
                                <p class="fw-bold mb-0" style="font-size: 15px;">{{ $category->name }}</p>
                                <i class="bi bi-arrow-right-short text-primary fs-5"></i>
                            </div>

                            {{-- DESKRIPSI KATEGORI (BARU) --}}
                            @php $desc = trim($category->description ?? ''); @endphp
                            @if($desc !== '')
                                <p class="text-muted small mt-2 mb-1">
                                    {{ Str::limit(strip_tags($desc), 85) }}
                                </p>
                            @endif

                            <p class="text-primary small mb-0">
                                Klik untuk lihat produk {{ strtolower($category->name) }}.
                            </p>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-muted text-center">Belum ada kategori tersedia.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- LAYANAN --}}
<section class="container mt-4">
    <div class="card-soft p-4 section-surface text-center">
        <h4 class="fw-bold mb-4">LAYANAN</h4>

        @php
            $services = [
                ['img'=>'kualitas.png','title'=>'Kualitas Terbaik','desc'=>'Hasil cetak tajam dengan teknologi modern.'],
                ['img'=>'service.png','title'=>'One Day Service','desc'=>'Pesanan bisa selesai lebih cepat.'],
                ['img'=>'pelayanan.png','title'=>'Pelayanan Ramah','desc'=>'Kami siap bantu melayani.'],
                ['img'=>'terjangkau.png','title'=>'Harga Terjangkau','desc'=>'Kualitas oke, harga bersahabat.'],
                ['img'=>'pengiriman.png','title'=>'Kirim Indonesia','desc'=>'Pengiriman ke berbagai daerah.'],
            ];
        @endphp

        <div class="row g-3 justify-content-center">
            @foreach($services as $service)
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="p-3 rounded-3 border bg-white h-100 hover:shadow-lg transition-all duration-300">
                        <img src="{{ asset('images/'.$service['img']) }}" alt="{{ $service['title'] }}"
                             class="img-fluid mx-auto mb-2" style="width: 56px; height: 56px; object-fit: contain;">
                        <h6 class="fw-bold mb-1">{{ $service['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $service['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- LANGKAH ORDER --}}
<section class="container mt-4 mb-2 pb-1">
    <div class="card-soft p-4 section-surface text-center">
        <h4 class="fw-bold mb-4">LANGKAH ORDER</h4>

        @php
            $steps = [
                'Pilih kategori produk.',
                'Pilih produk sesuai kebutuhan.',
                'Atur ukuran/bahan/spesifikasi.',
                'Upload desain.',
                'Checkout pesanan.',
                'Bayar & pesanan diproses.',
            ];
        @endphp

        <div class="row g-3 justify-content-center">
            @foreach($steps as $i => $step)
                <div class="col-6 col-md-2">
                    <div class="p-3 rounded-3 border bg-white h-100">
                        <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                             style="width:44px;height:44px;background:#4f46e5;">
                            {{ $i+1 }}
                        </div>
                        <p class="small text-muted mt-2 mb-0">{{ $step }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
