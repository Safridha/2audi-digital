@extends('layouts.admin')
@section('title', 'Monitoring Pesanan')

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

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-gray-800">
        Monitoring Pesanan
      </h2>

      @if($orders->count())
        <button type="submit"
                form="bulk-delete-orders-form"
                class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700"
                onclick="return confirm('Yakin hapus pesanan yang dipilih?')">
          Hapus Terpilih
        </button>
      @endif
    </div>

    {{-- Search --}}
    <form method="GET"
          action="{{ route('admin.orders.index') }}"
          class="mb-4 flex flex-wrap items-center gap-2">
      <input
        type="text"
        name="search"
        placeholder="Cari nama, email, produk, status..."
        value="{{ $search ?? '' }}"
        class="px-3 py-2 border rounded text-sm w-64 md:w-80 focus:ring focus:ring-indigo-300"
      >
      <button type="submit"
              class="px-3 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
        Cari
      </button>

      @if(!empty($search))
        <a href="{{ route('admin.orders.index') }}"
           class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
          Reset
        </a>
      @endif
    </form>

    {{-- Wrapper tabel (AJAX) --}}
    <div id="order-table">
      @include('admin.orders._table', ['orders' => $orders])
    </div>
  </div>

  {{-- Form bulk delete --}}
  <form id="bulk-delete-orders-form"
        action="{{ route('admin.orders.bulk-destroy') }}"
        method="POST"
        class="hidden">
    @csrf
    @method('DELETE')
  </form>

  <script>
    // AJAX pagination
    document.addEventListener('click', function (e) {
      const link = e.target.closest('#order-table .pagination a');
      if (!link) return;

      e.preventDefault();

      fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
          document.getElementById('order-table').innerHTML = html;
        })
        .catch(console.error);
    });

    // Checkbox select all
    document.addEventListener('change', function (e) {
      if (e.target && e.target.id === 'select-all-orders') {
        const checked = e.target.checked;
        document
          .querySelectorAll('#order-table .row-checkbox-order')
          .forEach(cb => cb.checked = checked);
      }
    });
  </script>
@endsection
