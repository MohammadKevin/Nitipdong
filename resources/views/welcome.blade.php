<x-app-layout>
    {{-- Flash Notifications & Welcome after Login (Shopee Style) --}}
    @if(session('success') || request('is_from_login'))
        <div class="page-container mt-3">
            <div class="flex items-center justify-between px-4 py-2.5 bg-cyan-50 border border-cyan-200 text-cyan-900 rounded-xl text-xs font-semibold animate-fade-up shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-cyan-600 text-sm"></i>
                    @if(request('is_from_login') && auth()->check())
                        <span>Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>! Selamat berbelanja di SakserShop.</span>
                    @else
                        <span>{{ session('success') }}</span>
                    @endif
                </div>
                @auth
                    <a href="{{ route('customer.dashboard') }}" class="text-[11px] font-bold text-cyan-700 hover:underline">
                        Lihat Pesanan Saya &rarr;
                    </a>
                @endauth
            </div>
        </div>
    @endif

    {{-- Hero Section (Shopee 2:1 Carousel & Promo Cards) --}}
    <section class="page-container py-3">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
            <!-- Hero Banner with Auto Carousel -->
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
                    }, 4500);
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
                <div class="relative rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-card h-full min-h-[310px] sm:min-h-[340px]"
                     @mouseenter="pauseAutoPlay()"
                     @mouseleave="resumeAutoPlay()">

                    <!-- Slide 1: Main Hero -->
                    <div x-show="currentSlide === 0"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <div class="absolute inset-0 opacity-30">
                            <img src="https://images.unsplash.com/photo-1550009158-9ebf69173e03?q=80&w=2001&auto=format&fit=crop" class="w-full h-full object-cover" alt="Hero">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/85 to-transparent"></div>

                        <div class="relative z-10 h-full flex flex-col justify-between p-6 sm:p-8">
                            <div class="max-w-lg">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-400/30 mb-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                    SakserShop Mega Mall 2026
                                </span>
                                <h1 class="text-xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                                    Pesta Diskon Akbar & Produk Official Store Terpercaya
                                </h1>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                                    Dapatkan jaminan 100% original, ekstra cashback hingga 50%, dan gratis ongkir ke seluruh Indonesia.
                                </p>
                            </div>
                            <div class="pt-5 flex items-center gap-3">
                                <a href="{{ url('/products') }}" class="btn-primary text-xs h-9 px-5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl shadow-md flex items-center gap-2">
                                    <span>Belanja Sekarang</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                                <a href="{{ url('/products?flash_sale=1') }}" class="px-4 h-9 inline-flex items-center justify-center rounded-xl border border-slate-700 bg-slate-900/80 text-slate-200 hover:text-white hover:border-slate-500 text-xs font-semibold transition-colors">
                                    <i class="fa-solid fa-bolt text-cyan-400 mr-1.5"></i>
                                    Flash Sale
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Flash Sale Promo -->
                    <div x-show="currentSlide === 1"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <div class="absolute inset-0 opacity-35">
                            <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Flash Sale">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-rose-950 via-rose-950/90 to-transparent"></div>

                        <div class="relative z-10 h-full flex flex-col justify-between p-6 sm:p-8">
                            <div class="max-w-lg">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-400/30 mb-2.5">
                                    <i class="fa-solid fa-fire text-amber-400"></i>
                                    Flash Sale Kilat
                                </span>
                                <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                                    Diskon Spesial Hingga 70% Setiap Hari
                                </h2>
                                <p class="text-xs sm:text-sm text-rose-100/90 mt-2 leading-relaxed">
                                    Pantau jam flash sale dan rebut barang impianmu dengan harga termurah sebelum kehabisan!
                                </p>
                            </div>
                            <div class="pt-5 flex items-center gap-3">
                                <a href="{{ url('/products?flash_sale=1') }}" class="btn-primary text-xs h-9 px-5 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-xl shadow-md flex items-center gap-2">
                                    <i class="fa-solid fa-bolt text-amber-300"></i>
                                    Serbu Flash Sale
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
                        <div class="absolute inset-0 opacity-30">
                            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Seller">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950 via-emerald-950/90 to-transparent"></div>

                        <div class="relative z-10 h-full flex flex-col justify-between p-6 sm:p-8">
                            <div class="max-w-lg">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-2.5">
                                    <i class="fa-solid fa-store text-emerald-400"></i>
                                    Seller Official Center
                                </span>
                                <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                                    Buka Toko Gratis & Raih Jutaan Pembeli
                                </h2>
                                <p class="text-xs sm:text-sm text-emerald-100/90 mt-2 leading-relaxed">
                                    Daftarkan tokomu dalam 2 menit tanpa biaya pendaftaran dan nikmati fitur promosi lengkap.
                                </p>
                            </div>
                            <div class="pt-5 flex items-center gap-3">
                                <a href="{{ route('store.register') }}" class="btn-primary text-xs h-9 px-5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl shadow-md flex items-center gap-2">
                                    <i class="fa-solid fa-rocket"></i>
                                    Buka Toko Sekarang
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Indicators -->
                    <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20 flex gap-1.5">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="goToSlide(i - 1)"
                                    class="h-1.5 rounded-full transition-all"
                                    :class="currentSlide === (i - 1) ? 'bg-cyan-400 w-6' : 'bg-white/40 w-2 hover:bg-white/70'">
                            </button>
                        </template>
                    </div>

                    <!-- Navigation Arrows -->
                    <button @click="currentSlide = (currentSlide - 1 + totalSlides) % totalSlides"
                            class="hidden md:flex absolute left-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-slate-900/50 hover:bg-slate-900/80 backdrop-blur-sm border border-white/20 items-center justify-center text-white transition-all">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button @click="nextSlide()"
                            class="hidden md:flex absolute right-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-slate-900/50 hover:bg-slate-900/80 backdrop-blur-sm border border-white/20 items-center justify-center text-white transition-all">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Right 2 Stacked Promo Banners (Shopee Style) -->
            <div class="lg:col-span-4 flex flex-col gap-3.5">
                <div class="p-5 rounded-2xl bg-gradient-to-br from-cyan-600 to-cyan-800 text-white shadow-card flex flex-col justify-between flex-1 relative overflow-hidden border border-cyan-500/30">
                    <div class="absolute -right-4 -bottom-4 text-cyan-400/20 text-7xl font-bold">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white px-2 py-0.5 rounded-md backdrop-blur-xs border border-white/20">
                            Gratis Ongkir XTRA
                        </span>
                        <h3 class="text-sm sm:text-base font-bold text-white mt-2.5 leading-snug">Voucher Ekstra Ongkir Rp0 Seluruh Indonesia</h3>
                        <p class="text-xs text-cyan-100 mt-1">Klaim voucher dan nikmati potongan pengiriman tanpa minimum belanja.</p>
                    </div>
                    <div class="pt-3">
                        <a href="{{ url('/products') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-white/20 hover:bg-white/30 px-3.5 py-1.5 rounded-lg backdrop-blur-xs transition-colors">
                            Klaim Kupon <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-900 text-white shadow-card flex flex-col justify-between flex-1 border border-slate-800 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 text-slate-800 text-7xl font-bold">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">
                            Garansi 100% Aman
                        </span>
                        <h3 class="text-sm sm:text-base font-bold text-white mt-2.5 leading-snug">Proteksi Belanja & Garansi Pengembalian</h3>
                        <p class="text-xs text-slate-400 mt-1">Dana Anda aman di rekening bersama hingga pesanan sampai dengan selamat.</p>
                    </div>
                    <div class="pt-3">
                        <a href="{{ route('store.register') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-400 hover:text-cyan-300">
                            Mulai Jual di SakserShop <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 10 Shopee-Style Quick Service Icon Ribbon --}}
    <section class="page-container py-3">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-card">
            <div class="grid grid-cols-5 sm:grid-cols-10 gap-3 text-center">
                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-cyan-700 leading-tight">SakserShop Mall</span>
                </a>

                <a href="{{ url('/products?flash_sale=1') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-rose-600 leading-tight">Flash Sale</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-emerald-700 leading-tight">Gratis Ongkir</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-amber-700 leading-tight">Murah Lebay</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-purple-700 leading-tight">Pulsa & Tagihan</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-blue-700 leading-tight">COD Bayar Ditempat</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-red-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-red-700 leading-tight">Pilih Lokal</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-yellow-50 border border-yellow-100 text-yellow-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-yellow-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-yellow-700 leading-tight">Koin SakserShop</span>
                </a>

                <a href="{{ route('customer.wishlist.index') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-pink-50 border border-pink-100 text-pink-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-pink-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-pink-700 leading-tight">Favorit Saya</span>
                </a>

                <a href="{{ url('/products') }}" class="flex flex-col items-center group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg mb-1.5 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 group-hover:text-indigo-700 leading-tight">Semua Promo</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Kategori Pilihan (Shopee Style Grid) --}}
    @if(isset($categories) && $categories->count() > 0)
    <section class="page-container py-3">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-border-all text-cyan-600"></i>
                        KATEGORI PILIHAN
                    </h2>
                </div>
                <a href="{{ url('/products') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                   class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 shadow-2xs flex items-center justify-center text-slate-600 group-hover:from-cyan-50 group-hover:to-cyan-100 group-hover:border-cyan-300 group-hover:text-cyan-700 text-2xl mb-2 transition-all">
                        @if($category->icon)
                            <i class="{{ $category->icon }}"></i>
                        @else
                            <i class="fa-solid fa-box-open"></i>
                        @endif
                    </div>
                    <span class="text-xs font-semibold text-slate-700 group-hover:text-cyan-800 transition-colors line-clamp-2 leading-tight">
                        {{ $category->name }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Shopee-Style Signature FLASH SALE Section with Live Flame & Countdown --}}
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
        <div class="bg-gradient-to-r from-rose-900 via-rose-950 to-slate-900 rounded-2xl p-5 text-white border border-rose-800/80 shadow-card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-rose-800/50">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl sm:text-2xl font-black italic tracking-tighter text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-fire text-amber-400 animate-bounce"></i>
                            FLASH SALE
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-400 text-slate-950 uppercase tracking-wider">
                            DISKON KILAT
                        </span>
                    </div>

                    {{-- Countdown Timer --}}
                    <div class="flex items-center gap-1.5 ml-2">
                        <div class="flex items-center gap-1 font-mono font-bold text-xs">
                            <span class="px-2 py-1 rounded-md bg-slate-950 text-white border border-rose-700/50" x-text="hours">00</span>
                            <span class="text-amber-400 font-bold">:</span>
                            <span class="px-2 py-1 rounded-md bg-slate-950 text-white border border-rose-700/50" x-text="minutes">00</span>
                            <span class="text-amber-400 font-bold">:</span>
                            <span class="px-2 py-1 rounded-md bg-slate-950 text-white border border-rose-700/50" x-text="seconds">00</span>
                        </div>
                    </div>
                </div>

                <a href="{{ url('/products?flash_sale=1') }}" class="text-xs font-bold text-amber-300 hover:text-white flex items-center gap-1">
                    <span>Lihat Semua Promo</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 pt-4">
                @foreach($activeFlashSale->items->take(6) as $fsItem)
                    @php $product = $fsItem->product; @endphp
                    @if($product)
                    <a href="{{ route('product.show', $product) }}"
                       class="bg-white rounded-xl overflow-hidden p-2.5 text-slate-900 group shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="relative w-full aspect-square rounded-lg overflow-hidden bg-slate-50 mb-2 border border-slate-100">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-xl">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                @endif
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[10px] font-black bg-rose-600 text-white">
                                    -{{ $fsItem->discount_percentage }}%
                                </span>
                            </div>

                            <p class="text-xs font-semibold text-slate-800 line-clamp-2 leading-snug group-hover:text-cyan-700 transition-colors">
                                {{ $product->name }}
                            </p>
                        </div>

                        <div class="mt-2.5">
                            <div class="flex flex-col">
                                <span class="text-sm sm:text-base font-extrabold text-rose-600 leading-none">
                                    Rp {{ number_format($fsItem->flash_sale_price, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 line-through mt-0.5">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Progress Bar Terjual --}}
                            <div class="mt-2 pt-1 border-t border-slate-100">
                                <div class="w-full bg-rose-100 rounded-full h-3.5 relative overflow-hidden flex items-center">
                                    <div class="bg-gradient-to-r from-amber-400 to-rose-500 h-full rounded-full transition-all" style="width: {{ max($fsItem->sold_percentage, 15) }}%"></div>
                                    <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-slate-900 uppercase">
                                        🔥 Terjual {{ $fsItem->stock_sold }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- SakserShop Mall (Official Stores & Verified Brands Showcase) --}}
    @if(isset($officialStores) && $officialStores->count() > 0)
    <section class="page-container py-3">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm sm:text-base font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-check text-cyan-600"></i>
                            SAKSERSHOP MALL
                        </span>
                        <span class="hidden sm:inline-block text-slate-300">•</span>
                        <div class="hidden sm:flex items-center gap-3 text-xs text-slate-500 font-medium">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-rotate-left text-cyan-600 text-[10px]"></i> 7 Hari Pengembalian</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-badge-check text-cyan-600 text-[10px]"></i> 100% Original</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-truck-fast text-cyan-600 text-[10px]"></i> Gratis Ongkir</span>
                        </div>
                    </div>
                </div>
                <a href="{{ url('/products') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 flex items-center gap-1">
                    <span>Lihat Semua Mall</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                @foreach($officialStores as $store)
                <a href="{{ url('/products?store='.$store->id) }}"
                   class="p-3.5 rounded-xl bg-slate-50 hover:bg-cyan-50/50 border border-slate-200/80 hover:border-cyan-300 transition-all text-center flex flex-col items-center group">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($store->name) }}&background=0891b2&color=fff&size=90"
                         class="w-14 h-14 rounded-2xl object-cover border border-slate-200 group-hover:scale-105 transition-transform mb-2 shadow-2xs" alt="{{ $store->name }}">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-cyan-800 line-clamp-1">{{ $store->name }}</h3>
                    <span class="text-[10px] text-slate-400 mt-0.5">{{ $store->products_count }} Produk</span>
                    <span class="mt-2 inline-flex items-center gap-1 text-[9px] font-bold text-cyan-800 bg-cyan-100/70 px-2 py-0.5 rounded-full border border-cyan-200">
                        <i class="fa-solid fa-circle-check text-cyan-600 text-[8px]"></i> Official
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Kupon & Voucher Promo Belanja --}}
    @if(isset($vouchers) && $vouchers->count() > 0)
    <section class="page-container py-3">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card"
             x-data="{
                copiedCode: null,
                copyCode(code) {
                    navigator.clipboard.writeText(code);
                    this.copiedCode = code;
                    setTimeout(() => this.copiedCode = null, 2000);
                }
             }">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-cyan-600"></i>
                        VOUCHER & KUPON DISKON
                    </h2>
                </div>
                <span class="text-xs text-slate-400">Klaim kode & gunakan saat checkout</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($vouchers as $vch)
                <div class="p-3.5 rounded-xl border border-dashed border-cyan-400 bg-cyan-50/30 flex items-center justify-between gap-3 relative overflow-hidden">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-white text-cyan-800 border border-cyan-300 shadow-2xs">
                                {{ $vch->code }}
                            </span>
                            <span class="text-[10px] font-bold text-cyan-700 uppercase">
                                {{ $vch->type === 'percent' ? 'Diskon '.$vch->amount.'%' : 'Potongan Rp '.number_format($vch->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mt-1.5 truncate">{{ $vch->name }}</h3>
                        <p class="text-[10px] text-slate-500">Min. Belanja Rp {{ number_format($vch->min_spend, 0, ',', '.') }}</p>
                    </div>

                    <button @click="copyCode('{{ $vch->code }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shrink-0 flex items-center gap-1 shadow-2xs"
                            :class="copiedCode === '{{ $vch->code }}' ? 'bg-emerald-600 text-white' : 'bg-cyan-700 hover:bg-cyan-800 text-white'">
                        <i :class="copiedCode === '{{ $vch->code }}' ? 'fa-solid fa-check' : 'fa-regular fa-copy'"></i>
                        <span x-text="copiedCode === '{{ $vch->code }}' ? 'Tersalin' : 'Klaim'"></span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Shopee-Style "REKOMENDASI / DAILY DISCOVER" Sticky Tabs & Catalog Grid --}}
    <section class="page-container py-4 mb-8" x-data="{ activeTab: 'rekomendasi' }">
        {{-- Sticky Header Tabs (Shopee Signature) --}}
        <div class="bg-white rounded-2xl p-2 border border-slate-200/80 shadow-card mb-5 sticky top-16 z-30">
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-1 text-center text-xs font-bold">
                <button @click="activeTab = 'rekomendasi'"
                        class="py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5"
                        :class="activeTab === 'rekomendasi' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50'">
                    <i class="fa-solid fa-sparkles text-[11px]"></i>
                    <span>Rekomendasi</span>
                </button>
                <button @click="activeTab = 'terlaris'"
                        class="py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5"
                        :class="activeTab === 'terlaris' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50'">
                    <i class="fa-solid fa-fire text-[11px]"></i>
                    <span>Terlaris</span>
                </button>
                <button @click="activeTab = 'official'"
                        class="py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5"
                        :class="activeTab === 'official' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50'">
                    <i class="fa-solid fa-shield-check text-[11px]"></i>
                    <span>Official Store</span>
                </button>
                <a href="{{ url('/products') }}"
                   class="hidden sm:flex py-2.5 px-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all items-center justify-center gap-1.5">
                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    <span>Semua Produk</span>
                </a>
            </div>
        </div>

        {{-- TAB 1: REKOMENDASI GRID --}}
        <div x-show="activeTab === 'rekomendasi'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5">
            @forelse($products as $prod)
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card hover:shadow-lg transition-all duration-200 flex flex-col justify-between group relative">
                {{-- Wishlist Heart Button Top Right --}}
                @auth
                    @if(auth()->user()->role === 'customer')
                        @php $isWish = $prod->isWishlistedBy(auth()->user()); @endphp
                        <div x-data="{ isWish: {{ $isWish ? 'true' : 'false' }}, isToggling: false, bounce: false }" class="absolute top-2 right-2 z-20">
                            <button type="button"
                                    @click.prevent.stop="
                                        if(isToggling) return;
                                        isToggling = true;
                                        bounce = true;
                                        fetch('{{ route('customer.wishlist.toggle', $prod) }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            isWish = data.is_wishlisted;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: data }));
                                            window.dispatchEvent(new CustomEvent('notify', {
                                                detail: {
                                                    title: data.is_wishlisted ? 'Ditambahkan ke Wishlist' : 'Dihapus dari Wishlist',
                                                    message: data.message,
                                                    type: data.is_wishlisted ? 'success' : 'info'
                                                }
                                            }));
                                        })
                                        .catch(err => console.error(err))
                                        .finally(() => {
                                            isToggling = false;
                                            setTimeout(() => bounce = false, 600);
                                        });
                                    "
                                    :title="isWish ? 'Hapus Wishlist' : 'Tambah Wishlist'"
                                    class="w-7 h-7 rounded-full bg-white/90 backdrop-blur-sm border flex items-center justify-center text-xs transition-all shadow-2xs cursor-pointer"
                                    :class="[
                                        isWish ? 'text-rose-600 border-rose-200 bg-rose-50/90' : 'text-slate-400 border-slate-200 hover:text-rose-600',
                                        bounce ? 'scale-125' : ''
                                    ]">
                                <i class="fa-heart" :class="isWish ? 'fa-solid text-rose-600' : 'fa-regular'"></i>
                            </button>
                        </div>
                    @endif
                @endauth

                {{-- Product Image --}}
                <a href="{{ route('product.show', $prod) }}" class="relative aspect-square bg-slate-50 overflow-hidden block">
                    @if($prod->image_url)
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif

                    {{-- Badges --}}
                    @if($prod->is_in_flash_sale)
                        <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-cyan-900 text-cyan-200 flex items-center gap-1 border border-cyan-700/50 shadow-2xs">
                            <i class="fa-solid fa-bolt text-cyan-400 text-[8px]"></i> Flash Sale
                        </span>
                    @elseif($prod->has_discount)
                        <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-600 text-white shadow-2xs">
                            -{{ $prod->discount_percentage_effective }}%
                        </span>
                    @endif

                    <span class="absolute bottom-1.5 left-1.5 px-1.5 py-0.2 rounded text-[8px] font-bold bg-emerald-600 text-white flex items-center gap-0.5 shadow-2xs">
                        <i class="fa-solid fa-truck-fast text-[7px]"></i> Bebas Ongkir
                    </span>
                </a>

                {{-- Product Info --}}
                <div class="p-3 flex-1 flex flex-col justify-between space-y-2.5">
                    <div>
                        <a href="{{ route('product.show', $prod) }}" class="text-xs font-semibold text-slate-800 group-hover:text-cyan-700 transition-colors line-clamp-2 leading-snug">
                            {{ $prod->name }}
                        </a>

                        <div class="mt-2 flex flex-col">
                            <span class="text-sm font-black text-slate-900 leading-tight">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </span>
                            @if($prod->has_discount)
                                <span class="text-[10px] text-slate-400 line-through mt-0.5">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star text-[9px]"></i>
                            <span>{{ number_format($prod->effective_rating, 1) }}</span>
                        </div>
                        <span class="truncate">{{ $prod->formatted_sold_count }} terjual</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 shadow-card">
                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs font-bold text-slate-700">Belum ada produk yang tersedia</p>
            </div>
            @endforelse
        </div>

        {{-- TAB 2: TERLARIS GRID --}}
        <div x-show="activeTab === 'terlaris'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5"
             x-cloak>
            @forelse($topProducts as $index => $prod)
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card hover:shadow-lg transition-all duration-200 flex flex-col justify-between group relative">
                {{-- Sales Rank Tag for Top 3 --}}
                @if($index === 0)
                    <div class="absolute top-2 left-2 z-20 px-2 py-0.5 rounded-lg bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black text-[9px] shadow-sm flex items-center gap-1 border border-amber-300">
                        <i class="fa-solid fa-crown text-[8px]"></i> Top #1
                    </div>
                @elseif($index === 1)
                    <div class="absolute top-2 left-2 z-20 px-2 py-0.5 rounded-lg bg-gradient-to-r from-slate-400 to-slate-300 text-slate-900 font-black text-[9px] shadow-sm flex items-center gap-1 border border-slate-300">
                        <i class="fa-solid fa-medal text-[8px]"></i> Top #2
                    </div>
                @elseif($index === 2)
                    <div class="absolute top-2 left-2 z-20 px-2 py-0.5 rounded-lg bg-gradient-to-r from-amber-700 to-amber-600 text-white font-black text-[9px] shadow-sm flex items-center gap-1 border border-amber-600">
                        <i class="fa-solid fa-award text-[8px]"></i> Top #3
                    </div>
                @else
                    <div class="absolute top-2 left-2 z-20 px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold text-[9px] shadow-2xs flex items-center gap-0.5 border border-amber-200">
                        <i class="fa-solid fa-fire text-amber-500 text-[8px]"></i> Terlaris
                    </div>
                @endif

                {{-- Wishlist Heart Button Top Right --}}
                @auth
                    @if(auth()->user()->role === 'customer')
                        @php $isWish = $prod->isWishlistedBy(auth()->user()); @endphp
                        <div x-data="{ isWish: {{ $isWish ? 'true' : 'false' }}, isToggling: false, bounce: false }" class="absolute top-2 right-2 z-20">
                            <button type="button"
                                    @click.prevent.stop="
                                        if(isToggling) return;
                                        isToggling = true;
                                        bounce = true;
                                        fetch('{{ route('customer.wishlist.toggle', $prod) }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            isWish = data.is_wishlisted;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: data }));
                                            window.dispatchEvent(new CustomEvent('notify', {
                                                detail: {
                                                    title: data.is_wishlisted ? 'Ditambahkan ke Wishlist' : 'Dihapus dari Wishlist',
                                                    message: data.message,
                                                    type: data.is_wishlisted ? 'success' : 'info'
                                                }
                                            }));
                                        })
                                        .catch(err => console.error(err))
                                        .finally(() => {
                                            isToggling = false;
                                            setTimeout(() => bounce = false, 600);
                                        });
                                    "
                                    :title="isWish ? 'Hapus Wishlist' : 'Tambah Wishlist'"
                                    class="w-7 h-7 rounded-full bg-white/90 backdrop-blur-sm border flex items-center justify-center text-xs transition-all shadow-2xs cursor-pointer"
                                    :class="[
                                        isWish ? 'text-rose-600 border-rose-200 bg-rose-50/90' : 'text-slate-400 border-slate-200 hover:text-rose-600',
                                        bounce ? 'scale-125' : ''
                                    ]">
                                <i class="fa-heart" :class="isWish ? 'fa-solid text-rose-600' : 'fa-regular'"></i>
                            </button>
                        </div>
                    @endif
                @endauth

                {{-- Product Image --}}
                <a href="{{ route('product.show', $prod) }}" class="relative aspect-square bg-slate-50 overflow-hidden block">
                    @if($prod->image_url)
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif

                    @if($prod->has_discount)
                        <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-600 text-white shadow-2xs">
                            -{{ $prod->discount_percentage_effective }}%
                        </span>
                    @endif

                    <span class="absolute bottom-1.5 left-1.5 px-1.5 py-0.2 rounded text-[8px] font-bold bg-emerald-600 text-white flex items-center gap-0.5 shadow-2xs">
                        <i class="fa-solid fa-truck-fast text-[7px]"></i> Bebas Ongkir
                    </span>
                </a>

                {{-- Product Info --}}
                <div class="p-3 flex-1 flex flex-col justify-between space-y-2.5">
                    <div>
                        <a href="{{ route('product.show', $prod) }}" class="text-xs font-semibold text-slate-800 group-hover:text-cyan-700 transition-colors line-clamp-2 leading-snug">
                            {{ $prod->name }}
                        </a>

                        <div class="mt-2 flex flex-col">
                            <span class="text-sm font-black text-slate-900 leading-tight">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </span>
                            @if($prod->has_discount)
                                <span class="text-[10px] text-slate-400 line-through mt-0.5">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star text-[9px]"></i>
                            <span>{{ number_format($prod->effective_rating, 1) }}</span>
                        </div>
                        <span class="font-extrabold text-amber-600 flex items-center gap-0.5 truncate">
                            <i class="fa-solid fa-fire text-[9px]"></i>
                            {{ $prod->formatted_sold_count }} terjual
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 shadow-card">
                <i class="fa-solid fa-fire text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs font-bold text-slate-700">Belum ada data produk terlaris</p>
            </div>
            @endforelse
        </div>

        {{-- TAB 3: OFFICIAL STORE GRID --}}
        <div x-show="activeTab === 'official'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5"
             x-cloak>
            @forelse($officialProducts as $prod)
            <div class="bg-white rounded-xl border border-cyan-100 overflow-hidden shadow-card hover:shadow-lg transition-all duration-200 flex flex-col justify-between group relative">
                {{-- Official Mall Badge --}}
                <div class="absolute top-2 left-2 z-20 px-2 py-0.5 rounded-lg bg-cyan-700 text-white font-extrabold text-[9px] shadow-sm flex items-center gap-1 border border-cyan-500">
                    <i class="fa-solid fa-shield-check text-[8px]"></i> Official
                </div>

                {{-- Wishlist Heart Button Top Right --}}
                @auth
                    @if(auth()->user()->role === 'customer')
                        @php $isWish = $prod->isWishlistedBy(auth()->user()); @endphp
                        <div x-data="{ isWish: {{ $isWish ? 'true' : 'false' }}, isToggling: false, bounce: false }" class="absolute top-2 right-2 z-20">
                            <button type="button"
                                    @click.prevent.stop="
                                        if(isToggling) return;
                                        isToggling = true;
                                        bounce = true;
                                        fetch('{{ route('customer.wishlist.toggle', $prod) }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            isWish = data.is_wishlisted;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: data }));
                                            window.dispatchEvent(new CustomEvent('notify', {
                                                detail: {
                                                    title: data.is_wishlisted ? 'Ditambahkan ke Wishlist' : 'Dihapus dari Wishlist',
                                                    message: data.message,
                                                    type: data.is_wishlisted ? 'success' : 'info'
                                                }
                                            }));
                                        })
                                        .catch(err => console.error(err))
                                        .finally(() => {
                                            isToggling = false;
                                            setTimeout(() => bounce = false, 600);
                                        });
                                    "
                                    :title="isWish ? 'Hapus Wishlist' : 'Tambah Wishlist'"
                                    class="w-7 h-7 rounded-full bg-white/90 backdrop-blur-sm border flex items-center justify-center text-xs transition-all shadow-2xs cursor-pointer"
                                    :class="[
                                        isWish ? 'text-rose-600 border-rose-200 bg-rose-50/90' : 'text-slate-400 border-slate-200 hover:text-rose-600',
                                        bounce ? 'scale-125' : ''
                                    ]">
                                <i class="fa-heart" :class="isWish ? 'fa-solid text-rose-600' : 'fa-regular'"></i>
                            </button>
                        </div>
                    @endif
                @endauth

                {{-- Product Image --}}
                <a href="{{ route('product.show', $prod) }}" class="relative aspect-square bg-slate-50 overflow-hidden block">
                    @if($prod->image_url)
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif

                    @if($prod->has_discount)
                        <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-600 text-white shadow-2xs">
                            -{{ $prod->discount_percentage_effective }}%
                        </span>
                    @endif

                    <span class="absolute bottom-1.5 left-1.5 px-1.5 py-0.2 rounded text-[8px] font-bold bg-cyan-600 text-white flex items-center gap-0.5 shadow-2xs">
                        <i class="fa-solid fa-certificate text-[7px]"></i> 100% Original
                    </span>
                </a>

                {{-- Product Info --}}
                <div class="p-3 flex-1 flex flex-col justify-between space-y-2.5">
                    <div>
                        <span class="text-[9px] font-bold text-cyan-700 truncate block">
                            {{ $prod->store?->name ?? 'Official Store' }}
                        </span>
                        <a href="{{ route('product.show', $prod) }}" class="text-xs font-semibold text-slate-800 group-hover:text-cyan-700 transition-colors line-clamp-2 leading-snug mt-0.5">
                            {{ $prod->name }}
                        </a>

                        <div class="mt-2 flex flex-col">
                            <span class="text-sm font-black text-slate-900 leading-tight">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </span>
                            @if($prod->has_discount)
                                <span class="text-[10px] text-slate-400 line-through mt-0.5">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star text-[9px]"></i>
                            <span>{{ number_format($prod->effective_rating, 1) }}</span>
                        </div>
                        <span class="text-cyan-700 font-semibold truncate">{{ $prod->store?->city ?: 'Official Mall' }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 shadow-card">
                <i class="fa-solid fa-store text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs font-bold text-slate-700">Belum ada produk Official Store</p>
            </div>
            @endforelse
        </div>

        {{-- Load More CTA --}}
        <div class="mt-8 text-center">
            <a href="{{ url('/products') }}" class="btn-primary h-10 px-8 text-xs font-bold bg-cyan-700 hover:bg-cyan-800 text-white rounded-xl shadow-md inline-flex items-center gap-2">
                <span>Lihat Semua Katalog Produk</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </section>
</x-app-layout>
