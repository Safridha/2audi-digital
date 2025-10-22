@extends('layouts.main')

@section('title', 'Beranda - 2 Audi Digital')

@section('content')

<!-- BODY GRADIENT -->
<section class="bg-gradient-to-b from-blue-50 to-white py-8">

    <!-- BANNER & KATEGORI PRODUK -->
    <div class="container mt-4">
        <div class="row align-items-start g-4">

            <div class="col-md-5">
                <div class="p-4 rounded shadow-sm h-100 bg-blue-100">
                    <h5 class="fw-bold text-center mb-4 text-blue-900">TENTANG 2 AUDI - PROMO</h5>
                    <div class="d-flex justify-content-center align-items-center"
                         style="height: 250px; background-color: #e0f2ff; border-radius: 8px;">
                        <p class="text-blue-800 m-0">[Area untuk carousel, promo, atau gambar utama]</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="p-4 rounded shadow-sm h-100 bg-blue-50">
                    <h5 class="fw-bold mb-4 text-center text-blue-900">KATEGORI PRODUK</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-4">
                        @forelse ($categories as $category)
                            <div class="text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300" style="width: 150px;">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="img-fluid rounded shadow-sm mb-2" 
                                         style="height: 100px; object-fit: cover;">
                                @else
                                    <div class="bg-blue-200 d-flex align-items-center justify-content-center text-blue-800 rounded mb-2" style="height: 100px;">
                                        <span>No Image</span>
                                    </div>
                                @endif
                                <p class="fw-bold mb-1 text-blue-900">{{ $category->name }}</p>
                                @if($category->price)
                                    <p class="text-blue-700 fw-semibold mb-0">{{ number_format($category->price, 0, ',', '.') }}/m</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-blue-800">Belum ada kategori tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- LAYANAN -->
    <section class="container mt-5 text-center border-t border-gray-200 pt-8">
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
                        <img src="{{ asset('images/'.$service['img']) }}" alt="{{ $service['title'] }}" class="img-fluid mx-auto" style="width: 60px; height: 60px; object-fit: contain;">
                    </div>
                    <h6 class="fw-bold text-blue-900">{{ $service['title'] }}</h6>
                    <p class="text-blue-800 small">{{ $service['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- LANGKAH ORDER -->
    <section class="container text-center mt-5 mb-5 pb-5 border-t border-gray-200 pt-8">
        <h4 class="fw-bold mb-5 text-blue-900">LANGKAH ORDER</h4>
        <div class="flex flex-col md:flex-row md:justify-center md:items-center md:space-x-8 space-y-8 md:space-y-0">
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
            <div class="flex flex-col items-center relative md:flex-1">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-800 text-white font-bold text-xl shadow-lg z-10">
                    {{ $index + 1 }}
                </div>
                <p class="text-blue-800 text-sm mt-3 max-w-xs text-center">{{ $step }}</p>
            </div>
            @endforeach
        </div>
    </section>

</section>

@endsection
