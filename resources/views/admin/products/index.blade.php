@extends('layouts.admin')
@section('title', 'Kelola Produk')

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
        Daftar Produk
      </h2>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
          Tambah
        </a>

        @if($products->count())
          <button type="submit"
                  form="bulk-delete-products-form"
                  class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
            Hapus Terpilih
          </button>
        @endif
      </div>
    </div>

    <div class="mb-4">
      <form method="GET"
            action="{{ route('admin.products.index') }}"
            class="flex flex-wrap items-center gap-2">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Cari produk / kategori..."
               class="border rounded px-3 py-1 text-sm w-full md:w-64">

        <button type="submit"
                class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">
          Cari
        </button>

        @if(request('q'))
          <a href="{{ route('admin.products.index') }}"
             class="text-sm text-gray-600 hover:underline">
            Reset
          </a>
        @endif
      </form>
    </div>

    <div id="product-table">
      @include('admin.products._table', ['products' => $products])
    </div>
  </div>

  <form id="bulk-delete-products-form"
        action="{{ route('admin.products.bulk-destroy') }}"
        method="POST"
        class="hidden">
    @csrf
    @method('DELETE')
  </form>

  <script>
    document.addEventListener('click', function (e) {
      const link = e.target.closest('#product-table .pagination a');
      if (!link) return;

      e.preventDefault();

      fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
          document.getElementById('product-table').innerHTML = html;
        })
        .catch(console.error);
    });

    document.addEventListener('change', function (e) {
      if (e.target && e.target.id === 'select-all-products') {
        const checked = e.target.checked;

        document
          .querySelectorAll('#product-table .row-checkbox-product')
          .forEach(cb => cb.checked = checked);
      }
    });
  </script>
@endsection
