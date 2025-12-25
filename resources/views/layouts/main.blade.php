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
        :root{
            --brand: #4f46e5;      /* indigo */
            --brandDark: #1e3a8a;  /* navy */
            --surface: #ffffff;
            --muted: #f5f7fb;      /* abu muda */
            --text: #0f172a;
        }

        body { background: var(--muted); color: var(--text); }

        /* Section rhythm (biar ga kebanyakan biru) */
        .section-surface { background: var(--surface); }
        .section-muted   { background: var(--muted); }

        /* Card look */
        .card-soft{
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .06);
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        }

        /* Buttons */
        .btn-brand{
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
        }
        .btn-brand:hover{ filter: brightness(.95); color:#fff; }
        .btn-ghost{
            background: #fff;
            color: var(--brand);
            border: 1px solid rgba(79,70,229,.25);
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
        }

        /* WhatsApp floating */
        .wa-float-wrapper {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }
        .wa-label {
            background: #25D366;
            color: #fff;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(0,0,0,0.18);
            white-space: nowrap;
        }
        .whatsapp-bubble {
            width: 58px; height: 58px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px rgba(0,0,0,0.22);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .whatsapp-bubble:hover { transform: scale(1.06); }
        .whatsapp-bubble i { color: white; font-size: 28px; }

        /* Hide label on small screens (biar ga nutup konten) */
        @media (max-width: 576px){
            .wa-label{ display:none; }
            .wa-float-wrapper{ right: 14px; bottom: 14px; }
        }

        /* Tooltip bootstrap */
        .tooltip-inner {
            background-color: var(--brandDark);
            color: #ffffff;
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18);
        }
        .tooltip.bs-tooltip-top .tooltip-arrow::before { border-top-color: var(--brandDark); }
        .tooltip.bs-tooltip-bottom .tooltip-arrow::before { border-bottom-color: var(--brandDark); }
        .tooltip.bs-tooltip-start .tooltip-arrow::before { border-left-color: var(--brandDark); }
        .tooltip.bs-tooltip-end .tooltip-arrow::before { border-right-color: var(--brandDark); }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col" x-data="{ openCart: false }">

    {{-- HEADER --}}
    @include('layouts.header')

    {{-- MAIN CONTENT --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('layouts.footer')

    <!-- WhatsApp Floating Button -->
    <div class="wa-float-wrapper">
        <div class="wa-label">Tanya Admin</div>
        <a href="https://wa.me/6285290474524" target="_blank" class="whatsapp-bubble" aria-label="WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <!-- MINI CART POPUP -->
    @php
        $cartItems = auth()->check()
            ? \App\Models\CartItem::with('product')->where('user_id', auth()->id())->get()
            : collect();
    @endphp

    <div x-show="openCart"
         x-transition
         class="fixed inset-0 bg-black/50 z-50 flex justify-end"
         style="display:none;">

        <div class="bg-white w-full sm:w-96 h-full p-4 shadow-xl overflow-y-auto"
             @click.away="openCart = false">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Keranjang Saya</h5>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
        });
    </script>
</body>
</html>
