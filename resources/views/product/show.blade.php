<x-app-layout>
    <div class="page-container py-6">
        <nav class="flex text-xs text-slate-400 mb-5 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="/" class="hover:text-emerald-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <a href="{{ url('/products') }}" class="hover:text-emerald-600 transition-colors">Katalog</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <a href="{{ url('/products?category='.($product->category->slug ?? '')) }}" class="hover:text-emerald-600 transition-colors">
                {{ $product->category->name ?? 'Kategori' }}
            </a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-700 font-medium truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs mb-8">
            <div class="lg:col-span-5 space-y-4">
                <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-50 border border-slate-200">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}" id="main-pdp-img">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-5xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif

                    @if($product->is_in_flash_sale)
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-400 text-slate-950 flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-bolt"></i> Flash Sale
                        </span>
                    @elseif($product->has_discount)
                        <span class="absolute top-3 left-3 px-2 py-1 rounded-md text-xs font-bold bg-rose-600 text-white shadow-sm">
                            -{{ $product->discount_percentage_effective }}% OFF
                        </span>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Official Store') }}&background=059669&color=fff"
                             class="w-10 h-10 rounded-xl border border-slate-200 object-cover shrink-0" alt="Store">
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">{{ $product->store->name ?? 'Toko Resmi BelanjaIn' }}</h4>
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-medium">
                                <i class="fa-solid fa-shield-check text-[10px]"></i> Official Seller
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('chat.index', ['store_id' => $product->store_id]) }}" class="btn-outline text-xs px-3 py-1.5 shrink-0 flex items-center gap-1.5">
                        <i class="fa-regular fa-comment-dots"></i> Chat Toko
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7 flex flex-col justify-between space-y-6"
                 x-data="{
                    qty: 1,
                    stock: {{ $product->stock }},
                    price: {{ $product->final_price }},
                    formatRupiah(num) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                    }
                 }">
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">
                            {{ $product->category->name ?? 'Umum' }}
                        </span>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 leading-snug tracking-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-500 pb-3 border-b border-slate-100 flex-wrap">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star"></i>
                            <span>5.0</span>
                            <span class="text-slate-400 font-normal">(120 ulasan)</span>
                        </div>
                        <span class="text-slate-300">•</span>
                        <span>Terjual <strong class="text-slate-700 font-semibold">150+ unit</strong></span>
                        <span class="text-slate-300">•</span>
                        @if($product->stock > 0)
                            <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-xs text-emerald-500"></i> Stok Tersedia ({{ $product->stock }} unit)
                            </span>
                        @else
                            <span class="text-rose-600 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-circle-xmark text-xs"></i> Stok Habis
                            </span>
                        @endif
                    </div>

                    @if($product->is_in_flash_sale)
                        <div class="p-4 rounded-xl bg-slate-900 text-white flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-amber-400 font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-bolt"></i> Harga Flash Sale
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                        Hemat {{ $product->discount_percentage_effective }}%
                                    </span>
                                </div>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-2xl font-extrabold text-white">
                                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-slate-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Sisa Kuota Promo</span>
                                <span class="text-xs font-bold text-amber-400">{{ $product->current_flash_sale_item->stock_remaining ?? $product->stock }} Unit</span>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-baseline gap-3">
                            <span class="text-2xl font-extrabold text-slate-900">
                                Rp {{ number_format($product->final_price, 0, ',', '.') }}
                            </span>
                            @if($product->has_discount)
                                <span class="text-sm text-slate-400 line-through">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                    Potongan {{ $product->discount_percentage_effective }}%
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Jumlah Pembelian
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="inline-flex items-center rounded-lg border border-slate-300 bg-white p-1">
                                <button type="button" @click="if(qty > 1) qty--" class="w-7 h-7 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                <input type="number" x-model.number="qty" min="1" :max="stock" class="w-12 text-center text-xs font-bold text-slate-900 border-none focus:ring-0 p-0">
                                <button type="button" @click="if(qty < stock) qty++" class="w-7 h-7 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                            <span class="text-xs text-slate-500">
                                Subtotal: <strong class="text-slate-900 font-bold" x-text="formatRupiah(qty * price)"></strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="quantity" :value="qty">
                            <button type="submit" @if($product->stock <= 0) disabled @endif
                                    class="w-full py-3 px-4 rounded-xl border border-emerald-600 text-emerald-700 hover:bg-emerald-50 text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-xs">
                                <i class="fa-solid fa-cart-plus"></i>
                                + Keranjang
                            </button>
                        </form>

                        <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="quantity" :value="qty">
                            <input type="hidden" name="action" value="buy">
                            <button type="submit" @if($product->stock <= 0) disabled @endif
                                    class="w-full btn-primary py-3 px-4 text-xs sm:text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-bag-shopping"></i>
                                Beli Sekarang
                            </button>
                        </form>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-2 text-center text-[10px] text-slate-500">
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-shield-halved text-emerald-600 mb-1 text-xs block"></i>
                            Jaminan Original
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-truck-fast text-emerald-600 mb-1 text-xs block"></i>
                            Pengiriman Cepat
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-rotate-left text-emerald-600 mb-1 text-xs block"></i>
                            7 Hari Retur
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Deskripsi Lengkap Produk</h3>
            <div class="prose max-w-none text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                {{ $product->description }}
            </div>
        </div>
    </div>
</x-app-layout>
