<header class="bg-indigo-500 shadow-sm py-3">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6">

        {{-- LOGO --}}
        <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="2 Audi Digital" class="h-20">
        </div>

        {{-- SEARCH BAR --}}
        <div class="flex-grow mx-6">
            <form action="{{ route('catalog.search') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="q"
                    placeholder="Mau Cari Apa?"
                    value="{{ request('q') }}"
                    class="w-full h-11 rounded-full bg-white pl-4 pr-11
                           text-gray-800 placeholder-gray-400
                           border-none focus:outline-none"
                >
                <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 
                               bg-transparent border-0 p-0">
                    <i class="bi bi-search text-gray-500 text-lg"></i>
                </button>
            </form>
        </div>

        {{-- MENU KANAN --}}
        <div class="flex items-center space-x-6 text-white font-bold">

            {{-- KERANJANG --}}
            @php
                $cartItemCount = auth()->check()
                    ? \App\Models\CartItem::where('user_id', auth()->id())->count()
                    : 0;
            @endphp

            <div class="relative">
                <button type="button"
                        @click="openCart = true"
                        class="text-white text-2xl"
                        data-bs-toggle="tooltip"
                        data-bs-placement="bottom"
                        title="Keranjang">
                    <i class="bi bi-cart3"></i>
                </button>

                @if($cartItemCount > 0)
                    <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs rounded-full px-2">
                        {{ $cartItemCount }}
                    </span>
                @endif
            </div>

            {{-- RIWAYAT PESANAN --}}
            <div>
                @auth
                    <a href="{{ route('orders.history') }}"
                       class="text-white text-2xl"
                       title="Riwayat Pesanan"
                       data-bs-toggle="tooltip"
                       data-bs-placement="bottom">
                        <i class="bi bi-clock-history"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-white text-2xl"
                       title="Riwayat Pesanan"
                       data-bs-toggle="tooltip"
                       data-bs-placement="bottom">
                        <i class="bi bi-clock-history"></i>
                    </a>
                @endauth
            </div>

            {{-- AKUN --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="text-white text-4xl"
                        data-bs-toggle="tooltip"
                        data-bs-placement="bottom"
                        title="Akun">
                    <i class="bi bi-person-circle"></i>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-3 w-48 bg-white text-gray-800 rounded-lg shadow-lg py-2 z-50">

                    @auth
                        <span class="block px-4 py-2">Halo, {{ Auth::user()->name }}</span>

                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 hover:bg-gray-100">
                            <i class="bi bi-pencil-square mr-2"></i> Kelola Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                <i class="bi bi-box-arrow-right mr-2"></i> Logout
                            </button>
                        </form>

                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-100">
                            <i class="bi bi-box-arrow-in-right mr-2"></i> Login
                        </a>

                        <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-100">
                            <i class="bi bi-person-plus mr-2"></i> Register
                        </a>
                    @endauth

                </div>
            </div>

        </div>
    </div>
</header>
