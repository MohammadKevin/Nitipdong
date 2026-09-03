@php
    $bottomCartCount = auth()->check() && auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0;
@endphp

<nav x-data="{
        mobileCartCount: {{ (int) $bottomCartCount }},
        init() {
            window.addEventListener('cart-updated', () => {
                @if(auth()->check() && auth()->user()->role === 'customer')
                fetch('{{ route('customer.cart.items') }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => { if (data.count !== undefined) this.mobileCartCount = data.count; })
                    .catch(() => {});
                @endif
            });
        }
     }"
     class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 md:hidden shadow-[0_-4px_20px_rgba(0,0,0,0.06)] pb-[env(safe-area-inset-bottom,0px)]"
     aria-label="Mobile Navigation">

    <div class="grid grid-cols-5 h-14 items-center justify-around px-1 text-[10px] font-medium text-slate-500">

        <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}"
           class="flex flex-col items-center justify-center py-1 transition-all group {{ request()->is('/') ? 'text-cyan-600 font-bold' : 'hover:text-slate-900' }}">
            <div class="relative">
                <i class="fa-solid fa-house text-base transition-transform group-active:scale-90 {{ request()->is('/') ? 'text-cyan-600' : 'text-slate-500' }}"></i>
            </div>
            <span class="mt-1 leading-none tracking-tight">Beranda</span>
        </a>

        <a href="{{ url('/products?flash_sale=1') }}"
           class="flex flex-col items-center justify-center py-1 transition-all group {{ request()->has('flash_sale') ? 'text-orange-500 font-bold' : 'hover:text-slate-900' }}">
            <div class="relative">
                <i class="fa-solid fa-bolt text-base transition-transform group-active:scale-90 {{ request()->has('flash_sale') ? 'text-orange-500' : 'text-slate-500' }}"></i>
                <span class="absolute -top-1 -right-2.5 px-1 py-0.2 rounded-full bg-orange-500 text-white text-[8px] font-black animate-pulse">PROMO</span>
            </div>
            <span class="mt-1 leading-none tracking-tight">Flash Sale</span>
        </a>

        <button type="button"
                @click="$dispatch('open-chat')"
                class="flex flex-col items-center justify-center py-1 transition-all group hover:text-slate-900 cursor-pointer">
            <div class="relative">
                <i class="fa-regular fa-comment-dots text-base transition-transform group-active:scale-90 text-slate-500 group-hover:text-cyan-600"></i>
            </div>
            <span class="mt-1 leading-none tracking-tight">Chat</span>
        </button>

        <a href="{{ route('customer.cart.index') }}"
           class="flex flex-col items-center justify-center py-1 transition-all group {{ request()->routeIs('customer.cart.*') ? 'text-cyan-600 font-bold' : 'hover:text-slate-900' }}">
            <div class="relative">
                <i class="fa-solid fa-cart-shopping text-base transition-transform group-active:scale-90 {{ request()->routeIs('customer.cart.*') ? 'text-cyan-600' : 'text-slate-500' }}"></i>
                <span x-show="mobileCartCount > 0"
                      x-text="mobileCartCount > 99 ? '99+' : mobileCartCount"
                      class="absolute -top-1.5 -right-2 min-w-[15px] h-3.5 px-1 rounded-full bg-cyan-600 text-white text-[9px] font-black flex items-center justify-center shadow-xs ring-2 ring-white">
                </span>
            </div>
            <span class="mt-1 leading-none tracking-tight">Keranjang</span>
        </a>

        @auth
            <a href="{{ auth()->user()->role === 'seller' ? route('seller.dashboard') : (auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'super_admin' ? route('super_admin.dashboard') : route('customer.dashboard'))) }}"
               class="flex flex-col items-center justify-center py-1 transition-all group {{ request()->routeIs('customer.dashboard') || request()->routeIs('seller.*') || request()->routeIs('admin.*') ? 'text-cyan-600 font-bold' : 'hover:text-slate-900' }}">
                <div class="w-5 h-5 rounded-full overflow-hidden border {{ request()->routeIs('customer.dashboard') ? 'border-cyan-600 ring-1 ring-cyan-200' : 'border-slate-300' }}">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                </div>
                <span class="mt-1 leading-none tracking-tight">Akun</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center py-1 transition-all group {{ request()->routeIs('login') || request()->routeIs('register') ? 'text-cyan-600 font-bold' : 'hover:text-slate-900' }}">
                <div class="relative">
                    <i class="fa-regular fa-user text-base transition-transform group-active:scale-90 {{ request()->routeIs('login') ? 'text-cyan-600' : 'text-slate-500' }}"></i>
                </div>
                <span class="mt-1 leading-none tracking-tight">Masuk</span>
            </a>
        @endauth

    </div>
</nav>
