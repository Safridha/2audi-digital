@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Kategori Baru</h5>
                </div>
                <div class="card-body">
                    
                    <!-- Menampilkan pesan sukses -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Form tambah kategori -->
                    <form action="{{ route('create.category') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Foto Contoh Produk -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Foto Contoh Produk</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Kategori -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kategori</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Keterangan / Deskripsi Kategori -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan Kategori</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan Kategori</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
