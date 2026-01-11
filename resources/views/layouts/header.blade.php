<header class="shadow-sm" style="background:#4f46e5">
  @php
    $cartItemCount = auth()->check()
      ? \App\Models\CartItem::where('user_id', auth()->id())->count()
      : 0;
  @endphp

  <div class="max-w-7xl mx-auto px-3 sm:px-6 py-3" x-data="{ mobileMenu:false }">

    {{-- TOP BAR --}}
    <div class="flex items-center gap-2 sm:gap-4">

      {{-- LOGO --}}
      <a href="{{ route('home') }}" class="shrink-0">
        <img src="{{ asset('images/logo.png') }}" alt="2 Audi Digital" class="h-10 sm:h-16">
      </a>

      {{-- SEARCH --}}
      <div class="flex-1 min-w-0">
        <form action="{{ route('catalog.search') }}" method="GET"
              class="relative w-full max-w-2xl sm:max-w-none sm:mx-auto">
          <input name="q" value="{{ request('q') }}" placeholder="Mau cari apa?"
                 class="w-full h-10 sm:h-11 rounded-full bg-white pl-4 pr-11 text-gray-800 placeholder-gray-400 border-0 focus:outline-none">
          <button class="absolute right-3 top-1/2 -translate-y-1/2 bg-transparent border-0 p-0" type="submit">
            <i class="bi bi-search text-gray-500 text-lg"></i>
          </button>
        </form>
      </div>

      {{-- DESKTOP ICONS --}}
      <div class="hidden sm:flex items-center gap-7 text-white font-bold shrink-0">

        {{-- ✅ HOME --}}
        <a href="{{ route('home') }}"
           x-init="$el._tt = new bootstrap.Tooltip($el)"
           @click="$el._tt?.hide()"
           class="text-white text-2xl"
           data-bs-placement="bottom" title="Home">
          <i class="bi bi-house-door"></i>
        </a>

        {{-- CART --}}
        <div class="relative">
          <a href="{{ route('cart.index') }}"
             x-init="$el._tt = new bootstrap.Tooltip($el)"
             @click="$el._tt?.hide()"
             class="text-white text-2xl"
             data-bs-placement="bottom" title="Keranjang">
            <i class="bi bi-cart3"></i>
          </a>
          @if($cartItemCount > 0)
            <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs rounded-full px-2">
              {{ $cartItemCount }}
            </span>
          @endif
        </div>

        {{-- RIWAYAT --}}
        <a href="{{ auth()->check() ? route('orders.history') : route('login') }}"
           x-init="$el._tt = new bootstrap.Tooltip($el)"
           @click="$el._tt?.hide()"
           class="text-white text-2xl"
           data-bs-placement="bottom" title="Riwayat Pesanan">
          <i class="bi bi-clock-history"></i>
        </a>

        {{-- AKUN --}}
        <div class="relative" x-data="{ open:false }">
          <button type="button"
                  x-init="$el._tt = new bootstrap.Tooltip($el)"
                  @click="open = !open; $el._tt?.hide()"
                  class="text-white text-4xl"
                  data-bs-placement="bottom" title="Akun">
            <i class="bi bi-person-circle"></i>
          </button>

          <div x-show="open" @click.away="open=false" x-transition
               class="absolute right-0 mt-3 w-52 bg-white text-gray-800 rounded-xl shadow-lg py-2 z-50">
            @auth
              <span class="block px-4 py-2 text-sm text-gray-600">Halo, {{ Auth::user()->name }}</span>
              <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-50">
                <i class="bi bi-pencil-square mr-2"></i> Kelola Profil
              </a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50">
                  <i class="bi bi-box-arrow-right mr-2"></i> Logout
                </button>
              </form>
            @else
              <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-50">
                <i class="bi bi-box-arrow-in-right mr-2"></i> Login
              </a>
              <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-50">
                <i class="bi bi-person-plus mr-2"></i> Register
              </a>
            @endauth
          </div>
        </div>

      </div>

      {{-- MOBILE HAMBURGER --}}
      <button type="button"
              class="sm:hidden text-white text-2xl shrink-0"
              @click="mobileMenu = !mobileMenu"
              aria-label="Menu">
        <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'"></i>
      </button>
    </div>

    {{-- MOBILE MENU --}}
    <div x-show="mobileMenu" x-transition
         class="sm:hidden mt-3 bg-white/10 rounded-xl p-3"
         @click.away="mobileMenu=false">

      <div class="grid gap-2 text-white">

        {{-- ✅ HOME --}}
        <a href="{{ route('home') }}"
           class="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
          <i class="bi bi-house-door text-lg"></i>
          <span>Home</span>
        </a>

        <a href="{{ route('cart.index') }}"
           class="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
          <i class="bi bi-cart3 text-lg"></i>
          <span>Keranjang</span>
        </a>

        <a href="{{ auth()->check() ? route('orders.history') : route('login') }}"
           class="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
          <i class="bi bi-clock-history text-lg"></i>
          <span>Riwayat Pesanan</span>
        </a>

        @auth
          <a href="{{ route('profile.edit') }}"
             class="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
            <i class="bi bi-person-gear text-lg"></i>
            <span>Kelola Profil</span>
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
              <i class="bi bi-box-arrow-right text-lg"></i>
              <span>Logout</span>
            </button>
          </form>
        @else
          <a href="{{ route('login') }}"
             class="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
            <i class="bi bi-box-arrow-in-right text-lg"></i>
            <span>Login</span>
          </a>
          <a href="{{ route('register') }}"
             class="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-white/10">
            <i class="bi bi-person-plus text-lg"></i>
            <span>Register</span>
          </a>
        @endauth

      </div>
    </div>

  </div>
</header>

<style>
.tooltip-inner{
  background:#fff!important;
  color:#111827!important;
  font-weight:600;
  font-size:12px;
  padding:6px 10px;
  border-radius:10px;
  box-shadow:0 10px 25px rgba(0,0,0,.15);
  border:1px solid rgba(17,24,39,.08)
}
.tooltip.bs-tooltip-top .tooltip-arrow::before{border-top-color:#fff!important}
.tooltip.bs-tooltip-bottom .tooltip-arrow::before{border-bottom-color:#fff!important}
.tooltip.bs-tooltip-start .tooltip-arrow::before{border-left-color:#fff!important}
.tooltip.bs-tooltip-end .tooltip-arrow::before{border-right-color:#fff!important}
</style>
