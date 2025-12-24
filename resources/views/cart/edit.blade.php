@extends('layouts.main')

@section('title', 'Edit Item Keranjang')

@section('content')
<div class="container mt-5 mb-5">
    <h4 class="fw-bold text-blue-900 text-center mb-3" style="font-size: 22px;">
        Edit Item Keranjang
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">{{ $cartItem->product->name }}</h5>

            <form action="{{ route('cart.update', $cartItem->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Panjang (m)</label>
                        <input type="number" step="0.01" name="length" class="form-control"
                               value="{{ old('length', $cartItem->length) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Lebar (m)</label>
                        <input type="number" step="0.01" name="width" class="form-control"
                               value="{{ old('width', $cartItem->width) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Qty</label>
                        <input type="number" name="quantity" class="form-control"
                               value="{{ old('quantity', $cartItem->quantity) }}" min="1" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Finishing</label>
                        <select name="finishing" class="form-select" required>
                            @php
                                $fin = strtolower(old('finishing', $cartItem->finishing));
                            @endphp
                            <option value="finishing" {{ $fin === 'finishing' ? 'selected' : '' }}>Finishing</option>
                            <option value="tanpa" {{ $fin === 'tanpa' ? 'selected' : '' }}>Tanpa Finishing</option>
                        </select>
                        <small class="text-muted">Finishing dihitung Rp 500 / m² (sesuai perhitungan di keranjang).</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Catatan (opsional)</label>
                        <input type="text" name="note" class="form-control"
                               value="{{ old('note', $cartItem->note) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upload Desain Baru (opsional)</label>
                        <input type="file" name="design_file" class="form-control">
                        <small class="text-muted">Max 10MB</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Desain Saat Ini</label>
                        <div>
                            @if($cartItem->design_file)
                                @php
                                    $designFile = $cartItem->design_file;
                                    $ext = strtolower(pathinfo($designFile, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                @endphp

                                @if($isImage)
                                    <a href="{{ asset('storage/'.$designFile) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$designFile) }}"
                                             alt="Desain"
                                             style="height:70px; width:110px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                    </a>
                                @else
                                    <a class="btn btn-outline-primary btn-sm"
                                       href="{{ asset('storage/'.$designFile) }}"
                                       target="_blank" download>
                                        Download Desain
                                    </a>
                                @endif
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
