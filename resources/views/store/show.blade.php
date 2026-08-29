@section('title', $store->name . ' — Official Store NitipDong')
@section('og_title', $store->name . ' — Toko Online Terpercaya di NitipDong')
@section('og_description', Str::limit(strip_tags($store->description ?? 'Kunjungi toko resmi ' . $store->name . ' di NitipDong.'), 160, '...'))
@section('og_image', $store->logo ? (str_starts_with($store->logo, 'http') ? $store->logo : asset('storage/' . $store->logo)) : asset('icon-app-web-terbaru/nitipdong-icon-mark.svg'))

<x-app-layout>
    <div class="page-container py-6" x-data="{
        activeTab: 'products',
        copiedVoucher: null,
        copyCode(code) {
            navigator.clipboard.writeText(code);
            this.copiedVoucher = code;
            setTimeout(() => {
                if (this.copiedVoucher === code) this.copiedVoucher = null;
            }, 2000);
        }
    }">
        
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="hover:text-cyan-700 transition-colors flex items-center gap-1">
                <i class="fa-solid fa-house text-[10px]"></i> Beranda
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('products.index') }}" class="hover:text-cyan-700 transition-colors">Produk</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-800 font-semibold">{{ $store->name }}</span>
        </nav>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden mb-6">
            
            <div class="h-36 sm:h-48 bg-gradient-to-r from-cyan-800 via-cyan-900 to-slate-950 relative">
                @if($store->banner_url)
                    <img src="{{ $store->banner_url }}" alt="{{ $store->name }}" class="w-full h-full object-cover opacity-60">
                @else
                    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                @endif
            </div>

            <div class="p-5 sm:p-6 pt-0 relative">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 -mt-12 sm:-mt-16">
                    <div class="flex items-end gap-4">
                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-4 border-white shadow-lg overflow-hidden shrink-0 bg-cyan-700">
                            <img src="{{ $store->logo_url }}" class="w-full h-full object-cover" alt="{{ $store->name }}">
                        </div>
                        <div class="mb-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $store->name }}</h1>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-cyan-50 text-cyan-800 text-[11px] font-bold border border-cyan-200">
                                    <i class="fa-solid fa-circle-check text-cyan-600 text-[10px]"></i> Toko Resmi
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 max-w-xl line-clamp-2">{{ $store->description ?? 'Selamat datang di toko resmi ' . $store->name . ' di NitipDong. Temukan berbagai produk original berkualitas!' }}</p>
                            <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                <p class="text-[11px] text-slate-600 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-location-dot text-cyan-600"></i>
                                    <span>Dikirim dari: <strong class="text-slate-800">{{ $store->city ?: ($store->effective_city ?: 'Jakarta Pusat') }}</strong></span>
                                </p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                    <i class="fa-solid fa-truck-fast text-emerald-600"></i> Gratis Ongkir 1 Kota
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @auth
                            @if(Auth::id() !== $store->user_id)
                                <button type="button"
                                        @click="$dispatch('open-chat', { receiver_id: {{ $store->user_id }}, receiver_name: '{{ addslashes($store->name) }}' })"
                                        class="btn-primary text-xs h-9.5 px-4.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-2 shadow-xs transition-all cursor-pointer">
                                    <i class="fa-solid fa-comments text-xs"></i>
                                    <span>Chat Penjual</span>
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-primary text-xs h-9.5 px-4.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-2 shadow-xs transition-all">
                                <i class="fa-solid fa-comments text-xs"></i>
                                <span>Chat Penjual</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-5 border-t border-slate-100 text-center sm:text-left">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm shrink-0 border border-cyan-200">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-slate-900 block">{{ $products->total() }}</span>
                            <span class="text-[11px] text-slate-400">Total Produk</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0 border border-amber-200">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-slate-900 block flex items-center gap-1">
                                {{ number_format($avgRating, 1) }} <span class="text-xs text-slate-400 font-normal">({{ $totalReviewsCount }} ulasan)</span>
                            </span>
                            <span class="text-[11px] text-slate-400">Rating Toko</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm shrink-0 border border-emerald-200">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-slate-900 block">{{ $totalCompletedOrders }}</span>
                            <span class="text-[11px] text-slate-400">Pesanan Berhasil</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center text-sm shrink-0 border border-purple-200">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-slate-900 block">{{ $store->created_at->translatedFormat('M Y') }}</span>
                            <span class="text-[11px] text-slate-400">Bergabung Sejak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($activeVouchers->count() > 0)
            <div class="mb-6 bg-gradient-to-br from-cyan-900 to-slate-900 rounded-2xl p-5 text-white shadow-card">
                <div class="flex items-center justify-between mb-3.5">
                    <h3 class="font-bold text-sm text-white flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-cyan-400"></i> Voucher Promo Toko {{ $store->name }}
                    </h3>
                    <span class="text-xs text-cyan-200">Gunakan saat checkout</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($activeVouchers as $v)
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-3.5 border border-white/15 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="font-mono font-bold text-xs text-cyan-300 block tracking-wider">{{ $v->code }}</span>
                            <p class="text-xs font-semibold text-white mt-0.5 truncate">
                                @if($v->type === 'fixed')
                                    Diskon Rp {{ number_format($v->value, 0, ',', '.') }}
                                @else
                                    Diskon {{ $v->value }}%
                                @endif
                            </p>
                            <p class="text-[10px] text-slate-300">Min. Belanja Rp {{ number_format($v->min_purchase, 0, ',', '.') }}</p>
                        </div>
                        <button type="button" @click="copyCode('{{ $v->code }}')"
                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer"
                                :class="copiedVoucher === '{{ $v->code }}' ? 'bg-emerald-500 text-white' : 'bg-white text-slate-900 hover:bg-cyan-100'">
                            <span x-text="copiedVoucher === '{{ $v->code }}' ? 'Tersalin!' : 'Salin Kode'"></span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3 border-b border-slate-200 mb-6 text-sm font-semibold">
            <button type="button" @click="activeTab = 'products'"
                    :class="activeTab === 'products' ? 'border-cyan-700 text-cyan-700 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'"
                    class="pb-3 px-1 transition-all cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                <span>Semua Produk ({{ $products->total() }})</span>
            </button>
            <button type="button" @click="activeTab = 'reviews'"
                    :class="activeTab === 'reviews' ? 'border-cyan-700 text-cyan-700 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'"
                    class="pb-3 px-1 transition-all cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                <span>Ulasan Toko ({{ $totalReviewsCount }})</span>
            </button>
        </div>

        <div x-show="activeTab === 'products'">
            
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('store.show', $store) }}" method="GET" class="flex-1 flex items-center gap-2 flex-wrap">
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari produk di toko ini..."
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-cyan-600">
                    </div>

                    @if($storeCategories->count() > 0)
                        <select name="kategori" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-200 text-xs text-slate-700 focus:border-cyan-600">
                            <option value="">Semua Kategori</option>
                            @foreach($storeCategories as $cat)
                                <option value="{{ $cat->slug }}" {{ $categorySlug === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select name="sort" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-200 text-xs text-slate-700 focus:border-cyan-600">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Terlaris</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                    </select>

                    <button type="submit" class="btn-primary text-xs h-9 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold">
                        Filter
                    </button>

                    @if($search || $categorySlug || $sort !== 'latest')
                        <a href="{{ route('store.show', $store) }}" class="text-xs text-rose-600 hover:underline ml-1">Reset</a>
                    @endif
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 sm:gap-5">
                    @foreach($products as $product)
                    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-card hover:shadow-card-hover transition-all flex flex-col group">
                        <a href="{{ route('product.show', $product) }}" class="relative aspect-square bg-slate-50 overflow-hidden block">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                            @endif

                            @if($product->is_in_flash_sale)
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-900 text-cyan-200 border border-cyan-700/50 flex items-center gap-1">
                                    <i class="fa-solid fa-bolt text-cyan-400 text-[9px]"></i> Flash Sale
                                </span>
                            @elseif($product->has_discount)
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                    -{{ $product->discount_percentage_effective }}%
                                </span>
                            @endif
                        </a>

                        <div class="p-4 flex-1 flex flex-col justify-between space-y-2">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider block truncate">
                                    {{ $product->category->name ?? 'Kategori' }}
                                </span>
                                <a href="{{ route('product.show', $product) }}" class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-cyan-700 transition-colors line-clamp-2 mt-0.5">
                                    {{ $product->name }}
                                </a>
                            </div>

                            <div>
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-sm sm:text-base font-extrabold text-slate-900">
                                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    </span>
                                    @if($product->has_discount)
                                        <span class="text-[11px] text-slate-400 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[10px] text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-star text-amber-400"></i>
                                        <strong>{{ number_format($product->rating ?? 5.0, 1) }}</strong>
                                    </span>
                                    <span>Stok: {{ $product->stock }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80">
                    <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-3"></i>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Produk Ditemukan</h3>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                </div>
            @endif
        </div>

        <div x-show="activeTab === 'reviews'" class="space-y-4">
            @if($storeReviews->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($storeReviews as $rev)
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $rev->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="User">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">{{ $rev->is_anonymous ? 'Pembeli Anonim' : $rev->user->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $rev->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-star {{ $i <= $rev->rating ? 'fa-solid' : 'fa-regular text-slate-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-xs text-slate-700 mt-2 leading-relaxed">{{ $rev->comment ?: 'Pembeli tidak meninggalkan komentar tertulis.' }}</p>
                        @if($rev->product)
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center gap-2 text-[11px] text-slate-500">
                                <i class="fa-solid fa-bag-shopping text-cyan-700 text-xs"></i>
                                <span class="truncate">Produk: <strong>{{ $rev->product->name }}</strong></span>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80">
                    <i class="fa-solid fa-star text-4xl text-slate-300 mb-3"></i>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Ulasan Pembeli</h3>
                    <p class="text-xs text-slate-400 mt-1">Ulasan dari transaksi selesai akan ditampilkan di sini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
