@extends('layouts.admin')
@section('title', 'Tambah Bahan')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        Tambah Bahan
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Form ini digunakan untuk menambahkan master data bahan. Data bahan akan digunakan
        pada halaman <span class="font-semibold">Kelola Stok Bahan (FIFO + Martingale)</span>.
    </p>

    {{-- Error Validasi --}}
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-100 p-6">
        <form action="{{ route('admin.bahans.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama Bahan --}}
            <div>
                <label for="nama_bahan" class="block text-sm font-medium text-gray-700">
                    Nama Bahan
                </label>
                <input type="text"
                       id="nama_bahan"
                       name="nama_bahan"
                       value="{{ old('nama_bahan') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                       placeholder="Misal: Kertas A3 100 gsm"
                       required>
            </div>

            {{-- Satuan --}}
            <div>
                <label for="satuan" class="block text-sm font-medium text-gray-700">
                    Satuan
                </label>
                <input type="text"
                       id="satuan"
                       name="satuan"
                       value="{{ old('satuan', 'lembar') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                       placeholder="Misal: lembar, liter, kg"
                       required>
            </div>

            {{-- Minimal Stok --}}
            <div>
                <label for="minimal_stock" class="block text-sm font-medium text-gray-700">
                    Minimal Stok (opsional)
                </label>
                <input type="number"
                       id="minimal_stock"
                       name="minimal_stock"
                       value="{{ old('minimal_stock', 0) }}"
                       min="0"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                       placeholder="Misal: 100">
                <p class="mt-1 text-xs text-gray-500">
                    Nilai ini akan dipakai sebagai batas minimal stok untuk peringatan / rekomendasi restock.
                </p>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600
                               text-white text-sm font-medium hover:bg-indigo-700">
                    Simpan
                </button>

                <a href="{{ route('admin.bahans.index') }}"
                   class="text-gray-600 hover:underline text-sm">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
