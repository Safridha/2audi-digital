@extends('layouts.admin')
@section('title', 'Ubah Status Pesanan')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        Ubah Status Pesanan
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Perbarui status pesanan sesuai proses pengerjaan.
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

    @php
        $printingTotalDb  = (int) $order->items->sum('printing_cost');
        $finishingTotalDb = (int) $order->items->sum('finishing_cost');
        $itemsTotalDb     = (int) $order->items->sum('line_total');

        $printingTotalCalc  = 0;
        $finishingTotalCalc = 0;

        foreach ($order->items as $it) {
            $qty  = (int) ($it->quantity ?? 0);

            $area = $it->area;
            if (is_null($area)) {
                $len  = (float) ($it->length ?? 0);
                $wid  = (float) ($it->width ?? 0);
                $area = $len * $wid;
            } else {
                $area = (float) $area;
            }

            $pricePerM2  = (int) ($it->product_price ?? 0);
            $finishPerM2 = (int) ($it->finishing_rate ?? 0);

            $printingTotalCalc += (int) round($pricePerM2 * $area * $qty);

            if (($it->finishing ?? 'tanpa') !== 'tanpa') {
                $finishingTotalCalc += (int) round($finishPerM2 * $area * $qty);
            }
        }

        $printingTotal  = $printingTotalDb  > 0 ? $printingTotalDb  : $printingTotalCalc;
        $finishingTotal = $finishingTotalDb > 0 ? $finishingTotalDb : $finishingTotalCalc;

        $totalProduk = $itemsTotalDb > 0
            ? $itemsTotalDb
            : (($printingTotal + $finishingTotal) > 0
                ? ($printingTotal + $finishingTotal)
                : (int) ($order->total_payment ?? 0));
    @endphp

    <div class="bg-white rounded-lg shadow border border-gray-100 p-6">

        {{-- Info Pesanan --}}
        <div class="mb-5">
            <h2 class="font-semibold text-lg text-gray-800">
                Detail Produk ({{ $order->items->count() }} item)
            </h2>

            <p class="text-sm text-gray-600 mt-1 leading-6">
                <span class="font-medium">Pemesan:</span>
                {{ $order->customer_name }} ({{ $order->customer_email }})<br>
                <span class="font-medium">Tanggal:</span>
                {{ $order->created_at?->format('d-m-Y H:i') ?? '-' }}
            </p>

            <div class="mt-4 space-y-3">
                @foreach($order->items as $it)
                    @php
                        $len  = (float) ($it->length ?? 0);
                        $wid  = (float) ($it->width ?? 0);
                        $area = is_null($it->area) ? ($len * $wid) : (float) $it->area;
                    @endphp

                    <div class="rounded-md border border-gray-200 p-3 text-sm text-gray-700">
                        <div class="font-semibold text-gray-800">
                            {{ $it->product->name ?? 'Produk' }}
                        </div>

                        <div class="mt-1 space-y-1">
                            <div>
                                <span class="font-medium">Ukuran:</span>
                                {{ $len }} m x {{ $wid }} m
                                @if($area > 0)
                                    ({{ number_format($area, 2, ',', '.') }} m²)
                                @endif
                            </div>

                            <div>
                                <span class="font-medium">Jumlah:</span>
                                {{ $it->quantity ?? '-' }}
                            </div>

                            <div>
                                <span class="font-medium">Finishing:</span>
                                {{ ($it->finishing ?? 'tanpa') === 'tanpa' ? 'Tanpa Finishing' : ucfirst($it->finishing) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-sm text-gray-700 space-y-1">
                <div>
                    <span class="font-medium">Biaya Cetak:</span>
                    Rp {{ number_format($printingTotal, 0, ',', '.') }}
                </div>

                <div>
                    <span class="font-medium">Total Produk:</span>
                    Rp {{ number_format($totalProduk, 0, ',', '.') }}
                </div>

                <div>
                    <span class="font-medium">Ongkir:</span>
                    Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                </div>

                <div>
                    <span class="font-medium">Grand Total:</span>
                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                </div>

                @if($order->note)
                    <div class="mt-2">
                        <span class="font-medium">Catatan:</span>
                        {{ $order->note }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Form Ubah Status --}}
        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Status Pesanan
                </label>

                <select name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="menunggu_pembayaran" @selected($order->status === 'menunggu_pembayaran')>
                        Menunggu Pembayaran
                    </option>
                    <option value="diproses" @selected($order->status === 'diproses')>
                        Pesanan Diproses
                    </option>
                    <option value="selesai" @selected($order->status === 'selesai')>
                        Pesanan Selesai
                    </option>
                    <option value="diantar_diambil" @selected($order->status === 'diantar_diambil')>
                        Siap Diantar / Diambil
                    </option>
                    <option value="dibatalkan" @selected($order->status === 'dibatalkan')>
                        Pesanan Dibatalkan
                    </option>
                </select>

                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600
                               text-white text-sm font-medium hover:bg-indigo-700">
                    Simpan
                </button>

                <a href="{{ route('admin.orders.index') }}"
                   class="text-gray-600 hover:underline text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
