@extends('layouts.admin')
@section('title', 'Kelola Stok Bahan')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="p-3 rounded bg-emerald-50 border border-emerald-200 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 rounded bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-1">
            Manajemen Stok Bahan (FIFO + Martingale)
        </h1>
        <p class="text-sm text-gray-500">
            Pantau stok bahan, target stok, dan rekomendasi pembelian.
        </p>
    </div>

    {{-- RINGKASAN CARD ATAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Total jenis bahan --}}
        <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Jenis Bahan</p>
            <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $totalBahan }}</p>
        </div>

        {{-- Bahan perlu restock --}}
        <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Bahan Perlu Restock</p>
            <p class="mt-2 text-2xl font-semibold {{ $bahanPerluRestock > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $bahanPerluRestock }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Berdasarkan target stok (Martingale) dan minimal stok di master bahan.
            </p>
        </div>
    </div>

    {{-- TABEL RINGKASAN FIFO + MARTINGALE --}}
    <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Ringkasan Stok 7 Hari Terakhir
                </h2>
                <p class="text-xs text-gray-500">
                    Periode: {{ $ringkasan[0]['periode_start'] ?? '-' }}
                    s/d {{ $ringkasan[0]['periode_end'] ?? '-' }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-indigo-50 text-gray-700">
                        <th class="px-3 py-2 text-left">#</th>
                        <th class="px-3 py-2 text-left">Nama Bahan</th>
                        <th class="px-3 py-2 text-right">Stok Sekarang</th>
                        <th class="px-3 py-2 text-right">Minimal Stok</th>
                        <th class="px-3 py-2 text-right">Pemakaian 7 Hari</th>
                        <th class="px-3 py-2 text-right">Target Stok</th>
                        <th class="px-3 py-2 text-right">Rekomendasi</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ringkasan as $i => $row)
                        @php
                            $bahan  = $row['bahan'];
                            $stok   = $row['stok_sekarang'];
                            $min    = $bahan->minimal_stock ?? 0;
                            $need   = $row['rekomendasi'];
                            $statusPerluRestock = $need > 0 || ($min > 0 && $stok < $min);
                        @endphp
                        <tr>
                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium text-gray-800">{{ $bahan->nama_bahan }}</div>
                                <div class="text-xs text-gray-500">Satuan: {{ $bahan->satuan }}</div>
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ $stok }} <span class="text-xs text-gray-400">{{ $bahan->satuan }}</span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ $min }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ $row['pemakaian'] }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ $row['target_stok'] }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($need > 0)
                                    {{ $need }} <span class="text-xs text-gray-400">{{ $bahan->satuan }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($statusPerluRestock)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Perlu Restock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button
                                    type="button"
                                    class="btn-detail-stok inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 hover:bg-gray-200 text-gray-700"
                                    data-url="{{ route('admin.stock.detail', $bahan->id) }}"
                                >
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                                Belum ada data stok. Tambahkan pembelian bahan terlebih dahulu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PANEL DETAIL STOK PER BAHAN (FIFO + USAGE) --}}
    <div id="detail-stok-panel" class="hidden">
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mt-4">
            <div id="detail-stok-content" class="text-sm text-gray-600">
                {{-- isi akan di-load via AJAX --}}
            </div>
        </div>
    </div>

    {{-- FORM AKSI: PEMBELIAN & PEMAKAIAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- FORM TAMBAH PEMBELIAN --}}
        <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Tambah Pembelian Bahan</h3>

            <form action="{{ route('admin.stock.pembelian') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bahan</label>
                    <select name="bahan_id" class="w-full border rounded px-3 py-2 text-sm" required>
                        <option value="" disabled selected>Pilih bahan</option>
                        @foreach(\App\Models\Bahan::orderBy('nama_bahan')->get() as $bahan)
                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                    <input type="number" step="0.01" name="qty"
                           class="w-full border rounded px-3 py-2 text-sm"
                           placeholder="Misal: 100" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan</label>
                    <input type="number" step="0.01" name="harga"
                           class="w-full border rounded px-3 py-2 text-sm"
                           placeholder="Misal: 20000" required>
                </div>

                <div class="pt-2 text-right">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                        Simpan Pembelian
                    </button>
                </div>
            </form>
        </div>

        {{-- FORM CATAT PEMAKAIAN (FIFO) --}}
        <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Catat Pemakaian Bahan (FIFO)</h3>

            <form action="{{ route('admin.stock.pemakaian') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bahan</label>
                    <select name="bahan_id" class="w-full border rounded px-3 py-2 text-sm" required>
                        <option value="" disabled selected>Pilih bahan</option>
                        @foreach(\App\Models\Bahan::orderBy('nama_bahan')->get() as $bahan)
                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty Dipakai</label>
                    <input type="number" step="0.01" name="qty"
                           class="w-full border rounded px-3 py-2 text-sm"
                           placeholder="Misal: 50" required>
                </div>

                <div class="pt-2 text-right">
                    <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                        Simpan Pemakaian
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- JS: Load detail stok via AJAX --}}
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-detail-stok');
    if (!btn) return;

    const url   = btn.dataset.url;
    const panel = document.getElementById('detail-stok-panel');
    const content = document.getElementById('detail-stok-content');

    content.innerHTML = 'Memuat detail stok...';
    panel.classList.remove('hidden');

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.text())
    .then(html => {
        content.innerHTML = html;
    })
    .catch(err => {
        console.error(err);
        content.innerHTML = '<p class="text-red-600 text-sm">Gagal memuat detail stok.</p>';
    });
});
</script>
@endsection
