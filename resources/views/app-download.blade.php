<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi NitipDong Mobile — Android (APK) & iOS (IPA)</title>
    <meta name="description" content="Download aplikasi NitipDong resmi untuk Android (APK) dan iOS (IPA). Nikmati pengalaman belanja dan titip beli lebih cepat, aman, dan hemat langsung dari smartphone Anda.">
    <link rel="icon" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-cyan-500 selection:text-white" x-data="{ platform: 'android' }">

    <!-- Sticky Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
        <div class="page-container">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl overflow-hidden border border-cyan-200 bg-cyan-50 flex items-center justify-center p-1 shadow-2xs group-hover:scale-105 transition-transform">
                        <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="font-extrabold text-base tracking-tight text-slate-900 block leading-none">
                            Nitip<span class="text-cyan-600">Dong</span>
                        </span>
                        <span class="text-[9px] font-bold text-cyan-700 uppercase tracking-widest">Apps Hub</span>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ url('/products') }}" class="hidden sm:inline-flex text-xs font-semibold text-slate-600 hover:text-cyan-600 transition-colors">
                        Jelajahi Produk
                    </a>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:text-cyan-700 hover:border-cyan-200 text-xs font-bold shadow-2xs transition-all">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Ke Website</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-8 pb-12 sm:pt-12 sm:pb-16 bg-gradient-to-b from-white via-cyan-50/40 to-slate-50 border-b border-slate-200/60">
            <div class="page-container">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    <!-- Left Hero Text & CTA -->
                    <div class="lg:col-span-7 xl:col-span-8 space-y-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-100/80 border border-cyan-200 text-cyan-800 text-xs font-extrabold shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-cyan-600 animate-pulse"></span>
                            <span>Aplikasi Resmi • Versi v{{ env('APP_MOBILE_LATEST_VERSION', '2.5.1') }}</span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            Belanja Online &amp; Titip Jastip <br class="hidden sm:inline">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">Lebih Cepat di Aplikasi</span>
                        </h1>

                        <p class="text-xs sm:text-sm lg:text-base text-slate-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            Download aplikasi NitipDong untuk Android dan iOS. Nikmati promo gratis ongkir Rp0, notifikasi resi pengiriman real-time, dan transaksi aman dengan proteksi rekening bersama.
                        </p>

                        <!-- CTA Download Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                            <a href="{{ route('app.download.android') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 active:scale-98 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-3 shadow-md shadow-cyan-600/20 transition-all">
                                <i class="fa-brands fa-android text-xl"></i>
                                <div class="text-left">
                                    <span class="block leading-none text-[10px] text-cyan-100 font-medium">Download Langsung</span>
                                    <span class="block text-sm font-black mt-0.5">Android APK (v{{ env('APP_MOBILE_LATEST_VERSION', '2.5.1') }})</span>
                                </div>
                            </a>

                            <a href="{{ route('app.download.ios') }}" class="w-full sm:w-auto px-5 py-3.5 rounded-xl bg-white hover:bg-slate-50 active:scale-98 border border-slate-300 hover:border-slate-400 text-slate-800 font-bold text-xs sm:text-sm flex items-center justify-center gap-3 shadow-2xs transition-all">
                                <i class="fa-brands fa-apple text-xl text-slate-900"></i>
                                <div class="text-left">
                                    <span class="block leading-none text-[10px] text-slate-500 font-medium">Sideload / IPA</span>
                                    <span class="block text-sm font-bold mt-0.5 text-slate-900">iOS iPhone (IPA)</span>
                                </div>
                            </a>
                        </div>

                        <!-- Trust Checkmarks -->
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-3 text-[11px] font-semibold text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-check text-emerald-500"></i> Bebas Iklan &amp; Malware
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-check text-emerald-500"></i> Server Cloud Cepat
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-check text-emerald-500"></i> Ukuran Ringan (~28 MB)
                            </span>
                        </div>
                    </div>

                    <!-- Right QR Code Download Card -->
                    <div class="lg:col-span-5 xl:col-span-4 flex justify-center lg:justify-end">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xl max-w-[280px] w-full text-center relative overflow-hidden">
                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-cyan-100 rounded-full blur-2xl pointer-events-none"></div>

                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-cyan-700 block mb-1">Scan Pakai Kamera HP</span>
                            <h3 class="text-sm font-black text-slate-900 mb-3">Install Otomatis di HP</h3>

                            <div class="w-40 h-40 mx-auto p-2 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=https%3A%2F%2Fbudayakita.com%2Fdownload%2Fapp&bgcolor=ffffff&color=083344&margin=1"
                                     alt="QR Code NitipDong Download"
                                     class="w-full h-full object-contain rounded-xl">
                            </div>

                            <p class="text-[11px] text-slate-500 leading-snug">
                                Arahkan kamera HP Anda ke kode QR untuk mengunduh APK versi terbaru secara instan.
                            </p>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-center gap-1.5 text-[10.5px] font-bold text-emerald-700">
                                <i class="fa-solid fa-shield-check text-xs"></i>
                                <span>APK Resmi Terverifikasi</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Stats Bar -->
        <section class="bg-white border-b border-slate-200/80 py-6 shadow-2xs">
            <div class="page-container">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-slate-100">
                    <div class="px-2">
                        <span class="block text-2xl sm:text-3xl font-black text-cyan-700">100%</span>
                        <span class="text-xs font-semibold text-slate-500 mt-0.5 block">Produk Original</span>
                    </div>
                    <div class="px-2">
                        <span class="block text-2xl sm:text-3xl font-black text-cyan-700">Rp0</span>
                        <span class="text-xs font-semibold text-slate-500 mt-0.5 block">Voucher Gratis Ongkir</span>
                    </div>
                    <div class="px-2">
                        <span class="block text-2xl sm:text-3xl font-black text-cyan-700">Real-Time</span>
                        <span class="text-xs font-semibold text-slate-500 mt-0.5 block">Pelacakan Resi Kurir</span>
                    </div>
                    <div class="px-2">
                        <span class="block text-2xl sm:text-3xl font-black text-cyan-700">24/7</span>
                        <span class="text-xs font-semibold text-slate-500 mt-0.5 block">Bantuan &amp; AI Assistant</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- App Features Highlights -->
        <section class="py-12 sm:py-16 page-container">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-extrabold uppercase tracking-wider text-cyan-600 block mb-1">Keunggulan Aplikasi</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kenapa Harus Pakai Aplikasi NitipDong?</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">Dibuat khusus untuk memberikan pengalaman belanja yang lebih lancar dan hemat.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-300 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 border border-cyan-100 flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Update Resi Otomatis</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pantau perjalanan paket dari toko ke rumah tanpa perlu cek resi manual berulang kali.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-300 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Voucher Khusus Aplikasi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Klaim kupon diskon tambahan dan voucher gratis ongkir tanpa batas minimum belanja.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-300 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Rekening Bersama Escrow</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pembayaran Anda aman terjaga. Dana baru diteruskan ke seller setelah paket Anda terima.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-300 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Chat Toko &amp; AI Asisten</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Tanya ketersediaan stok produk dan konsultasi rekomendasi barang secara instan 24/7.</p>
                </div>
            </div>
        </section>

        <!-- Step-by-Step Installation Guide -->
        <section class="py-12 sm:py-16 bg-white border-y border-slate-200/80">
            <div class="page-container max-w-3xl">
                <div class="text-center mb-8">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-cyan-600 block mb-1">Panduan Pengguna</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Cara Install Aplikasi</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Ikuti 3 langkah mudah berikut untuk memasang aplikasi di HP Anda</p>
                </div>

                <!-- Platform Tabs -->
                <div class="flex items-center justify-center gap-2 mb-6">
                    <button type="button" 
                            @click="platform = 'android'"
                            class="px-5 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 cursor-pointer transition-all"
                            :class="platform === 'android' ? 'bg-cyan-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <i class="fa-brands fa-android text-sm"></i>
                        <span>Panduan Android (APK)</span>
                    </button>

                    <button type="button" 
                            @click="platform = 'ios'"
                            class="px-5 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 cursor-pointer transition-all"
                            :class="platform === 'ios' ? 'bg-cyan-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <i class="fa-brands fa-apple text-sm"></i>
                        <span>Panduan iOS iPhone (IPA)</span>
                    </button>
                </div>

                <!-- Android Steps -->
                <div x-show="platform === 'android'" class="space-y-3.5">
                    <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-8 h-8 rounded-xl bg-cyan-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            1
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">Download File APK</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Klik tombol <strong>"Download APK"</strong> di atas atau <a href="{{ route('app.download.android') }}" class="text-cyan-700 font-bold underline hover:text-cyan-800">download langsung APK Android</a> (~28 MB).
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-8 h-8 rounded-xl bg-cyan-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            2
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">Izinkan Pemasangan APK (Sumber Tidak Dikenal)</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Saat membuka file, jika muncul peringatan keamanan, pilih <strong>Pengaturan (Settings)</strong> → Aktifkan <strong>"Izinkan dari sumber ini (Allow from this source)"</strong>.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-8 h-8 rounded-xl bg-cyan-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            3
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">Pasang &amp; Mulai Belanja</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Tekan tombol <strong>"Install"</strong>. Tunggu beberapa detik hingga selesai, lalu buka aplikasi NitipDong untuk login dan mulai berbelanja! 🎉
                            </p>
                        </div>
                    </div>
                </div>

                <!-- iOS Steps -->
                <div x-show="platform === 'ios'" x-cloak class="space-y-3.5">
                    <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            1
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">Unduh Paket IPA iOS</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Unduh file <a href="{{ route('app.download.ios') }}" class="text-cyan-700 font-bold underline hover:text-cyan-800">NitipDong-latest.ipa</a> ke komputer PC atau Mac Anda.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            2
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">Gunakan Sideloadly atau AltStore</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Buka aplikasi <a href="https://sideloadly.io" target="_blank" rel="noopener" class="text-cyan-700 font-bold underline">Sideloadly</a> atau AltStore di PC/Mac, lalu sambungkan iPhone via kabel data USB.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            3
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">Tarik File &amp; Pasang ke iPhone</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Masukkan file IPA ke Sideloadly, klik Start untuk proses instalasi langsung ke iPhone Anda.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Specs Details Card -->
        <section class="py-10 page-container max-w-3xl">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-cyan-600"></i>
                    <span>Informasi Teknis Rilis Aplikasi</span>
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Versi Saat Ini</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">v{{ env('APP_MOBILE_LATEST_VERSION', '2.5.1') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Ukuran File APK</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">~28.4 MB</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Sistem Operasi</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">Android 5.0+ / iOS 13+</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Lisensi</span>
                        <span class="font-bold text-emerald-600 text-sm mt-0.5 block">Gratis (Official)</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Clean Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="page-container flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} NitipDong Platform. Hak Cipta Dilindungi.</p>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <a href="{{ url('/') }}" class="hover:text-cyan-700 transition-colors">Beranda</a>
                <a href="{{ url('/products') }}" class="hover:text-cyan-700 transition-colors">Katalog Produk</a>
                <a href="{{ route('store.register') }}" class="hover:text-cyan-700 transition-colors">Buka Toko</a>
            </div>
        </div>
    </footer>

</body>
</html>
