<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SakserShop') }} — Autentikasi Akun</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">
</head>
<body class="font-sans text-slate-900 antialiased bg-slate-50 min-h-screen">
    <div class="min-h-screen flex">
        
        <div class="hidden lg:flex lg:w-1/2 bg-slate-950 relative overflow-hidden flex-col justify-between p-12 text-white border-r border-slate-800">
            <div class="relative z-10 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg overflow-hidden border border-cyan-400/40 bg-cyan-950 flex items-center justify-center">
                        <img src="{{ asset('img/saksershop-logo.png') }}" alt="SakserShop Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="font-bold text-lg tracking-tight text-white leading-tight">
                        Sakser<span class="text-cyan-400 font-extrabold">Shop</span>
                    </span>
                </a>
                <a href="/" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            <div class="relative z-10 max-w-md my-auto py-12">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-300 border border-cyan-400/20 mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    Marketplace Terpercaya
                </span>
                <h2 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
                    Pusat Belanja & Toko Online Masa Depan
                </h2>
                <p class="text-sm text-slate-300 mt-3 leading-relaxed">
                    Akses jutaan produk pilihan, kelola pesanan real-time, atau mulai bisnis digital toko resmi Anda bersama ekosistem SakserShop.
                </p>

                <div class="grid grid-cols-2 gap-3.5 mt-8 pt-8 border-t border-slate-800 text-xs">
                    <div class="flex items-center gap-2.5 text-slate-300">
                        <i class="fa-solid fa-circle-check text-cyan-400"></i>
                        <span>Keamanan 100% Terverifikasi</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-slate-300">
                        <i class="fa-solid fa-circle-check text-cyan-400"></i>
                        <span>Bebas Biaya Admin Awal</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-[11px] text-slate-500">
                &copy; {{ date('Y') }} SakserShop Platform. Hak Cipta Dilindungi.
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 bg-white relative">
            <a href="/" class="lg:hidden absolute top-6 left-6 flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-cyan-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>

            <div class="lg:hidden flex items-center gap-2.5 mb-8">
                <div class="w-8 h-8 rounded-lg overflow-hidden border border-cyan-200 bg-cyan-50 flex items-center justify-center">
                    <img src="{{ asset('img/saksershop-logo.png') }}" alt="SakserShop Logo" class="w-full h-full object-cover">
                </div>
                <span class="text-xl font-bold text-slate-900 tracking-tight">Sakser<span class="text-cyan-600 font-extrabold">Shop</span></span>
            </div>

            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
