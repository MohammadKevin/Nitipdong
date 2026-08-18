<x-app-layout>
    <div class="bg-gradient-to-b from-slate-50 to-white min-h-screen pb-16">
        <div class="max-w-7xl mx-auto px-4 pt-6">

            {{-- Breadcrumb --}}
            <nav class="flex text-sm text-slate-500 mb-6 items-center gap-2 flex-wrap">
                <a href="/" class="hover:text-cyan-600 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Beranda
                </a>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="#" class="hover:text-cyan-600 transition-colors">{{ $product->category->name ?? 'Kategori' }}</a>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-slate-700 font-medium truncate max-w-[300px]">{{ $product->name }}</span>
            </nav>

            {{-- Main Product Card dengan Design Modern --}}
            <div class="bg-white shadow-xl rounded-3xl p-8 mb-6 border border-slate-100" x-data="productGallery()">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    {{-- Left: Image Gallery dengan Zoom --}}
                    <div class="space-y-4">
                        <!-- Main Image Display -->
                        <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 border-2 border-slate-100 group">
                            <img :src="activeImage" x-bind:alt="'{{ $product->name }}'" class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 group-hover:scale-110" />

                            <!-- Badge -->
                            @if($product->badge)
                                <span class="absolute top-4 left-4 px-4 py-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-black rounded-full shadow-lg">
                                    {{ strtoupper($product->badge) }}
                                </span>
                            @elseif($product->discount_percentage > 0)
                                <span class="absolute top-4 left-4 px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs font-black rounded-full shadow-lg animate-pulse">
                                    DISKON {{ $product->discount_percentage }}%
                                </span>
                            @endif

                            <!-- Favorite Heart -->
                            <button class="absolute top-4 right-4 w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center hover:scale-110 transition-all shadow-xl group/heart">
                                <svg class="w-6 h-6 text-slate-400 group-hover/heart:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                        </div>

                        <!-- Thumbnails Gallery -->
                        <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
                            <template x-for="(image, index) in images" :key="index">
                                <button @click="selectImage(index)"
                                    :class="{ 'gallery-thumb active': activeIndex === index, 'gallery-thumb': activeIndex !== index }"
                                    class="flex-shrink-0">
                                    <img :src="image" :alt="'{{ $product->name }} ' + (index + 1)" class="w-full h-full object-cover">
                                </button>
                            </template>

                            <!-- Placeholder for more images (isi foto nanti) -->
                            <button class="gallery-thumb border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center hover:bg-slate-100 transition-colors flex-shrink-0">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                        </div>

                        {{-- Share Section --}}
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <span class="text-sm font-semibold text-slate-700">Bagikan:</span>
                            <div class="flex gap-2">
                                <a href="#" class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center hover:scale-110 transition-all shadow-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                                <a href="#" class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 text-white flex items-center justify-center hover:scale-110 transition-all shadow-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                <a href="#" class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center hover:scale-110 transition-all shadow-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Product Details --}}
                    <div class="space-y-6">
                        {{-- Category Badge --}}
                        <div>
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-50 to-blue-50 text-cyan-600 text-xs font-bold uppercase tracking-wider rounded-xl border border-cyan-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $product->category->name ?? 'Produk' }}
                            </span>
                        </div>

                        <h1 class="text-3xl text-slate-900 font-bold leading-tight">{{ $product->name }}</h1>

                        {{-- Ratings & Sales --}}
                        <div class="flex items-center gap-6 pb-6 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="w-5 h-5 {{ $i < floor($product->rating) ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-slate-700 font-bold text-lg">{{ $product->rating }}</span>
                            </div>
                            <div class="w-px h-6 bg-slate-200"></div>
                            <div class="text-sm text-slate-600">
                                <span class="font-bold text-slate-800">{{ number_format($product->sold_count) }}+</span> Terjual
                            </div>
                        </div>

                        {{-- Price Section --}}
                        <div class="bg-gradient-to-br from-slate-50 to-cyan-50/30 p-6 rounded-2xl border border-cyan-100">
                            @if($product->discount_percentage > 0)
                                <div class="flex items-baseline gap-3 mb-2">
                                    <span class="text-sm text-slate-400 line-through">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                    <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-bold rounded-full">{{ $product->discount_percentage }}% OFF</span>
                                </div>
                                <div class="text-4xl font-black bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                                    Rp{{ number_format($product->getDiscountedPrice(), 0, ',', '.') }}
                                </div>
                            @else
                                <div class="text-4xl font-black bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        {{-- Shipping & Stock Info --}}
                        <div class="space-y-4 text-sm">
                            <div class="flex items-start gap-4">
                                <span class="w-32 text-slate-500 font-medium flex-shrink-0">Pengiriman</span>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 text-emerald-600 font-bold mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Gratis Ongkir
                                    </div>
                                    <p class="text-slate-600">Estimasi tiba: <span class="font-semibold">19-21 Agu</span> • Ke Jakarta Pusat</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="w-32 text-slate-500 font-medium flex-shrink-0">Stok</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="font-bold text-slate-800">{{ $product->stock }} Unit Tersedia</span>
                                </div>
                            </div>
                        </div>

                        {{-- Add to Cart Form --}}
                        <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="space-y-5 pt-6 border-t border-slate-100">
                            @csrf

                            {{-- Quantity Selector --}}
                            <div class="flex items-center gap-4" x-data="{ qty: 1 }">
                                <span class="text-sm font-semibold text-slate-700 w-32">Kuantitas</span>
                                <div class="flex items-center border-2 border-slate-300 rounded-xl overflow-hidden shadow-sm">
                                    <button type="button" @click="if(qty > 1) qty--" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-xl">−</button>
                                    <input type="number" name="quantity" :value="qty" x-model="qty" min="1" :max="{{ $product->stock }}" class="w-20 h-12 text-center border-none focus:ring-0 text-slate-800 font-bold bg-slate-50">
                                    <button type="button" @click="if(qty < {{ $product->stock }}) qty++" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-xl">+</button>
                                </div>
                                <span class="text-sm text-slate-500">Max: {{ $product->stock }}</span>
                            </div>

                            {{-- CTA Buttons --}}
                            @auth
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="submit" name="action" value="cart"
                                        class="h-14 border-3 border-cyan-500 text-cyan-600 font-bold rounded-xl hover:bg-cyan-50 transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Masukkan Keranjang
                                    </button>
                                    <button type="submit" name="action" value="buy"
                                        class="h-14 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Beli Sekarang
                                    </button>
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-4">
                                    <a href="{{ route('login') }}"
                                        class="h-14 border-3 border-cyan-500 text-cyan-600 font-bold rounded-xl hover:bg-cyan-50 transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Masukkan Keranjang
                                    </a>
                                    <a href="{{ route('login') }}"
                                        class="h-14 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Beli Sekarang
                                    </a>
                                </div>
                            @endauth
                        </form>

                        {{-- BelanjaIn Guarantee --}}
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-5 rounded-2xl border border-emerald-200 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-emerald-800 mb-1">Garansi BelanjaIn</h4>
                                <p class="text-sm text-emerald-700">100% uang kembali jika produk tidak sesuai atau cacat. Belanja aman dan terpercaya.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Store Info Card --}}
            <div class="bg-white shadow-lg rounded-3xl p-6 mb-6 border border-slate-100">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 rounded-2xl border-3 border-gradient-to-br from-cyan-200 to-blue-200 overflow-hidden flex-shrink-0 bg-slate-100 shadow-lg">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Toko') }}&background=0ea5e9&color=fff&size=128&bold=true" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-slate-800 mb-1">{{ $product->store->name ?? 'Nama Toko' }}</h3>
                            <div class="flex items-center gap-2 text-sm mb-3">
                                <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    Online
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-500">Aktif 5 menit lalu</span>
                            </div>
                            <div class="flex gap-3">
                                <button class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white rounded-xl flex items-center gap-2 font-bold transition-all shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    Chat Sekarang
                                </button>
                                <button class="px-5 py-2.5 border-2 border-slate-300 text-slate-700 hover:border-cyan-500 hover:text-cyan-600 rounded-xl font-bold transition-all">
                                    Kunjungi Toko
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-6 md:border-l border-slate-200 md:pl-8">
                        <div class="text-center">
                            <p class="font-black text-2xl bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">4.9</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">Rating Toko</p>
                        </div>
                        <div class="text-center">
                            <p class="font-black text-2xl bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">2.5K</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">Produk</p>
                        </div>
                        <div class="text-center">
                            <p class="font-black text-2xl bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">98%</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">Chat Dibalas</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Description --}}
            <div class="bg-white shadow-lg rounded-3xl p-8 mb-6 border border-slate-100">
                <h2 class="font-bold text-2xl text-slate-800 mb-6 pb-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-cyan-500 to-blue-600 rounded-full"></div>
                    Detail Produk
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <div>
                            <span class="text-sm text-slate-500">Kategori</span>
                            <p class="font-semibold text-slate-800">{{ $product->category->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="text-sm text-slate-500">Kondisi</span>
                            <p class="font-semibold text-slate-800">Baru</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div>
                            <span class="text-sm text-slate-500">Dikirim dari</span>
                            <p class="font-semibold text-slate-800">Jakarta Pusat</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <div>
                            <span class="text-sm text-slate-500">Berat</span>
                            <p class="font-semibold text-slate-800">500 gram</p>
                        </div>
                    </div>
                </div>

                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Deskripsi Produk
                </h3>
                <div class="prose max-w-none text-slate-600 leading-relaxed">
                    {{ $product->description ?? 'Tidak ada deskripsi produk.' }}
                </div>
            </div>

            {{-- Related Products dengan Horizontal Scroll --}}
            @if($storeProducts->count() > 0)
                <div class="bg-white shadow-lg rounded-3xl p-8 border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-bold text-2xl text-slate-800 flex items-center gap-3">
                            <div class="w-1 h-8 bg-gradient-to-b from-cyan-500 to-blue-600 rounded-full"></div>
                            Produk Lain dari Toko Ini
                        </h2>
                        <a href="#" class="text-cyan-600 font-semibold flex items-center gap-1 hover:gap-2 transition-all">
                            Lihat Semua
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>

                    <div data-carousel class="relative">
                        <button data-carousel-prev class="scroll-arrow scroll-arrow-left">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <div data-carousel-container class="scroll-container" style="cursor: grab;">
                            @foreach($storeProducts as $related)
                                <a href="{{ route('product.show', $related) }}" class="product-card group" style="min-width: 176px;">
                                    <div class="aspect-square bg-slate-50 overflow-hidden rounded-xl">
                                        @if($related->image)
                                            <img src="{{ asset('storage/'.$related->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $related->name }}">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <p class="text-sm text-slate-700 line-clamp-2 leading-snug mb-2">{{ $related->name }}</p>
                                        <p class="text-lg font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">Rp{{ number_format($related->price, 0, ',', '.') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <button data-carousel-next class="scroll-arrow scroll-arrow-right">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        function productGallery() {
            return {
                images: [],
                activeIndex: 0,
                activeImage: '',

                init() {
                    // Inisialisasi dengan gambar utama
                    @if($product->image)
                        this.images.push('{{ asset('storage/'.$product->image) }}');
                    @endif

                    // Tambahkan gambar tambahan jika ada
                    @if($product->images && is_array($product->images))
                        @foreach($product->images as $img)
                            this.images.push('{{ asset('storage/'.$img) }}');
                        @endforeach
                    @endif

                    // Set active image
                    if (this.images.length > 0) {
                        this.activeImage = this.images[0];
                    } else {
                        this.activeImage = 'https://via.placeholder.com/600x600?text=No+Image';
                        this.images.push(this.activeImage);
                    }
                },

                selectImage(index) {
                    this.activeIndex = index;
                    this.activeImage = this.images[index];
                }
            }
        }
    </script>
</x-app-layout>
