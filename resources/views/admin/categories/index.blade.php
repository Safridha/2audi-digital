@extends('layouts.admin')
@section('title', 'Kelola Kategori')

@section('content')
  @if (session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800 border border-green-200">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800 border border-red-200">
      {{ session('error') }}
    </div>
  @endif

  <div class="bg-white p-6 rounded-lg shadow">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
      <h2 class="text-xl font-semibold text-gray-800">
        Daftar Kategori
      </h2>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.categories.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
          Tambah
        </a>

        @if($categories->count())
          <button type="submit"
                  form="bulk-delete-categories-form"
                  class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700"
                  onclick="return confirm('Yakin hapus kategori yang dipilih?')">
            Hapus Terpilih
          </button>
        @endif
      </div>
    </div>

    <div class="mb-4">
      <form method="GET"
            action="{{ route('admin.categories.index') }}"
            class="flex flex-wrap items-center gap-2">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Cari kategori..."
               class="border rounded px-3 py-1 text-sm w-full md:w-64">

        <button type="submit"
                class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">
          Cari
        </button>

        @if(request('q'))
          <a href="{{ route('admin.categories.index') }}"
             class="text-sm text-gray-600 hover:underline">
            Reset
          </a>
        @endif
      </form>
    </div>

    <div id="category-table">
      @include('admin.categories._table', ['categories' => $categories])
    </div>
  </div>

  <form id="bulk-delete-categories-form"
        action="{{ route('admin.categories.bulk-destroy') }}"
        method="POST"
        class="hidden">
    @csrf
    @method('DELETE')
  </form>

  <script>
    document.addEventListener('click', e => {
      const link = e.target.closest('#category-table .pagination a');
      if (!link) return;

      e.preventDefault();
      fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => document.getElementById('category-table').innerHTML = html)
        .catch(console.error);
    });

    document.addEventListener('change', e => {
      if (e.target?.id === 'select-all-categories') {
        document
          .querySelectorAll('.row-checkbox-category')
          .forEach(cb => cb.checked = e.target.checked);
      }
    });
  </script>
@endsection
