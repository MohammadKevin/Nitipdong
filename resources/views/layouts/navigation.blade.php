{{-- BelanjaIn Navigation — Modern Minimalist --}}
<nav x-data="{ mobileOpen: false, userOpen: false }" class="bg-white border-b border-slate-100 sticky top-0 z-50"
    style="box-shadow: 0 1px 8px rgba(0,0,0,0.06);">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6">
        <div class="flex items-center h-16 gap-4">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-9 h-9 rounded-xl overflow-hidden border border-cyan-100 shadow-sm">
                    <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-xl text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                    Belanja<span style="color:var(--brand);">In</span>
                </span>
            </a>

            {{-- Search Bar --}}
            <div class="flex-1 max-w-xl hidden md:flex">
                <div class="relative w-full">
                    <input type="text"
                        placeholder="Cari produk, toko, atau merek..."
                        class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition-all placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Right Side Actions --}}
            <div class="flex items-center gap-1 ml-auto">

                {{-- Cart --}}
                @auth
                    @php $cartCount = auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0; @endphp
                    <a href="{{ route('customer.cart.index') }}"
                        class="relative flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-white text-[10px] font-bold flex items-center justify-center"
                                style="background:var(--brand); font-family:'Outfit',sans-serif;">{{ $cartCount }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </a>
                @endauth

                {{-- Notif Bell --}}
                <button class="flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-cyan-50 hover:text-cyan-600 transition-colors relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                @if(Route::has('login'))
                    @auth
                        {{-- User Avatar Dropdown --}}
                        <div class="relative" @click.outside="userOpen = false">
                            <button @click="userOpen = !userOpen"
                                class="flex items-center gap-2.5 ml-1 px-3 py-1.5 rounded-xl hover:bg-slate-50 transition-colors">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=06b6d4&color=fff&size=80"
                                    class="w-8 h-8 rounded-full ring-2 ring-cyan-100" alt="Avatar">
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-slate-800 leading-none" style="font-family:'Outfit',sans-serif;">
                                        {{ Str::words(Auth::user()->name, 1, '') }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div x-show="userOpen" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden">

                                {{-- User Info Header --}}
                                <div class="px-4 py-3 border-b border-slate-50">
                                    <p class="font-semibold text-slate-800 text-sm truncate" style="font-family:'Outfit',sans-serif;">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="py-1.5">
                                    @if(Auth::user()->role === 'super_admin')
                                        <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            Super Admin Panel
                                        </a>
                                    @elseif(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            Admin Panel
                                        </a>
                                    @elseif(Auth::user()->role === 'seller')
                                        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            Toko Saya
                                        </a>
                                    @else
                                        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            Pesanan Saya
                                        </a>
                                        <a href="{{ route('customer.cart.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            Keranjang
                                        </a>
                                    @endif

                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Edit Profil
                                    </a>
                                </div>

                                <div class="border-t border-slate-50 py-1.5">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 ml-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-cyan-600 hover:text-cyan-700 transition-colors" style="font-family:'Outfit',sans-serif;">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn-primary text-sm py-2 px-5">
                                Daftar
                            </a>
                        </div>
                    @endauth
                @endif
            </div>
        </div>

        {{-- Mobile Search --}}
        <div class="md:hidden pb-3">
            <div class="relative">
                <input type="text" placeholder="Cari produk..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>
</nav>