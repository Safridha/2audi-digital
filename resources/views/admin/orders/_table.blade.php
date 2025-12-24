@php use Illuminate\Support\Str; @endphp

<div class="overflow-x-auto">
  <table class="w-full border-collapse border border-gray-300 text-sm">
    <thead class="bg-indigo-100 text-left">
      <tr>
        <th class="border p-2 w-10 text-center">
          @if($orders->count())
            <input type="checkbox" id="select-all-orders">
          @endif
        </th>
        <th class="border p-2 w-32 text-center">Tanggal dan Waktu</th>
        <th class="border p-2">Produk</th>
        <th class="border p-2">Detail Cetakan</th>
        <th class="border p-2">Pemesan</th>
        <th class="border p-2">Pengiriman & Pembayaran</th>
        <th class="border p-2 w-40 text-right">Total</th>
        <th class="border p-2 w-40 text-center">Desain</th>
        <th class="border p-2 w-40 text-center">Status</th>
        <th class="border p-2 w-32 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody>
    @forelse($orders as $order)

      <tr class="hover:bg-gray-50">

        {{-- checkbox per baris (ikut form bulk) --}}
        <td class="border p-2 text-center align-top">
          <input type="checkbox"
                 form="bulk-delete-orders-form"
                 name="order_ids[]"
                 value="{{ $order->id }}"
                 class="row-checkbox-order">
        </td>

        {{-- Tanggal dan Waktu --}}
        <td class="border p-2 text-center align-top leading-tight">
          <div class="font-medium">
            {{ $order->created_at?->format('d-m-Y') ?? '-' }}
          </div>
          <div class="text-xs text-gray-500">
            {{ $order->created_at?->format('H:i') }}
          </div>
        </td>

        {{-- Produk (SEMUA item) --}}
        <td class="border p-2 align-top text-xs">
          @forelse($order->items as $it)
            <div class="mb-2 pb-2 border-b last:border-b-0">
              <div class="font-semibold text-sm">
                {{ $it->product?->name ?? '-' }}
              </div>
              <div class="text-gray-500">
                ID Produk: {{ $it->product?->id ?? ($it->product_id ?? '-') }}
              </div>
            </div>
          @empty
            <div class="font-semibold">-</div>
            <div class="text-xs text-gray-500">ID Produk: -</div>
          @endforelse
        </td>

        {{-- Detail Cetakan (SEMUA item) --}}
        <td class="border p-2 align-top text-xs">
          @forelse($order->items as $it)
            <div class="mb-2 pb-2 border-b last:border-b-0">
              <div>Panjang: {{ $it->length ?? '-' }} m</div>
              <div>Lebar: {{ $it->width ?? '-' }} m</div>
              <div>Jumlah: {{ $it->quantity ?? '-' }}</div>
              <div>
                Finishing:
                {{ ($it->finishing ?? 'tanpa') === 'tanpa' ? 'Tanpa Finishing' : ucfirst($it->finishing) }}
              </div>
            </div>
          @empty
            <div>-</div>
          @endforelse

          @if($order->note)
            <div class="mt-2 text-gray-600">
              <span class="font-semibold">Catatan pesanan:</span>
              {{ Str::limit($order->note, 120) }}
            </div>
          @endif
        </td>

        {{-- Pemesan --}}
        <td class="border p-2 align-top text-xs">
          <div class="font-semibold text-sm">
            {{ $order->customer_name }}
          </div>

          <div class="text-gray-600">
            {{ $order->customer_email }}
          </div>

          @if($order->customer_phone ?? null)
            <div class="mt-1 text-gray-800">
              <span class="font-semibold">No HP:</span>
              {{ $order->customer_phone }}
            </div>
          @endif

          <div class="mt-1 text-gray-700 leading-snug">
            <div>
              <span class="font-semibold">Alamat:</span>
              {{ $order->address }}
            </div>
            <div>
              Kec. {{ $order->district }}, {{ $order->city }}
            </div>
            <div>
              <span class="font-semibold">Kode Pos:</span>
              {{ $order->postal_code }}
            </div>
          </div>
        </td>

        {{-- Pengiriman & Pembayaran --}}
        <td class="border p-2 align-top text-xs">
          <div>
            <span class="font-semibold">Pengambilan:</span>
            {{ $order->shipping_option === 'ambil' ? 'Ambil di Toko' : 'Dikirim ke Alamat' }}
          </div>

          @if($order->shipping_option === 'kirim')
            <div>
              <span class="font-semibold">Kurir:</span>
              {{ strtoupper($order->shipping_courier ?? '-') }}
              {{ $order->shipping_service }}
              @if($order->shipping_etd)
                ({{ $order->shipping_etd }} hari)
              @endif
            </div>
          @endif

          <div class="mt-1">
            <span class="font-semibold">Pembayaran:</span>
            @switch($order->payment_method)
              @case('transfer')   Transfer Bank @break
              @case('ewallet')    E-Wallet @break
              @case('tunai_toko') Tunai di Toko @break
              @default            {{ $order->payment_method ?? '-' }}
            @endswitch
          </div>
        </td>

        {{-- Total --}}
        <td class="border p-2 text-right align-top text-xs">
          <div>
            <span class="font-semibold">Produk:</span>
            Rp {{ number_format($order->total_payment, 0, ',', '.') }}
          </div>

          <div>
            <span class="font-semibold">Ongkir:</span>
            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
          </div>

          <hr class="my-1">
          <div class="font-bold text-blue-900">
            Total: Rp {{ number_format($order->grand_total, 0, ',', '.') }}
          </div>
        </td>

        {{-- Desain: hanya tombol download saja --}}
        <td class="border p-2 text-center align-top text-xs">
          @php $hasDesign = false; @endphp

          @foreach($order->items as $item)
            @if($item->design_file)
              @php $hasDesign = true; @endphp
              <div class="mb-1">
                <a href="{{ asset('storage/'.$item->design_file) }}"
                   class="inline-block px-2 py-1 border border-blue-500 text-blue-600 rounded hover:bg-blue-50"
                   target="_blank"
                   download>
                  Download Desain
                </a>
              </div>
            @endif
          @endforeach

          @unless($hasDesign)
            <span class="text-gray-400">Tidak ada</span>
          @endunless
        </td>

        {{-- Status --}}
        <td class="border p-2 text-center align-top">
          @php
            $label = [
              'menunggu_pembayaran' => 'Menunggu pembayaran',
              'diproses'            => 'Diproses',
              'selesai'             => 'Selesai',
              'diantar_diambil'     => 'Diantar / Diambil',
              'dibatalkan'          => 'Dibatalkan',
              'pembayaran_berhasil' => 'Pembayaran Berhasil',
              'pembayaran_gagal'    => 'Pembayaran Gagal',
            ][$order->status] ?? $order->status;
          @endphp

          <span class="px-3 py-1 rounded-full text-xs
            @if($order->status === 'menunggu_pembayaran')
                bg-yellow-100 text-yellow-800
            @elseif($order->status === 'diproses')
                bg-blue-100 text-blue-800
            @elseif($order->status === 'selesai')
                bg-green-100 text-green-800
            @elseif($order->status === 'diantar_diambil')
                bg-purple-100 text-purple-800
            @elseif($order->status === 'dibatalkan')
                bg-red-100 text-red-800
            @elseif($order->status === 'pembayaran_berhasil')
                bg-emerald-100 text-emerald-800
            @elseif($order->status === 'pembayaran_gagal')
                bg-red-100 text-red-800
            @else
                bg-gray-100 text-gray-700
            @endif">
            {{ $label }}
          </span>
        </td>

        {{-- Aksi --}}
        <td class="border p-2 text-center align-top text-sm">
          <a href="{{ route('admin.orders.edit', $order->id) }}"
             class="text-blue-600 hover:underline mr-1">
            Edit
          </a>
          |
          <form action="{{ route('admin.orders.destroy', $order->id) }}"
                method="POST"
                class="inline"
                onsubmit="return confirm('Yakin hapus pesanan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline ml-1">
              Hapus
            </button>
          </form>
        </td>

      </tr>
    @empty
      <tr>
        <td colspan="10" class="border p-4 text-center text-gray-500">
          Belum ada pesanan.
        </td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

@if(method_exists($orders, 'links'))
  <div class="mt-4">
    {{ $orders->links() }}
  </div>
@endif
