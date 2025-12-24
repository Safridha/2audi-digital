<x-app-layout>
    <!-- NAVBAR ATAS -->
    <nav x-data="{ open: false }"
         class="sticky top-0 z-50 bg-white/95 backdrop-blur
                border-b border-gray-300 dark:bg-gray-800/95 dark:border-gray-700
                shadow-md ring-1 ring-black/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24">
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 object-contain">
                    </a>

                    <!-- Navigation Links (rapi: 1 baris + bisa scroll horizontal) -->
                    <div class="hidden md:flex items-center gap-2 whitespace-nowrap overflow-x-auto max-w-[900px] hide-scrollbar">
                        <a href="{{ route('admin.dashboard') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Kelola Kategori
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.products.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Kelola Produk
                        </a>

                        {{-- MENU BARU: KELOLA BAHAN --}}
                        <a href="{{ route('admin.bahans.index') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.bahans.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Kelola Bahan
                        </a>

                        <a href="{{ route('admin.stock.index') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.stock.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Kelola Stok Bahan
                        </a>

                        <a href="{{ route('admin.orders.index') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Monitoring Pesanan
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                           class="whitespace-nowrap text-sm font-medium px-3 py-2 rounded
                                  {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}">
                            Kelola Pengguna
                        </a>
                    </div>
                </div>

                <!-- Dropdown User -->
                <div class="flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-2 px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:text-indigo-600 whitespace-nowrap">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </nav>

    <!-- CSS kecil untuk sembunyikan scrollbar menu -->
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- KONTEN UTAMA -->
    <main class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
        @yield('content')
    </main>
</x-app-layout>
