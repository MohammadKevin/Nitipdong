<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BelanjaIn') }} — Belanja Online Mudah & Aman</title>
        <meta name="description" content="BelanjaIn — Platform marketplace terpercaya di Indonesia. Belanja jutaan produk berkualitas dari official store & seller resmi dengan diskon menarik dan jaminan keamanan transaksi.">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link rel="icon" type="image/jpeg" href="{{ asset('img/icon.jpg') }}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('head')
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col">

        <div class="bg-slate-950 text-slate-300 text-[11px] py-1.5 border-b border-slate-800 hidden sm:block">
            <div class="page-container flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5 text-cyan-400 font-medium">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i> 100% Transaksi Aman & Terverifikasi
                    </span>
                    <span class="text-slate-700">|</span>
                   
                </div>
                <div class="flex items-center gap-4 text-slate-400">
                    <a href="{{ route('store.register') }}" class="hover:text-cyan-300 transition-colors">Mulai Jualan di BelanjaIn</a>
                    <span class="text-slate-700">|</span>
                    <a href="#" class="hover:text-cyan-300 transition-colors">Pusat Bantuan</a>
                </div>
            </div>
        </div>

        @include('layouts.navigation')

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-slate-200/80 mt-16 text-slate-600 text-xs">
            <div class="page-container py-12">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8 pb-10 border-b border-slate-100">
                    <div class="md:col-span-2 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg overflow-hidden bg-cyan-50 border border-cyan-200 flex items-center justify-center">
                                <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-full h-full object-cover">
                            </div>
                            <span class="font-bold text-lg text-slate-900 tracking-tight">Belanja<span class="text-cyan-600 font-extrabold">In</span></span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                            Platform curated e-commerce modern untuk jual beli produk terpercaya, diskon flash sale harian, dan voucher belanja resmi dari berbagai merchant di Indonesia.
                        </p>
                        <div class="flex items-center gap-3 pt-2">
                            <a href="#" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                <i class="fa-brands fa-instagram text-xs"></i>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                <i class="fa-brands fa-facebook text-xs"></i>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                <i class="fa-brands fa-x-twitter text-xs"></i>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 flex items-center justify-center text-slate-500 transition-colors">
                                <i class="fa-brands fa-whatsapp text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-3 text-xs uppercase tracking-wider">Jelajahi</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ url('/products') }}" class="hover:text-cyan-700 transition-colors">Semua Produk</a></li>
                            <li><a href="{{ url('/products') }}?flash_sale=1" class="hover:text-cyan-700 transition-colors">Flash Sale</a></li>
                            <li><a href="{{ route('store.register') }}" class="hover:text-cyan-700 transition-colors">Buka Toko Gratis</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Voucher Promo</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-3 text-xs uppercase tracking-wider">Layanan</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Pusat Bantuan</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Cara Belanja</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Status Pengiriman</a></li>
                            <li><a href="#" class="hover:text-cyan-700 transition-colors">Garansi & Retur</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-3 text-xs uppercase tracking-wider">Keamanan & Mitra</h4>
                        <p class="text-[11px] text-slate-400 mb-3">Mendukung metode transfer bank & e-wallet resmi di Indonesia.</p>
                        <div class="flex flex-wrap gap-2 text-slate-700 font-semibold text-[10px]">
                            <span class="px-2.5 py-1 bg-slate-100 rounded-md border border-slate-200">BCA</span>
                            <span class="px-2.5 py-1 bg-slate-100 rounded-md border border-slate-200">Mandiri</span>
                            <span class="px-2.5 py-1 bg-slate-100 rounded-md border border-slate-200">BRI</span>
                            <span class="px-2.5 py-1 bg-slate-100 rounded-md border border-slate-200">BNI</span>
                            <span class="px-2.5 py-1 bg-slate-100 rounded-md border border-slate-200">QRIS</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
                    <p>&copy; {{ date('Y') }} BelanjaIn Platform. Hak Cipta Dilindungi.</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="hover:underline">Syarat & Ketentuan</a>
                        <a href="#" class="hover:underline">Kebijakan Privasi</a>
                        <a href="#" class="hover:underline">Keamanan</a>
                    </div>
                </div>
            </div>
        </footer>

        <x-chat-widget />

        @stack('scripts')
    </body>
</html>
