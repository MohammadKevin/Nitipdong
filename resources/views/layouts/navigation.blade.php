@php
    $initialCartCount = auth()->check() && auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0;
    $initialCarts = auth()->check() && auth()->user()->role === 'customer'
        ? auth()->user()->carts()->with(['product.store'])->latest()->take(10)->get()
        : collect();
    $initialCartSubtotal = $initialCarts->sum(fn($c) => ($c->product ? $c->product->final_price : 0) * $c->quantity);
    $initialCartList = $initialCarts->map(fn($c) => [
        'id'            => $c->id,
        'obfuscated_id' => $c->obfuscated_id,
        'name'          => $c->product?->name ?? 'Produk',
        'image_url'     => $c->product?->image_url ?? asset('img/saksershop-logo.png'),
        'product_url'   => $c->product ? route('product.show', $c->product) : '#',
        'price'         => $c->product ? $c->product->final_price : 0,
        'quantity'      => $c->quantity,
        'stock'         => $c->product?->stock ?? 99,
        'variant'       => $c->variant,
        'subtotal'      => ($c->product ? $c->product->final_price : 0) * $c->quantity,
        'update_url'    => route('customer.cart.update', $c),
        'delete_url'    => route('customer.cart.destroy', $c),
    ])->values();

    $initialWishlistCount = auth()->check() && auth()->user()->role === 'customer' ? auth()->user()->wishlists()->count() : 0;
    $initialWishlists = auth()->check() && auth()->user()->role === 'customer'
        ? auth()->user()->wishlists()->with(['product.store'])->latest()->get()
        : collect();
    $initialWishlistList = $initialWishlists->map(function($w) {
        $p = $w->product;
        if (!$p) return null;
        return [
            'id'             => $w->id,
            'product_id'     => $p->id,
            'name'           => $p->name,
            'price'          => (float) $p->final_price,
            'original_price' => (float) $p->price,
            'has_discount'   => (bool) $p->has_discount,
            'discount_percentage' => (int) $p->discount_percentage_effective,
            'image_url'      => $p->image_url ?? asset('img/saksershop-logo.png'),
            'product_url'    => route('product.show', $p),
            'store_name'     => $p->store->name ?? 'Official Store',
            'delete_url'     => route('customer.wishlist.destroy', $w),
            'cart_store_url' => route('customer.cart.store', $p),
        ];
    })->filter()->values();
@endphp

