@extends('layouts.admin')
@section('title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto">

    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
        Edit Produk
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Ubah data produk lengkap berikut kategori, gambar, harga, dan konfigurasi bahan.
        Konfigurasi bahan digunakan untuk pengurangan stok otomatis, tidak ditampilkan ke pelanggan.
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

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 p-6">
        <form action="{{ route('admin.products.update', $product->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">

            @csrf
            @method('PUT')

            {{-- ============== BAGIAN INFORMASI PRODUK ============== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Kiri --}}
                <div class="space-y-4">
                    {{-- Nama Produk --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Nama Produk
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $product->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               required>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Kategori
                        </label>
                        <select name="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                       focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    @selected(old('category_id', $product->category_id) == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Harga (Rp)
                        </label>
                        <input type="number"
                               name="price"
                               value="{{ old('price', $product->price) }}"
                               min="0"
                               step="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="Contoh: 12000">
                        <p class="text-xs text-gray-500 mt-1">
                            Isi tanpa titik/koma. Contoh: 12000 untuk Rp 12.000.
                        </p>
                        @error('price')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Kanan --}}
                <div class="space-y-4">
                    {{-- Gambar --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Gambar Produk
                        </label>
                        <input type="file"
                               name="image"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        @error('image')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if ($product->image)
                            <p class="text-xs text-gray-500 mt-2">Gambar saat ini:</p>
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 class="h-20 mt-2 rounded border shadow-sm object-cover">
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Deskripsi
                        </label>
                        <textarea name="description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                         focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="- Spanduk | Banner&#10;- X-Banner | Roll Up Banner">{{ old('description', $product->description) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Gunakan format per baris untuk menampilkan poin pada katalog.
                        </p>
                        @error('description')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============== BAGIAN KONFIGURASI BAHAN ============== --}}
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2">
                    Konfigurasi Bahan untuk Produk ini
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    Pilih bahan yang dipakai untuk memproduksi <span class="font-semibold">1 unit</span> produk ini,
                    lalu isi jumlah kebutuhannya. Konfigurasi ini akan dipakai untuk pengurangan stok otomatis.
                </p>

                @if($bahans->isEmpty())
                    <p class="text-xs text-red-600">
                        Belum ada data bahan. Tambahkan bahan terlebih dahulu di menu <strong>Kelola Bahan</strong>.
                    </p>
                @else
                    <div class="overflow-x-auto rounded border border-gray-200">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left w-10">Pakai</th>
                                    <th class="px-3 py-2 text-left">Nama Bahan</th>
                                    <th class="px-3 py-2 text-left">Satuan</th>
                                    <th class="px-3 py-2 text-right">Qty per 1 Meter</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($bahans as $bahan)
                                    @php
                                        $defaultQty = $selectedBahans[$bahan->id] ?? null;
                                        $oldQty = old("bahans.$bahan->id.qty_per_unit", $defaultQty);
                                        $checkedOld = old("bahans.$bahan->id.enabled");
                                        $checked = $checkedOld || (!is_null($defaultQty) && $defaultQty > 0);
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox"
                                                   name="bahans[{{ $bahan->id }}][enabled]"
                                                   value="1"
                                                   {{ $checked ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                                {{ $bahan->nama_bahan }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-gray-500">
                                            {{ $bahan->satuan }}
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input type="number"
                                                   step="0.001"
                                                   min="0"
                                                   name="bahans[{{ $bahan->id }}][qty_per_unit]"
                                                   value="{{ $oldQty }}"
                                                   placeholder="0"
                                                   class="w-28 border rounded px-2 py-1 text-right text-xs
                                                          focus:ring-indigo-500 focus:border-indigo-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    Update
                </button>

                <a href="{{ route('admin.products.index') }}"
                   class="text-gray-600 hover:underline text-sm">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
