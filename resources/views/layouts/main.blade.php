<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '2 Audi Digital')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { background-color: #f8f8f8; }
        header { background-color: #1e3a8a; color: white; }
        footer { background-color: #1e40af; color: white; padding: 2rem 0; }

        .whatsapp-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.2s;
            z-index: 50;
        }
        .whatsapp-bubble:hover { transform: scale(1.1); }
        .whatsapp-bubble i { color: white; font-size: 28px; }

        /* WA LABEL + PANAH */
        .wa-float-wrapper {
            position: fixed;
            bottom: 110px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .wa-label {
            position: relative;
            background-color: #25D366;
            color: #fff;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            white-space: nowrap;
            line-height: 1;
        }

        .wa-label::after {
            content: "";
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px 6px 0 6px;
            border-style: solid;
            border-color: #25D366 transparent transparent transparent;
        }

        .tooltip-inner {
            background-color: #1e3a8a; 
            color: #ffffff;
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18);
        }

        /* Panah tooltip */
        .tooltip.bs-tooltip-top .tooltip-arrow::before { border-top-color: #1e3a8a; }
        .tooltip.bs-tooltip-bottom .tooltip-arrow::before { border-bottom-color: #1e3a8a; }
        .tooltip.bs-tooltip-start .tooltip-arrow::before { border-left-color: #1e3a8a; }
        .tooltip.bs-tooltip-end .tooltip-arrow::before { border-right-color: #1e3a8a; }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col" x-data="{ openCart: false }">

    {{-- CART COUNT UNTUK BADGE (JUMLAH ITEM/BARIS, BUKAN TOTAL QTY) --}}
    @php
        $cartCount = auth()->check()
            ? \App\Models\CartItem::where('user_id', auth()->id())->count()
            : 0;
    @endphp

    {{-- HEADER --}}
    @include('layouts.header')

    {{-- MAIN CONTENT --}}
    <main class="mt-4 flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('layouts.footer')

    <!-- WhatsApp Floating Button + Label -->
    <div class="wa-float-wrapper">
        <div class="wa-label">Tanya Admin</div>
        <a href="https://wa.me/6285290474524" target="_blank" class="whatsapp-bubble">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <!-- MINI CART POPUP --> 
    @php
        $cartItems = auth()->check()
            ? \App\Models\CartItem::with('product')
                ->where('user_id', auth()->id())
                ->get()
            : collect();
    @endphp

    <div x-show="openCart"
         x-transition
         class="fixed inset-0 bg-black/50 z-50 flex justify-end"
         style="display:none;">

        <div class="bg-white w-full sm:w-96 h-full p-4 shadow-xl overflow-y-auto"
             @click.away="openCart = false">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold">Keranjang Saya</h5>
                <button class="btn-close" @click="openCart = false"></button>
            </div>

            @if($cartItems->isEmpty())
                <p class="text-muted text-center mt-4">Keranjang masih kosong.</p>
            @else
                <ul class="list-group mb-3">
                    @foreach($cartItems as $item)
                        @php
                            $area = $item->length * $item->width;
                            $harga = $item->product->price;
                            $productTotal = $area * $item->quantity * $harga;

                            // finishing rate
                            $finishingRate = $item->finishing === 'finishing' ? 500 : 0;
                            $finishingTotal = $finishingRate * $area * $item->quantity;

                            $total = $productTotal + $finishingTotal;
                        @endphp

                        <li class="list-group-item">
                            <div class="fw-semibold">{{ $item->product->name }}</div>

                            <div class="small text-muted">
                                {{ $item->length }} × {{ $item->width }} m ({{ $area }} m²) × {{ $item->quantity }}
                            </div>

                            <div class="small">
                                Produk: Rp {{ number_format($productTotal, 0, ',', '.') }} <br>
                                Finishing: Rp {{ number_format($finishingTotal, 0, ',', '.') }}
                            </div>

                            <div class="fw-bold text-end">
                                Total: Rp {{ number_format($total, 0, ',', '.') }}
                            </div>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('cart.index') }}" class="btn btn-primary w-100">
                    Lihat Keranjang Lengkap
                </a>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AKTIFKAN TOOLTIP BOOTSTRAP --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) {
                return new bootstrap.Tooltip(el);
            });
        });
    </script>

</body>
</html>
