@extends('layouts.main')

@section('title', 'Katalog Produk - ' . $category->name)

@section('content')
<div class="container mt-4 mb-5">

    <h4 class="fw-bold text-blue-900 text-center mb-3" style="font-size: 22px;">
        Katalog Produk: {{ $category->name }}
    </h4>

    @if ($products->count())
        <div class="row g-4">
            @foreach ($products as $product)
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 shadow-sm border-0">

                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 class="card-img-top"
                                 style="height:180px; object-fit:cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="height:180px;">
                                <span class="text-muted">Tidak ada gambar</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold mb-2">{{ $product->name }}</h6>

                            <p class="fw-semibold text-blue-900 mb-2">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>

                            {{-- DESKRIPSI PRODUK --}}
                            @if($product->description)
                                <div class="text-muted" style="font-size: 13px;">
                                    @foreach(preg_split("/\r\n|\n|\r/", trim($product->description)) as $line)
                                        @if(trim($line) !== '')
                                            <div>• {{ ltrim(trim($line), "-• ") }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @auth
                                <a href="{{ route('product.order', $product) }}"
                                   class="btn btn-primary w-100 mt-auto">
                                    Pesan
                                </a>
                            @else
                                <button class="btn btn-primary w-100 mt-auto"
                                        onclick="needLogin()">
                                    Pesan
                                </button>
                            @endauth
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-3 text-muted text-center">Belum ada produk untuk kategori ini.</p>
    @endif
</div>

<script>
function needLogin() {
    alert('Silakan login untuk memesan.');
    window.location.href = '{{ route("login") }}';
}
</script>
@endsection
