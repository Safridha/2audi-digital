@extends('layouts.admin')
@section('title', 'Tambah Produk')

@section('content')
  <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow max-w-4xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
      Tambah Produk
    </h2>

    {{-- Error validasi global --}}
    @if ($errors->any())
      <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      {{-- ============== BAGIAN INFORMASI PRODUK ============== --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Kiri: Nama, Kategori, Harga --}}
        <div class="space-y-4">
          {{-- Nama --}}
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
              Nama Produk
            </label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="w-full border rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
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
                    class="w-full border rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
              <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih kategori</option>
              @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
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
            <input
              type="number"
              name="price"
              min="0"
              step="1"
              value="{{ old('price', 0) }}"
              class="w-full border rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Contoh: 12000">
            <p class="text-xs text-gray-500 mt-1">
              Isi tanpa titik/koma. Contoh: <span class="font-semibold">12000</span> untuk Rp 12.000.
            </p>
            @error('price')
              <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Kanan: Gambar + Deskripsi --}}
        <div class="space-y-4">
          {{-- Gambar --}}
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
              Gambar Produk
            </label>
            <input type="file"
                   name="image"
                   class="w-full border rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('image')
              <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Deskripsi --}}
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
              Deskripsi
            </label>
            <textarea name="description"
                      rows="4"
                      class="w-full border rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Tulis poin per baris.&#10;Contoh:&#10;- Spanduk | Banner&#10;- X-Banner | Roll Up Banner">{{ old('description') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">
              Tip: isi deskripsi <span class="font-semibold">per baris</span>. Setiap baris akan tampil sebagai poin di katalog.
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
          Pilih bahan apa saja yang digunakan untuk memproduksi <span class="font-semibold">1 unit</span> produk ini,
          lalu isi kebutuhan bahan per unit. Data ini dipakai untuk pemotongan stok otomatis (FIFO + Martingale),
          tidak ditampilkan ke pelanggan.
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
                  <th class="px-3 py-2 text-left w-10">
                    Pakai
                  </th>
                  <th class="px-3 py-2 text-left">
                    Nama Bahan
                  </th>
                  <th class="px-3 py-2 text-left">
                    Satuan
                  </th>
                  <th class="px-3 py-2 text-right">
                    Qty per 1 Meter
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                @foreach($bahans as $bahan)
                  @php
                    $oldQty = old("bahans.{$bahan->id}.qty_per_unit");
                    $checked = old("bahans.{$bahan->id}.enabled") || $oldQty;
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
                             class="w-28 border rounded px-2 py-1 text-right text-xs focus:ring-indigo-500 focus:border-indigo-500">
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- Tombol --}}
      <div class="flex items-center gap-3 pt-2">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm" type="submit">
          Simpan
        </button>
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:underline text-sm">
          Batal
        </a>
      </div>
    </form>
  </div>
@endsection
