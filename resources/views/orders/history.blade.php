@extends('layouts.main')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container mt-5 mb-5">
    <h3 class="fw-bold mb-4 text-center">Riwayat Pesanan</h3>

    @if($orders->isEmpty())
        <div class="alert alert-info">
            Kamu belum punya pesanan.
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($order->product)->name ?? 'Produk' }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td>
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
                                                $text  = 'Diproses';
                                            } elseif ($status === \App\Models\Order::STATUS_SELESAI) {
                                                $badge = 'success';
                                                $text  = 'Selesai';
                                            } elseif ($status === \App\Models\Order::STATUS_DIBATALKAN) {
                                                $badge = 'danger';
                                                $text  = 'Dibatalkan';
                                            }
                                        @endphp

                                        <span class="badge bg-{{ $badge }}">
                                            {{ $text }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('checkout.pay', $order->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Lihat / Bayar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
