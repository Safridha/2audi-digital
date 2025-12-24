@extends('layouts.main')

@section('title', 'Beranda - 2 Audi Digital')

@section('content')

    {{-- STYLE KHUSUS CAROUSEL --}}
    <style>
        #carousel-area {
            position: relative;
            overflow: hidden;
            height: 250px;
            background-color: #e0f2ff;
            border-radius: 8px;
        }

        #carousel-area .carousel-slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }

        #carousel-area .carousel-slide.active {
            opacity: 1;
            z-index: 2;
        }
    </style>

    <!-- BANNER & KATEGORI PRODUK -->
    <div class="container mt-4">
        <div class="row align-items-start g-4">

            <!-- Carousel Promo + Tentang 2 Audi -->
            <div class="col-md-5">
                <div class="p-4 rounded shadow-sm h-100 bg-blue-100">
                    <h5 class="fw-bold text-center mb-4 text-blue-900">TENTANG 2 AUDI - PROMO</h5>

                    <!-- Carousel Area -->
                    <div id="carousel-area" class="position-relative rounded">
                        <img src="{{ asset('images/carousel/hero-1.png') }}"
                             class="carousel-slide active"
                             alt="Promo 1">

                        <img src="{{ asset('images/carousel/hero-2.png') }}"
                             class="carousel-slide"
                             alt="Promo 2">
                    </div>

                    <!-- Script geser otomatis dengan fade -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const slides = document.querySelectorAll('#carousel-area .carousel-slide');
                            if (!slides.length) return;

                            let index = 0;

                            setInterval(() => {
                                // hapus active dari slide sekarang
                                slides[index].classList.remove('active');
                                // pindah ke slide berikutnya (looping)
                                index = (index + 1) % slides.length;
                                // kasih active ke slide baru
                                slides[index].classList.add('active');
                            }, 4000); // ganti slide tiap 4 detik
                        });
                    </script>
                </div>
            </div>

            <!-- Kategori Produk -->
            <div class="col-md-7" id="kategori-produk">
                <div class="p-4 rounded shadow-sm h-100 bg-blue-100">
                    <h5 class="fw-bold mb-4 text-center text-blue-900">KATEGORI PRODUK</h5>

                    @php use Illuminate\Support\Str; @endphp

                    <div class="d-flex flex-wrap justify-content-center gap-4">
                        @forelse ($categories as $category)
                            <a href="{{ route('catalog.products', $category->id) }}"
                               class="text-decoration-none text-reset">
                                <div class="text-center p-3 rounded bg-blue-50 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
                                     style="width: 180px;">

                                     {{-- Gambar kategori --}}
                                        @if($category->image)
                                            <img
                                                src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}"
                                                class="img-fluid rounded shadow-sm mb-2"
                                                style="height:120px; width:100%; object-fit:cover;"
                                            >
                                        @else
                                            <div class="bg-blue-200 d-flex align-items-center justify-content-center text-blue-800 rounded mb-2"
                                                style="height:120px; width:100%;">
                                                No Image
                                            </div>
                                        @endif


                                        {{-- Nama --}}
                                        <p class="fw-bold mb-2 text-blue-900 text-center" style="font-size: 15px;">
                                            {{ $category->name }}
                                        </p>

                                        {{-- Deskripsi jadi poin (rapi & informatif) --}}
                                        @php
                                            $desc = trim($category->description ?? '');
                                            $lines = $desc !== '' ? preg_split("/\r\n|\n|\r/", $desc) : [];
                                        @endphp

                                        @if($desc !== '')
                                            <ul class="text-blue-800 small mb-2 ps-3 text-start"
                                                style="list-style: disc; line-height: 1.6;">
                                                @foreach(array_slice($lines, 0, 3) as $line)
                                                    @php $line = trim($line); @endphp
                                                    @if($line !== '')
                                                        <li>{{ ltrim($line, "-• \t") }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif


                                    {{-- Hint klik --}}
                                    <p class="text-blue-700 small mb-0">
                                        Klik untuk lihat produk {{ strtolower($category->name) }}.
                                    </p>
                                </div>
                            </a>
                        @empty
                            <p class="text-blue-800">Belum ada kategori tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- LAYANAN -->
    <section class="container mt-5 text-center border-top pt-4">
        <h4 class="fw-bold mb-4 text-blue-900">LAYANAN</h4>
        <div class="row justify-content-center g-4">
            @php
                $services = [
                    ['img'=>'kualitas.png','title'=>'Cetak dengan Kualitas Terbaik','desc'=>'Hasil cetak berkualitas dengan teknologi modern.'],
                    ['img'=>'service.png','title'=>'One Day Service','desc'=>'Pesanan bisa selesai dalam hitungan jam.'],
                    ['img'=>'pelayanan.png','title'=>'Pelayanan Terbaik','desc'=>'Tim kami profesional dan ramah.'],
                    ['img'=>'terjangkau.png','title'=>'Harga Terjangkau','desc'=>'Kualitas premium harga ramah di kantong.'],
                    ['img'=>'pengiriman.png','title'=>'Dikirim ke Seluruh Indonesia','desc'=>'Pesanan siap antar ke seluruh pelosok Indonesia.'],
                ];
            @endphp

            @foreach($services as $service)
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="p-3 rounded shadow-md bg-blue-50 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="icon-circle mb-3">
                            <img src="{{ asset('images/'.$service['img']) }}" alt="{{ $service['title'] }}"
                                 class="img-fluid mx-auto" style="width: 60px; height: 60px; object-fit: contain;">
                        </div>
                        <h6 class="fw-bold text-blue-900">{{ $service['title'] }}</h6>
                        <p class="text-blue-800 small mb-0">{{ $service['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- LANGKAH ORDER -->
    <section class="container text-center mt-5 mb-5 pb-5 border-top pt-4">
        <h4 class="fw-bold mb-5 text-blue-900">LANGKAH ORDER</h4>
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-4">
            @php
                $steps = [
                    'Pilih Kategori – Tentukan kategori produk.',
                    'Pilih Produk – Klik produk sesuai kebutuhanmu.',
                    'Atur Spesifikasi – Sesuaikan ukuran, bahan, dan detail lainnya.',
                    'Upload Desain – Unggah file gambar/desain.',
                    'Checkout – Periksa pesanan lalu lakukan checkout.',
                    'Bayar Pesanan – Pilih metode pembayaran dan selesaikan.',
                ];
            @endphp

            @foreach($steps as $index => $step)
                <div class="d-flex flex-column align-items-center flex-fill">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-blue-800 text-white fw-bold shadow-lg"
                         style="width: 48px; height: 48px; font-size: 18px;">
                        {{ $index + 1 }}
                    </div>
                    <p class="text-blue-800 text-sm mt-3 mb-0" style="max-width: 180px;">
                        {{ $step }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

@endsection
