<header class="bg-indigo-500 shadow-sm py-3">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6">

        <!-- LOGO -->
        <div class="flex items-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="2 Audi Digital" class="h-20">
        </div>

        <!-- SEARCH BAR -->
        <div class="flex-grow mx-6">
            <div class="relative">
                <input type="text" placeholder="Mau Cari Apa?" 
                       class="w-full h-11 pl-8 pr-10 rounded-full border-none focus:outline-none">
                <i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-lg"></i>
            </div>
        </div>

        <!-- MENU -->
        <div class="flex items-center space-x-6 text-white font-bold">

            <!-- KATEGORI PRODUK -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-white text-sm md:text-base hover:underline">
                    KATEGORI PRODUK
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded shadow-lg z-50">
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Kategori 1</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Kategori 2</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Kategori 3</a>
                </div>
            </div>

            <!-- KERANJANG -->
            <div class="relative">
                <a href="#" class="text-white text-2xl">
                    <i class="bi bi-cart3"></i>
                </a>
                @if(session('cartCount', 0) > 0)
                    <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs rounded-full px-2">
                        {{ session('cartCount') }}
                    </span>
                @endif
            </div>

            <!-- AKUN -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-white text-4xl">
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
