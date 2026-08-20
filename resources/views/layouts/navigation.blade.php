<nav x-data="{
        mobileSearch: false,
        userOpen: false,
        notifOpen: false,
        cartOpen: false,
        cartCount: {{ Auth::check() && Auth::user()->role === 'customer' ? Auth::user()->carts()->count() : 0 }},
        cartBounce: false,
        searchQuery: '{{ addslashes(request('q', '')) }}',
        searchSuggestions: { products: [], stores: [], categories: [] },
        showSuggestions: false,
        isLoadingSuggestions: false,
        async fetchSuggestions() {
            const q = this.searchQuery.trim();
            if (q.length < 2) {
                this.searchSuggestions = { products: [], stores: [], categories: [] };
                this.showSuggestions = false;
                return;
            }
            this.isLoadingSuggestions = true;
            try {
                const res = await fetch(`{{ route('api.search.suggestions') }}?q=${encodeURIComponent(q)}`);
                if (res.ok) {
                    this.searchSuggestions = await res.json();
                    this.showSuggestions = (this.searchSuggestions.products.length > 0 || this.searchSuggestions.stores.length > 0 || this.searchSuggestions.categories.length > 0);
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoadingSuggestions = false;
            }
        }
    }"
    @cart-updated.window="cartCount = $event.detail.count; cartBounce = true; setTimeout(() => cartBounce = false, 1200)"
    class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 shadow-xs">

    <div class="page-container">
        <div class="flex items-center justify-between h-16 gap-4">

            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="BelanjaIn Home">
                <div class="w-9 h-9 rounded-xl overflow-hidden border border-cyan-200 bg-cyan-50 flex items-center justify-center shadow-xs">
                    <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-slate-900 leading-none block">
                        Belanja<span class="text-cyan-600 font-black">In</span>
                    </span>
                    <span class="text-[9px] font-bold text-cyan-700 tracking-wider uppercase">Official Mall</span>
                </div>
            </a>

            {{-- Center Search Bar with Live Suggestions --}}
            <div class="flex-1 max-w-xl hidden md:flex flex-col justify-center relative" @click.outside="showSuggestions = false">
                <form action="{{ url('/products') }}" method="GET" class="w-full flex items-center relative">
                    <div class="relative w-full flex items-center">
                        <input type="text" name="q" x-model="searchQuery"
                               @input.debounce.250ms="fetchSuggestions()"
                               @focus="if(searchQuery.trim().length >= 2) showSuggestions = true"
                               placeholder="Cari di BelanjaIn (contoh: Laptop, Sepatu, Smartwatch, TWS)..."
                               class="w-full h-10 pl-9 pr-24 rounded-xl border border-slate-200 bg-slate-50/70 text-xs focus:bg-white focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100 transition-all">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 text-xs" :class="isLoadingSuggestions ? 'animate-spin fa-spinner' : 'fa-magnifying-glass'"></i>
                        <div class="absolute right-1.5 flex items-center gap-1">
                            <span class="text-[10px] text-slate-400 font-mono hidden lg:inline-block px-1.5 py-0.5 bg-slate-200/60 rounded">Ctrl K</span>
                            <button type="submit" class="h-7 px-3.5 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-lg shadow-xs transition-colors cursor-pointer">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Live Search Autocomplete PopUp --}}
                <div x-show="showSuggestions" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-11 left-0 right-0 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs max-h-96 overflow-y-auto">
                    
                    {{-- Categories --}}
                    <template x-if="searchSuggestions.categories && searchSuggestions.categories.length > 0">
                        <div class="p-2 border-b border-slate-100 bg-slate-50/50">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Kategori</span>
                            <div class="flex items-center gap-2 mt-1 flex-wrap px-2">
                                <template x-for="cat in searchSuggestions.categories" :key="cat.id">
                                    <a :href="cat.url" class="px-2.5 py-1 bg-white hover:bg-cyan-50 text-slate-700 hover:text-cyan-700 rounded-lg border border-slate-200 text-[11px] font-semibold transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-tag text-[9px] text-cyan-600"></i>
                                        <span x-text="cat.name"></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Stores --}}
                    <template x-if="searchSuggestions.stores && searchSuggestions.stores.length > 0">
                        <div class="p-2 border-b border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Toko Resmi</span>
                            <div class="space-y-1 mt-1">
                                <template x-for="st in searchSuggestions.stores" :key="st.id">
                                    <a :href="st.url" class="p-2 hover:bg-cyan-50/50 rounded-xl transition-colors flex items-center gap-2.5 block">
                                        <div class="w-7 h-7 rounded-lg bg-cyan-100 text-cyan-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            <i class="fa-solid fa-store text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="font-bold text-slate-800 text-xs truncate block" x-text="st.name"></span>
                                            <span class="text-[10px] text-slate-400 truncate block" x-text="st.city"></span>
                                        </div>
                                        <span class="text-[10px] font-bold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded-full border border-cyan-200">Official</span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Products --}}
                    <template x-if="searchSuggestions.products && searchSuggestions.products.length > 0">
                        <div class="p-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Produk Terkait</span>
                            <div class="space-y-1 mt-1">
                                <template x-for="p in searchSuggestions.products" :key="p.id">
                                    <a :href="p.url" class="p-2 hover:bg-cyan-50/50 rounded-xl transition-colors flex items-center gap-2.5 block">
                                        <img :src="p.image_url" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.src='/img/icon.jpg'">
                                        <div class="flex-1 min-w-0">
                                            <span class="font-semibold text-slate-800 text-xs truncate block" x-text="p.name"></span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="font-bold text-cyan-800 text-xs" x-text="'Rp ' + Number(p.price).toLocaleString('id-ID')"></span>
                                                <template x-if="p.has_discount">
                                                    <span class="text-[10px] text-rose-500 font-bold bg-rose-50 px-1 rounded" x-text="'-' + p.discount_percentage + '%'"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="p-2 bg-slate-50 border-t border-slate-100 text-center">
                        <button type="button" @click="$el.closest('form') ? $el.closest('form').submit() : window.location.href='/products?q=' + encodeURIComponent(searchQuery)"
                                class="text-[11px] font-bold text-cyan-700 hover:underline">
                            Lihat Semua Hasil Pencarian &rarr;
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-1 pl-1 font-medium overflow-hidden whitespace-nowrap">
                    <span class="text-slate-500 font-bold">Populer:</span>
                    <a href="{{ url('/products?q=Laptop') }}" class="hover:text-cyan-700 transition-colors truncate">Laptop Gaming</a>
                    <a href="{{ url('/products?q=Sepatu') }}" class="hover:text-cyan-700 transition-colors truncate">Sepatu Pria</a>
                    <a href="{{ url('/products?q=Smartwatch') }}" class="hover:text-cyan-700 transition-colors truncate">Smartwatch</a>
                    <a href="{{ url('/products?q=TWS') }}" class="hover:text-cyan-700 transition-colors truncate">TWS Earbuds</a>
                    <a href="{{ url('/products?q=Kemeja') }}" class="hover:text-cyan-700 transition-colors truncate">Kemeja</a>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">

                <button @click="mobileSearch = !mobileSearch" aria-label="Search" class="btn-icon md:hidden">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                @auth
                    @php 
                        $cartCount = auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0;
                        $wishlistCount = auth()->user()->role === 'customer' ? auth()->user()->wishlists()->count() : 0;
                        $userCarts = auth()->user()->role === 'customer'
                            ? auth()->user()->carts()->with(['product.store'])->latest()->take(5)->get()
                            : collect();
                        $cartSubtotal = $userCarts->sum(fn($c) => ($c->product ? $c->product->final_price : 0) * $c->quantity);
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

                    {{-- PopUp Mini-Cart Dropdown --}}
                    <div class="relative" @click.outside="cartOpen = false">
                        <button type="button" id="nav-cart-btn" @click="cartOpen = !cartOpen" aria-label="Keranjang Belanja"
                                class="btn-icon relative cursor-pointer transition-transform duration-300"
                                :class="cartBounce ? 'scale-125 text-cyan-600' : ''"
                                title="Keranjang Belanja">
                            <i class="fa-solid fa-cart-shopping text-sm" :class="cartBounce ? 'text-cyan-600' : 'text-slate-600'"></i>
                            <span x-show="cartCount > 0"
                                  class="absolute top-1 right-1 min-w-[15px] h-3.5 px-0.5 rounded-full bg-cyan-600 text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white transition-all"
                                  :class="cartBounce ? 'scale-125 ring-cyan-200' : ''"
                                  x-text="cartCount > 99 ? '99+' : cartCount">
                            </span>
                        </button>

                        {{-- Dropdown Container --}}
                        <div x-show="cartOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden text-xs">
                            
                            {{-- Header --}}
                            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                                <div class="flex items-center gap-1.5 font-bold text-slate-900">
                                    <i class="fa-solid fa-cart-shopping text-cyan-600"></i>
                                    <span>Keranjang Belanja ({{ $cartCount }})</span>
                                </div>
                                <a href="{{ route('customer.cart.index') }}" @click="cartOpen = false" class="text-[11px] font-semibold text-cyan-700 hover:text-cyan-800 hover:underline">
                                    Lihat Semua
                                </a>
                            </div>

                            {{-- Body List --}}
                            @if($userCarts->count() > 0)
                                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto p-1">
                                    @foreach($userCarts as $cItem)
                                        @if($cItem->product)
                                            <a href="{{ route('product.show', $cItem->product) }}" @click="cartOpen = false"
                                               class="p-2.5 hover:bg-cyan-50/40 rounded-xl transition-colors flex items-center gap-3 block group">
                                                <img src="{{ $cItem->product->image_url }}" alt="{{ $cItem->product->name }}" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shrink-0">
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-semibold text-slate-800 text-xs truncate group-hover:text-cyan-700 transition-colors">{{ $cItem->product->name }}</h4>
                                                    @if($cItem->variant)
                                                        <span class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.2 rounded inline-block mt-0.5">{{ $cItem->variant }}</span>
                                                    @endif
                                                    <div class="flex items-center justify-between mt-1">
                                                        <span class="text-[11px] text-slate-500 font-medium">{{ $cItem->quantity }} &times; Rp {{ number_format($cItem->product->final_price, 0, ',', '.') }}</span>
                                                        <span class="font-bold text-cyan-800 text-xs">Rp {{ number_format($cItem->product->final_price * $cItem->quantity, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Footer Checkout Buttons --}}
                                <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-slate-500 font-medium">Total Perkiraan:</span>
                                        <span class="font-black text-sm text-cyan-800">
                                            Rp {{ number_format($cartSubtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('customer.cart.index') }}" @click="cartOpen = false"
                                           class="h-8 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                            Buka Keranjang
                                        </a>
                                        <a href="{{ route('customer.order.checkout') }}" @click="cartOpen = false"
                                           class="h-8 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs flex items-center justify-center shadow-xs transition-colors">
                                            Checkout ({{ $cartCount }})
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="py-8 px-4 text-center space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center mx-auto text-lg">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>
                                    <p class="font-bold text-slate-800 text-xs">Wah, keranjang belanjamu kosong</p>
                                    <p class="text-[11px] text-slate-400">Yuk, isi dengan barang-barang impianmu!</p>
                                    <div class="pt-2">
                                        <a href="{{ url('/products') }}" @click="cartOpen = false" class="btn-primary text-xs h-7.5 px-4 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white inline-flex items-center justify-center font-semibold">
                                            Mulai Belanja
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('chat.index') }}" aria-label="Pesan Chat" class="btn-icon relative" title="Pesan & Chat">
                        <i class="fa-regular fa-comment-dots text-sm text-slate-600"></i>
                    </a>

                    <x-notification-dropdown />

                    <div class="h-4 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                    <div class="relative" @click.outside="userOpen = false">
                        <button @click="userOpen = !userOpen" class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-slate-100 transition-colors">
                            <img src="{{ Auth::user()->avatar_url }}"
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
                    {{-- Guest Cart Icon with PopUp --}}
                    <div class="relative" @click.outside="cartOpen = false">
                        <button type="button" @click="cartOpen = !cartOpen" aria-label="Keranjang Belanja" class="btn-icon relative cursor-pointer" title="Keranjang Belanja">
                            <i class="fa-solid fa-cart-shopping text-sm text-slate-600"></i>
                        </button>

                        <div x-show="cartOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 p-5 text-center text-xs space-y-3">
                            <div class="w-12 h-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center mx-auto text-lg">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-xs">Keranjang Belanja</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Masuk ke akun Anda untuk melihat produk yang telah disimpan di keranjang.</p>
                            </div>
                            <div class="pt-1 flex flex-col gap-1.5">
                                <a href="{{ route('login') }}" class="w-full h-8 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center justify-center transition-colors">
                                    Masuk Akun
                                </a>
                                <a href="{{ route('register') }}" class="w-full h-8 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold flex items-center justify-center transition-colors">
                                    Daftar Akun Baru
                                </a>
                            </div>
                        </div>
                    </div>

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