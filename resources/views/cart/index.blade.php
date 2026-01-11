@extends('layouts.main')

@section('title', 'Keranjang')

@section('content')

<style>
/* ===============================
   RESPONSIVE CART TABLE (MOBILE)
   =============================== */
@media (max-width: 767px) {

    .cart-table thead {
        display: none;
    }

    .cart-table,
    .cart-table tbody,
    .cart-table tr,
    .cart-table td {
        display: block;
        width: 100%;
    }

    .cart-table tr {
        background: #fff;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem;
    }

    .cart-table td {
        border: none;
        padding: 0.35rem 0;
        text-align: left !important;
    }

    .cart-table td::before {
        content: attr(data-label);
        font-weight: 600;
        display: block;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 2px;
    }
}
</style>

<div class="container mt-3 mb-5">

    <h4 class="fw-bold black text-center mb-3" style="font-size: 22px;">
        Keranjang Saya
    </h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
        <p class="text-muted">Keranjang masih kosong.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Belanja Produk</a>
    @else

        <div class="table-responsive">
            <table class="table table-bordered align-middle bg-white cart-table">
                <thead class="table-light">
                    <tr class="text-center">
                        <th></th>
                        <th>Produk</th>
                        <th>Ukuran (m)</th>
                        <th>Catatan</th>
                        <th>Harga / m²</th>
                        <th>Total Produk</th>
                        <th>Total Finishing</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Desain</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($items as $item)
                    @php $designFile = $item->design_file ?? null; @endphp

                    <tr>
                        <td data-label="Pilih">
                            <input type="checkbox"
                                   form="checkout-form"
                                   name="items[]"
                                   value="{{ $item->id }}"
                                   class="item-checkbox"
                                   data-product-total="{{ $item->product_total }}"
                                   data-finishing-total="{{ $item->finishing_total }}"
                                   data-line-total="{{ $item->line_total }}"
                                   checked>
                        </td>

                        <td data-label="Produk">
                            <div class="fw-semibold">{{ $item->product->name }}</div>
                            <div class="text-muted small">ID Item: {{ $item->id }}</div>
                        </td>

                        <td data-label="Ukuran">
                            {{ number_format($item->length, 2) }} × {{ number_format($item->width, 2) }}
                        </td>

                        <td data-label="Catatan">
                            {{ $item->note ?? '-' }}
                        </td>

                        <td data-label="Harga / m²">
                            {{ number_format($item->harga_per_m2, 2) }}
                        </td>

                        <td data-label="Total Produk">
                            {{ number_format($item->product_total, 2) }}
                        </td>

                        <td data-label="Total Finishing">
                            {{ $item->finishing_total > 0 ? number_format($item->finishing_total, 2) : '-' }}
                        </td>

                        <td data-label="Qty">
                            {{ $item->quantity }}
                        </td>

                        <td data-label="Total" class="fw-semibold">
                            {{ number_format($item->line_total, 2) }}
                        </td>

                        <td data-label="Desain">
    @if($designFile)
        @if($item->is_design_image)
            <a href="{{ asset('storage/'.$designFile) }}" target="_blank" title="Lihat Desain">
                <img src="{{ asset('storage/'.$designFile) }}"
                     alt="Desain"
                     style="
                        max-width: 100%;
                        height: auto;
                        max-height: 80px;
                        object-fit: cover;
                        border-radius: 6px;
                        border: 1px solid #e5e7eb;
                     ">
            </a>

            <div class="mt-1">
                <a href="{{ asset('storage/'.$designFile) }}"
                   target="_blank"
                   download
                   class="btn btn-outline-primary btn-sm py-0 px-2">
                    Download
                </a>
            </div>
        @else
            <a href="{{ asset('storage/'.$designFile) }}"
               target="_blank"
               download
               class="btn btn-outline-primary btn-sm">
                Lihat / Download
            </a>
        @endif
    @else
        <span class="text-muted">-</span>
    @endif
</td>

                        <td data-label="Aksi">
                            <a href="{{ route('cart.edit', $item->id) }}"
                               class="btn btn-warning btn-sm w-100 mb-1">
                                Edit
                            </a>

                            <form action="{{ route('cart.remove', $item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <p class="small text-muted mt-2">
            * Finishing dihitung dengan tarif <strong>Rp 500 / m²</strong>.
        </p>

        <div class="d-flex gap-2">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                Lanjut Belanja
            </a>

            <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET">
                <button type="submit" class="btn btn-primary">
                    Checkout Terpilih
                </button>
            </form>
        </div>

    @endif
</div>

{{-- ❗ JS TIDAK DIUBAH --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check_all');

    function getItemCheckboxes() {
        return document.querySelectorAll('.item-checkbox');
    }

    function recalcTotals() {
        const itemCheckboxes = getItemCheckboxes();
        let productTotal = 0, finishingTotal = 0, grandTotal = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                productTotal += parseFloat(cb.dataset.productTotal || 0);
                finishingTotal += parseFloat(cb.dataset.finishingTotal || 0);
                grandTotal += parseFloat(cb.dataset.lineTotal || 0);
            }
        });
    }

    recalcTotals();
});
</script>
@endsection
