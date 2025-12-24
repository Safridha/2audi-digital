@extends('layouts.admin')
@section('title', 'Tambah Kategori')

@section('content')
  <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Tambah Kategori</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Nama --}}
      <div class="mb-4">
        <label class="block mb-1 text-sm text-gray-700 dark:text-gray-200">Nama Kategori</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Gambar --}}
      <div class="mb-4">
        <label class="block mb-1 text-sm text-gray-700 dark:text-gray-200">Gambar</label>
        <input type="file" name="image" class="w-full border rounded px-3 py-2">
        @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Deskripsi --}}
      <div class="mb-6">
        <label class="block mb-1 text-sm text-gray-700 dark:text-gray-200">Deskripsi</label>
        <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center gap-3">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" type="submit">
          Simpan
        </button>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:underline">Batal</a>
      </div>
    </form>
  </div>
@endsection
