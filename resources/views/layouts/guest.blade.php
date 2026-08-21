<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BelanjaIn') }} — Autentikasi Akun</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/belanjain-logo.svg') }}">
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

<div class="min-h-screen flex flex-col lg:flex-row relative" id="auth-wrapper">

    {{-- ══════════════════════════════════════════════════
         LEFT PANEL — Dark navy (desktop only lg:flex)
    ══════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-between p-12 text-white"
         style="background: linear-gradient(160deg, #0c1a35 0%, #0f2a4a 45%, #083a52 100%);">

        {{-- Decorative glow blobs --}}
        <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(8,145,178,0.15), transparent 70%);"></div>
        <div class="absolute -bottom-20 -left-10 w-96 h-96 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(14,116,144,0.12), transparent 70%);"></div>

        {{-- Logo --}}
        <div class="relative z-10 flex items-center gap-3 mt-24 ml-20">
            <img src="{{ asset('img/belanjain-logo.svg') }}" alt="BelanjaIn" class="w-14 h-14 object-contain">
            <span class="font-extrabold text-3xl tracking-tight text-white">
                Belanja<span style="color:#38bdf8;">In</span>
            </span>
        </div>

        {{-- Main copy --}}
        <div class="relative z-10 max-w-md">
            <p class="text-xs font-bold uppercase tracking-widest mb-5 flex items-center gap-2" style="color:#0891b2;">
                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#0891b2;"></span>
                Marketplace Terpercaya
            </p>
            <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight tracking-tight mb-6">
                Pusat Belanja &amp; Toko Online<br>Masa Depan
            </h2>
            <p class="text-base text-slate-300 leading-relaxed max-w-sm">
                Akses jutaan produk pilihan, kelola pesanan real-time, atau mulai bisnis digital toko resmi Anda bersama ekosistem SakserShop.
            </p>
            <div class="grid grid-cols-2 gap-5 mt-12 pt-8 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                         style="background:rgba(8,145,178,0.15); border:1px solid rgba(8,145,178,0.3);">
                        <i class="fa-solid fa-shield-halved text-sm" style="color:#0891b2;"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-none">Keamanan 100%</p>
                        <p class="text-xs text-slate-400 mt-0.5">Terverifikasi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                         style="background:rgba(8,145,178,0.15); border:1px solid rgba(8,145,178,0.3);">
                        <i class="fa-solid fa-wallet text-sm" style="color:#0891b2;"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-none">Bebas Biaya</p>
                        <p class="text-xs text-slate-400 mt-0.5">Admin Awal</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} BelanjaIn Platform. Hak Cipta Dilindungi.</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         CURVE DIVIDER — absolute, centered exactly on the 50% boundary
         viewBox 120 wide: x=0..120, boundary is at x=60 (center).
         Curve starts at x=60 top, bulges LEFT to ~x=15 at midpoint,
         returns to x=60 bottom. White fill from curve rightward covers
         the right panel seamlessly. Blue gradient stroke = accent line.
    ══════════════════════════════════════════════════ --}}
    <div class="auth-curve-divider">
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
            {{-- White fill: from curve back to right edge (x=120) --}}
            <path d="M 60,0 C 15,220 5,380 15,500 C 25,620 55,780 60,1000
                     L 120,1000 L 120,0 Z"
                  fill="#ffffff"/>
            {{-- Smooth blue S-curve stroke --}}
            <path d="M 60,0 C 15,220 5,380 15,500 C 25,620 55,780 60,1000"
                  stroke="url(#cg)" stroke-width="5" stroke-linecap="round"
                  stroke-linejoin="round" fill="none"/>
        </svg>
    </div>

    {{-- ══════════════════════════════════════════════════
         RIGHT PANEL (desktop white) / FULL PAGE (mobile dark+white)
    ══════════════════════════════════════════════════ --}}
    <div class="w-full lg:w-1/2 flex flex-col min-h-screen" style="background:#ffffff;">

        {{-- ── MOBILE: dark header + dome curve (hidden on desktop) ── --}}
        <div class="lg:hidden relative flex flex-col items-center px-6 pt-12 pb-16"
             style="background: linear-gradient(160deg, #0c1a35 0%, #0f2a4a 45%, #0a3d5c 100%);">

            {{-- Close / back button --}}
            <a href="/"
               class="absolute top-5 right-5 w-9 h-9 rounded-full flex items-center justify-center
                      text-white/70 hover:text-white border border-white/20 bg-white/10
                      hover:bg-white/20 transition-all text-sm z-30">
                ✕
            </a>

            {{-- Mobile logo: direct logo image --}}
            <img src="{{ asset('img/belanjain-logo.svg') }}" alt="BelanjaIn" class="w-16 h-16 object-contain mb-3 drop-shadow-lg">
            <span class="font-extrabold text-2xl tracking-tight text-white">
                Belanja<span style="color:#38bdf8;">In</span>
            </span>

            {{-- Dome curve anchored to bottom of dark header — always flush --}}
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
                {{-- White dome fill covers the bottom of the dark header --}}
                <path d="M 0,80 Q 500,5 1000,80 L 1000,82 L 0,82 Z" fill="#ffffff"/>
                {{-- Blue gradient stroke on the dome edge --}}
                <path d="M 0,80 Q 500,5 1000,80"
                      stroke="url(#mg)" stroke-width="5"
                      stroke-linecap="round" fill="none"/>
            </svg>
        </div>

        {{-- ── FORM AREA — solid white ── --}}
        <div class="flex-1 bg-white
                    lg:flex lg:items-center lg:justify-center
                    px-6 pt-8 pb-10 sm:px-10 lg:px-16 lg:py-0
                    relative">

            {{-- Desktop close button --}}
            <a href="/"
               class="hidden lg:flex absolute top-5 right-5 z-50 w-9 h-9 rounded-full
                      bg-white shadow-md hover:shadow-lg items-center justify-center
                      text-slate-500 hover:text-slate-900 transition-all
                      border border-slate-200 text-sm font-medium">
                ✕
            </a>

            {{-- Form slot --}}
            <div class="w-full max-w-sm mx-auto lg:mx-0 relative z-10">
                {{ $slot }}
            </div>
        </div>
    </div>


</div>

<x-toast-notifier />

{{-- Premium Full-Screen Loading Overlay --}}
<div x-data="{ submitting: false }"
     x-on:auth-submitting.window="submitting = true"
     x-show="submitting"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md text-white select-none">
    <div class="relative flex items-center justify-center mb-6">
        <!-- Outer Glowing Spin Ring -->
        <div class="w-16 h-16 rounded-full border-4 border-cyan-500/20 border-t-cyan-400 animate-spin shadow-[0_0_15px_rgba(6,182,212,0.25)]"></div>
        <!-- Inner Glowing Logo -->
        <img src="{{ asset('img/belanjain-logo.svg') }}" alt="Logo" class="w-8 h-8 object-contain absolute animate-pulse">
    </div>
    <h3 class="text-base font-extrabold text-white tracking-tight animate-pulse">Menghubungkan ke Akun Anda</h3>
    <p class="text-xs text-slate-400 mt-1">Mohon tunggu sebentar, mempersiapkan dashboard...</p>
</div>

@stack('scripts')
</body>
</html>
