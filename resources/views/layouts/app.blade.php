<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden max-w-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NitipDong') }} — Belanja Online Mudah, Cepat & Aman</title>
        <meta name="description" content="NitipDong — Marketplace terpercaya di Indonesia. Belanja jutaan produk original, flash sale kilat diskon hingga 70%, dan voucher gratis ongkir Rp0 ke seluruh Indonesia.">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link rel="icon" type="image/svg+xml" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#0891b2">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="NitipDong">
        <link rel="apple-touch-icon" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('head')
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col font-sans selection:bg-cyan-500 selection:text-white overflow-x-hidden w-full max-w-full">

        <div class="bg-slate-950 text-slate-300 text-[11px] py-1.5 border-b border-slate-800 hidden sm:block">
            <div class="page-container flex items-center justify-between">
                <div class="flex items-center gap-3 text-slate-400">
                    <a href="{{ route('store.register') }}" class="hover:text-cyan-300 font-medium transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-store text-[10px] text-cyan-400"></i> Mulai Jual di NitipDong
                    </a>
                    <span class="text-slate-700">|</span>
                    <div class="flex items-center gap-2">
                        <span>Ikuti kami:</span>
                        <a href="#" class="hover:text-cyan-300"><i class="fa-brands fa-facebook text-[11px]"></i></a>
                        <a href="#" class="hover:text-cyan-300"><i class="fa-brands fa-instagram text-[11px]"></i></a>
                        <a href="#" class="hover:text-cyan-300"><i class="fa-brands fa-tiktok text-[11px]"></i></a>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-slate-400">
                    <a href="#" class="hover:text-cyan-300 transition-colors flex items-center gap-1">
                        <i class="fa-regular fa-bell text-[10px]"></i> Notifikasi
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="#" class="hover:text-cyan-300 transition-colors flex items-center gap-1">
                        <i class="fa-regular fa-circle-question text-[10px]"></i> Bantuan
                    </a>
                    <span class="text-slate-700">|</span>
                    <span class="flex items-center gap-1 text-slate-300">
                        <i class="fa-solid fa-globe text-[10px]"></i> Bahasa Indonesia
                    </span>
                </div>
            </div>
        </div>

        @include('layouts.navigation')

        <main class="flex-1 pb-20 md:pb-0">
            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-slate-200/80 mt-12 text-slate-600 text-xs">
            <div class="page-container py-12">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8 pb-10 border-b border-slate-100">

                    <div class="md:col-span-2 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl overflow-hidden bg-cyan-50 border border-cyan-200 flex items-center justify-center shadow-xs p-1">
                                <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Logo" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <span class="font-bold text-lg text-slate-900 tracking-tight block leading-none">
                                    Nitip<span class="text-cyan-600 font-black">Dong</span>
                                </span>
                                <span class="text-[9px] font-bold text-cyan-700 uppercase tracking-wider">Marketplace &amp; Official Mall</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                            Platform e-commerce &amp; titip beli terpercaya di Indonesia. Menyediakan jutaan produk original, flash sale harian diskon hingga 80%, voucher gratis ongkir Rp0, dan perlindungan transaksi 100% aman.
                        </p>
                        
                        <div class="pt-2">
                            <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-2">Ikuti Media Sosial Kami</p>
                            <div class="flex items-center gap-2">
                                <a href="#" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                    <i class="fa-brands fa-instagram text-xs"></i>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                    <i class="fa-brands fa-facebook text-xs"></i>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                    <i class="fa-brands fa-tiktok text-xs"></i>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                    <i class="fa-brands fa-youtube text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-3.5 text-xs uppercase tracking-wider">Layanan Pelanggan</h4>
                        <ul class="space-y-2 text-xs">
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Pusat Bantuan &amp; FAQ</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Cara Belanja &amp; Bayar</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Pengiriman &amp; Pelacakan Resi</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Garansi 100% Produk Original</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Pengembalian Barang &amp; Retur</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Customer Service 24/7</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-3.5 text-xs uppercase tracking-wider">Solusi Penjual</h4>
                        <ul class="space-y-2 text-xs">
                            <li><a href="{{ route('store.register') }}" class="text-cyan-700 font-bold hover:underline">Buka Toko Gratis</a></li>
                            <li><a href="{{ route('store.register') }}" class="hover:text-cyan-700 transition-colors">Mitra Official Store</a></li>
                            <li><a href="{{ url('/products') }}" class="hover:text-cyan-700 transition-colors">Katalog Semua Produk</a></li>
                            <li><a href="{{ url('/products?flash_sale=1') }}" class="hover:text-cyan-700 transition-colors">Flash Sale Kilat</a></li>
                            <li><a href="{{ route('customer.vouchers.index') }}" class="hover:text-cyan-700 transition-colors">Klaim Voucher Belanja</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Kebijakan Privasi &amp; Syarat</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-2.5 text-xs uppercase tracking-wider">Pembayaran Resmi</h4>
                        <div class="grid grid-cols-3 gap-1.5 mb-4 text-center font-bold text-[10px] text-slate-700">
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">BCA</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">Mandiri</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">BRI</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">BNI</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">QRIS</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">GoPay</span>
                        </div>

                        <h4 class="font-bold text-slate-900 mb-2.5 text-xs uppercase tracking-wider">Jasa Pengiriman</h4>
                        <div class="grid grid-cols-3 gap-1.5 text-center font-bold text-[10px] text-slate-700">
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">JNE</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">J&T</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">SiCepat</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">AnterAja</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">GoSend</span>
                            <span class="p-1.5 bg-slate-50 rounded-lg border border-slate-200">Instant</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
                    <p>&copy; {{ date('Y') }} PT NitipDong Niaga Nusantara. Hak Cipta Dilindungi.</p>
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                            <i class="fa-solid fa-shield-check text-xs"></i> 100% Verified SSL Secure
                        </span>
                        <span class="text-slate-300">•</span>
                        <a href="#" class="hover:underline">Kebijakan Privasi</a>
                        <a href="#" class="hover:underline">Syarat & Ketentuan</a>
                        <a href="#" class="hover:underline">Peta Situs</a>
                    </div>
                </div>
            </div>
        </footer>

        <x-chat-popup />
        <x-chat-widget />
        <x-toast-notifier />
        <x-mobile-bottom-nav />

        @stack('scripts')
    </body>
</html>