<nav x-data="navbarComponent()"
    @cart-updated.window="handleCartUpdated()"
    @wishlist-updated.window="handleWishlistUpdated($event)"
    class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 shadow-xs">

    <div class="page-container">
        <div class="flex items-center justify-between h-16 gap-4">

            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="NitipDong Home">
                <div class="w-9 h-9 rounded-xl overflow-hidden border border-cyan-200 bg-cyan-50 flex items-center justify-center shadow-xs p-1">
                    <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-slate-900 leading-none block">
                        Nitip<span class="text-cyan-600 font-black">Dong</span>
                    </span>
                    <span class="text-[9px] font-bold text-cyan-700 tracking-wider uppercase">Official Store &amp; Marketplace</span>
                </div>
            </a>

            <div class="flex-1 max-w-3xl hidden md:flex flex-col justify-center relative" @click.outside="showSuggestions = false">
                <form action="{{ url('/products') }}" method="GET" class="w-full flex items-center relative">
                    <div class="relative w-full flex items-center">
                        <input type="text" name="q" x-model="searchQuery"
                               @input.debounce.250ms="fetchSuggestions()"
                               @focus="if(searchQuery.trim().length >= 2) showSuggestions = true"
                               placeholder="Cari di NitipDong (contoh: iPhone, Sepatu Sneakers, Kopi Robusta, Skincare)..."
                               class="w-full h-10 pl-9 pr-24 rounded-xl border border-slate-200 bg-slate-50/70 text-xs focus:bg-white focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100 transition-all text-slate-800">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 text-xs" :class="isLoadingSuggestions ? 'animate-spin fa-spinner' : 'fa-magnifying-glass'"></i>
                        <div class="absolute right-1.5 flex items-center gap-1">
                            <span class="text-[10px] text-slate-400 font-mono hidden lg:inline-block px-1.5 py-0.5 bg-slate-200/60 rounded">Ctrl K</span>
                            <button type="submit" class="h-7 px-4 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-lg shadow-xs transition-colors cursor-pointer">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>

                <div x-show="showSuggestions" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-11 left-0 right-0 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs max-h-96 overflow-y-auto">

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

                    <template x-if="searchSuggestions.products && searchSuggestions.products.length > 0">
                        <div class="p-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Produk Terkait</span>
                            <div class="space-y-1 mt-1">
                                <template x-for="p in searchSuggestions.products" :key="p.id">
                                    <a :href="p.url" class="p-2 hover:bg-cyan-50/50 rounded-xl transition-colors flex items-center gap-2.5 block">
                                        <img :src="p.image_url" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.src='/img/saksershop-logo.png'">
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

                <a href="{{ route('app.download') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-cyan-200 bg-cyan-50/70 hover:bg-cyan-100 text-cyan-800 text-xs font-bold transition-all shadow-2xs group" title="Download Aplikasi NitipDong">
                    <i class="fa-solid fa-download text-sm text-cyan-600 group-hover:scale-110 transition-transform"></i>
                    <span>Download App</span>
                </a>

                <a href="{{ route('app.download') }}" class="md:hidden inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-800 text-[11px] font-bold shadow-2xs active:scale-95 transition-all shrink-0" title="Download Aplikasi NitipDong">
                    <i class="fa-solid fa-download text-[11px] text-cyan-600"></i>
                    <span>App</span>
                </a>

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
                    
                    <div class="relative" @click.outside="wishlistOpen = false">
                        <button type="button" id="nav-wishlist-btn" @click="wishlistOpen = !wishlistOpen" aria-label="Wishlist Saya"
                                class="btn-icon relative cursor-pointer transition-transform duration-300"
                                :class="wishlistBounce ? 'scale-125 text-rose-600' : ''"
                                title="Wishlist & Produk Favorit">
                            <i class="fa-solid fa-heart text-sm" :class="wishlistCount > 0 ? 'text-rose-500' : 'text-slate-600'"></i>
                            <span x-show="wishlistCount > 0"
                                  class="absolute top-1 right-1 min-w-[15px] h-3.5 px-0.5 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white transition-all"
                                  :class="wishlistBounce ? 'scale-125 ring-rose-200' : ''"
                                  x-text="wishlistCount > 99 ? '99+' : wishlistCount">
                            </span>
                        </button>

                        <div x-show="wishlistOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden text-xs">

                            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                                <div class="flex items-center gap-1.5 font-bold text-slate-900">
                                    <i class="fa-solid fa-heart text-rose-500"></i>
                                    <span>Wishlist Saya (<span x-text="wishlistCount"></span>)</span>
                                </div>
                                <a href="{{ route('customer.wishlist.index') }}" @click="wishlistOpen = false" class="text-[11px] font-semibold text-cyan-700 hover:text-cyan-800 hover:underline">
                                    Lihat Semua
                                </a>
                            </div>

                            <template x-if="wishlistItems.length > 0">
                                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto p-1.5 scrollbar-thin">
                                    <template x-for="item in wishlistItems" :key="item.id">
                                        <div class="p-2.5 hover:bg-slate-50/80 rounded-xl transition-colors flex items-center gap-2.5">
                                            <a :href="item.product_url" @click="wishlistOpen = false" class="shrink-0">
                                                <img :src="item.image_url" :alt="item.name" class="w-12 h-12 rounded-lg object-cover border border-slate-200" onerror="this.src='/img/saksershop-logo.png'">
                                            </a>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-1">
                                                    <a :href="item.product_url" @click="wishlistOpen = false" class="font-semibold text-slate-800 text-xs truncate hover:text-cyan-700 transition-colors block" x-text="item.name"></a>
                                                    <button type="button" @click="deleteWishlistItem(item)"
                                                            class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-1 rounded-lg transition-colors cursor-pointer" title="Hapus dari wishlist">
                                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                                <div class="flex items-center justify-between mt-1.5">
                                                    <span class="font-bold text-cyan-800 text-xs" x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                                    <button type="button" @click="addWishlistToCart(item)"
                                                            class="px-2.5 py-1 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-[10px] flex items-center gap-1 transition-all shadow-2xs cursor-pointer">
                                                        <i class="fa-solid fa-cart-plus text-[9px]"></i>
                                                        <span>+ Keranjang</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="wishlistItems.length === 0">
                                <div class="p-6 text-center text-slate-400">
                                    <i class="fa-regular fa-heart text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-semibold text-slate-700 text-xs">Wishlist Anda Masih Kosong</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Simpan produk impian Anda dengan menekan ikon hati.</p>
                                    <a href="{{ url('/products') }}" @click="wishlistOpen = false" class="mt-3 inline-block px-4 py-1.5 rounded-xl bg-cyan-700 text-white font-bold text-xs hover:bg-cyan-800 transition-colors">
                                        Cari Produk Sekarang
                                    </a>
                                </div>
                            </template>

                            <template x-if="wishlistItems.length > 0">
                                <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-500"><span x-text="wishlistCount"></span> Produk disimpan</span>
                                    <a href="{{ route('customer.wishlist.index') }}" @click="wishlistOpen = false" class="btn-primary h-8 px-4 text-xs font-bold bg-cyan-700 hover:bg-cyan-800 text-white rounded-xl shadow-2xs">
                                        Buka Wishlist &rarr;
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>

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

                        <div x-show="cartOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden text-xs">

                            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                                <div class="flex items-center gap-1.5 font-bold text-slate-900">
                                    <i class="fa-solid fa-cart-shopping text-cyan-600"></i>
                                    <span>Keranjang Belanja (<span x-text="cartCount"></span>)</span>
                                </div>
                                <a href="{{ route('customer.cart.index') }}" @click="cartOpen = false" class="text-[11px] font-semibold text-cyan-700 hover:text-cyan-800 hover:underline">
                                    Lihat Semua
                                </a>
                            </div>

                            <template x-if="cartItems.length > 0">
                                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto p-1">
                                    <template x-for="item in cartItems" :key="item.id">
                                        <div class="p-2.5 hover:bg-cyan-50/40 rounded-xl transition-colors flex items-center gap-2.5">
                                            <a :href="item.product_url" @click="cartOpen = false" class="shrink-0">
                                                <img :src="item.image_url" :alt="item.name" class="w-12 h-12 rounded-lg object-cover border border-slate-200" onerror="this.src='/img/saksershop-logo.png'">
                                            </a>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-1">
                                                    <a :href="item.product_url" @click="cartOpen = false" class="font-semibold text-slate-800 text-xs truncate hover:text-cyan-700 transition-colors block" x-text="item.name"></a>
                                                    <button type="button" @click="deleteCartItem(item)"
                                                            :disabled="isUpdatingCart"
                                                            class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-1 rounded-lg transition-colors cursor-pointer disabled:opacity-50" title="Hapus dari keranjang">
                                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                                <template x-if="item.variant">
                                                    <span class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded inline-block mt-0.5" x-text="item.variant"></span>
                                                </template>
                                                <div class="flex items-center justify-between mt-1.5">
                                                    
                                                    <div class="flex items-center rounded-lg border border-slate-200 bg-white overflow-hidden shadow-2xs">
                                                        <button type="button" @click="updateCartQty(item, item.quantity - 1)"
                                                                :disabled="isUpdatingCart"
                                                                class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors cursor-pointer disabled:opacity-50">
                                                            <i class="fa-solid fa-minus text-[9px]"></i>
                                                        </button>
                                                        <span class="w-7 text-center text-xs font-bold text-slate-800 select-none" x-text="item.quantity"></span>
                                                        <button type="button" @click="updateCartQty(item, item.quantity + 1)"
                                                                :disabled="isUpdatingCart || item.quantity >= item.stock"
                                                                class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors cursor-pointer disabled:opacity-50">
                                                            <i class="fa-solid fa-plus text-[9px]"></i>
                                                        </button>
                                                    </div>
                                                    <span class="font-bold text-cyan-800 text-xs" x-text="'Rp ' + Number(item.subtotal).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="cartItems.length === 0">
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
                            </template>

                            <template x-if="cartItems.length > 0">
                                <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-slate-500 font-medium">Total Perkiraan:</span>
                                        <span class="font-black text-sm text-cyan-800" x-text="'Rp ' + Number(cartSubtotal).toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('customer.cart.index') }}" @click="cartOpen = false"
                                           class="h-8 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                            Buka Keranjang
                                        </a>
                                        <a href="{{ route('customer.order.checkout') }}" @click="cartOpen = false"
                                           class="h-8 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs flex items-center justify-center shadow-xs transition-colors">
                                            <span>Checkout (</span><span x-text="cartCount"></span><span>)</span>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    @endif

                    <button type="button" @click="$dispatch('open-chat')" aria-label="Pesan Chat" class="btn-icon relative cursor-pointer" title="Pesan & Chat">
                        <i class="fa-regular fa-comment-dots text-sm text-slate-600"></i>
                    </button>

                    <x-notification-dropdown />

                    <div class="h-4 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                    <div class="relative" @click.outside="userOpen = false">
                        <button @click="userOpen = !userOpen" class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-slate-100 transition-colors">
                            @if(Auth::check())
                                <img src="{{ Auth::user()->avatar_url }}"
                                     class="w-6 h-6 rounded-full border border-cyan-200 object-cover" alt="{{ Auth::user()->name }}">
                                <div class="hidden sm:block text-left max-w-[110px]">
                                    <p class="text-xs font-semibold text-slate-800 leading-tight truncate">
                                        {{ Auth::user()->name }}
                                    </p>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 hidden sm:block transition-transform" :class="userOpen ? 'rotate-180' : ''"></i>
                            @else
                                <i class="fa-solid fa-user text-slate-500"></i>
                            @endif
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

                    <a href="{{ route('login') }}" class="btn-outline text-xs h-8 px-3 hidden md:inline-flex">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs h-8 px-3 hidden md:inline-flex">
                        Daftar
                    </a>
                @endauth

            </div>
        </div>

        <div class="pb-2.5 md:hidden">
            <form action="{{ url('/products') }}" method="GET" class="relative">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari di NitipDong (produk, toko, kategori)..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl text-xs pl-9 pr-16 h-9 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none text-slate-800 transition-all">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-xs"></i>
                <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 h-6.5 bg-cyan-600 hover:bg-cyan-700 text-white text-[11px] font-bold rounded-lg transition-colors shadow-2xs">
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
                <div class="ml-auto flex items-center gap-3 shrink-0">
                    <a href="{{ url('/products') }}?flash_sale=1" class="font-bold text-cyan-700 hover:text-cyan-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-amber-500 text-[10px]"></i>
                        Flash Sale
                    </a>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <a href="{{ route('app.landing') }}" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 hover:bg-cyan-50 text-[11px] font-bold text-slate-700 hover:text-cyan-700 transition-colors">
                        <i class="fa-brands fa-android text-cyan-600 text-xs"></i>
                        <span>APK Mobile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function navbarComponent() {
    return {
        mobileSearch: false,
        userOpen: false,
        notifOpen: false,
        cartOpen: false,
        cartCount: {{ (int) $initialCartCount }},
        cartItems: @json($initialCartList),
        cartBounce: false,
        isUpdatingCart: false,
        wishlistOpen: false,
        wishlistCount: {{ (int) $initialWishlistCount }},
        wishlistItems: @json($initialWishlistList),
        wishlistBounce: false,
        isUpdatingWishlist: false,
        searchQuery: @json(request('q', '')),
        searchSuggestions: { products: [], stores: [], categories: [] },
        showSuggestions: false,
        isLoadingSuggestions: false,
        handleCartUpdated() {
            this.fetchCartItems();
            this.cartBounce = true;
            this.cartOpen = true;
            setTimeout(() => { this.cartBounce = false; }, 1200);
            setTimeout(() => { this.cartOpen = false; }, 4000);
        },
        handleWishlistUpdated(e) {
            if (e && e.detail) {
                if (e.detail.total_count !== undefined) this.wishlistCount = e.detail.total_count;
                else if (e.detail.count !== undefined) this.wishlistCount = e.detail.count;
                
                if (e.detail.items !== undefined) this.wishlistItems = e.detail.items;
                else this.fetchWishlistItems();
            } else {
                this.fetchWishlistItems();
            }
            this.wishlistBounce = true;
            this.wishlistOpen = true;
            setTimeout(() => { this.wishlistBounce = false; }, 1200);
            setTimeout(() => { this.wishlistOpen = false; }, 4000);
        },
        async fetchWishlistItems() {
            try {
                const res = await fetch('{{ route('customer.wishlist.items') }}');
                if (res.ok) {
                    const data = await res.json();
                    this.wishlistItems = data.items || [];
                    this.wishlistCount = data.count || 0;
                }
            } catch (e) {
                console.error(e);
            }
        },
        async deleteWishlistItem(item) {
            if (this.isUpdatingWishlist) return;
            this.isUpdatingWishlist = true;
            try {
                const res = await fetch(item.delete_url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    this.wishlistItems = data.items || [];
                    this.wishlistCount = data.total_count || 0;
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { title: 'Wishlist Diperbarui', message: data.message || 'Produk dihapus dari wishlist.', type: 'info' }
                    }));
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isUpdatingWishlist = false;
            }
        },
        async addWishlistToCart(item) {
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('quantity', 1);

                const res = await fetch(item.cart_store_url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { title: 'Berhasil Masuk Keranjang', message: `1x ${item.name} berhasil ditambahkan!`, type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { title: 'Gagal', message: data.message || 'Gagal menambahkan ke keranjang.', type: 'error' }
                    }));
                }
            } catch (e) {
                console.error(e);
            }
        },
        async fetchSuggestions() {
            const q = this.searchQuery ? this.searchQuery.trim() : '';
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
                    const hasProducts = Array.isArray(this.searchSuggestions.products) && this.searchSuggestions.products.length > 0;
                    const hasStores = Array.isArray(this.searchSuggestions.stores) && this.searchSuggestions.stores.length > 0;
                    const hasCategories = Array.isArray(this.searchSuggestions.categories) && this.searchSuggestions.categories.length > 0;
                    this.showSuggestions = hasProducts || hasStores || hasCategories;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoadingSuggestions = false;
            }
        },
        async fetchCartItems() {
            try {
                const res = await fetch('{{ route('customer.cart.items') }}');
                if (res.ok) {
                    const data = await res.json();
                    this.cartItems = data.items || [];
                    this.cartCount = data.count || 0;
                    this.cartSubtotal = data.subtotal || 0;
                }
            } catch (e) {
                console.error(e);
            }
        },
        async updateCartQty(item, newQty) {
            if (this.isUpdatingCart) return;
            if (newQty < 1) {
                this.deleteCartItem(item);
                return;
            }
            if (item.stock && newQty > item.stock) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { title: 'Maksimum Stok', message: `Stok produk hanya tersedia ${item.stock} unit.`, type: 'error' }
                }));
                return;
            }
            this.isUpdatingCart = true;
            try {
                const res = await fetch(item.update_url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ quantity: newQty })
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    this.cartItems = data.items;
                    this.cartCount = data.count;
                    this.cartSubtotal = data.subtotal;
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { title: 'Gagal', message: data.message || 'Gagal mengubah jumlah barang.', type: 'error' }
                    }));
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isUpdatingCart = false;
            }
        },
        async deleteCartItem(item) {
            if (this.isUpdatingCart) return;
            this.isUpdatingCart = true;
            try {
                const res = await fetch(item.delete_url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    this.cartItems = data.items;
                    this.cartCount = data.count;
                    this.cartSubtotal = data.subtotal;
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { title: 'Dihapus', message: 'Barang berhasil dihapus dari keranjang.', type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { title: 'Gagal', message: data.message || 'Gagal menghapus barang.', type: 'error' }
                    }));
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isUpdatingCart = false;
            }
        }
    };
}
</script>
