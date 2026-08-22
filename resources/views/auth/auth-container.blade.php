<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $initialMode === 'register' ? 'NitipDong — Buat Akun Baru' : 'NitipDong — Masuk ke Akun' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0891b2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NitipDong">
    <link rel="apple-touch-icon" href="{{ asset('img/icons/icon-192x192.png') }}">
    <style>
        /* Dedicated solid dark gradient background */
        .auth-brand-bg {
            background: linear-gradient(145deg, #0b1528 0%, #0f2744 40%, #083344 100%) !important;
            background-color: #0b1528 !important;
        }
        /* Smooth bezier easing for sliding auth panel */
        .auth-sliding-overlay {
            transition: transform 700ms cubic-bezier(0.65, 0, 0.35, 1), clip-path 700ms cubic-bezier(0.65, 0, 0.35, 1);
            will-change: transform, clip-path;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white text-slate-900 overflow-x-hidden selection:bg-cyan-500 selection:text-white" 
      style="font-family: 'Plus Jakarta Sans', sans-serif;">

{{-- SVG Definition for Dynamic Tamed Double-Curve S-Wave ClipPaths --}}
<svg class="sr-only" width="0" height="0" aria-hidden="true">
    <defs>
        {{-- Tamed S-Wave for Login (Banner on Right, 5-8% gentle curve) --}}
        <clipPath id="authDoubleWaveLogin" clipPathUnits="objectBoundingBox">
            <path d="M 1 0 L 0.06 0 C 0 0.25, 0 0.45, 0.04 0.60 C 0.07 0.75, 0.07 0.90, 0.02 1.0 L 1 1 Z" />
        </clipPath>
        {{-- Tamed S-Wave for Register (Banner on Left, Mirrored) --}}
        <clipPath id="authDoubleWaveRegister" clipPathUnits="objectBoundingBox">
            <path d="M 0 0 L 0.94 0 C 1 0.25, 1 0.45, 0.96 0.60 C 0.93 0.75, 0.93 0.90, 0.98 1.0 L 0 1 Z" />
        </clipPath>
        {{-- Cyan Glowing Gradient for Edge Stroke --}}
        <linearGradient id="cyanDualGlow" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%"   stop-color="#06b6d4" />
            <stop offset="35%"  stop-color="#38bdf8" />
            <stop offset="70%"  stop-color="#0ea5e9" />
            <stop offset="100%" stop-color="#06b6d4" />
        </linearGradient>
    </defs>
</svg>

<div x-data="{
    isRegister: {{ $initialMode === 'register' ? 'true' : 'false' }},
    init() {
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.mode) {
                this.isRegister = (e.state.mode === 'register');
            } else {
                this.isRegister = window.location.pathname.includes('register');
            }
            this.updateTitle();
        });
    },
    setMode(mode) {
        this.isRegister = (mode === 'register');
        const targetUrl = this.isRegister ? '{{ route('register') }}' : '{{ route('login') }}';
        window.history.pushState({ mode: this.isRegister ? 'register' : 'login' }, '', targetUrl);
        this.updateTitle();
    },
    updateTitle() {
        document.title = this.isRegister 
            ? 'NitipDong — Buat Akun Baru' 
            : 'NitipDong — Masuk ke Akun';
    }
}" class="w-full min-h-screen relative overflow-hidden bg-white flex flex-col lg:flex-row border-none shadow-none m-0 p-0" id="auth-main-container">

    {{-- ══════════════════════════════════════════════════
         DESKTOP SLIDING LAYOUT (lg: and above)
    ══════════════════════════════════════════════════ --}}

    {{-- 1. LEFT FORM CONTAINER (LOGIN FORM) --}}
    <div class="hidden lg:flex lg:w-1/2 min-h-screen flex-col justify-center items-center p-6 sm:p-12 lg:px-16 bg-white relative z-10 overflow-hidden border-none shadow-none m-0">
        {{-- Close Button --}}
        <a href="/" class="absolute top-6 left-6 z-50 w-9 h-9 rounded-full bg-white shadow-xs border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition-all" title="Kembali ke Beranda">
            ✕
        </a>

        <div class="w-full max-w-md mx-auto my-auto transition-all duration-500"
             :class="!isRegister ? 'opacity-100 translate-x-0 pointer-events-auto delay-150' : 'opacity-0 -translate-x-12 pointer-events-none'">
            
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="mb-6">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-cyan-600 mb-1.5 block">Autentikasi Akun</span>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Selamat Datang Kembali
                </h1>
                <p class="text-xs text-slate-500 mt-1">Masuk ke akun NitipDong Anda untuk melanjutkan transaksi</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-on:submit="$dispatch('auth-submitting')">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="login_email" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 block">
                        Alamat Email
                    </label>
                    <input id="login_email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username" 
                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-3 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" 
                           placeholder="nama@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="login_password" class="text-xs font-semibold uppercase tracking-wider text-slate-600 block">
                            Kata Sandi
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-cyan-700 font-semibold hover:underline">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="login_password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password" 
                               class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-3 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none pr-12" 
                               placeholder="••••••••">
                        <button type="button"
                                onclick="const i=document.getElementById('login_password'); i.type=i.type==='password'?'text':'password';"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-regular fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500" />
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="login_remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                    <label for="login_remember_me" class="ml-2 text-xs text-slate-600 cursor-pointer">Ingat sesi login saya</label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-3 rounded-xl shadow-md hover:shadow-lg shadow-cyan-600/20 transition-all duration-200 flex items-center justify-center cursor-pointer text-xs tracking-wider uppercase">
                    Masuk ke Akun
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">Belum memiliki akun? 
                    <button type="button" @click="setMode('register')" class="text-cyan-700 font-bold hover:underline cursor-pointer">
                        Daftar sekarang
                    </button>
                </p>
            </div>
        </div>

        {{-- Fixed Absolute Bottom Footer --}}
        <div class="absolute bottom-6 left-8 text-[11px] text-slate-400">
            &copy; {{ date('Y') }} NitipDong Platform. Hak Cipta Dilindungi.
        </div>
    </div>

    {{-- 2. RIGHT FORM CONTAINER (REGISTER FORM) --}}
    <div class="hidden lg:flex lg:w-1/2 min-h-screen flex-col justify-center items-center p-6 sm:p-12 lg:px-16 bg-white relative z-10 overflow-hidden border-none shadow-none m-0">
        {{-- Close Button --}}
        <a href="/" class="absolute top-6 right-6 z-50 w-9 h-9 rounded-full bg-white shadow-xs border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition-all" title="Kembali ke Beranda">
            ✕
        </a>

        <div class="w-full max-w-md mx-auto my-auto transition-all duration-500"
             :class="isRegister ? 'opacity-100 translate-x-0 pointer-events-auto delay-150' : 'opacity-0 translate-x-12 pointer-events-none'">
            
            <div class="mb-6">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-cyan-600 mb-1.5 block">Pendaftaran Baru</span>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Buat Akun NitipDong
                </h1>
                <p class="text-xs text-slate-500 mt-1">Daftar sekarang untuk mulai berbelanja dan menikmati voucher eksklusif</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5" x-on:submit="$dispatch('auth-submitting')">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="reg_name" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 block">
                        Nama Lengkap
                    </label>
                    <input id="reg_name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autocomplete="name"
                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none"
                           placeholder="Budi Santoso">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500"/>
                </div>

                {{-- Email --}}
                <div>
                    <label for="reg_email" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 block">
                        Alamat Email
                    </label>
                    <input id="reg_email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="username"
                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none"
                           placeholder="yourmail@mail.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500"/>
                </div>

                {{-- Password --}}
                <div>
                    <label for="reg_password" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 block">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input id="reg_password"
                               type="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none pr-12"
                               placeholder="Minimal 8 karakter">
                        <button type="button"
                                onclick="const i=document.getElementById('reg_password'); i.type=i.type==='password'?'text':'password';"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-regular fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500"/>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="reg_password_confirmation" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 block">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="relative">
                        <input id="reg_password_confirmation"
                               type="password"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none pr-12"
                               placeholder="Ulangi kata sandi">
                        <button type="button"
                                onclick="const i=document.getElementById('reg_password_confirmation'); i.type=i.type==='password'?'text':'password';"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-regular fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500"/>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-3 rounded-xl shadow-md hover:shadow-lg shadow-cyan-600/20 transition-all duration-200 flex items-center justify-center cursor-pointer text-xs tracking-wider uppercase mt-2">
                    Daftar Akun
                </button>
            </form>

            <div class="mt-5 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">Sudah memiliki akun? 
                    <button type="button" @click="setMode('login')" class="text-cyan-700 font-bold hover:underline cursor-pointer">
                        Masuk sekarang
                    </button>
                </p>
            </div>
        </div>

        {{-- Fixed Absolute Bottom Footer --}}
        <div class="absolute bottom-6 right-8 text-[11px] text-slate-400 text-right">
            &copy; {{ date('Y') }} NitipDong Platform. Hak Cipta Dilindungi.
        </div>
    </div>

    {{-- 3. THE SLIDING SOLID DEEP NAVY & CYAN BRAND BANNER OVERLAY PANEL (Balanced S-Wave ClipPath) --}}
    <div class="hidden lg:flex lg:w-1/2 h-full absolute top-0 bottom-0 left-0 z-30 auth-sliding-overlay auth-brand-bg flex-col justify-center items-center px-8 lg:px-16 py-12 text-white border-none shadow-none m-0"
         :style="!isRegister ? 'clip-path: url(#authDoubleWaveLogin);' : 'clip-path: url(#authDoubleWaveRegister);'"
         :class="!isRegister ? 'translate-x-full' : 'translate-x-0'">

        {{-- Dynamic Glowing Cyan S-Curve Edge Stroke (Tamed 5-8% excursion) --}}
        <svg class="absolute inset-0 w-full h-full pointer-events-none z-20 overflow-visible"
             viewBox="0 0 100 1000" preserveAspectRatio="none">
            {{-- Stroke for Login (Banner on Right) --}}
            <path x-show="!isRegister"
                  d="M 6 0 C 0 250, 0 450, 4 600 C 7 750, 7 900, 2 1000"
                  stroke="url(#cyanDualGlow)" stroke-width="3" fill="none"
                  vector-effect="non-scaling-stroke"
                  style="filter: drop-shadow(0 0 8px rgba(6, 182, 212, 0.5));" />
            {{-- Stroke for Register (Banner on Left, Mirrored) --}}
            <path x-show="isRegister"
                  d="M 94 0 C 100 250, 100 450, 96 600 C 93 750, 93 900, 98 1000"
                  stroke="url(#cyanDualGlow)" stroke-width="3" fill="none"
                  vector-effect="non-scaling-stroke"
                  style="filter: drop-shadow(0 0 8px rgba(6, 182, 212, 0.5));" />
        </svg>

        {{-- Background Radial Glows --}}
        <div class="absolute -top-24 -right-24 w-[420px] h-[420px] rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(6,182,212,0.25), transparent 70%);"></div>
        <div class="absolute -bottom-24 -left-20 w-[420px] h-[420px] rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(14,116,144,0.25), transparent 70%);"></div>

        {{-- Centered Content Wrapper for the Banner --}}
        <div class="w-full max-w-lg mx-auto flex flex-col justify-center space-y-8 my-auto relative z-10">

            {{-- Top Brand Logo --}}
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl overflow-hidden bg-white border border-cyan-200/80 flex items-center justify-center shadow-lg p-1.5 shrink-0">
                    <img src="{{ asset('img/saksershop-logo.png') }}" alt="NitipDong Logo" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-3xl tracking-tight text-white whitespace-nowrap leading-none drop-shadow-sm">
                        Nitip<span class="text-cyan-400 font-black">Dong</span>
                    </span>
                    <span class="text-[9px] font-bold text-sky-200 uppercase tracking-widest mt-1">Official Marketplace</span>
                </div>
            </div>

            {{-- Dynamic Brand Copy Container with smooth cross-fade --}}
            <div>
                {{-- Copy 1: Shown when Overlay is on the RIGHT (Login Mode) --}}
                <div x-show="!isRegister" 
                     x-transition:enter="transition ease-out duration-400 delay-200"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    <p class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 text-cyan-300">
                        <span class="w-2 h-2 rounded-full inline-block bg-cyan-400 animate-pulse"></span>
                        Marketplace &amp; Jastip Terpercaya
                    </p>
                    <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight tracking-tight">
                        Pusat Belanja &amp; Toko Online<br><span class="text-cyan-300">Masa Depan</span>
                    </h2>
                    <p class="text-base text-sky-100/90 leading-relaxed font-normal max-w-md">
                        Akses jutaan produk pilihan, kelola pesanan real-time, atau mulai bisnis digital toko resmi Anda bersama ekosistem NitipDong.
                    </p>
                </div>

                {{-- Copy 2: Shown when Overlay is on the LEFT (Register Mode) --}}
                <div x-show="isRegister" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-400 delay-200"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    <p class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 text-cyan-300">
                        <span class="w-2 h-2 rounded-full inline-block bg-cyan-400 animate-pulse"></span>
                        Komunitas Shopper Indonesia
                    </p>
                    <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight tracking-tight">
                        Bergabung Bersama<br><span class="text-cyan-300">Jutaan Pengguna</span>
                    </h2>
                    <p class="text-base text-sky-100/90 leading-relaxed font-normal max-w-md">
                        Nikmati kemudahan titip beli barang impian, voucher potongan ongkir se-Indonesia, dan keamanan transaksi dengan rekening bersama escrow.
                    </p>
                </div>
            </div>

            {{-- High-Contrast Value Proposition Badges --}}
            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/15">
                <div class="flex items-center gap-3.5 bg-white/10 backdrop-blur-sm border border-white/10 text-white rounded-xl p-3.5 shadow-xs">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-cyan-400/20 border border-cyan-300/30 text-cyan-300">
                        <i class="fa-solid fa-shield-halved text-base"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white leading-none">Keamanan 100%</p>
                        <p class="text-[11px] text-sky-200 mt-1">Escrow Terverifikasi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3.5 bg-white/10 backdrop-blur-sm border border-white/10 text-white rounded-xl p-3.5 shadow-xs">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-cyan-400/20 border border-cyan-300/30 text-cyan-300">
                        <i class="fa-solid fa-ticket text-base"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white leading-none">Bebas Ongkir</p>
                        <p class="text-[11px] text-sky-200 mt-1">Voucher Ekstra Rp0</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Fixed Absolute Bottom Footer for Banner --}}
        <div class="absolute bottom-6 text-xs text-sky-200/80 text-center">
            &copy; {{ date('Y') }} NitipDong Platform. Platform Belanja &amp; Jastip Terpercaya.
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         MOBILE & TABLET VIEW (< lg)
    ══════════════════════════════════════════════════ --}}
    <div class="lg:hidden w-full flex flex-col min-h-screen bg-white">
        
        {{-- Mobile Dark Header --}}
        <div class="relative flex flex-col items-center px-6 pt-10 pb-16 auth-brand-bg">
            
            {{-- Mobile Close Button --}}
            <a href="/" class="absolute top-5 right-5 w-9 h-9 rounded-full flex items-center justify-center text-white/70 hover:text-white border border-white/20 bg-white/10 hover:bg-white/20 transition-all text-sm z-30" title="Tutup">
                ✕
            </a>

            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-10 h-10 rounded-xl overflow-hidden border border-cyan-200 bg-white flex items-center justify-center shadow-md p-1">
                    <img src="{{ asset('img/saksershop-logo.png') }}" alt="NitipDong Logo" class="w-full h-full object-cover">
                </div>
                <span class="text-2xl font-extrabold text-white tracking-tight">Nitip<span class="text-cyan-400 font-black">Dong</span></span>
            </div>

            {{-- Segmented Tab Switcher for Mobile --}}
            <div class="flex items-center bg-black/40 p-1 rounded-xl border border-white/15 w-full max-w-xs relative z-20 shadow-inner">
                <button type="button" 
                        @click="setMode('login')"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center cursor-pointer"
                        :class="!isRegister ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-300 hover:text-white'">
                    Masuk
                </button>
                <button type="button" 
                        @click="setMode('register')"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center cursor-pointer"
                        :class="isRegister ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-300 hover:text-white'">
                    Daftar
                </button>
            </div>

            {{-- Dome curve SVG --}}
            <svg style="position:absolute;bottom:-1px;left:0;width:100%;height:60px;display:block;"
                 viewBox="0 0 1000 80" preserveAspectRatio="none"
                 xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="mg-mob" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%"   stop-color="#0ea5e9"/>
                        <stop offset="50%"  stop-color="#2563eb"/>
                        <stop offset="100%" stop-color="#06b6d4"/>
                    </linearGradient>
                </defs>
                <path d="M 0,80 Q 500,5 1000,80 L 1000,82 L 0,82 Z" fill="#ffffff"/>
                <path d="M 0,80 Q 500,5 1000,80"
                      stroke="url(#mg-mob)" stroke-width="4.5"
                      stroke-linecap="round" fill="none"/>
            </svg>
        </div>

        {{-- Mobile Form Body with smooth tab fade --}}
        <div class="flex-1 px-6 pt-6 pb-10 max-w-sm mx-auto w-full">
            
            {{-- Mobile Login Form --}}
            <div x-show="!isRegister" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <div class="mb-5">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Masuk untuk melanjutkan transaksi belanja Anda</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-3.5" x-on:submit="$dispatch('auth-submitting')">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" placeholder="nama@email.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 block">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-cyan-700 font-semibold hover:underline">Lupa sandi?</a>
                            @endif
                        </div>
                        <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="mob_rem" class="w-4 h-4 rounded border-slate-300 text-cyan-600">
                        <label for="mob_rem" class="ml-2 text-xs text-slate-600">Ingat saya</label>
                    </div>
                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2.5 rounded-xl shadow-md hover:shadow-lg shadow-cyan-600/20 transition-all text-xs tracking-wider uppercase">
                        Masuk
                    </button>
                </form>
            </div>

            {{-- Mobile Register Form --}}
            <div x-show="isRegister" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Buat Akun Baru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar sekarang untuk berbelanja &amp; titip barang</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-3" x-on:submit="$dispatch('auth-submitting')">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" placeholder="Budi Santoso">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" placeholder="yourmail@mail.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">Kata Sandi</label>
                        <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" placeholder="Minimal 8 karakter">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl px-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" placeholder="Ulangi kata sandi">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500" />
                    </div>
                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2.5 rounded-xl shadow-md hover:shadow-lg shadow-cyan-600/20 transition-all text-xs tracking-wider uppercase mt-2">
                        Daftar Akun
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<x-toast-notifier />

{{-- Full-Screen Loading Overlay --}}
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
            <img src="{{ asset('img/saksershop-logo.png') }}" alt="Logo" class="w-full h-full object-cover">
        </div>
    </div>
    <h3 class="text-base font-extrabold text-white tracking-tight animate-pulse">Memproses Akun Anda</h3>
    <p class="text-xs text-slate-400 mt-1">Mohon tunggu sebentar...</p>
</div>

@stack('scripts')
</body>
</html>
