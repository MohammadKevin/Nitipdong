<x-app-layout>
    @if(session('success'))
        <div class="page-container mt-3">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-cyan-50 border border-cyan-200 text-cyan-900 rounded-md text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-cyan-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <section class="page-container pt-4 pb-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
            <!-- Hero Banner with Auto Carousel (5s delay) -->
            <div class="lg:col-span-8" x-data="{
                currentSlide: 0,
                totalSlides: 3,
                autoPlay: null,
                isPaused: false,
                init() {
                    this.startAutoPlay();
                },
                startAutoPlay() {
                    this.autoPlay = setInterval(() => {
                        if (!this.isPaused) {
                            this.nextSlide();
                        }
                    }, 5000);
                },
                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                },
                goToSlide(index) {
                    this.currentSlide = index;
                },
                pauseAutoPlay() {
                    this.isPaused = true;
                },
                resumeAutoPlay() {
                    this.isPaused = false;
                }
            }">
                <div class="relative rounded-xl overflow-hidden bg-slate-950 border border-slate-800 shadow-card h-full min-h-[300px]"
                     @mouseenter="pauseAutoPlay()"
                     @mouseleave="resumeAutoPlay()">

                    <!-- Slide 1: Main Hero -->
                    <div x-show="currentSlide === 0"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <div class="absolute inset-0 opacity-25">
                            <img src="https://images.unsplash.com/photo-1550009158-9ebf69173e03?q=80&w=2001&auto=format&fit=crop" class="w-full h-full object-cover" alt="Hero">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>

                        <div class="relative z-10 h-full flex flex-col justify-between p-7 sm:p-9">
                            <div class="max-w-lg">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-300 border border-cyan-400/20 mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                    Curated Tech & Lifestyle 2026
                                </span>
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                                    Eksplorasi Produk Pilihan dengan Standar Kurasi Tertinggi
                                </h1>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed font-normal">
                                    Koleksi gadget original, kebutuhan esensial, dan official store terverifikasi langsung dengan jaminan perlindungan pembeli 100%.
                                </p>
                            </div>
                            <div class="pt-6 flex items-center gap-3">
                                <a href="{{ url('/products') }}" class="btn-primary text-xs h-9 px-4.5 bg-cyan-600 hover:bg-cyan-500 text-white shadow-sm">
                                    Jelajahi Katalog
                                    <i class="fa-solid fa-arrow-right text-[10px] ml-2"></i>
                                </a>
                                <a href="{{ url('/products?flash_sale=1') }}" class="px-4 h-9 inline-flex items-center justify-center rounded-md border border-slate-700 bg-slate-900/80 text-slate-200 hover:text-white hover:border-slate-500 text-xs font-medium transition-colors">
                                    Lihat Flash Sale
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Flash Sale -->
                    <div x-show="currentSlide === 1"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <div class="absolute inset-0 opacity-30">
                            <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Flash Sale">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-rose-950 via-rose-950/90 to-transparent"></div>

                        <div class="relative z-10 h-full flex flex-col justify-between p-7 sm:p-9">
                            <div class="max-w-lg">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-300 border border-yellow-400/30 mb-3">
                                    <i class="fa-solid fa-bolt"></i>
                                    Flash Sale Aktif
                                </span>
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                                    Diskon Hingga 70% Produk Pilihan Hari Ini
                                </h1>
                                <p class="text-xs sm:text-sm text-rose-100 mt-2.5 leading-relaxed font-normal">
                                    Buruan serbu! Stok terbatas dan waktu terbatas untuk hemat maksimal.
                                </p>
                            </div>
                            <div class="pt-6 flex items-center gap-3">
                                <a href="{{ url('/products?flash_sale=1') }}" class="btn-primary text-xs h-9 px-4.5 bg-rose-600 hover:bg-rose-500 text-white shadow-sm">
                                    <i class="fa-solid fa-bolt text-yellow-300 mr-1.5"></i>
                                    Lihat Flash Sale
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: Seller Registration -->
                    <div x-show="currentSlide === 2"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <div class="absolute inset-0 opacity-25">
                            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Seller">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950 via-emerald-950/90 to-transparent"></div>

                        <div class="relative z-10 h-full flex flex-col justify-between p-7 sm:p-9">
                            <div class="max-w-lg">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-3">
                                    <i class="fa-solid fa-store"></i>
                                    Peluang Bisnis
                                </span>
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                                    Buka Toko & Jangkau Pasar Digital BelanjaIn
                                </h1>
                                <p class="text-xs sm:text-sm text-emerald-100 mt-2.5 leading-relaxed font-normal">
                                    Daftarkan toko resmi Anda gratis tanpa biaya pendaftaran awal.
                                </p>
                            </div>
                            <div class="pt-6 flex items-center gap-3">
                                <a href="{{ route('store.register') }}" class="btn-primary text-xs h-9 px-4.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm">
                                    <i class="fa-solid fa-rocket mr-1.5"></i>
                                    Mulai Berjualan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Indicators -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-20 flex gap-2">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="goToSlide(i - 1)"
                                    class="w-2 h-2 rounded-full transition-all"
                                    :class="currentSlide === (i - 1) ? 'bg-white w-6' : 'bg-white/40 hover:bg-white/60'">
                            </button>
                        </template>
                    </div>

                    <!-- Navigation Arrows - Positioned at edges -->
                    <button @click="currentSlide = (currentSlide - 1 + totalSlides) % totalSlides"
                            class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 items-center justify-center text-white transition-all shadow-lg">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button @click="nextSlide()"
                            class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 items-center justify-center text-white transition-all shadow-lg">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="lg:col-span-4 flex flex-col gap-4">
                <div class="p-5 rounded-xl bg-white border border-slate-200/80 shadow-card flex flex-col justify-between flex-1">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-200">
                            Garansi Pengiriman
                        </span>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 mt-2.5 leading-snug">Voucher Ekstra Ongkir Rp0 Bebas Syarat</h3>
                        <p class="text-xs text-slate-500 mt-1">Gunakan kode voucher eksklusif untuk hemat biaya pengiriman ke seluruh Nusantara.</p>
                    </div>
                    <div class="pt-3">
                        <a href="{{ url('/products') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 inline-flex items-center gap-1">
                            Klaim Kupon <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>
                </div>

                <div class="p-5 rounded-xl bg-slate-900 text-white shadow-card flex flex-col justify-between flex-1 border border-slate-800">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
                            Peluang Bisnis
                        </span>
                        <h3 class="text-xs sm:text-sm font-bold text-white mt-2.5 leading-snug">Buka Toko & Jangkau Pasar Digital BelanjaIn</h3>
                        <p class="text-xs text-slate-400 mt-1">Daftarkan toko resmi Anda gratis tanpa biaya pendaftaran awal.</p>
                    </div>
                    <div class="pt-3">
                        <a href="{{ route('store.register') }}" class="text-xs font-semibold text-cyan-400 hover:text-cyan-300 inline-flex items-center gap-1">
                            Mulai Berjualan <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features with Auto Scroll -->
        <div class="mt-4 relative" x-data="{
            autoScroll: null,
            init() {
                const container = this.$refs.scrollContainer;
                const cardWidth = 280 + 14;
                const totalCards = 6;

                this.autoScroll = setInterval(() => {
                    container.scrollLeft += 1;

                    if (container.scrollLeft >= cardWidth * totalCards) {
                        container.classList.remove('scroll-smooth');
                        container.scrollLeft = 0;
                        setTimeout(() => {
                            container.classList.add('scroll-smooth');
                        }, 50);
                    }
                }, 30);
            },
            pauseScroll() {
                if (this.autoScroll) {
                    clearInterval(this.autoScroll);
                    this.autoScroll = null;
                }
            },
            resumeScroll() {
                if (!this.autoScroll) {
                    this.init();
                }
            }
        }">
            <div x-ref="scrollContainer"
                 @mouseenter="pauseScroll()"
                 @mouseleave="resumeScroll()"
                 class="flex gap-3.5 overflow-x-auto scrollbar-hide scroll-smooth">

                <!-- Feature 1 -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm shrink-0 border border-cyan-100">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Jaminan 100% Original</p>
                        <p class="text-[11px] text-slate-500">Semua seller terverifikasi resmi</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm shrink-0 border border-cyan-100">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Pengiriman Instan & Aman</p>
                        <p class="text-[11px] text-slate-500">Tracking paket langsung real-time</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm shrink-0 border border-cyan-100">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Proteksi Pengembalian</p>
                        <p class="text-[11px] text-slate-500">Garansi retur mudah hingga 7 hari</p>
                    </div>
                </div>

                <!-- Feature 4: Pembayaran Aman -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm shrink-0 border border-emerald-100">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Pembayaran Terlindungi</p>
                        <p class="text-[11px] text-slate-500">Transaksi aman dengan enkripsi SSL</p>
                    </div>
                </div>

                <!-- Feature 5: Customer Service 24/7 -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center text-sm shrink-0 border border-purple-100">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Layanan Pelanggan 24/7</p>
                        <p class="text-[11px] text-slate-500">CS responsif siap bantu kapan saja</p>
                    </div>
                </div>

                <!-- Feature 6: Cashback & Rewards -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm shrink-0 border border-amber-100">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Cashback & Poin Rewards</p>
                        <p class="text-[11px] text-slate-500">Belanja makin untung dengan poin</p>
                    </div>
                </div>

                <!-- Duplicate for seamless loop - Feature 1 -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm shrink-0 border border-cyan-100">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Jaminan 100% Original</p>
                        <p class="text-[11px] text-slate-500">Semua seller terverifikasi resmi</p>
                    </div>
                </div>

                <!-- Duplicate - Feature 2 -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-card min-w-[280px] flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm shrink-0 border border-cyan-100">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Pengiriman Instan & Aman</p>
                        <p class="text-[11px] text-slate-500">Tracking paket langsung real-time</p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    </section>

    @if(isset($categories) && $categories->count() > 0)
    <section class="page-container py-3">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Kategori</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih kategori yang Anda cari</p>
                </div>
                <a href="{{ url('/products') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800">
                    Lihat Semua &rarr;
                </a>
            </div>

            <!-- Horizontal Scrollable Categories -->
            <div class="overflow-x-auto scrollbar-hide">
                <div class="flex gap-4 pb-2">
                    @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                       class="flex flex-col items-center text-center min-w-[85px] group">
                        <!-- Icon Container with larger size -->
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 shadow-sm flex items-center justify-center text-slate-600 group-hover:from-cyan-50 group-hover:to-cyan-100 group-hover:border-cyan-300 group-hover:text-cyan-700 text-2xl mb-2 transition-all duration-200">
                            @if($category->icon)
                                <i class="{{ $category->icon }}"></i>
                            @else
                                <i class="fa-solid fa-tag"></i>
                            @endif
                        </div>
                        <!-- Category Name -->
                        <span class="text-xs font-medium text-slate-700 group-hover:text-cyan-800 transition-colors line-clamp-2 leading-tight">
                            {{ $category->name }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <style>
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    </section>
    @endif

    @if(isset($activeFlashSale) && $activeFlashSale && $activeFlashSale->items->count() > 0)
    <section class="page-container py-3"
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
        <div class="bg-slate-900 rounded-xl p-5 text-white border border-slate-800 shadow-card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-cyan-400/10 border border-cyan-400/20 text-cyan-400 flex items-center justify-center text-base">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm sm:text-base font-bold text-white tracking-tight">{{ $activeFlashSale->name }}</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">LIMITED TIME</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-slate-950 px-3.5 py-1.5 rounded-lg border border-slate-800">
                    <span class="text-xs text-slate-400 font-medium">Sisa Waktu:</span>
                    <div class="flex items-center gap-1 font-mono font-bold text-xs text-cyan-300">
                        <span class="px-1.5 py-0.5 rounded bg-slate-800" x-text="hours">00</span>
                        <span class="text-slate-600">:</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-800" x-text="minutes">00</span>
                        <span class="text-slate-600">:</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-800" x-text="seconds">00</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 pt-4">
                @foreach($activeFlashSale->items->take(6) as $fsItem)
                    @php $product = $fsItem->product; @endphp
                    @if($product)
                    <a href="{{ route('product.show', $product) }}"
                       class="product-card group text-slate-900 p-2.5">
                        <div class="relative w-full aspect-square rounded-lg overflow-hidden bg-slate-100 mb-2.5 border border-slate-100">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-xl">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                            @endif
                            <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                -{{ $fsItem->discount_percentage }}%
                            </span>
                        </div>

                        <p class="text-xs text-slate-800 line-clamp-2 leading-snug group-hover:text-cyan-700 transition-colors">
                            {{ $product->name }}
                        </p>

                        <div class="mt-2">
                            <p class="text-xs sm:text-sm font-bold text-rose-600">
                                Rp {{ number_format($fsItem->flash_sale_price, 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-slate-400 line-through">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="mt-2.5 pt-1.5 border-t border-slate-100">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-rose-500 h-full rounded-full" style="width: {{ $fsItem->sold_percentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
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

    <section class="page-container py-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Koleksi Produk Pilihan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Produk original langsung dari official seller terverifikasi</p>
            </div>

            <a href="{{ url('/products') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800">
                Lihat Semua Produk &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
            @forelse($products as $prod)
            <div class="product-card group">
                <a href="{{ route('product.show', $prod) }}" class="block">
                    <div class="product-img-frame">
                        @if($prod->image)
                            <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover" alt="{{ $prod->name }}" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        @endif

                        @if($prod->has_discount)
                            <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                -{{ $prod->discount_percentage_effective }}%
                            </span>
                        @endif

                        @if($prod->is_in_flash_sale)
                            <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-cyan-900 text-cyan-200 flex items-center gap-1 border border-cyan-700/50">
                                <i class="fa-solid fa-bolt text-cyan-400"></i> Flash Sale
                            </span>
                        @endif
                    </div>
                </a>

                <div class="p-3.5 flex flex-col justify-between flex-1 gap-2.5">
                    <div>
                        <div class="flex items-center gap-1 text-[11px] text-slate-400 mb-1 truncate">
                            <i class="fa-solid fa-store text-[9px] text-cyan-700"></i>
                            <span class="truncate">{{ $prod->store->name ?? 'Official Store' }}</span>
                        </div>

                        <a href="{{ route('product.show', $prod) }}" class="text-xs sm:text-sm font-normal text-slate-800 line-clamp-2 hover:text-cyan-700 transition-colors leading-snug">
                            {{ $prod->name }}
                        </a>
                    </div>

                    <div>
                        <div class="flex flex-col">
                            <span class="text-sm sm:text-base font-bold text-slate-900">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </span>
                            @if($prod->has_discount)
                                <span class="text-[11px] text-slate-400 line-through">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500 pt-2.5 border-t border-slate-100 mt-2.5">
                            <div class="flex items-center gap-1 text-amber-500 font-semibold text-[11px]">
                                <i class="fa-solid fa-star"></i>
                                <span>5.0</span>
                            </div>
                            <span class="text-slate-400 text-[11px]">50+ terjual</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                <i class="fa-solid fa-boxes-stacked text-2xl mb-2 text-slate-300"></i>
                <p class="text-xs font-semibold text-slate-700">Belum ada produk yang tersedia</p>
            </div>
            @endforelse
        </div>
    </section>

    @if(isset($vouchers) && $vouchers->count() > 0)
    <section class="page-container py-3 mb-6">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Kupon & Voucher Belanja</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Klaim kode voucher untuk potongan harga otomatis saat checkout</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5"
                 x-data="{
                    copiedCode: null,
                    copyCode(code) {
                        navigator.clipboard.writeText(code);
                        this.copiedCode = code;
                        setTimeout(() => this.copiedCode = null, 2000);
                    }
                 }">
                @foreach($vouchers->take(4) as $vch)
                <div class="p-3.5 rounded-lg border border-dashed border-cyan-300 bg-cyan-50/20 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-white text-cyan-800 border border-cyan-200">
                                {{ $vch->code }}
                            </span>
                            <span class="text-[10px] text-slate-400">
                                {{ $vch->store_id ? ($vch->store->name ?? 'Toko') : 'Platform' }}
                            </span>
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 mt-2">{{ $vch->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            @if($vch->type === 'percent')
                                Diskon {{ $vch->amount }}% (Min. Rp {{ number_format($vch->min_spend, 0, ',', '.') }})
                            @else
                                Potongan Rp {{ number_format($vch->amount, 0, ',', '.') }} (Min. Rp {{ number_format($vch->min_spend, 0, ',', '.') }})
                            @endif
                        </p>
                    </div>

                    <button @click="copyCode('{{ $vch->code }}')"
                            class="w-full py-1.5 rounded-md text-xs font-semibold transition-all flex items-center justify-center gap-1.5"
                            :class="copiedCode === '{{ $vch->code }}' ? 'bg-cyan-700 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'">
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
