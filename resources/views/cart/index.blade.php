@extends('layouts.main')

@section('title', 'Keranjang')

@section('content')
<div class="container mt-3 mb-5">

    {{-- ✅ judul disamakan seperti halaman katalog --}}
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
            <table class="table table-bordered align-middle bg-white">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width:40px;">
                            <input type="checkbox" id="check_all">
                        </th>
                        <th class="text-start" style="min-width:220px;">Produk</th>
                        <th style="width:140px;">Ukuran (m)</th>
                        <th style="width:160px;">Catatan</th>
                        <th class="text-end" style="width:120px;">Harga / m²</th>
                        <th class="text-end" style="width:140px;">Total Produk</th>
                        <th class="text-end" style="width:140px;">Total Finishing</th>
                        <th style="width:70px;">Qty</th>
                        <th class="text-end" style="width:140px;">Total</th>
                        <th style="width:140px;">Desain</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($items as $item)
                    @php
                        $designFile = $item->design_file ?? null;
                    @endphp

                    <tr>
                        <td class="text-center">
                            {{-- ✅ checkbox ini dikirim ke FORM CHECKOUT yang terpisah --}}
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

                        {{-- Produk --}}
                        <td class="text-start">
                            <div class="fw-semibold">{{ $item->product->name }}</div>
                            <div class="text-muted small">ID Item: {{ $item->id }}</div>
                        </td>

                        {{-- Ukuran --}}
                        <td class="text-center">
                            {{ number_format($item->length, 2) }} × {{ number_format($item->width, 2) }}
                        </td>

                        {{-- Catatan --}}
                        <td class="text-start">
                            @if($item->note)
                                <span class="small">{{ $item->note }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>

                        {{-- Harga/m2 --}}
                        <td class="text-end">
                            {{ number_format($item->harga_per_m2, 2) }}
                        </td>

                        {{-- Total Produk --}}
                        <td class="text-end">
                            {{ number_format($item->product_total, 2) }}
                        </td>

                        {{-- Total Finishing --}}
                        <td class="text-end">
                            @if($item->finishing_total > 0)
                                {{ number_format($item->finishing_total, 2) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- Qty --}}
                        <td class="text-center">
                            {{ $item->quantity }}
                        </td>

                        {{-- Total --}}
                        <td class="text-end fw-semibold">
                            {{ number_format($item->line_total, 2) }}
                        </td>

                        {{-- Desain --}}
                        <td class="text-center">
                            @if($designFile)
                                @if($item->is_design_image)
                                    <a href="{{ asset('storage/'.$designFile) }}" target="_blank" title="Lihat Desain">
                                        <img src="{{ asset('storage/'.$designFile) }}"
                                             alt="Desain"
                                             style="height:45px; width:70px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
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
                                       class="btn btn-outline-primary btn-sm py-0 px-2">
                                        Lihat / Download
                                    </a>
                                @endif
                            @else
                                <span class="text-muted small">Tidak ada</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column gap-2" style="min-width: 90px;">
                                <a href="{{ route('cart.edit', $item->id) }}"
                                   class="btn btn-warning btn-sm w-100">
                                    Edit
                                </a>

                                {{-- ✅ form delete berdiri sendiri (tidak nested di form checkout) --}}
                                <form action="{{ route('cart.remove', $item->id) }}"
                                      method="POST"
                                      class="w-100"
                                      onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach

                {{-- TOTAL --}}
                <tr class="table-light">
                    <td colspan="5" class="text-end fw-bold">Total (terpilih)</td>

                    <td class="text-end fw-bold">
                        <span id="selected_product_total">{{ number_format($grandProductTotal, 2) }}</span>
                    </td>

                    <td class="text-end fw-bold">
                        <span id="selected_finishing_total">{{ number_format($grandFinishingTotal, 2) }}</span>
                    </td>

                    <td class="text-center fw-bold">—</td>

                    <td class="text-end fw-bold">
                        <span id="selected_grand_total">{{ number_format($grandTotal, 2) }}</span>
                    </td>

                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>

        <p class="small text-muted mt-2">
            * Finishing dihitung dengan tarif <strong>Rp 500 / m²</strong>. Jika tidak memilih finishing, kolom <strong>Total Finishing</strong> akan tampil <strong>-</strong>.
        </p>

        <div class="d-flex gap-2">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                Lanjut Belanja
            </a>

            {{-- ✅ FORM CHECKOUT TERPISAH --}}
            <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET" class="m-0">
                <button type="submit" class="btn btn-primary">
                    Checkout Terpilih
                </button>
            </form>
        </div>

    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check_all');

    function getItemCheckboxes() {
        return document.querySelectorAll('.item-checkbox');
    }

    function recalcTotals() {
        const itemCheckboxes = getItemCheckboxes();

        let productTotal   = 0;
        let finishingTotal = 0;
        let grandTotal     = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                productTotal   += parseFloat(cb.dataset.productTotal   || 0);
                finishingTotal += parseFloat(cb.dataset.finishingTotal || 0);
                grandTotal     += parseFloat(cb.dataset.lineTotal      || 0);
            }
        });

        const formatter = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.getElementById('selected_product_total').innerText   = formatter.format(productTotal);
        document.getElementById('selected_finishing_total').innerText = formatter.format(finishingTotal);
        document.getElementById('selected_grand_total').innerText     = formatter.format(grandTotal);
    }

    // pilih semua
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            getItemCheckboxes().forEach(cb => cb.checked = checkAll.checked);
            recalcTotals();
        });
    }

    // event change tiap checkbox
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('item-checkbox')) return;

        const itemCheckboxes = getItemCheckboxes();
        if (checkAll) {
            checkAll.checked = Array.from(itemCheckboxes).every(x => x.checked);
        }
        recalcTotals();
    });

    recalcTotals();

    // ✅ optional: cegah checkout kalau tidak ada yang dicentang
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            const anyChecked = Array.from(getItemCheckboxes()).some(x => x.checked);
            if (!anyChecked) {
                e.preventDefault();
                alert('Pilih minimal 1 item untuk checkout.');
            }
        });
    }
});
</script>
@endsection
