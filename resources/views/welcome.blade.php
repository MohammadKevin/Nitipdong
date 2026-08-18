<x-app-layout>
    @if(session('success'))
        <div class="page-container mt-4">
            <div class="flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <section class="page-container pt-4 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 relative rounded-2xl overflow-hidden bg-slate-900 shadow-sm border border-slate-200/80 min-h-[280px] sm:min-h-[320px] flex items-center"
                 x-data="{
                    active: 0,
                    slides: [
                        {
                            title: 'Pesta Diskon Gadget & Elektronik Terkini',
                            subtitle: 'Dapatkan potongan harga spesial hingga 70% dengan garansi resmi 100%.',
                            badge: 'Promo Spesial Hari Ini',
                            img: 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?q=80&w=2001&auto=format&fit=crop',
                            url: '{{ url('/products') }}'
                        },
                        {
                            title: 'Koleksi Fashion Musim Ini Terbaru',
                            subtitle: 'Gaya modern, bahan premium, dan gratis ongkir ke seluruh Indonesia.',
                            badge: 'Official Brand Festival',
                            img: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop',
                            url: '{{ url('/products') }}'
                        }
                    ],
                    timer: null,
                    init() {
                        this.timer = setInterval(() => {
                            this.active = (this.active + 1) % this.slides.length;
                        }, 5000);
                    }
                 }">
                
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="active === index"
                         x-transition:enter="transition-opacity duration-700"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <img :src="slide.img" class="w-full h-full object-cover opacity-40 scale-105 transition-transform duration-1000" alt="Banner">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-900/60 to-transparent p-6 sm:p-10 flex flex-col justify-center max-w-lg">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 w-fit mb-3">
                                <i class="fa-solid fa-sparkles"></i>
                                <span x-text="slide.badge"></span>
                            </span>
                            <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight" x-text="slide.title"></h2>
                            <p class="text-xs sm:text-sm text-slate-300 mt-2 mb-6" x-text="slide.subtitle"></p>
                            <div>
                                <a :href="slide.url" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs sm:text-sm font-semibold shadow-sm transition-colors">
                                    Belanja Sekarang
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="absolute bottom-4 right-6 flex items-center gap-1.5 z-20">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="active = index"
                                class="h-1.5 rounded-full transition-all duration-300"
                                :class="active === index ? 'w-6 bg-emerald-400' : 'w-2 bg-white/40'"></button>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            Gratis Ongkir
                        </span>
                        <h3 class="text-sm font-bold text-slate-900 mt-2 leading-snug">Voucher Ekstra Ongkir Rp0 Tanpa Syarat</h3>
                        <p class="text-xs text-slate-500 mt-1">Klaim kupon pengiriman hemat untuk semua toko.</p>
                    </div>
                    <div class="mt-4 relative z-10">
                        <a href="{{ url('/products') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                            Klaim Voucher <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-900 text-white shadow-xs flex flex-col justify-between relative overflow-hidden group border border-slate-800">
                    <div class="relative z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">
                            Mitra Penjual
                        </span>
                        <h3 class="text-sm font-bold text-white mt-2 leading-snug">Punya Usaha? Buka Toko Gratis di BelanjaIn</h3>
                        <p class="text-xs text-slate-400 mt-1">Jangkau jutaan calon pembeli di seluruh Indonesia.</p>
                    </div>
                    <div class="mt-4 relative z-10">
                        <a href="{{ route('store.register') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 inline-flex items-center gap-1">
                            Daftar Toko Sekarang <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 pt-4 border-t border-slate-200/60">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-800 leading-tight">Gratis Ongkir</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">Seluruh wilayah Nusantara</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-800 leading-tight">100% Original</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">Garansi resmi toko terpercaya</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-800 leading-tight">Garansi Retur</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">Komplain mudah & tepat waktu</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-800 leading-tight">Bantuan 24/7</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">Pusat solusi customer care</p>
                </div>
            </div>
        </div>
    </section>

    @if(isset($categories) && $categories->count() > 0)
    <section class="page-container py-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Kategori Pilihan</h2>
                    <p class="text-xs text-slate-500">Temukan barang kebutuhanmu berdasarkan kelompok kategori</p>
                </div>
                <a href="{{ url('/products') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                @foreach($categories->take(8) as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                   class="flex flex-col items-center text-center p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/40 hover:border-emerald-200 transition-all group">
                    <div class="w-11 h-11 rounded-xl bg-white border border-slate-200/60 shadow-xs flex items-center justify-center text-slate-600 group-hover:text-emerald-600 text-base mb-2 group-hover:scale-105 transition-all">
                        @if($category->icon)
                            <i class="{{ $category->icon }}"></i>
                        @else
                            <i class="fa-solid fa-tag"></i>
                        @endif
                    </div>
                    <span class="text-xs font-medium text-slate-700 group-hover:text-emerald-700 transition-colors line-clamp-1">
                        {{ $category->name }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(isset($activeFlashSale) && $activeFlashSale && $activeFlashSale->items->count() > 0)
    <section class="page-container py-4"
             x-data="{
                secondsRemaining: {{ $activeFlashSale->remaining_seconds }},
                hours: '00',
                minutes: '00',
                seconds: '00',
                init() {
                    this.updateTime();
                    setInterval(() => {
                        if (this.secondsRemaining > 0) {
                            this.secondsRemaining--;
                            this.updateTime();
                        }
                    }, 1000);
                },
                updateTime() {
                    let h = Math.floor(this.secondsRemaining / 3600);
                    let m = Math.floor((this.secondsRemaining % 3600) / 60);
                    let s = this.secondsRemaining % 60;
                    this.hours = String(h).padStart(2, '0');
                    this.minutes = String(m).padStart(2, '0');
                    this.seconds = String(s).padStart(2, '0');
                }
             }">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-5 text-white border border-slate-800 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-700/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-extrabold text-white tracking-tight">{{ $activeFlashSale->name }}</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse">LIVE</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Penawaran harga terbaik dengan stok kuota sangat terbatas</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-slate-950/60 px-3.5 py-2 rounded-xl border border-slate-700/60">
                    <span class="text-[11px] text-slate-300 font-medium">Berakhir dalam:</span>
                    <div class="flex items-center gap-1 font-mono font-bold text-xs">
                        <span class="px-2 py-1 rounded bg-slate-800 text-amber-400" x-text="hours">00</span>
                        <span class="text-slate-500">:</span>
                        <span class="px-2 py-1 rounded bg-slate-800 text-amber-400" x-text="minutes">00</span>
                        <span class="text-slate-500">:</span>
                        <span class="px-2 py-1 rounded bg-slate-800 text-amber-400" x-text="seconds">00</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 pt-4">
                @foreach($activeFlashSale->items->take(6) as $fsItem)
                    @php $product = $fsItem->product; @endphp
                    @if($product)
                    <a href="{{ route('product.show', $product) }}"
                       class="bg-white rounded-xl overflow-hidden border border-slate-200/40 p-2.5 flex flex-col justify-between group hover:border-emerald-400 transition-all text-slate-900 shadow-xs">
                        <div>
                            <div class="relative w-full aspect-square rounded-lg overflow-hidden bg-slate-100 mb-2">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-xl">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                @endif
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white shadow-xs">
                                    -{{ $fsItem->discount_percentage }}%
                                </span>
                            </div>

                            <h3 class="text-xs font-medium text-slate-800 line-clamp-2 leading-snug group-hover:text-emerald-700 transition-colors">
                                {{ $product->name }}
                            </h3>

                            <div class="mt-2">
                                <p class="text-sm font-bold text-rose-600">
                                    Rp {{ number_format($fsItem->flash_sale_price, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-slate-400 line-through">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                <div class="bg-rose-500 h-full rounded-full" style="width: {{ $fsItem->sold_percentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[9px] text-slate-500 mt-1 font-medium">
                                <span>Terjual {{ $fsItem->stock_sold }}</span>
                                <span>Sisa {{ $fsItem->stock_remaining }}</span>
                            </div>
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="page-container py-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900">Rekomendasi Produk Pilihan</h2>
                <p class="text-xs text-slate-500">Temukan barang berkualitas dari toko resmi & terverifikasi</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url('/products') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    Lihat Semua Produk &rarr;
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
            @forelse($products as $prod)
            <div class="product-card group">
                <a href="{{ route('product.show', $prod) }}" class="block">
                    <div class="product-img-box">
                        @if($prod->image)
                            <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $prod->name }}" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        @endif

                        @if($prod->has_discount)
                            <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white shadow-xs">
                                -{{ $prod->discount_percentage_effective }}%
                            </span>
                        @endif

                        @if($prod->is_in_flash_sale)
                            <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-400 text-slate-950 flex items-center gap-1 shadow-xs">
                                <i class="fa-solid fa-bolt"></i> Flash Sale
                            </span>
                        @endif
                    </div>
                </a>

                <div class="p-3.5 flex flex-col justify-between flex-1 gap-2">
                    <div>
                        <div class="flex items-center gap-1 text-[10px] text-slate-400 mb-1 truncate">
                            <i class="fa-solid fa-store text-[9px] text-emerald-600"></i>
                            <span class="truncate">{{ $prod->store->name ?? 'Toko Resmi' }}</span>
                        </div>

                        <a href="{{ route('product.show', $prod) }}" class="text-xs font-medium text-slate-800 line-clamp-2 hover:text-emerald-600 transition-colors leading-snug">
                            {{ $prod->name }}
                        </a>
                    </div>

                    <div>
                        <div class="flex flex-col">
                            <span class="text-sm sm:text-base font-bold text-slate-900">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </span>
                            @if($prod->has_discount)
                                <span class="text-[10px] text-slate-400 line-through">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100 mt-2">
                            <div class="flex items-center gap-1 text-amber-500 font-semibold">
                                <i class="fa-solid fa-star text-[10px]"></i>
                                <span>5.0</span>
                            </div>
                            <span class="text-slate-400 text-[10px]">Terjual 50+</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada produk yang tersedia</p>
            </div>
            @endforelse
        </div>
    </section>

    @if(isset($vouchers) && $vouchers->count() > 0)
    <section class="page-container py-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Kupon & Voucher Belanja</h2>
                    <p class="text-xs text-slate-500">Gunakan kupon saat checkout untuk mendapatkan potongan harga ekstra</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                 x-data="{
                    copiedCode: null,
                    copyCode(code) {
                        navigator.clipboard.writeText(code);
                        this.copiedCode = code;
                        setTimeout(() => this.copiedCode = null, 2000);
                    }
                 }">
                @foreach($vouchers->take(4) as $vch)
                <div class="p-4 rounded-xl border border-dashed border-emerald-300 bg-emerald-50/30 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-white text-emerald-700 border border-emerald-200">
                                {{ $vch->code }}
                            </span>
                            <span class="text-[10px] text-slate-400">
                                {{ $vch->store_id ? ($vch->store->name ?? 'Toko') : 'Platform' }}
                            </span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mt-2">{{ $vch->name }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            @if($vch->type === 'percent')
                                Diskon {{ $vch->amount }}% (Min. Rp {{ number_format($vch->min_spend, 0, ',', '.') }})
                            @else
                                Potongan Rp {{ number_format($vch->amount, 0, ',', '.') }} (Min. Rp {{ number_format($vch->min_spend, 0, ',', '.') }})
                            @endif
                        </p>
                    </div>

                    <button @click="copyCode('{{ $vch->code }}')"
                            class="w-full py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1.5"
                            :class="copiedCode === '{{ $vch->code }}' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'">
                        <i :class="copiedCode === '{{ $vch->code }}' ? 'fa-solid fa-check' : 'fa-regular fa-copy'"></i>
                        <span x-text="copiedCode === '{{ $vch->code }}' ? 'Tersalin!' : 'Salin Kode'"></span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-app-layout>