<x-app-layout>
    <div class="page-container py-5">
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="/" class="hover:text-cyan-700 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ url('/products') }}" class="hover:text-cyan-700 transition-colors">Katalog</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ url('/products?category='.($product->category->slug ?? '')) }}" class="hover:text-cyan-700 transition-colors">
                {{ $product->category->name ?? 'Kategori' }}
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 bg-white p-6 sm:p-7 rounded-xl border border-slate-200/80 shadow-card mb-6 items-start">
            <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-20">
                <div class="relative w-full aspect-square rounded-lg overflow-hidden bg-slate-50 border border-slate-200">
                    @php
                        $allImages = array_filter(array_merge(
                            $product->image ? [$product->image] : [],
                            $product->images ?? []
                        ));
                    @endphp

                    @if(count($allImages))
                        @php
                            $firstImageUrl = str_starts_with($allImages[0], 'img/')
                                ? asset($allImages[0])
                                : asset('storage/' . $allImages[0]);
                        @endphp
                        <img src="{{ $firstImageUrl }}"
                             class="w-full h-full object-cover"
                             alt="{{ $product->name }}"
                             id="main-pdp-img">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-4xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif

                    @if($product->is_in_flash_sale)
                        <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded text-[11px] font-bold bg-cyan-900 text-cyan-200 flex items-center gap-1 border border-cyan-700/50 shadow-sm">
                            <i class="fa-solid fa-bolt text-cyan-400"></i> Flash Sale
                        </span>
                    @elseif($product->has_discount)
                        <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded text-[11px] font-bold bg-rose-600 text-white shadow-sm">
                            -{{ $product->discount_percentage_effective }}% OFF
                        </span>
                    @endif
                </div>

                {{-- Thumbnail strip --}}
                @if(count($allImages) > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($allImages as $i => $imgPath)
                    @php
                        $thumbUrl = str_starts_with($imgPath, 'img/')
                            ? asset($imgPath)
                            : asset('storage/' . $imgPath);
                    @endphp
                    <button type="button"
                        onclick="document.getElementById('main-pdp-img').src='{{ $thumbUrl }}'; document.querySelectorAll('.pdp-thumb').forEach(el => el.classList.remove('ring-2','ring-cyan-600')); this.classList.add('ring-2','ring-cyan-600');"
                        class="pdp-thumb shrink-0 w-14 h-14 rounded-md overflow-hidden border border-slate-200 bg-white {{ $i === 0 ? 'ring-2 ring-cyan-600' : '' }}">
                        <img src="{{ $thumbUrl }}" class="w-full h-full object-cover" alt="Foto {{ $i+1 }}">
                    </button>
                    @endforeach
                </div>
                @endif

                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Official Store') }}&background=0891b2&color=fff"
                             class="w-9 h-9 rounded-md border border-slate-200 object-cover shrink-0" alt="Store">
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">{{ $product->store->name ?? 'Official Store' }}</h4>
                            <span class="inline-flex items-center gap-1 text-[10px] text-cyan-800 font-semibold">
                                <i class="fa-solid fa-certificate text-[9px] text-cyan-600"></i> Verified Official Partner
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('chat.index', ['store_id' => $product->store_id]) }}" class="btn-outline text-xs h-7.5 px-3 shrink-0 flex items-center gap-1.5 rounded-md">
                        <i class="fa-regular fa-comment-dots text-xs"></i> Chat Toko
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
                        <span class="text-xs font-bold text-cyan-700 uppercase tracking-wider bg-cyan-50 px-2 py-0.5 rounded border border-cyan-200">
                            {{ $product->category->name ?? 'Produk Esensial' }}
                        </span>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mt-2 leading-tight tracking-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-500 pb-3 border-b border-slate-100 flex-wrap">
                        <div class="flex items-center gap-1 text-amber-500 font-semibold">
                            <i class="fa-solid fa-star text-xs"></i>
                            <span class="text-slate-900 font-bold">{{ number_format($product->rating ?? 5.0, 1) }}</span>
                            <span class="text-slate-400 font-normal">({{ $product->reviews->count() }} ulasan pembeli)</span>
                        </div>
                        <span class="text-slate-300">•</span>
                        <span>Terjual <strong class="text-slate-800 font-medium">{{ $product->sold_count ?? 0 }}+ unit</strong></span>
                        <span class="text-slate-300">•</span>
                        @if($product->stock > 0)
                            <span class="text-cyan-800 font-medium flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-600 inline-block"></span> Stok Siap Kirim ({{ $product->stock }})
                            </span>
                        @else
                            <span class="text-rose-600 font-medium flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 inline-block"></span> Stok Habis
                            </span>
                        @endif
                    </div>

                    @if($product->is_in_flash_sale)
                        <div class="p-4 rounded-lg bg-slate-900 text-white flex items-center justify-between gap-4 border border-slate-800">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-cyan-300 font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-bolt text-[10px]"></i> Sesi Flash Sale
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                        Diskon {{ $product->discount_percentage_effective }}%
                                    </span>
                                </div>
                                <div class="flex items-baseline gap-2.5 mt-1.5">
                                    <span class="text-2xl font-extrabold text-white">
                                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-slate-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block uppercase tracking-wider">Kuota Sisa</span>
                                <span class="text-sm font-bold text-cyan-300">{{ $product->current_flash_sale_item->stock_remaining ?? $product->stock }} Unit</span>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/80 flex items-baseline gap-3">
                            <span class="text-2xl font-extrabold text-slate-900">
                                Rp {{ number_format($product->final_price, 0, ',', '.') }}
                            </span>
                            @if($product->has_discount)
                                <span class="text-xs text-slate-400 line-through">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                    -{{ $product->discount_percentage_effective }}%
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Jumlah Pembelian
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="inline-flex items-center rounded-md border border-slate-300 bg-white">
                                <button type="button" @click="if(qty > 1) qty--" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <input type="number" x-model.number="qty" min="1" :max="stock" class="w-12 text-center text-xs font-bold text-slate-900 border-none focus:ring-0 p-0 h-8">
                                <button type="button" @click="if(qty < stock) qty++" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                            <span class="text-xs text-slate-500">
                                Subtotal Barang: <strong class="text-slate-900 font-bold" x-text="formatRupiah(qty * price)"></strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex flex-col sm:flex-row gap-3">
                        @auth
                            @if(auth()->user()->role === 'customer')
                                @php $isWishlisted = $product->isWishlistedBy(auth()->user()); @endphp
                                <form action="{{ route('customer.wishlist.toggle', $product) }}" method="POST" class="shrink-0">
                                    @csrf
                                    <button type="submit" title="{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                                            class="w-10 h-10 rounded-md border {{ $isWishlisted ? 'border-rose-300 bg-rose-50 text-rose-600' : 'border-slate-300 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50' }} flex items-center justify-center text-base transition-colors shadow-xs">
                                        <i class="{{ $isWishlisted ? 'fa-solid text-rose-600' : 'fa-regular' }} fa-heart"></i>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" title="Masuk untuk simpan ke Wishlist"
                               class="w-10 h-10 rounded-md border border-slate-300 text-slate-400 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 flex items-center justify-center text-base transition-colors shrink-0">
                                <i class="fa-regular fa-heart"></i>
                            </a>
                        @endauth

                        <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="quantity" :value="qty">
                            <button type="submit" @if($product->stock <= 0) disabled @endif
                                    class="w-full h-10 rounded-md border border-cyan-700 text-cyan-800 hover:bg-cyan-50 text-xs font-semibold transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-cart-plus"></i>
                                + Keranjang Belanja
                            </button>
                        </form>

                        <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="quantity" :value="qty">
                            <input type="hidden" name="action" value="buy">
                            <button type="submit" @if($product->stock <= 0) disabled @endif
                                    class="w-full btn-primary h-10 text-xs flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-bag-shopping"></i>
                                Beli Sekarang
                            </button>
                        </form>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5 pt-1 text-center text-[11px] text-slate-500">
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-shield-halved text-cyan-600 mb-1 text-xs block"></i>
                            100% Produk Original
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-truck-fast text-cyan-600 mb-1 text-xs block"></i>
                            Ekstra Bebas Ongkir
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-rotate-left text-cyan-600 mb-1 text-xs block"></i>
                            Garansi 7 Hari Retur
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deskripsi Produk --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-6 sm:p-7 shadow-card mb-6">
            <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Deskripsi & Spesifikasi Produk</h3>
            <div class="text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                {{ $product->description }}
            </div>
        </div>

        {{-- Ulasan & Penilaian Pembeli --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-6 sm:p-7 shadow-card mb-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Ulasan & Penilaian Pembeli</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Ulasan asli dari pelanggan terverifikasi BelanjaIn</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-amber-50 text-amber-800 rounded-full border border-amber-200">
                    <i class="fa-solid fa-star text-amber-500 text-[10px] mr-1"></i> {{ number_format($product->rating ?? 5.0, 1) }} / 5.0
                </span>
            </div>

            @php
                $totalReviews = $product->reviews->count();
                $starCounts = [
                    5 => $product->reviews->where('rating', 5)->count(),
                    4 => $product->reviews->where('rating', 4)->count(),
                    3 => $product->reviews->where('rating', 3)->count(),
                    2 => $product->reviews->where('rating', 2)->count(),
                    1 => $product->reviews->where('rating', 1)->count(),
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pb-6 border-b border-slate-100">
                {{-- Score Box --}}
                <div class="md:col-span-4 bg-slate-50/80 rounded-xl p-5 border border-slate-200/80 text-center flex flex-col items-center justify-center">
                    <span class="text-4xl sm:text-5xl font-black text-slate-900 leading-none">
                        {{ number_format($product->rating ?? 5.0, 1) }}
                    </span>
                    <div class="flex items-center gap-1 text-amber-400 text-sm mt-2">
                        @for($s = 1; $s <= 5; $s++)
                            <i class="fa-solid fa-star {{ $s <= round($product->rating ?? 5) ? 'text-amber-400' : 'text-slate-300' }}"></i>
                        @endfor
                    </div>
                    <span class="text-xs text-slate-500 mt-1 font-medium">Berdasarkan {{ $totalReviews }} ulasan pembeli</span>
                </div>

                {{-- Rating Bars --}}
                <div class="md:col-span-8 space-y-2 flex flex-col justify-center">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $cnt = $starCounts[$star];
                            $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-bold text-slate-700 w-8 flex items-center gap-1">
                                {{ $star }} <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                            </span>
                            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-12 text-right text-slate-400 font-mono text-[11px]">{{ $cnt }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Reviews List --}}
            @if($totalReviews > 0)
                <div class="divide-y divide-slate-100 mt-4">
                    @foreach($product->reviews as $review)
                        <div class="py-5 space-y-2.5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($review->is_anonymous ? 'Pengguna Anonim' : ($review->user->name ?? 'Pembeli')) }}&background=0891b2&color=fff"
                                         class="w-9 h-9 rounded-full object-cover border border-slate-200" alt="User">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900">
                                            @if($review->is_anonymous)
                                                {{ Str::mask($review->user->name ?? 'User', '*', 1, -1) }} (Anonim)
                                            @else
                                                {{ $review->user->name ?? 'Pembeli BelanjaIn' }}
                                            @endif
                                        </h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <div class="flex items-center text-amber-400 text-[10px]">
                                                @for($r = 1; $r <= 5; $r++)
                                                    <i class="fa-solid fa-star {{ $r <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-[10px] text-slate-400">• {{ $review->created_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Pembeli Terverifikasi
                                </span>
                            </div>

                            @if($review->comment)
                                <p class="text-xs text-slate-700 leading-relaxed">
                                    {{ $review->comment }}
                                </p>
                            @endif

                            {{-- Review Photos Gallery --}}
                            @if(!empty($review->images) && is_array($review->images))
                                <div class="flex gap-2 pt-1 overflow-x-auto">
                                    @foreach($review->images as $revImg)
                                        <a href="{{ asset('storage/' . $revImg) }}" target="_blank"
                                           class="w-16 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 shrink-0 hover:opacity-90 transition-opacity">
                                            <img src="{{ asset('storage/' . $revImg) }}" class="w-full h-full object-cover" alt="Foto Ulasan">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Seller Reply Box --}}
                            @if($review->seller_reply)
                                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                                    <div class="flex items-center gap-1.5 font-bold text-cyan-800 text-[11px]">
                                        <i class="fa-solid fa-reply text-cyan-600 text-[10px]"></i>
                                        <span>Respon dari Penjual ({{ $product->store->name ?? 'Toko' }})</span>
                                        @if($review->seller_replied_at)
                                            <span class="text-[10px] text-slate-400 font-normal">• {{ $review->seller_replied_at->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-slate-600 text-xs pl-4">
                                        {{ $review->seller_reply }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center text-slate-400">
                    <i class="fa-regular fa-comment-dots text-3xl mb-2 text-slate-300"></i>
                    <h4 class="text-xs font-bold text-slate-700">Belum Ada Ulasan Pembeli</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Jadilah orang pertama yang membeli dan memberikan ulasan untuk produk ini!</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
