@extends('layouts.admin')
@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        Edit Kategori
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Ubah data kategori produk yang digunakan pada halaman
        <span class="font-semibold">Kelola Produk</span>.
    </p>

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
        <form action="{{ route('admin.categories.update', $category->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Nama Kategori
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $category->name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                       required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Gambar
                </label>
                <input type="file"
                       name="image"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">

                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}"
                         class="h-16 mt-3 rounded border shadow-sm"
                         alt="{{ $category->name }}">
                @endif
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Deskripsi
                </label>
                <textarea name="description"
                          rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('description', $category->description) }}</textarea>

                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    Update
                </button>

                <a href="{{ route('admin.categories.index') }}"
                   class="text-gray-600 hover:underline text-sm">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
