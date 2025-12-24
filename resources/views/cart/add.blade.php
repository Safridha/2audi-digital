@extends('layouts.main')

@section('title', 'Tambah ke Keranjang')

@section('content')
<div class="container mt-5 mb-5">

    <h3 class="fw-bold mb-4 text-blue-900 text-center">Detail Cetakan & Keranjang</h3>

    <div class="row g-4">

        {{-- RINGKASAN PRODUK --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}"
                         class="card-img-top"
                         alt="{{ $product->name }}"
                         style="height:200px; object-fit:cover;">
                @endif

                <div class="card-body">
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>

                    @if($product->description)
                        <p class="small text-muted mb-2" style="white-space: pre-line;">
                            {{ $product->description }}
                        </p>
                    @endif

                    <p class="fw-semibold text-blue-900 mb-0">
                        Harga satuan: Rp {{ number_format($product->price, 0, ',', '.') }} / m²
                    </p>
                </div>
            </div>
        </div>

        {{-- FORM DETAIL CETAKAN UNTUK KERANJANG --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4">

                <form action="{{ route('cart.add', $product->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <h5 class="fw-bold mb-3">Detail Cetakan</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Panjang (m)</label>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('length') is-invalid @enderror"
                                   name="length" value="{{ old('length') }}" required>
                            @error('length')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Lebar (m)</label>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('width') is-invalid @enderro"
