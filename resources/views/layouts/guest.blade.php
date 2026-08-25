<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NitipDong') }} — Autentikasi Akun</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">
    <style>
        /* Mobile: dark navy full-page gradient */
        .auth-page-bg {
            background: linear-gradient(160deg, #0c1a35 0%, #0f2a4a 45%, #0a3d5c 100%);
        }
        /* Desktop curve divider — centered exactly on the 50% boundary */
        .auth-curve-divider {
            position: absolute;
            top: 0;
            left: calc(50% - 60px); /* 120px wide, centered on boundary */
            width: 120px;
            height: 100%;
            z-index: 20;
            pointer-events: none;
        }
        @media (max-width: 1023px) {
            .auth-curve-divider { display: none; }
        }
    </style>
</head>
<body class="font-sans antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden;">

<div class="min-h-screen flex flex-col {{ $reverse ? 'lg:flex-row-reverse' : 'lg:flex-row' }} relative" id="auth-wrapper">

    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-between p-12 text-white"
         style="background: linear-gradient(160deg, #0c1a35 0%, #0f2a4a 45%, #083a52 100%);">

        <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(8,145,178,0.15), transparent 70%);"></div>
        <div class="absolute -bottom-20 -left-10 w-96 h-96 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(14,116,144,0.12), transparent 70%);"></div>

        <div class="relative z-10 flex items-center gap-3 mt-24 {{ $reverse ? 'mr-20 justify-start' : 'ml-20 justify-start' }}">
            <div class="w-12 h-12 rounded-2xl overflow-hidden bg-cyan-50 border border-cyan-200 flex items-center justify-center shadow-md p-1.5">
                <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong" class="w-full h-full object-contain">
            </div>
            <span class="font-extrabold text-3xl tracking-tight text-white">
                Nitip<span class="text-cyan-400">Dong</span>
            </span>
        </div>

        <div class="relative z-10 max-w-md {{ $reverse ? 'self-start pl-8' : '' }}">
            <p class="text-xs font-bold uppercase tracking-widest mb-5 flex items-center gap-2 text-cyan-400">
                <span class="w-1.5 h-1.5 rounded-full inline-block bg-cyan-400 animate-pulse"></span>
                Marketplace Terpercaya
            </p>
            <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight tracking-tight mb-6">
                Pusat Belanja &amp; Toko Online<br>Masa Depan
            </h2>
            <p class="text-base text-slate-300 leading-relaxed max-w-sm">
                Akses jutaan produk pilihan, kelola pesanan real-time, atau mulai bisnis digital toko resmi Anda bersama ekosistem NitipDong.
            </p>
            <div class="grid grid-cols-2 gap-5 mt-12 pt-8 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 bg-cyan-500/20 border border-cyan-400/30">
                        <i class="fa-solid fa-shield-halved text-sm text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-none">Keamanan 100%</p>
                        <p class="text-xs text-slate-400 mt-0.5">Terverifikasi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 bg-cyan-500/20 border border-cyan-400/30">
                        <i class="fa-solid fa-wallet text-sm text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-none">Bebas Biaya</p>
                        <p class="text-xs text-slate-400 mt-0.5">Admin Awal</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-10 {{ $reverse ? 'pl-8' : '' }}">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} NitipDong Platform. Hak Cipta Dilindungi.</p>
        </div>
    </div>

    <div class="auth-curve-divider" @if($reverse) style="transform: scaleX(-1);" @endif>
        <svg viewBox="0 0 120 1000" preserveAspectRatio="none"
             xmlns="http://www.w3.org/2000/svg"
             style="width:100%;height:100%;display:block;">
            <defs>
                <linearGradient id="cg" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#0ea5e9"/>
                    <stop offset="40%"  stop-color="#2563eb"/>
                    <stop offset="100%" stop-color="#0e7490"/>
                </linearGradient>
            </defs>
            <path d="M 60,0 C 15,220 5,380 15,500 C 25,620 55,780 60,1000
                     L 120,1000 L 120,0 Z"
                  fill="#ffffff"/>
            <path d="M 60,0 C 15,220 5,380 15,500 C 25,620 55,780 60,1000"
                  stroke="url(#cg)" stroke-width="5" stroke-linecap="round"
                  stroke-linejoin="round" fill="none"/>
        </svg>
    </div>

    <div class="w-full lg:w-1/2 flex flex-col min-h-screen bg-white relative">

        <div class="lg:hidden relative flex flex-col items-center px-6 pt-12 pb-16"
             style="background: linear-gradient(160deg, #0c1a35 0%, #0f2a4a 45%, #0a3d5c 100%);">

            <a href="/"
               class="absolute top-5 right-5 w-9 h-9 rounded-full flex items-center justify-center
                      text-white/70 hover:text-white border border-white/20 bg-white/10
                      hover:bg-white/20 transition-all text-sm z-30">
                ✕
            </a>

            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-10 h-10 rounded-xl overflow-hidden border border-cyan-200 bg-cyan-50 flex items-center justify-center shadow-xs p-1">
                    <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-2xl font-extrabold text-white tracking-tight">Nitip<span class="text-cyan-400">Dong</span></span>
            </div>

            <svg style="position:absolute;bottom:-1px;left:0;width:100%;height:68px;display:block;"
                 viewBox="0 0 1000 80" preserveAspectRatio="none"
                 xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="mg" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%"   stop-color="#0ea5e9"/>
                        <stop offset="50%"  stop-color="#2563eb"/>
                        <stop offset="100%" stop-color="#0e7490"/>
                    </linearGradient>
                </defs>
                <path d="M 0,80 Q 500,5 1000,80 L 1000,82 L 0,82 Z" fill="#ffffff"/>
                <path d="M 0,80 Q 500,5 1000,80"
                      stroke="url(#mg)" stroke-width="5"
                      stroke-linecap="round" fill="none"/>
            </svg>
        </div>

        <div class="flex-1 bg-white
                    lg:flex lg:items-center lg:justify-center
                    px-6 pt-8 pb-10 sm:px-10 lg:px-16 lg:py-0
                    relative">

            <a href="/"
               class="hidden lg:flex absolute top-5 {{ $reverse ? 'left-5' : 'right-5' }} z-50 w-9 h-9 rounded-full
                      bg-white shadow-md hover:shadow-lg items-center justify-center
                      text-slate-500 hover:text-slate-900 transition-all
                      border border-slate-200 text-sm font-medium">
                ✕
            </a>

            <div class="w-full max-w-sm mx-auto lg:mx-0 relative z-10">
                {{ $slot }}
            </div>
        </div>
    </div>

</div>

<x-toast-notifier />

<div x-data="{ submitting: false }"
     x-on:auth-submitting.window="submitting = true"
     x-show="submitting"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md text-white select-none">
    <div class="relative flex items-center justify-center mb-6">
        <div class="w-16 h-16 rounded-full border-4 border-cyan-500/20 border-t-cyan-400 animate-spin shadow-[0_0_15px_rgba(6,182,212,0.25)]"></div>
        <div class="w-8 h-8 rounded-lg overflow-hidden absolute animate-pulse">
            <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
        </div>
    </div>
    <h3 class="text-base font-extrabold text-white tracking-tight animate-pulse">Menghubungkan ke Akun Anda</h3>
    <p class="text-xs text-slate-400 mt-1">Mohon tunggu sebentar, mempersiapkan dashboard...</p>
</div>

@stack('scripts')
</body>
</html>
