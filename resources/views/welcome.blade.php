@section('title', 'NitipDong — Marketplace & Jastip Terpercaya di Indonesia')
@section('og_title', 'NitipDong — Belanja Online Mudah, Cepat & Aman')
@section('og_description', 'Belanja jutaan produk original, flash sale kilat diskon hingga 80%, voucher gratis ongkir Rp0, dan perlindungan transaksi 100% aman di NitipDong.')

<x-app-layout>
    @if(session('success') || request('is_from_login'))
        <div class="page-container mt-3">
            <div class="flex items-center justify-between px-4 py-3 bg-cyan-50 border border-cyan-200/80 text-cyan-950 rounded-xl text-xs font-semibold shadow-xs animate-fade-up">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-cyan-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <i class="fa-solid fa-check text-xs"></i>
                    </span>
                    <div>
                        @if(request('is_from_login') && auth()->check())
                            <span class="font-bold text-slate-900">Halo, {{ auth()->user()->name }}! 👋</span>
                            <span class="text-cyan-800 font-normal ml-1">Selamat datang di <strong>NitipDong</strong>. Temukan ribuan produk pilihan &amp; promo terbaik hari ini.</span>
                        @else
                            <span>{{ session('success') }}</span>
                        @endif
                    </div>
                </div>
                @auth
                    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-cyan-200 text-cyan-800 hover:bg-cyan-600 hover:text-white transition-all text-xs font-bold shadow-xs">
                        <span>Dasbor Saya</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @endauth
            </div>
        </div>
    @endif
    <section class="page-container py-3 sm:py-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-stretch">
            
            {{-- Left: Modern Promo Carousel with Real Product Artwork (8 Cols) --}}
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
                    }, 6000);
                },
                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                },
                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                },
                goToSlide(index) {
                    this.currentSlide = index;
                }
            }" @mouseenter="isPaused = true" @mouseleave="isPaused = false">
                <div class="relative rounded-lg overflow-hidden bg-slate-900 border border-slate-200/80 shadow-md h-full min-h-[340px] sm:min-h-[370px] lg:min-h-[390px] flex flex-col justify-between group">
                    
                    {{-- ── SLIDE 0: FESTIVAL DISKON & GAJIAN SALE ── --}}
                    <div x-show="currentSlide === 0"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 scale-98"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 bg-gradient-to-br from-[#062438] via-[#0b3c5d] to-[#041624] text-white p-5 sm:p-7 lg:p-9 flex flex-col justify-between overflow-hidden">
                        
                        {{-- Ambient Glows --}}
                        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-cyan-500/25 blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 right-10 w-96 h-96 rounded-full bg-sky-500/20 blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center my-auto">
                            {{-- Left Copy & CTA --}}
                            <div class="md:col-span-7 space-y-3">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-400/40 backdrop-blur-md shadow-xs">
                                    <i class="fa-solid fa-fire text-amber-400 text-xs animate-pulse"></i>
                                    <span>FLASH SALE • DISKON S/D 70%</span>
                                </div>
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                                    Promo Spesial Belanja &amp; Bebas Ongkir Rp0
                                </h1>
                                <p class="text-xs sm:text-sm text-sky-100/85 leading-relaxed max-w-md">
                                    Dapatkan ribuan produk pilihan dari gadget, sneakers, fashion, hingga skincare dengan jaminan harga termurah se-Indonesia.
                                </p>
                                <div class="flex flex-wrap items-center gap-2.5 pt-2">
                                    <a href="{{ url('/products') }}" 
                                       class="h-10 px-5 bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white rounded-xl font-bold text-xs shadow-lg shadow-cyan-600/30 flex items-center gap-2 transition-all cursor-pointer">
                                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                                        <span>Belanja Sekarang</span>
                                    </a>
                                    <a href="{{ url('/products?flash_sale=1') }}" 
                                       class="h-10 px-4 bg-white/10 hover:bg-white/20 text-slate-100 hover:text-white border border-white/20 rounded-xl font-bold text-xs backdrop-blur-sm transition-all flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-bolt text-amber-400 text-xs"></i>
                                        <span>Serbu Diskon Kilat</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Right Visual Product Showcase Cards --}}
                            <div class="md:col-span-5 hidden md:flex items-center justify-center relative">
                                <div class="relative w-64 h-64 flex items-center justify-center">
                                    {{-- Product Card 1: Smartphone --}}
                                    <div class="absolute -left-2 top-2 w-36 bg-white/95 rounded-2xl p-2.5 shadow-2xl border border-white/40 transform -rotate-6 hover:rotate-0 transition-transform duration-300 z-20">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 relative mb-1.5">
                                            <img src="{{ asset('img/iphone-15-pro-max.jpg') }}" alt="iPhone" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-rose-600 text-white">-40%</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-800 truncate">iPhone 15 Pro Max</p>
                                        <p class="text-[10px] font-extrabold text-cyan-600">Rp 18.999.000</p>
                                    </div>

                                    {{-- Product Card 2: Sneakers --}}
                                    <div class="absolute right-0 bottom-2 w-36 bg-white/95 rounded-2xl p-2.5 shadow-2xl border border-white/40 transform rotate-6 hover:rotate-0 transition-transform duration-300 z-10">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 relative mb-1.5">
                                            <img src="{{ asset('img/nike.jpg') }}" alt="Nike" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-amber-500 text-slate-950">★ 4.9</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-800 truncate">Nike Air Jordan</p>
                                        <p class="text-[10px] font-extrabold text-cyan-600">Rp 1.850.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ── SLIDE 1: OFFICIAL MALL & 100% ORIGINAL ── --}}
                    <div x-show="currentSlide === 1"
                         x-cloak
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 scale-98"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 bg-gradient-to-br from-[#0a192f] via-[#112240] to-[#0c4a6e] text-white p-5 sm:p-7 lg:p-9 flex flex-col justify-between overflow-hidden">
                        
                        {{-- Ambient Glows --}}
                        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-blue-500/25 blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 left-10 w-96 h-96 rounded-full bg-cyan-500/20 blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center my-auto">
                            {{-- Left Copy & CTA --}}
                            <div class="md:col-span-7 space-y-3">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-400/40 backdrop-blur-md shadow-xs">
                                    <i class="fa-solid fa-certificate text-sky-400 text-xs"></i>
                                    <span>100% ORIGINAL • GARANSI RESMI</span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                                    NitipDong Official Mall &amp; Super Seller
                                </h2>
                                <p class="text-xs sm:text-sm text-sky-100/85 leading-relaxed max-w-md">
                                    Jaminan produk asli langsung dari distributor resmi, garansi pengembalian tanpa ribet 7 hari, dan pengiriman prioritas express.
                                </p>
                                <div class="flex flex-wrap items-center gap-2.5 pt-2">
                                    <a href="{{ url('/products') }}" 
                                       class="h-10 px-5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white rounded-xl font-bold text-xs shadow-lg shadow-sky-600/30 flex items-center gap-2 transition-all cursor-pointer">
                                        <i class="fa-solid fa-store text-xs"></i>
                                        <span>Jelajahi Official Mall</span>
                                    </a>
                                    <a href="{{ route('store.register') }}" 
                                       class="h-10 px-4 bg-white/10 hover:bg-white/20 text-slate-100 hover:text-white border border-white/20 rounded-xl font-bold text-xs backdrop-blur-sm transition-all flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-shield-check text-sky-400 text-xs"></i>
                                        <span>Daftar Toko Resmi</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Right Visual Showcase Cards --}}
                            <div class="md:col-span-5 hidden md:flex items-center justify-center relative">
                                <div class="relative w-64 h-64 flex items-center justify-center">
                                    {{-- Card: Skincare --}}
                                    <div class="absolute -left-2 top-2 w-36 bg-white/95 rounded-2xl p-2.5 shadow-2xl border border-white/40 transform -rotate-3 hover:rotate-0 transition-transform duration-300 z-20">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 relative mb-1.5">
                                            <img src="{{ asset('img/skintific.jpg') }}" alt="Skintific" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-cyan-600 text-white">Official</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-800 truncate">Skintific Moisturizer</p>
                                        <p class="text-[10px] font-extrabold text-cyan-600">Rp 129.000</p>
                                    </div>

                                    {{-- Card: Gaming / Laptop --}}
                                    <div class="absolute right-0 bottom-2 w-36 bg-white/95 rounded-2xl p-2.5 shadow-2xl border border-white/40 transform rotate-6 hover:rotate-0 transition-transform duration-300 z-10">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 relative mb-1.5">
                                            <img src="{{ asset('img/ps5resmi.webp') }}" alt="PS5" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-emerald-600 text-white">Garansi 2 Thn</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-800 truncate">PlayStation 5 Slim</p>
                                        <p class="text-[10px] font-extrabold text-cyan-600">Rp 8.499.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ── SLIDE 2: LAYANAN JASTIP & REKENING BERSAMA ── --}}
                    <div x-show="currentSlide === 2"
                         x-cloak
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 scale-98"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 bg-gradient-to-br from-[#063327] via-[#0c4738] to-[#041c15] text-white p-5 sm:p-7 lg:p-9 flex flex-col justify-between overflow-hidden">
                        
                        {{-- Ambient Glows --}}
                        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-emerald-500/25 blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 right-10 w-96 h-96 rounded-full bg-teal-500/20 blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center my-auto">
                            {{-- Left Copy & CTA --}}
                            <div class="md:col-span-7 space-y-3">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 backdrop-blur-md shadow-xs">
                                    <i class="fa-solid fa-plane-departure text-emerald-400 text-xs"></i>
                                    <span>JASTIP AMANAH • PROTEKSI ESCROW</span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                                    Titip Beli Barang Impian dari Mana Saja
                                </h2>
                                <p class="text-xs sm:text-sm text-emerald-100/85 leading-relaxed max-w-md">
                                    Jangkau barang unik antar kota hingga mancanegara. Dana Anda tersimpan aman di rekening bersama hingga barang tiba di tangan.
                                </p>
                                <div class="flex flex-wrap items-center gap-2.5 pt-2">
                                    <a href="{{ url('/products') }}" 
                                       class="h-10 px-5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition-all cursor-pointer">
                                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                        <span>Cari Jastip Populer</span>
                                    </a>
                                    <a href="{{ route('store.register') }}" 
                                       class="h-10 px-4 bg-white/10 hover:bg-white/20 text-slate-100 hover:text-white border border-white/20 rounded-xl font-bold text-xs backdrop-blur-sm transition-all flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-handshake-simple text-emerald-400 text-xs"></i>
                                        <span>Buka Jasa Titip</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Right Visual Showcase Cards --}}
                            <div class="md:col-span-5 hidden md:flex items-center justify-center relative">
                                <div class="relative w-64 h-64 flex items-center justify-center">
                                    {{-- Card: Gadget / Macbook --}}
                                    <div class="absolute -left-2 top-2 w-36 bg-white/95 rounded-2xl p-2.5 shadow-2xl border border-white/40 transform -rotate-4 hover:rotate-0 transition-transform duration-300 z-20">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 relative mb-1.5">
                                            <img src="{{ asset('img/macbookm3pro.jpg') }}" alt="Macbook" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-emerald-600 text-white">Jastip Tokyo</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-800 truncate">MacBook Pro M3</p>
                                        <p class="text-[10px] font-extrabold text-emerald-600">Rp 26.500.000</p>
                                    </div>

                                    {{-- Card: Skincare Ordinary --}}
                                    <div class="absolute right-0 bottom-2 w-36 bg-white/95 rounded-2xl p-2.5 shadow-2xl border border-white/40 transform rotate-6 hover:rotate-0 transition-transform duration-300 z-10">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 relative mb-1.5">
                                            <img src="{{ asset('img/ordinary.jpg') }}" alt="The Ordinary" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-teal-600 text-white">Jastip Korea</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-800 truncate">The Ordinary Serum</p>
                                        <p class="text-[10px] font-extrabold text-emerald-600">Rp 185.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ── CAROUSEL CONTROLS (Bottom Bar) ── --}}
                    <div class="relative z-30 flex items-center justify-between p-3.5 sm:p-4 bg-gradient-to-t from-slate-950/70 via-slate-950/30 to-transparent">
                        {{-- Arrow Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="prevSlide()" 
                                    class="w-8 h-8 rounded-xl bg-white/15 hover:bg-white/30 text-white flex items-center justify-center backdrop-blur-md transition-all text-xs cursor-pointer border border-white/15 shadow-sm" 
                                    aria-label="Slide sebelumnya">
                                <i class="fa-solid fa-chevron-left text-[11px]"></i>
                            </button>
                            <button type="button" 
                                    @click="nextSlide()" 
                                    class="w-8 h-8 rounded-xl bg-white/15 hover:bg-white/30 text-white flex items-center justify-center backdrop-blur-md transition-all text-xs cursor-pointer border border-white/15 shadow-sm" 
                                    aria-label="Slide berikutnya">
                                <i class="fa-solid fa-chevron-right text-[11px]"></i>
                            </button>
                        </div>

                        {{-- Slide Pagination Indicator Pills --}}
                        <div class="flex items-center gap-2">
                            <template x-for="i in totalSlides" :key="i">
                                <button type="button" 
                                        @click="goToSlide(i - 1)"
                                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                        :class="currentSlide === (i - 1) ? 'w-8 bg-cyan-400 shadow-[0_0_10px_rgba(6,182,212,0.6)]' : 'w-2 bg-white/35 hover:bg-white/60'">
                                </button>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <div class="lg:col-span-4 flex flex-col gap-3.5">

    {{-- Card 1: Voucher — bentuk tiket --}}
    <div class="flex-1 rounded-xl bg-[#0B4F4F] text-white shadow-sm relative overflow-hidden flex">
        <div class="flex-1 p-4 sm:p-5 flex flex-col justify-between">
            <div>
                <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-emerald-300 mb-2">
                    Gratis Ongkir
                </span>
                <h3 class="text-lg font-extrabold leading-snug">
                    Ongkir Rp0 ke Seluruh Indonesia
                </h3>
                <p class="text-[11px] text-emerald-100/80 mt-1.5 leading-relaxed max-w-[85%]">
                    Potongan ongkir instan tanpa minimum belanja, langsung kepakai di checkout.
                </p>
            </div>
            <p class="text-[10px] text-emerald-200/70 mt-3 font-medium">
                Kuota harian terbatas — klaim sebelum habis
            </p>
        </div>

        <div class="relative w-0 border-l-2 border-dashed border-white/25">
            <span class="absolute -top-2 -left-2 w-4 h-4 rounded-full bg-slate-50"></span>
            <span class="absolute -bottom-2 -left-2 w-4 h-4 rounded-full bg-slate-50"></span>
        </div>

        <a href="{{ route('customer.vouchers.index') }}"
           class="w-24 sm:w-28 flex flex-col items-center justify-center gap-1.5 px-2 py-4 hover:bg-white/5 transition-colors">
            <i class="fa-solid fa-ticket text-lg text-emerald-300"></i>
            <span class="text-[11px] font-bold text-center leading-tight">Klaim<br>Kupon</span>
        </a>
    </div>

    {{-- Card 2: Proteksi Rekber — bentuk stempel --}}
    <div class="flex-1 rounded-xl p-4 sm:p-5 bg-[#0A1930] text-white border border-white/10 shadow-sm relative overflow-hidden flex flex-col justify-between">
        <div class="absolute inset-0 opacity-[0.05] pointer-events-none"
             style="background-image: repeating-linear-gradient(135deg, #fff 0 2px, transparent 2px 14px);"></div>

        <div class="relative z-10 flex items-start justify-between gap-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-300">
                    Proteksi Rekening Bersama
                </span>
                <h3 class="text-lg font-extrabold leading-snug mt-1.5">
                    Dana Aman Sampai Barang Diterima
                </h3>
                <p class="text-[11px] text-slate-300 mt-1.5 leading-relaxed max-w-[90%]">
                    Pembayaran ditahan sistem escrow dan baru diteruskan ke penjual setelah kamu konfirmasi pesanan sesuai.
                </p>
            </div>
            <div class="shrink-0 w-14 h-14 rounded-full border-2 border-dashed border-amber-400/60 flex items-center justify-center -rotate-[8deg]">
                <i class="fa-solid fa-shield-halved text-amber-300 text-lg"></i>
            </div>
        </div>

        <div class="relative z-10 pt-3 mt-3 border-t border-white/10 flex items-center justify-between">
            <a href="{{ route('store.register') }}"
               class="text-xs font-bold text-[#0A1930] bg-amber-300 hover:bg-amber-200 px-4 py-2 rounded-lg transition-colors">
                Mulai Jual di NitipDong
            </a>
            <span class="text-[10px] text-slate-400 font-medium">Terverifikasi &amp; diawasi sistem</span>
        </div>
    </div>

</div>
        </div>

        <div class="mt-3.5 bg-white rounded-2xl border border-sky-100/90 p-4 shadow-xs" x-data="{
            scrollLeft() {
                this.$refs.categorySlider.scrollBy({ left: -320, behavior: 'smooth' });
            },
            scrollRight() {
                this.$refs.categorySlider.scrollBy({ left: 320, behavior: 'smooth' });
            }
        }">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kategori Pilihan</span>
                    @if(isset($categories) && $categories->count() > 0)
                        <span class="text-[11px] font-semibold text-slate-400">({{ $categories->count() }})</span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                @click="scrollLeft()" 
                                class="w-7 h-7 rounded-lg border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 hover:text-cyan-700 text-slate-500 flex items-center justify-center text-xs transition-colors cursor-pointer" 
                                title="Geser Kiri"
                                aria-label="Geser Kiri">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" 
                                @click="scrollRight()" 
                                class="w-7 h-7 rounded-lg border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 hover:text-cyan-700 text-slate-500 flex items-center justify-center text-xs transition-colors cursor-pointer" 
                                title="Geser Kanan"
                                aria-label="Geser Kanan">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>

                    <div class="h-4 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                    <a href="{{ url('/products') }}" class="text-xs font-bold text-cyan-700 hover:text-cyan-800 flex items-center gap-1 shrink-0">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>

            <div x-ref="categorySlider" 
                 class="grid grid-rows-1 grid-flow-col auto-cols-[95px] sm:auto-cols-[115px] gap-2.5 sm:gap-3 overflow-x-auto scrollbar-none scroll-smooth pb-1 text-center select-none">
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" 
                       class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-slate-50/50 hover:bg-cyan-50/70 border border-slate-100 hover:border-cyan-200 hover:shadow-xs transition-all group shrink-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-cyan-700 border border-sky-100 flex items-center justify-center text-base sm:text-lg mb-1.5 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-2xs">
                            @if($cat->icon)
                                <i class="{{ $cat->icon }}"></i>
                            @else
                                <i class="fa-solid fa-box"></i>
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 group-hover:text-cyan-800 transition-colors line-clamp-2 leading-tight w-full px-0.5">
                            {{ $cat->name }}
                        </span>
                    </a>
                    @endforeach
                @else
                    <a href="{{ url('/products') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-slate-50/50 hover:bg-cyan-50/70 border border-slate-100 hover:border-cyan-200 hover:shadow-xs transition-all group shrink-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-cyan-700 border border-sky-100 flex items-center justify-center text-base sm:text-lg mb-1.5 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-2xs">
                            <i class="fa-solid fa-laptop"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 group-hover:text-cyan-800 leading-tight">Elektronik</span>
                    </a>
                    <a href="{{ url('/products') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-slate-50/50 hover:bg-cyan-50/70 border border-slate-100 hover:border-cyan-200 hover:shadow-xs transition-all group shrink-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-cyan-700 border border-sky-100 flex items-center justify-center text-base sm:text-lg mb-1.5 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-2xs">
                            <i class="fa-solid fa-shirt"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 group-hover:text-cyan-800 leading-tight">Fashion</span>
                    </a>
                    <a href="{{ url('/products') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-slate-50/50 hover:bg-cyan-50/70 border border-slate-100 hover:border-cyan-200 hover:shadow-xs transition-all group shrink-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-cyan-700 border border-sky-100 flex items-center justify-center text-base sm:text-lg mb-1.5 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-2xs">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 group-hover:text-cyan-800 leading-tight">Kuliner</span>
                    </a>
                    <a href="{{ url('/products') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-slate-50/50 hover:bg-cyan-50/70 border border-slate-100 hover:border-cyan-200 hover:shadow-xs transition-all group shrink-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-cyan-700 border border-sky-100 flex items-center justify-center text-base sm:text-lg mb-1.5 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-2xs">
                            <i class="fa-solid fa-spa"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 group-hover:text-cyan-800 leading-tight">Kecantikan</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="page-container py-1.5">
        <div class="bg-white rounded-2xl border border-sky-100 p-3 sm:p-4 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            
            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center shrink-0 border border-cyan-100">
                    <i class="fa-solid fa-shield-check text-base"></i>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block leading-tight">100% Produk Original</span>
                    <span class="text-[10px] text-slate-400">Jaminan garansi uang kembali</span>
                </div>
            </div>

            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                    <i class="fa-solid fa-truck-fast text-base"></i>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block leading-tight">Gratis Ongkir Rp0</span>
                    <span class="text-[10px] text-slate-400">Pengiriman cepat se-Indonesia</span>
                </div>
            </div>

            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center shrink-0 border border-sky-100">
                    <i class="fa-solid fa-credit-card text-base"></i>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block leading-tight">Pembayaran Lengkap &amp; Aman</span>
                    <span class="text-[10px] text-slate-400">QRIS, VA Bank, &amp; Kartu</span>
                </div>
            </div>

            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                    <i class="fa-solid fa-headset text-base"></i>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block leading-tight">Garansi Retur &amp; CS 24/7</span>
                    <span class="text-[10px] text-slate-400">Respon cepat bantuan kendala</span>
                </div>
            </div>

        </div>
    </section>

    @if(isset($activeFlashSale) && $activeFlashSale->items->count() > 0)
    <section class="page-container py-3"
             x-data="{
                endTime: new Date('{{ $activeFlashSale->end_time->toIso8601String() }}').getTime(),
                hours: '00',
                minutes: '00',
                seconds: '00',
                updateTimer() {
                    const now = new Date().getTime();
                    const distance = this.endTime - now;
                    if (distance > 0) {
                        this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                        this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                        this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                    } else {
                        this.hours = '00';
                        this.minutes = '00';
                        this.seconds = '00';
                    }
                },
                init() {
                    this.updateTimer();
                    setInterval(() => this.updateTimer(), 1000);
                }
             }">
        <div class="bg-white rounded-2xl border border-orange-200 p-4 shadow-xs">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center text-sm font-bold shadow-xs">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 uppercase tracking-tight">
                                FLASH SALE KILAT
                            </h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200">
                                Diskon Terbatas
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500">Berakhir dalam:</span>
                    <div class="flex items-center gap-1 font-mono text-xs font-bold text-white">
                        <span class="px-2 py-1 rounded bg-slate-900" x-text="hours">00</span>
                        <span class="text-slate-800 font-bold">:</span>
                        <span class="px-2 py-1 rounded bg-slate-900" x-text="minutes">00</span>
                        <span class="text-slate-800 font-bold">:</span>
                        <span class="px-2 py-1 rounded bg-slate-900" x-text="seconds">00</span>
                    </div>
                    <a href="{{ url('/products?flash_sale=1') }}" class="ml-2 text-xs font-bold text-cyan-700 hover:underline flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 pt-3.5">
                @foreach($activeFlashSale->items->take(6) as $item)
                @php $prod = $item->product; @endphp
                @if($prod)
                <div class="bg-white rounded-xl p-2.5 border border-slate-200 hover:border-cyan-400 hover:shadow-md transition-all flex flex-col justify-between group">
                    <a href="{{ route('product.show', $prod) }}" class="block">
                        <div class="relative w-full aspect-square rounded-lg overflow-hidden bg-slate-100 mb-2">
                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                            <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-orange-600 text-white shadow-xs">
                                -{{ $item->discount_percentage ?? 35 }}%
                            </span>
                        </div>

                        <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-cyan-700 transition-colors">
                            {{ $prod->name }}
                        </h3>

                        <div class="mt-2 flex flex-col">
                            <span class="text-sm font-black text-orange-600 leading-tight">
                                Rp {{ number_format($item->discount_price, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400 line-through mt-0.5">
                                Rp {{ number_format($prod->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </a>

                    <div class="mt-2.5 pt-2 border-t border-slate-100">
                        @php 
                            $soldCount = rand(8, 25);
                            $stockPct = rand(65, 92);
                        @endphp
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden relative">
                            <div class="bg-cyan-600 h-full rounded-full" style="width: {{ $stockPct }}%;"></div>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-500 mt-1 block">
                            Tersisa {{ rand(3, 12) }} item
                        </span>

                        <a href="{{ route('product.show', $prod) }}" class="mt-2 w-full h-7 bg-cyan-50 hover:bg-cyan-600 hover:text-white text-cyan-700 text-[11px] font-bold rounded-lg border border-cyan-200 hover:border-transparent flex items-center justify-center transition-colors">
                            Beli Sekarang
                        </a>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

        </div>
    </section>
    @endif

    @if(isset($officialStores) && $officialStores->count() > 0)
    <section class="page-container py-3">
        <div class="bg-white rounded-2xl border border-sky-100 p-4 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Official Store &amp; Brand Resmi
                    </h2>
                </div>
                <a href="{{ url('/products') }}" class="text-xs font-bold text-cyan-700 hover:underline flex items-center gap-1">
                    <span>Lihat Semua Toko</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach($officialStores as $store)
                <a href="{{ route('store.show', $store->slug ?? $store->id) }}" 
                   class="flex flex-col items-center text-center p-3.5 rounded-xl bg-slate-50/70 hover:bg-cyan-50/50 border border-slate-200 hover:border-cyan-300 transition-all group">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-2xs mb-2 group-hover:scale-105 transition-transform flex items-center justify-center">
                        <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                    </div>
                    <span class="text-xs font-bold text-slate-900 group-hover:text-cyan-800 transition-colors line-clamp-1 flex items-center gap-1">
                        {{ $store->name }}
                        <i class="fa-solid fa-circle-check text-cyan-600 text-[10px]"></i>
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5">
                        {{ $store->products_count ?? 12 }} Produk
                    </span>
                    <span class="mt-2 text-[10px] font-bold text-cyan-700 bg-white border border-cyan-200 px-2.5 py-0.5 rounded-md group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                        Kunjungi Toko
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="page-container py-3 mb-8" x-data="{ feedTab: 'all' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">
                    Rekomendasi Untuk Anda
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Produk pilihan terlaris dan paling banyak dicari</p>
            </div>

            <div class="flex items-center gap-1.5 text-xs font-bold">
                <button type="button" 
                        @click="feedTab = 'all'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer"
                        :class="feedTab === 'all' ? 'bg-cyan-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                    Semua Produk
                </button>
                <button type="button" 
                        @click="feedTab = 'top'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer"
                        :class="feedTab === 'top' ? 'bg-cyan-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                    Terlaris
                </button>
                <button type="button" 
                        @click="feedTab = 'official'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer"
                        :class="feedTab === 'official' ? 'bg-cyan-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                    Official Mall
                </button>
            </div>
        </div>

        <div x-show="feedTab === 'all'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5">
            @forelse($products as $prod)
            <div class="bg-white rounded-xl border border-sky-100 overflow-hidden shadow-xs hover:shadow-md hover:border-cyan-300 transition-all flex flex-col justify-between group relative">
                
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
                                        .finally(() => {
                                            isToggling = false;
                                            setTimeout(() => bounce = false, 300);
                                        });
                                    "
                                    class="w-7 h-7 rounded-full bg-white/90 shadow-xs border border-slate-200 flex items-center justify-center text-xs transition-transform active:scale-90"
                                    :class="bounce ? 'scale-125' : ''"
                                    title="Tambah ke Wishlist">
                                <i class="fa-heart transition-colors duration-200 text-xs"
                                   :class="isWish ? 'fa-solid text-red-500' : 'fa-regular text-slate-400 group-hover:text-red-400'"></i>
                            </button>
                        </div>
                    @endif
                @endauth

                <div>
                    <a href="{{ route('product.show', $prod) }}" class="block relative aspect-square bg-slate-50 overflow-hidden border-b border-slate-100">
                        <img src="{{ $prod->image_url }}" 
                             alt="{{ $prod->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                             loading="lazy">

                        @if($prod->has_discount)
                            <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-orange-600 text-white shadow-xs">
                                -{{ $prod->discount_percentage_effective }}%
                            </span>
                        @endif

                        <span class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded text-[8px] font-bold bg-emerald-600 text-white">
                            <i class="fa-solid fa-truck-fast text-[7px]"></i> Bebas Ongkir
                        </span>
                    </a>

                    <div class="p-3">
                        <div class="flex items-center gap-1 mb-1 text-[10px] text-cyan-700 font-medium">
                            <span class="px-1.5 py-0.5 rounded bg-sky-50 border border-sky-200 text-[9px] font-bold truncate">
                                {{ $prod->category->name ?? 'Produk' }}
                            </span>
                            <span class="text-slate-400 truncate">&bull; {{ $prod->store->name ?? 'NitipDong' }}</span>
                        </div>

                        <a href="{{ route('product.show', $prod) }}" class="block">
                            <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-cyan-700 transition-colors">
                                {{ $prod->name }}
                            </h3>
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
                </div>

                <div class="p-3 pt-0">
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star text-[9px]"></i>
                            <span>{{ number_format($prod->effective_rating, 1) }}</span>
                        </div>
                        <span class="text-slate-400 truncate">
                            {{ $prod->formatted_sold_count }} terjual
                        </span>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full py-16 px-4 text-center bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-cyan-700 border border-sky-100 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Produk Tersedia</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Produk pilihan dari toko-toko terpercaya akan segera hadir untuk Anda.</p>
                <div class="mt-4">
                    <a href="{{ url('/products') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors">
                        <span>Jelajahi Katalog</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <div x-show="feedTab === 'top'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5" x-cloak>
            @forelse($topProducts as $index => $prod)
            <div class="bg-white rounded-xl border border-sky-100 overflow-hidden shadow-xs hover:shadow-md hover:border-cyan-300 transition-all flex flex-col justify-between group relative">
                
                @if($index < 3)
                    <div class="absolute top-2 left-2 z-20 px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-extrabold text-[9px] shadow-xs flex items-center gap-1">
                        <i class="fa-solid fa-crown text-[8px]"></i> Top #{{ $index + 1 }}
                    </div>
                @endif

                <div>
                    <a href="{{ route('product.show', $prod) }}" class="block relative aspect-square bg-slate-50 overflow-hidden border-b border-slate-100">
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                        @if($prod->has_discount)
                            <span class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-orange-600 text-white shadow-xs">
                                -{{ $prod->discount_percentage_effective }}%
                            </span>
                        @endif
                    </a>

                    <div class="p-3">
                        <div class="flex items-center gap-1 mb-1 text-[10px] text-cyan-700 font-medium">
                            <span class="px-1.5 py-0.5 rounded bg-sky-50 border border-sky-200 text-[9px] font-bold truncate">
                                {{ $prod->category->name ?? 'Produk' }}
                            </span>
                            <span class="text-slate-400 truncate">&bull; {{ $prod->store->name ?? 'NitipDong' }}</span>
                        </div>

                        <a href="{{ route('product.show', $prod) }}" class="block">
                            <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-cyan-700 transition-colors">
                                {{ $prod->name }}
                            </h3>
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
                </div>

                <div class="p-3 pt-0">
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
            <div class="col-span-full py-16 px-4 text-center bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-fire text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Produk Terlaris</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Daftar produk paling laris akan ditampilkan setelah transaksi berlangsung.</p>
            </div>
            @endforelse
        </div>

        <div x-show="feedTab === 'official'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5" x-cloak>
            @forelse($officialProducts as $prod)
            <div class="bg-white rounded-xl border border-cyan-200 overflow-hidden shadow-xs hover:shadow-md hover:border-cyan-400 transition-all flex flex-col justify-between group relative">
                
                <div class="absolute top-2 left-2 z-20 px-2 py-0.5 rounded bg-cyan-700 text-white font-extrabold text-[9px] shadow-xs flex items-center gap-1">
                    <i class="fa-solid fa-shield-check text-[8px]"></i> Official
                </div>

                <div>
                    <a href="{{ route('product.show', $prod) }}" class="block relative aspect-square bg-slate-50 overflow-hidden border-b border-slate-100">
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                        @if($prod->has_discount)
                            <span class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-orange-600 text-white shadow-xs">
                                -{{ $prod->discount_percentage_effective }}%
                            </span>
                        @endif
                    </a>

                    <div class="p-3">
                        <div class="flex items-center gap-1 mb-1 text-[10px] text-cyan-700 font-medium">
                            <span class="text-[9px] font-bold text-cyan-700 bg-cyan-50 border border-cyan-200 px-1 py-0.2 rounded">MALL</span>
                            <span class="truncate">{{ $prod->store->name ?? 'NitipDong Official' }}</span>
                        </div>

                        <a href="{{ route('product.show', $prod) }}" class="block">
                            <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-cyan-700 transition-colors">
                                {{ $prod->name }}
                            </h3>
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
                </div>

                <div class="p-3 pt-0">
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star text-[9px]"></i>
                            <span>{{ number_format($prod->effective_rating, 1) }}</span>
                        </div>
                        <span class="text-slate-400 truncate">
                            {{ $prod->formatted_sold_count }} terjual
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 px-4 text-center bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-700 border border-cyan-100 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-store text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Produk Official Store</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Toko resmi terverifikasi sedang menyiapkan katalog produk terbaik.</p>
            </div>
            @endforelse
        </div>

        @if($products->count() > 0)
        <div class="mt-8 text-center">
            <a href="{{ url('/products') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all">
                <span>Lihat Semua Produk</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
        @endif

    </section>

    <section class="page-container mt-6 sm:mt-10 mb-8 sm:mb-12">
        <div class="bg-gradient-to-r from-cyan-50/80 via-sky-50/50 to-white rounded-2xl border border-cyan-100 p-5 sm:p-7 lg:p-8 shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                
                <div class="lg:col-span-8 space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-white p-1.5 shadow-xs border border-cyan-200 shrink-0 flex items-center justify-center">
                            <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Icon" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-cyan-700 uppercase tracking-wider block">Aplikasi Mobile Resmi</span>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">
                                Belanja Lebih Praktis Lewat Aplikasi NitipDong
                            </h3>
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-xl">
                        Dapatkan promo gratis ongkir eksklusif, notifikasi status pesanan secara langsung, dan kemudahan belanja dari ribuan toko resmi terpercaya.
                    </p>

                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-700 pt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-2xs">
                            <i class="fa-solid fa-truck-fast text-cyan-600 text-xs"></i>
                            <span>Gratis Ongkir Rp0</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-2xs">
                            <i class="fa-solid fa-shield-check text-emerald-600 text-xs"></i>
                            <span>100% Produk Original</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-2xs">
                            <i class="fa-solid fa-bell text-amber-500 text-xs"></i>
                            <span>Update Resi Real-Time</span>
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="{{ route('app.download') }}" class="h-10 px-5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition-colors">
                            <i class="fa-brands fa-android text-sm"></i>
                            <span>Unduh APK Android (v{{ env('APP_MOBILE_LATEST_VERSION', '2.5.1') }})</span>
                        </a>
                        <a href="{{ route('app.landing') }}" class="h-10 px-4 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-circle-info text-xs text-slate-400"></i>
                            <span>Info Aplikasi</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 hidden sm:flex justify-center lg:justify-end">
                    <div class="bg-white rounded-xl p-4 text-center border border-slate-200 shadow-xs max-w-[210px] w-full">
                        <div class="w-32 h-32 mx-auto p-1 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center mb-2">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https%3A%2F%2Fbudayakita.com%2Fdownload%2Fapp&bgcolor=ffffff&color=0f172a&margin=1"
                                 alt="QR Download NitipDong"
                                 class="w-full h-full object-contain rounded">
                        </div>
                        <h4 class="text-xs font-bold text-slate-900">Scan untuk Download</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5">Arahkan kamera HP Anda</p>
                        <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-center gap-1 text-[10px] text-emerald-600 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>APK Aman &amp; Terverifikasi</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-app-layout>
