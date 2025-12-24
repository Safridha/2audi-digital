@extends('layouts.admin')
@section('title','Kelola Pengguna')

@section('content')
  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800 border border-green-200">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800 border border-red-200">
      {{ session('error') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800 border border-red-200">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">

    {{-- Judul --}}
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-3">
      Daftar Pengguna
    </h2>

    {{-- Search + Tombol --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">

      {{-- 🔍 Search --}}
      <form method="GET"
            action="{{ route('admin.users.index') }}"
            class="flex items-center gap-2">
        <input type="text"
               name="search"
               placeholder="Cari nama / email / role..."
               value="{{ $search ?? '' }}"
               class="px-3 py-2 border rounded w-64 focus:outline-none focus:ring focus:ring-indigo-300">

        <button type="submit"
                class="px-3 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
          Cari
        </button>

        @if(!empty($search))
          <a href="{{ route('admin.users.index') }}"
             class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
            Reset
          </a>
        @endif
      </form>

      {{-- Tombol Tambah + Hapus Terpilih --}}
      <div class="flex items-center gap-2">

        {{-- Tombol Tambah --}}
        <a href="{{ route('admin.users.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
          Tambah
        </a>

        {{-- Tombol Hapus Terpilih --}}
        @if($users->count())
          <button type="submit"
                  form="bulk-delete-users-form"
                  class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700"
                  onclick="return confirm('Yakin hapus pengguna yang dipilih?')">
            Hapus Terpilih
          </button>
        @endif
      </div>
    </div>

    {{-- TABEL (tanpa form membungkus) --}}
    <div id="user-table">
      @include('admin.users._table', ['users'=>$users])
    </div>
  </div>

  {{-- FORM BULK DELETE TERPISAH (tidak nested) --}}
  <form id="bulk-delete-users-form"
        action="{{ route('admin.users.bulk-destroy') }}"
        method="POST"
        class="hidden">
    @csrf
    @method('DELETE')
    {{-- checkbox dari _table ikut lewat form="bulk-delete-users-form" --}}
  </form>

  {{-- AJAX pagination + checkbox select-all --}}
  <script>
    // AJAX pagination
    document.addEventListener('click', function(e){
      const link = e.target.closest('#user-table .pagination a');
      if(!link) return;

      e.preventDefault();

      fetch(link.href, { headers: { 'X-Requested-With':'XMLHttpRequest' }})
        .then(r => r.text())
        .then(html => {
          const wrapper = document.getElementById('user-table');
          wrapper.innerHTML = html;
        })
        .catch(console.error);
    });

    // Checkbox select-all (event delegation biar tetap jalan setelah AJAX)
    document.addEventListener('change', function (e) {
      if (e.target && e.target.id === 'select-all-users') {
        const checked = e.target.checked;
        document
          .querySelectorAll('.row-checkbox-user')
          .forEach(cb => {
            if (!cb.disabled) {
              cb.checked = checked;
            }
          });
      }
    });
  </script>
@endsection
