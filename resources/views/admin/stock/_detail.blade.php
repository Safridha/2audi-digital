<div class="space-y-4">

    <div>
        <h3 class="text-sm font-semibold text-gray-800">
            Detail Stok: {{ $bahan->nama_bahan }}
        </h3>
        <p class="text-xs text-gray-500">
            Satuan: {{ $bahan->satuan }} &middot;
            Minimal stok: {{ $bahan->minimal_stock }}
        </p>
    </div>

    {{-- Tabel batch FIFO --}}
    <div class="bg-white rounded-lg border border-gray-100 p-3">
        <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">
            Batch Stok (FIFO)
        </h4>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-2 py-1 text-left">Tanggal Masuk</th>
                        <th class="px-2 py-1 text-right">Qty Awal</th>
                        <th class="px-2 py-1 text-right">Qty Sisa</th>
                        <th class="px-2 py-1 text-right">Harga Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batches as $batch)
                        <tr>
                            <td class="px-2 py-1">
                                {{ $batch->tanggal_masuk?->format('d M Y') ?? $batch->created_at->format('d M Y') }}
                            </td>
                            <td class="px-2 py-1 text-right">
                                {{ $batch->qty_awal }} {{ $bahan->satuan }}
                            </td>
                            <td class="px-2 py-1 text-right">
                                {{ $batch->qty_sisa }} {{ $bahan->satuan }}
                            </td>
                            <td class="px-2 py-1 text-right">
                                Rp {{ number_format($batch->harga_satuan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-3 text-center text-gray-400">
                                Belum ada batch stok untuk bahan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Log pemakaian terakhir --}}
    <div class="bg-white rounded-lg border border-gray-100 p-3">
        <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">
            Riwayat Pemakaian Terakhir
        </h4>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-2 py-1 text-left">Tanggal</th>
                        <th class="px-2 py-1 text-right">Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usages as $usage)
                        <tr>
                            <td class="px-2 py-1">
                                {{ \Carbon\Carbon::parse($usage->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-2 py-1 text-right">
                                {{ $usage->qty }} {{ $bahan->satuan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-2 py-3 text-center text-gray-400">
                                Belum ada pemakaian yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
