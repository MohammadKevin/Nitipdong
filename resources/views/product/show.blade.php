<x-app-layout>
    <div class="bg-[#f5f5f5] min-h-screen pb-12">
        <div class="max-w-[1200px] mx-auto px-4 pt-4">

            {{-- Breadcrumb --}}
            <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap">
                <a href="/" class="hover:text-[#06b6d4] transition-colors">Beranda</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="#" class="hover:text-[#06b6d4] transition-colors">{{ $product->category->name ?? 'Kategori' }}</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-slate-600 truncate max-w-[200px]">{{ $product->name }}</span>
            </nav>

            {{-- Main Product Card --}}
            <div class="bg-white shadow-sm p-5 md:p-7 mb-4 flex flex-col lg:flex-row gap-8">

                {{-- Left: Image Gallery --}}
                <div class="w-full lg:w-[420px] shrink-0" x-data="{ activeImg: '{{ $product->image ? asset('storage/'.$product->image) : '' }}' }">
                    <div class="w-full aspect-square border border-slate-100 overflow-hidden bg-slate-50 mb-3 relative">
                        <img :src="activeImg || 'https://via.placeholder.com/420x420?text=No+Image'" class="w-full h-full object-cover" alt="{{ $product->name }}">
                        <span class="absolute top-2 left-2 bg-[#06b6d4] text-white text-[10px] font-bold px-2 py-0.5 rounded-sm">Mall</span>
                    </div>
                    {{-- Thumbnails --}}
                    <div class="flex gap-2">
                        @if($product->image)
                            <button @click="activeImg='{{ asset('storage/'.$product->image) }}'"
                                :class="activeImg === '{{ asset('storage/'.$product->image) }}' ? 'border-[#06b6d4]' : 'border-slate-200 hover:border-[#06b6d4]'"
                                class="w-14 h-14 border-2 overflow-hidden transition-colors rounded-sm">
                                <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover">
                            </button>
                        @endif
                        {{-- Placeholder thumbs --}}
                        <button class="w-14 h-14 border border-dashed border-slate-300 flex items-center justify-center text-slate-300 hover:border-[#06b6d4] transition-colors rounded-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>

                    {{-- Share --}}
                    <div class="mt-5 flex items-center gap-4 text-sm text-slate-500">
                        <span>Bagikan:</span>
                        <div class="flex gap-2">
                            <a href="#" class="w-7 h-7 rounded-full bg-[#1877f2] text-white flex items-center justify-center hover:opacity-80">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-7 h-7 rounded-full bg-[#25d366] text-white flex items-center justify-center hover:opacity-80">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </div>
                        <button class="ml-auto flex items-center gap-1.5 text-slate-500 hover:text-[#06b6d4] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            Favorit
                        </button>
                    </div>
                </div>

                {{-- Right: Product Details --}}
                <div class="flex-1 min-w-0">
                    {{-- Brand / Category Tag --}}
                    <p class="text-xs text-[#06b6d4] font-semibold uppercase tracking-wider mb-2">{{ $product->category->name ?? 'Produk' }}</p>
                    <h1 class="text-xl text-slate-900 font-semibold leading-snug mb-3">{{ $product->name }}</h1>

                    {{-- Ratings --}}
                    <div class="flex items-center gap-4 text-sm mb-5 pb-5 border-b border-slate-100">
                        <div class="flex items-center gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-slate-700 ml-1 font-medium">4.8</span>
                        </div>
                        <span class="text-slate-300">|</span>
                        <span class="text-slate-500">454 Penilaian</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-slate-500">2,5RB+ Terjual</span>
                    </div>

                    {{-- Price --}}
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-3xl font-bold text-[#06b6d4]">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="text-slate-400 line-through text-sm">Rp{{ number_format($product->price * 1.3, 0, ',', '.') }}</span>
                        <span class="bg-[#06b6d4] text-white text-xs font-bold px-2 py-0.5 rounded-sm">30% OFF</span>
                    </div>

                    {{-- Shipping Info --}}
                    <div class="space-y-3 mb-6 text-sm">
                        <div class="flex items-start gap-3">
                            <span class="w-24 text-slate-500 shrink-0">Pengiriman</span>
                            <div>
                                <div class="flex items-center gap-2 text-emerald-600 font-medium mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    Gratis Ongkir
                                </div>
                                <p class="text-xs text-slate-500">Estimasi tiba: 19-21 Agu • Ke Jakarta Pusat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-24 text-slate-500 shrink-0">Stok</span>
                            <span class="font-medium text-slate-700">Tersedia</span>
                        </div>
                    </div>

                    {{-- Add to Cart --}}
                    <form action="{{ route('customer.cart.store', $product) }}" method="POST">
                        @csrf

                        {{-- Quantity --}}
                        <div class="flex items-center gap-3 mb-6" x-data="{ qty: 1 }">
                            <span class="text-sm text-slate-500 w-24">Kuantitas</span>
                            <div class="flex items-center border border-slate-300 rounded-sm overflow-hidden">
                                <button type="button" @click="if(qty > 1) qty--" class="w-9 h-9 flex items-center justify-center text-slate-500 hover:bg-slate-100 border-r border-slate-300 text-xl font-light transition-colors">−</button>
                                <input type="number" name="quantity" :value="qty" x-model="qty" min="1" class="w-14 h-9 text-center border-none focus:ring-0 text-slate-700 text-sm p-0">
                                <button type="button" @click="qty++" class="w-9 h-9 flex items-center justify-center text-slate-500 hover:bg-slate-100 border-l border-slate-300 text-xl font-light transition-colors">+</button>
                            </div>
                            <span class="text-xs text-slate-400">Stok cukup</span>
                        </div>

                        {{-- CTA Buttons --}}
                        @auth
                            <div class="flex gap-3">
                                <button type="submit" name="action" value="cart"
                                    class="flex-1 h-12 border-2 border-[#06b6d4] text-[#06b6d4] font-semibold rounded-sm hover:bg-[#ecfeff] transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Masukkan Keranjang
                                </button>
                                <button type="submit" name="action" value="buy"
                                    class="flex-1 h-12 bg-[#06b6d4] hover:bg-[#0891b2] text-white font-semibold rounded-sm transition-colors shadow-sm">
                                    Beli Sekarang
                                </button>
                            </div>
                        @else
                            <div class="flex gap-3">
                                <a href="{{ route('login') }}"
                                    class="flex-1 h-12 border-2 border-[#06b6d4] text-[#06b6d4] font-semibold rounded-sm hover:bg-[#ecfeff] transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Masukkan Keranjang
                                </a>
                                <a href="{{ route('login') }}"
                                    class="flex-1 h-12 bg-[#06b6d4] hover:bg-[#0891b2] text-white font-semibold rounded-sm transition-colors flex items-center justify-center shadow-sm">
                                    Beli Sekarang
                                </a>
                            </div>
                        @endauth
                    </form>

                    {{-- BelanjaIn Guarantee --}}
                    <div class="mt-6 pt-5 border-t border-slate-100 flex items-center gap-3">
                        <svg class="w-6 h-6 text-[#06b6d4] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <div class="text-sm">
                            <span class="font-semibold text-slate-800">Garansi BelanjaIn</span>
                            <span class="text-slate-500 ml-1">— Dapatkan barang atau uang kembali penuh.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Store Info --}}
            <div class="bg-white shadow-sm p-5 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full border-2 border-[#06b6d4]/20 overflow-hidden shrink-0 bg-slate-100">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Toko') }}&background=ee4d2d&color=fff" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $product->store->name ?? 'Nama Toko' }}</h3>
                        <p class="text-xs text-slate-500 mb-2">Online • Aktif baru-baru ini</p>
                        <div class="flex gap-2">
                            <button class="text-xs px-3 py-1.5 border border-[#06b6d4] text-[#06b6d4] hover:bg-[#ecfeff] rounded-sm flex items-center gap-1 font-medium transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                Chat
                            </button>
                            <button class="text-xs px-3 py-1.5 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded-sm font-medium transition-colors">Lihat Toko</button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-x-8 gap-y-2 text-sm sm:border-l border-slate-200 sm:pl-8">
                    <div class="text-center"><p class="font-semibold text-slate-800">4.9</p><p class="text-xs text-slate-500">Rating</p></div>
                    <div class="text-center"><p class="font-semibold text-slate-800">2,5RB</p><p class="text-xs text-slate-500">Produk</p></div>
                    <div class="text-center"><p class="font-semibold text-slate-800">98%</p><p class="text-xs text-slate-500">Chat Dibalas</p></div>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white shadow-sm p-5 mb-4">
                <h2 class="font-semibold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100">Detail Produk</h2>
                <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm mb-4">
                    <div class="flex gap-2"><span class="text-slate-500 w-28 shrink-0">Kategori</span><span class="text-slate-700">{{ $product->category->name ?? '-' }}</span></div>
                    <div class="flex gap-2"><span class="text-slate-500 w-28 shrink-0">Kondisi</span><span class="text-slate-700">Baru</span></div>
                    <div class="flex gap-2"><span class="text-slate-500 w-28 shrink-0">Dikirim dari</span><span class="text-slate-700">Jakarta Pusat</span></div>
                </div>
                <h3 class="font-semibold text-slate-800 text-sm mb-3 pt-3 border-t border-slate-100">Deskripsi Produk</h3>
                <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->description ?? 'Tidak ada deskripsi produk.' }}</div>
            </div>

            {{-- Related Products --}}
            @if($storeProducts->count() > 0)
                <div class="bg-white shadow-sm p-5">
                    <h2 class="font-semibold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100">Produk Lain dari Toko Ini</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                        @foreach($storeProducts as $related)
                            <a href="{{ route('product.show', $related) }}" class="group border border-transparent hover:border-[#06b6d4] hover:-translate-y-0.5 transition-all">
                                <div class="aspect-square bg-slate-50 overflow-hidden">
                                    @if($related->image)
                                        <img src="{{ asset('storage/'.$related->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $related->name }}">
                                    @else
                                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300 text-xs">N/A</div>
                                    @endif
                                </div>
                                <div class="p-2">
                                    <p class="text-xs text-slate-700 line-clamp-2 leading-snug">{{ $related->name }}</p>
                                    <p class="text-sm font-semibold text-[#06b6d4] mt-1">Rp{{ number_format($related->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
