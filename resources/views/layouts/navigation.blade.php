<nav x-data="{
        mobileSearch: false,
        userOpen: false,
        notifOpen: false
    }"
    class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 shadow-xs">

    <div class="page-container">
        <div class="flex items-center justify-between h-14 gap-4">

            <a href="/" class="flex items-center gap-2.5 shrink-0 group" aria-label="BelanjaIn Home">
                <div class="w-8 h-8 rounded-lg overflow-hidden border border-cyan-200 bg-cyan-50 flex items-center justify-center">
                    <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-base tracking-tight text-slate-900 leading-tight">
                    Belanja<span class="text-cyan-600 font-extrabold">In</span>
                </span>
            </a>

            <form action="{{ url('/products') }}" method="GET" class="flex-1 max-w-xl hidden md:flex items-center relative">
                <div class="relative w-full flex items-center">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input id="nav-search" type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari produk, kategori, brand, atau official store..."
                           class="w-full h-9 pl-8 pr-20 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:border-cyan-600 focus:ring-2 focus:ring-cyan-500/20 outline-none transition-all placeholder:text-slate-400">
                    <div class="absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center gap-1">
                        <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 text-[9px] font-mono text-slate-400 bg-slate-100 border border-slate-200 rounded">Ctrl K</kbd>
                        <button type="submit"
                                class="h-7 px-3 bg-cyan-700 hover:bg-cyan-800 active:bg-cyan-900 text-white text-xs font-semibold rounded transition-colors">
                            Cari
                        </button>
                    </div>
                </div>
            </form>

            <div class="flex items-center gap-1.5 sm:gap-2">

                <button @click="mobileSearch = !mobileSearch" aria-label="Search" class="btn-icon md:hidden">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                @auth
                    @php 
                        $cartCount = auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0;
                        $wishlistCount = auth()->user()->role === 'customer' ? auth()->user()->wishlists()->count() : 0;
                    @endphp

                    @if(auth()->user()->role === 'customer')
                    <a href="{{ route('customer.wishlist.index') }}" aria-label="Wishlist" class="btn-icon relative" title="Wishlist & Produk Favorit">
                        <i class="fa-regular fa-heart text-sm text-slate-600"></i>
                        @if($wishlistCount > 0)
                            <span class="absolute top-1 right-1 min-w-[15px] h-3.5 px-0.5 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white">
                                {{ $wishlistCount > 99 ? '99+' : $wishlistCount }}
                            </span>
                        @endif
                    </a>
                    @endif

                    <a href="{{ route('customer.cart.index') }}" aria-label="Keranjang Belanja" class="btn-icon relative" title="Keranjang Belanja">
                        <i class="fa-solid fa-cart-shopping text-sm text-slate-600"></i>
                        @if($cartCount > 0)
                            <span class="absolute top-1 right-1 min-w-[15px] h-3.5 px-0.5 rounded-full bg-cyan-600 text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white">
                                {{ $cartCount > 99 ? '99+' : $cartCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('chat.index') }}" aria-label="Pesan Chat" class="btn-icon relative" title="Pesan & Chat">
                        <i class="fa-regular fa-comment-dots text-sm text-slate-600"></i>
                    </a>

                    <div class="relative" @click.outside="notifOpen = false">
                        <button @click="notifOpen = !notifOpen" aria-label="Notifikasi" class="btn-icon relative" title="Notifikasi">
                            <i class="fa-regular fa-bell text-sm text-slate-600"></i>
                        </button>

                        <div x-show="notifOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-1.5 w-72 bg-white rounded-lg shadow-dropdown border border-slate-200 py-1 z-50 overflow-hidden">
                            <div class="px-3.5 py-2 border-b border-slate-100 flex items-center justify-between">
                                <span class="font-semibold text-xs text-slate-800">Notifikasi</span>
                                <span class="text-[10px] text-cyan-600 font-medium cursor-pointer hover:underline">Tandai Dibaca</span>
                            </div>
                            <div class="py-6 text-center text-slate-400 text-xs">
                                <i class="fa-regular fa-bell-slash text-lg mb-1 text-slate-300 block"></i>
                                Belum ada notifikasi baru
                            </div>
                        </div>
                    </div>

                    <div class="h-4 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                    <div class="relative" @click.outside="userOpen = false">
                        <button @click="userOpen = !userOpen" class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-slate-100 transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0891b2&color=fff&size=60"
                                 class="w-6 h-6 rounded-full border border-cyan-200 object-cover" alt="{{ Auth::user()->name }}">
                            <div class="hidden sm:block text-left max-w-[110px]">
                                <p class="text-xs font-semibold text-slate-800 leading-tight truncate">
                                    {{ Auth::user()->name }}
                                </p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 hidden sm:block transition-transform" :class="userOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="userOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-1.5 w-52 bg-white rounded-lg shadow-dropdown border border-slate-200 py-1 z-50 overflow-hidden">

                            <div class="px-3.5 py-2 border-b border-slate-100">
                                <p class="font-semibold text-slate-900 text-xs truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-1 text-xs">
                                @if(Auth::user()->role === 'super_admin')
                                    <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-shield-halved w-4 text-cyan-600"></i>
                                        Super Admin Panel
                                    </a>
                                @elseif(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-gauge w-4 text-cyan-600"></i>
                                        Admin Panel
                                    </a>
                                @elseif(Auth::user()->role === 'seller')
                                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-store w-4 text-cyan-600"></i>
                                        Seller Center
                                    </a>
                                    <a href="{{ route('seller.products.index') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-boxes-stacked w-4 text-slate-400"></i>
                                        Katalog Toko
                                    </a>
                                    <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-clipboard-list w-4 text-slate-400"></i>
                                        Pesanan Masuk
                                    </a>
                                    <a href="{{ route('seller.reviews.index') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-star w-4 text-amber-500"></i>
                                        Ulasan Pembeli
                                    </a>
                                @else
                                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-bag-shopping w-4 text-cyan-600"></i>
                                        Pesanan Saya
                                    </a>
                                    <a href="{{ route('customer.wishlist.index') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-regular fa-heart w-4 text-rose-500"></i>
                                        Wishlist Saya
                                    </a>
                                    <a href="{{ route('customer.addresses.index') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                        <i class="fa-solid fa-location-dot w-4 text-cyan-600"></i>
                                        Buku Alamat
                                    </a>
                                    <a href="{{ route('store.register') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-cyan-700 font-semibold hover:bg-cyan-50 transition-colors">
                                        <i class="fa-solid fa-store w-4 text-cyan-600"></i>
                                        Buka Toko Gratis
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-cyan-600 transition-colors">
                                    <i class="fa-solid fa-user-gear w-4 text-slate-400"></i>
                                    Pengaturan Akun
                                </a>
                            </div>

                            <div class="pt-1 border-t border-slate-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-1.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors text-left font-medium">
                                        <i class="fa-solid fa-arrow-right-from-bracket w-4"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-outline text-xs h-8 px-3">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs h-8 px-3">
                        Daftar
                    </a>
                @endauth

            </div>
        </div>

        <div x-show="mobileSearch" x-cloak class="pb-2.5 md:hidden">
            <form action="{{ url('/products') }}" method="GET" class="relative">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari produk, kategori, toko..."
                       class="input text-xs pl-8 pr-14 h-8">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 px-2.5 h-6 bg-cyan-700 text-white text-[11px] font-semibold rounded">
                    Cari
                </button>
            </form>
        </div>
    </div>

    @php
        $navCategories = \App\Models\Category::take(8)->get();
    @endphp
    <div class="border-t border-slate-100/80 bg-white hidden sm:block">
        <div class="page-container">
            <div class="flex items-center gap-5 h-9 overflow-x-auto text-xs text-slate-600 scrollbar-none">
                <a href="{{ url('/products') }}" class="font-semibold text-slate-900 hover:text-cyan-600 shrink-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-list text-slate-400 text-[10px]"></i>
                    Semua Kategori
                </a>
                @foreach($navCategories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="hover:text-cyan-600 shrink-0 transition-colors flex items-center gap-1.5">
                        @if($cat->icon)
                            <i class="{{ $cat->icon }} text-slate-400 text-[10px]"></i>
                        @endif
                        {{ $cat->name }}
                    </a>
                @endforeach
                <a href="{{ url('/products') }}?flash_sale=1" class="ml-auto font-bold text-cyan-700 hover:text-cyan-800 shrink-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-amber-500 text-[10px]"></i>
                    Flash Sale
                </a>
            </div>
        </div>
    </div>
</nav>