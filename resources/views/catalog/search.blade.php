@extends('layouts.main')

@section('title', 'Hasil Pencarian: ' . $keyword)

@section('content')
<div class="container mt-5 mb-5">

    <h3 class="fw-bold mb-3 text-blue-900">
        Hasil Pencarian: "{{ $keyword }}"
    </h3>

    @if($products->isEmpty())
        <p class="text-muted">
            Tidak ada produk yang cocok dengan kata kunci tersebut.
        </p>
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
            Kembali ke Beranda
        </a>
    @else
        <p class="text-muted mb-4">
            Ditemukan {{ $products->count() }} produk.
        </p>

        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 class="card-img-top"
                                 alt="{{ $product->name }}"
                                 style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="height:180px;">
                                <span class="text-muted small">Tidak ada gambar</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold mb-1">
                                {{ $product->name }}
                            </h6>

                            @if($product->category)
                                <div class="small text-muted mb-1">
                                    Kategori: {{ $product->category->name }}
                                </div>
                            @endif

                            <div class="fw-semibold text-primary mb-2">
                                Rp {{ number_format($product->price, 0, ',', '.') }} / m²
                            </div>

                            {{-- Deskripsi sebagai bullet list --}}
                            @if($product->description)
                                @php
                                    $lines = preg_split("/\r\n|\n|\r/", trim($product->description));
                                    $lines = array_filter($lines, fn($l) => trim($l) !== '');
                                @endphp

                                @if(count($lines))
                                    <ul class="small text-muted mb-3 ps-3" style="flex:1; list-style-type: disc;">
                                        @foreach($lines as $line)
                                            <li>{{ $line }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif

                            <a href="{{ route('checkout.index', $product->id) }}"
                               class="btn btn-sm btn-primary w-100 mt-auto">
                                Pesan / Custom
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>
@endsection
