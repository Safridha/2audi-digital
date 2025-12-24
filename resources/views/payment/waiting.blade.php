@extends('layouts.main')

@section('title', 'Pesanan #' . $order->id)

@section('content')
{{-- Fix jarak konten ke header/navbar (tanpa ngubah logic) --}}
<style>
    /* Kalau navbar kamu fixed / sticky, biasanya konten jadi kejauhan.
       Ini bikin konten naik tapi tetap aman dan rapi. */
    .order-pay-page{
        margin-top: 12px !important;   /* naikkan konten */
        padding-top: 0 !important;
    }

    /* Optional: kecilin jarak judul ke atas */
    .order-pay-page .page-header{
        margin-bottom: 14px !important;
    }
</style>

<div class="container mb-5 order-pay-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 page-header">
        <div>
            <h3 class="fw-bold mb-1">
                Pesanan #{{ $order->id }}
            </h3>
        </div>

        @php
            $status = $order->status;
            $badge  = 'secondary';
            $text   = ucfirst(str_replace('_', ' ', $status));

            if ($status === \App\Models\Order::STATUS_MENUNGGU) {
                $badge = 'warning';
                $text  = 'Menunggu Pembayaran';
            } elseif ($status === \App\Models\Order::STATUS_DIBAYAR) {
                $badge = 'info';
                $text  = 'Pembayaran Berhasil';
            } elseif ($status === \App\Models\Order::STATUS_DIPROSES) {
                $badge = 'primary';
                $text  = 'Pesanan Diproses';
            } elseif ($status === \App\Models\Order::STATUS_SELESAI) {
                $badge = 'success';
                $text  = 'Pesanan Selesai';
            } elseif ($status === (\App\Models\Order::STATUS_DIBATALKAN ?? null)) {
                $badge = 'danger';
                $text  = 'Pesanan Dibatalkan';
            }

            // RATE finishing (samakan dengan yang kamu pakai di checkout controller)
            $FINISHING_RATE = 500;

            // hitung total cetak & finishing dari item
            // Prioritas: pakai kolom kalau ada -> kalau 0 baru hitung manual
            $printingTotal  = (int) $order->items->sum(fn($i) => (int) ($i->printing_cost ?? 0));
            $finishingTotal = (int) $order->items->sum(fn($i) => (int) ($i->finishing_cost ?? 0));

            // fallback cetak jika printing_cost belum tersimpan
            if ($printingTotal === 0) {
                $printingTotal = (int) $order->items->sum(function($i){
                    $area = $i->area ?? (($i->length ?? 0) * ($i->width ?? 0));
                    $qty  = (int) ($i->quantity ?? 0);
                    $price= (int) ($i->product_price ?? 0);
                    return (int) ($area * $qty * $price);
                });
            }

            // fallback finishing yang BENAR (jangan pakai finishing_rate karena kamu gak simpan itu)
            if ($finishingTotal === 0) {
                $finishingTotal = (int) $order->items->sum(function($i) use ($FINISHING_RATE){
                    $area = $i->area ?? (($i->length ?? 0) * ($i->width ?? 0));
                    $qty  = (int) ($i->quantity ?? 0);
                    $isFinishing = (($i->finishing ?? 'tanpa') !== 'tanpa');
                    return $isFinishing ? (int) ($FINISHING_RATE * $area * $qty) : 0;
                });
            }

            $snapToken = $order->snap_token;
        @endphp
    </div>

    <div class="row g-4">

        {{-- KIRI --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <h5 class="fw-semibold mb-3">Ringkasan Pesanan</h5>

                    <div class="mb-3">
                        <p class="mb-1 fw-semibold">Nama Pemesan</p>
                        <p class="mb-0">{{ $order->customer_name }}</p>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Ukuran</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $area = $item->area ?? (($item->length ?? 0) * ($item->width ?? 0));
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ optional($item->product)->name ?? 'Produk' }}
                                            </div>
                                            <small class="text-muted d-block">
                                                Finishing: {{ ucfirst($item->finishing) }}
                                            </small>
                                            @if($order->note)
                                                <small class="text-muted d-block">
                                                    Catatan: {{ $order->note }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->length, 2, ',', '.') }} m
                                            × {{ number_format($item->width, 2, ',', '.') }} m
                                            <br>
                                            <small class="text-muted">
                                                Area: {{ number_format($area, 2, ',', '.') }} m²
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($item->line_total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <h6 class="fw-semibold mb-2">Alamat Pengiriman</h6>
                    <p class="mb-1">
                        {{ $order->address }}<br>
                        {{ $order->district }}, {{ $order->city }} - {{ $order->postal_code }}
                    </p>
                    <p class="mb-3">
                        <small class="text-muted">
                            Telp: {{ $order->customer_phone }}
                        </small>
                    </p>

                    <h6 class="fw-semibold mb-2">Metode Pengiriman</h6>
                    <p class="mb-1">
                        @if($order->shipping_option === 'ambil')
                            Ambil di Toko
                        @else
                            Dikirim ke Alamat
                        @endif
                    </p>

                    @if($order->shipping_option === 'kirim')
                        <p class="mb-0">
                            <small class="text-muted">
                                Kurir:
                                {{ strtoupper($order->shipping_courier ?? '-') }}
                                {{ $order->shipping_service ? ' - ' . $order->shipping_service : '' }}
                                @if($order->shipping_etd)
                                    (ETD: {{ $order->shipping_etd }} hari)
                                @endif
                            </small>
                        </p>
                    @endif

                </div>
            </div>
        </div>

        {{-- KANAN --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Detail Pembayaran</h5>
                        <span class="badge bg-{{ $badge }} small">
                            {{ $text }}
                        </span>
                    </div>

                    <div class="small text-muted mb-2">
                        No. Pesanan: #{{ $order->id }}<br>
                        Tanggal: {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}
                    </div>

                    <hr class="mt-2 mb-3">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Biaya Cetak</span>
                        <span>Rp {{ number_format($printingTotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Biaya Finishing</span>
                        <span>Rp {{ number_format($finishingTotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format((int) $order->shipping_cost, 0, ',', '.') }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3 fw-bold">
                        <span>Total Pembayaran</span>
                        <span>Rp {{ number_format((int) $order->grand_total, 0, ',', '.') }}</span>
                    </div>

                    <p class="small text-muted mb-3">
                        Metode pembayaran: {{ strtoupper($order->payment_method ?? 'MIDTRANS') }}
                    </p>

                    @if($order->status === \App\Models\Order::STATUS_MENUNGGU)
                        <button id="pay-button" class="btn btn-primary w-100 mb-2" {{ empty($snapToken) ? 'disabled' : '' }}>
                            Bayar Sekarang
                        </button>

                        @if(empty($snapToken))
                            <p class="small text-danger mb-0">
                                Snap token belum tersedia. Pastikan proses generate token midtrans sudah jalan.
                            </p>
                        @else
                            <p class="small text-muted mb-0">
                                Anda akan dialihkan ke Midtrans untuk menyelesaikan pembayaran.
                            </p>
                        @endif

                    @elseif($order->status === \App\Models\Order::STATUS_DIBAYAR || $order->status === \App\Models\Order::STATUS_DIPROSES)
                        <p class="small mb-0">
                            <strong>Pembayaran Berhasil - Pesanan Diproses.</strong><br>
                            Silakan cek halaman ini kembali untuk melihat update status.
                        </p>

                    @elseif($order->status === \App\Models\Order::STATUS_SELESAI)
                        <p class="small mb-0">
                            <strong>Pesanan Selesai.</strong> Terima kasih sudah berbelanja di 2 Audi Digital Printing.
                        </p>

                    @else
                        <p class="small text-danger mb-0">
                            Pembayaran gagal / dibatalkan. Silakan buat pesanan baru jika ingin mencoba lagi.
                        </p>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

@if($order->status === \App\Models\Order::STATUS_MENUNGGU)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
      const btn = document.getElementById('pay-button');
      if (btn) {
        btn.addEventListener('click', function () {
          const token = @json($snapToken);
          if (!token) return;

          snap.pay(token, {
            onSuccess: function () { window.location.reload(); },
            onPending: function () { window.location.reload(); },
            onError: function () { alert('Terjadi kesalahan saat memproses pembayaran.'); },
            onClose: function () { alert('Popup pembayaran ditutup sebelum selesai.'); }
          });
        });
      }
    </script>
@endif
@endsection
