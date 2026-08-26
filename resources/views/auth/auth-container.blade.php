<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $initialMode === 'register' ? 'NitipDong — Buat Akun Baru' : 'NitipDong — Masuk ke Akun' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0891b2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NitipDong">
    <link rel="apple-touch-icon" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">

    <style>
        /* Ambient luxury dark background */
        .auth-ambient-canvas {
            background: radial-gradient(circle at 10% 20%, rgba(6, 182, 212, 0.12) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(14, 116, 144, 0.15) 0%, transparent 45%),
                        #080f1a;
        }

        /* 3D Perspective Stage */
        .auth-3d-stage {
            perspective: 2000px;
            perspective-origin: center center;
        }

        /* 3D Flipper Card Container */
        .auth-3d-flipper {
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.85s cubic-bezier(0.34, 1.3, 0.64, 1);
            will-change: transform;
        }

        .auth-3d-flipper.flipped {
            transform: rotateY(180deg);
        }

        /* 3D Card Faces */
        .auth-3d-face {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform-style: preserve-3d;
        }

        .auth-3d-front {
            transform: rotateY(0deg);
            z-index: 2;
        }

        .auth-3d-back {
            transform: rotateY(180deg);
            z-index: 1;
        }

        /* Floating 3D Elements with Parallax Depth */
        .auth-depth-sm {
            transform: translateZ(25px);
        }
        .auth-depth-md {
            transform: translateZ(45px);
        }
        .auth-depth-lg {
            transform: translateZ(65px);
        }

        /* Specular Light Sheen sweep on 3D turn */
        .auth-glare-sweep {
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, transparent 20%, rgba(255, 255, 255, 0.15) 45%, rgba(6, 182, 212, 0.3) 50%, rgba(255, 255, 255, 0.15) 55%, transparent 80%);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease, transform 0.85s ease;
            transform: translateX(-100%) rotateY(0deg);
            z-index: 50;
            border-radius: 1.5rem;
        }

        .auth-3d-flipper.flipping .auth-glare-sweep {
            opacity: 1;
            transform: translateX(100%) rotateY(0deg);
        }

        /* Rotating 3D Badge Indicator */
        .badge-orbit-ring {
            box-shadow: 0 0 30px rgba(6, 182, 212, 0.4), inset 0 0 15px rgba(6, 182, 212, 0.3);
        }

        /* Custom Scrollbar */
        .auth-custom-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .auth-custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-900 overflow-x-hidden selection:bg-cyan-500 selection:text-white" 
      style="font-family: 'Plus Jakarta Sans', sans-serif;">

<div x-data="{
    isRegister: {{ ($initialMode === 'register' || $errors->has('name') || $errors->has('password_confirmation') || old('name')) ? 'true' : 'false' }},
    isFlipping: false,
    init() {
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.mode) {
                this.triggerFlip(e.state.mode === 'register', false);
            } else {
                this.triggerFlip(window.location.pathname.includes('register'), false);
            }
        });
    },
    setMode(mode) {
        const targetIsRegister = (mode === 'register');
        if (this.isRegister === targetIsRegister) return;
        this.triggerFlip(targetIsRegister, true);
    },
    triggerFlip(targetRegisterState, pushHistory = true) {
        this.isFlipping = true;
        this.isRegister = targetRegisterState;
        
        if (pushHistory) {
            const targetUrl = this.isRegister ? '{{ route('register') }}' : '{{ route('login') }}';
            window.history.pushState({ mode: this.isRegister ? 'register' : 'login' }, '', targetUrl);
        }
        
        this.updateTitle();
        
        setTimeout(() => {
            this.isFlipping = false;
        }, 850);
    },
    updateTitle() {
        document.title = this.isRegister 
            ? 'NitipDong — Buat Akun Baru' 
            : 'NitipDong — Masuk ke Akun';
    }
}" class="w-full min-h-screen relative overflow-hidden auth-ambient-canvas flex items-center justify-center p-3 sm:p-6 lg:p-10" id="auth-main-container">

    {{-- Ambient Lighting Spheres --}}
    <div class="fixed -top-40 -left-40 w-96 h-96 rounded-full bg-cyan-500/15 blur-[120px] pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 rounded-full bg-sky-600/15 blur-[120px] pointer-events-none"></div>
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-cyan-600/5 blur-[150px] pointer-events-none"></div>

    {{-- Close / Back to Home Floating Pill --}}
    <a href="/" 
       class="fixed top-4 left-4 sm:top-6 sm:left-6 z-50 flex items-center gap-2 px-3.5 py-2 rounded-full bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 shadow-lg backdrop-blur-md transition-all group text-xs font-semibold"
       title="Kembali ke Beranda">
        <i class="fa-solid fa-arrow-left text-xs text-cyan-400 group-hover:-translate-x-1 transition-transform"></i>
        <span>Beranda</span>
    </a>

    {{-- 3D Rotator Stage --}}
    <div class="w-full max-w-sm sm:max-w-md lg:max-w-5xl auth-3d-stage my-auto py-4">
        
        {{-- 3D Card Flipper Container --}}
        <div class="auth-3d-flipper w-full min-h-[580px] sm:min-h-[620px] lg:min-h-[660px] rounded-3xl shadow-2xl border border-white/10"
             :class="{ 'flipped': isRegister, 'flipping': isFlipping }">

            {{-- Dynamic Glare Overlay --}}
            <div class="auth-glare-sweep"></div>

            {{-- ======================================================== --}}
            {{-- FRONT FACE: LOGIN SCREEN --}}
            {{-- ======================================================== --}}
            <div class="auth-3d-face auth-3d-front rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl flex flex-col lg:flex-row backdrop-blur-xl">
                
                {{-- Left: Login Form (Light Interior) --}}
                <div class="w-full lg:w-1/2 bg-white flex flex-col justify-center px-5 sm:px-8 lg:px-12 py-6 sm:py-8 lg:py-10 relative overflow-y-auto auth-custom-scroll">
                    
                    <div class="w-full max-w-md mx-auto auth-depth-sm">
                        
                        {{-- Mobile Top Header & Tabs --}}
                        <div class="flex lg:hidden items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-cyan-50 border border-cyan-200 p-1 flex items-center justify-center shadow-xs">
                                    <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
                                </div>
                                <span class="text-base font-extrabold text-slate-900 tracking-tight">Nitip<span class="text-cyan-600">Dong</span></span>
                            </div>
                            <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200/60">
                                <button type="button" @click="setMode('login')" class="px-3 py-1 text-xs font-bold rounded-lg transition-all" :class="!isRegister ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">Masuk</button>
                                <button type="button" @click="setMode('register')" class="px-3 py-1 text-xs font-bold rounded-lg transition-all" :class="isRegister ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">Daftar</button>
                            </div>
                        </div>

                        {{-- Alerts / Status --}}
                        <x-auth-session-status class="mb-3" :status="session('status')" />

                        @if(session('error'))
                            <div class="mb-3.5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="mb-3.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        {{-- Form Header --}}
                        <div class="mb-5">
                            <div class="hidden lg:flex items-center gap-2 mb-1.5">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md bg-cyan-50 text-cyan-700 border border-cyan-200">
                                    Autentikasi Akun
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-lock text-[10px] text-cyan-600"></i> SSL 256-bit
                                </span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                Selamat Datang Kembali
                            </h1>
                            <p class="text-xs text-slate-500 mt-1">Masuk ke akun NitipDong Anda untuk melanjutkan transaksi belanja</p>
                        </div>

                        {{-- Login Form --}}
                        <form method="POST" action="{{ route('login') }}" class="space-y-3.5" x-on:submit="$dispatch('auth-submitting')">
                            @csrf

                            <div>
                                <label for="login_email" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">
                                    Alamat Email
                                </label>
                                <div class="relative">
                                    <input id="login_email" 
                                           type="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="username" 
                                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" 
                                           placeholder="nama@email.com">
                                    <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
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
                                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl pl-10 pr-12 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none" 
                                           placeholder="••••••••">
                                    <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                                    <button type="button"
                                            onclick="const i=document.getElementById('login_password'); i.type=i.type==='password'?'text':'password';"
                                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer p-1">
                                        <i class="fa-regular fa-eye-slash text-sm"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div class="flex items-center">
                                <input id="login_remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer">
                                <label for="login_remember_me" class="ml-2 text-xs text-slate-600 cursor-pointer select-none">Ingat sesi login saya</label>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-cyan-500 hover:from-cyan-500 hover:to-cyan-400 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-cyan-600/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer text-xs tracking-wider uppercase">
                                <span>Masuk ke Akun</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="relative my-3.5">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-3 text-slate-400 font-bold text-[10px] tracking-wider">atau masuk dengan</span></div>
                        </div>

                        {{-- Google Sign In Button --}}
                        <a href="{{ route('auth.google') }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-bold py-2.5 px-4 rounded-xl border border-slate-200 hover:border-slate-300 shadow-xs hover:shadow transition-all flex items-center justify-center gap-3 text-xs group">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                            </svg>
                            <span>Masuk dengan Google</span>
                        </a>

                        {{-- Switch Mode Trigger (3D Rotate) --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                            <p class="text-xs text-slate-500">
                                Belum memiliki akun NitipDong? 
                                <button type="button" 
                                        @click="setMode('register')" 
                                        class="text-cyan-700 font-bold hover:text-cyan-600 hover:underline cursor-pointer inline-flex items-center gap-1.5 ml-1">
                                    <span>Daftar sekarang</span>
                                    <i class="fa-solid fa-rotate text-xs text-cyan-600"></i>
                                </button>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right: Brand Showcase (Dark Gradient with 3D Depth on Desktop) --}}
                <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#09182b] via-[#0f2744] to-[#083344] p-8 lg:p-12 text-white flex-col justify-between relative overflow-hidden">
                    
                    {{-- Ambient Background Glows --}}
                    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-cyan-500/20 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-sky-600/20 blur-3xl pointer-events-none"></div>

                    {{-- Top Brand Info --}}
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-3 auth-depth-md">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-2 flex items-center justify-center shadow-lg badge-orbit-ring">
                                <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Logo" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl font-extrabold tracking-tight text-white leading-none">
                                    Nitip<span class="text-cyan-400 font-black">Dong</span>
                                </h3>
                                <span class="text-[9px] font-bold text-sky-200 uppercase tracking-widest mt-0.5 block">Official Marketplace</span>
                            </div>
                        </div>

                        {{-- 3D Mode Switch Button Pill --}}
                        <button type="button"
                                @click="setMode('register')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-sky-200 hover:text-white text-xs font-semibold backdrop-blur-sm transition-all cursor-pointer shadow-xs">
                            <span>Buat Akun</span>
                            <i class="fa-solid fa-rotate-right text-cyan-400"></i>
                        </button>
                    </div>

                    {{-- Center Main Copy --}}
                    <div class="my-auto py-8 relative z-10 auth-depth-lg space-y-4 max-w-md">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-400/10 border border-cyan-300/30 text-cyan-300 text-xs font-bold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            Marketplace &amp; Jastip Terpercaya
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                            Pusat Belanja &amp; Toko Online <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-sky-200">Masa Depan</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-sky-100/80 leading-relaxed font-normal">
                            Akses ribuan produk pilihan, jastip instan se-Indonesia, garansi rekening bersama escrow terpercaya, dan bebas ongkir setiap hari.
                        </p>
                    </div>

                    {{-- Bottom Feature Pills --}}
                    <div class="relative z-10 grid grid-cols-2 gap-3 pt-6 border-t border-white/10 auth-depth-sm">
                        <div class="flex items-center gap-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-cyan-400/20 border border-cyan-300/30 text-cyan-300 text-sm">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white leading-none">Keamanan 100%</p>
                                <p class="text-[10px] text-sky-200 mt-1">Escrow Terverifikasi</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-cyan-400/20 border border-cyan-300/30 text-cyan-300 text-sm">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white leading-none">Bebas Ongkir</p>
                                <p class="text-[10px] text-sky-200 mt-1">Voucher Ekstra Rp0</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ======================================================== --}}
            {{-- BACK FACE: REGISTER SCREEN (Flipped 180deg in 3D Space) --}}
            {{-- ======================================================== --}}
            <div class="auth-3d-face auth-3d-back rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl flex flex-col lg:flex-row backdrop-blur-xl">
                
                {{-- Left: Brand Showcase for Register (Desktop) --}}
                <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#083344] via-[#0f2744] to-[#09182b] p-8 lg:p-12 text-white flex-col justify-between relative overflow-hidden">
                    
                    {{-- Ambient Background Glows --}}
                    <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-cyan-400/20 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

                    {{-- Top Brand Info --}}
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-3 auth-depth-md">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-2 flex items-center justify-center shadow-lg badge-orbit-ring">
                                <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Logo" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl font-extrabold tracking-tight text-white leading-none">
                                    Nitip<span class="text-cyan-400 font-black">Dong</span>
                                </h3>
                                <span class="text-[9px] font-bold text-emerald-200 uppercase tracking-widest mt-0.5 block">Komunitas Shopper</span>
                            </div>
                        </div>

                        {{-- 3D Mode Switch Button Pill --}}
                        <button type="button"
                                @click="setMode('login')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-sky-200 hover:text-white text-xs font-semibold backdrop-blur-sm transition-all cursor-pointer shadow-xs">
                            <i class="fa-solid fa-rotate-left text-cyan-400"></i>
                            <span>Sudah Ada Akun</span>
                        </button>
                    </div>

                    {{-- Center Main Copy --}}
                    <div class="my-auto py-8 relative z-10 auth-depth-lg space-y-4 max-w-md">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-400/10 border border-cyan-300/30 text-cyan-300 text-xs font-bold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Gabung Ekosistem Digital
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                            Bergabung Bersama <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-emerald-200">Jutaan Pengguna</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-sky-100/80 leading-relaxed font-normal">
                            Nikmati kemudahan titip beli barang impian, voucher cashback diskon member baru, serta kesempatan membuka toko resmi gratis.
                        </p>
                    </div>

                    {{-- Bottom Feature Pills --}}
                    <div class="relative z-10 grid grid-cols-2 gap-3 pt-6 border-t border-white/10 auth-depth-sm">
                        <div class="flex items-center gap-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-emerald-400/20 border border-emerald-300/30 text-emerald-300 text-sm">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white leading-none">Bonus Member</p>
                                <p class="text-[10px] text-emerald-200 mt-1">Voucher Pengguna Baru</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-cyan-400/20 border border-cyan-300/30 text-cyan-300 text-sm">
                                <i class="fa-solid fa-store"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white leading-none">Buka Toko</p>
                                <p class="text-[10px] text-sky-200 mt-1">Gratis Tanpa Biaya</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right: Register Form (Light Interior) --}}
                <div class="w-full lg:w-1/2 bg-white flex flex-col justify-center px-5 sm:px-8 lg:px-12 py-6 sm:py-8 lg:py-10 relative overflow-y-auto auth-custom-scroll">
                    
                    <div class="w-full max-w-md mx-auto auth-depth-sm">

                        {{-- Mobile Top Header & Tabs --}}
                        <div class="flex lg:hidden items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-cyan-50 border border-cyan-200 p-1 flex items-center justify-center shadow-xs">
                                    <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
                                </div>
                                <span class="text-base font-extrabold text-slate-900 tracking-tight">Nitip<span class="text-cyan-600">Dong</span></span>
                            </div>
                            <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200/60">
                                <button type="button" @click="setMode('login')" class="px-3 py-1 text-xs font-bold rounded-lg transition-all" :class="!isRegister ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">Masuk</button>
                                <button type="button" @click="setMode('register')" class="px-3 py-1 text-xs font-bold rounded-lg transition-all" :class="isRegister ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">Daftar</button>
                            </div>
                        </div>

                        @if(session('error'))
                            <div class="mb-3.5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        {{-- Form Header --}}
                        <div class="mb-4">
                            <div class="hidden lg:flex items-center gap-2 mb-1.5">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Pendaftaran Baru
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-bolt text-[10px] text-amber-500"></i> Proses 1 Menit
                                </span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                Buat Akun NitipDong
                            </h1>
                            <p class="text-xs text-slate-500 mt-1">Daftar sekarang untuk mulai berbelanja dan menikmati voucher eksklusif</p>
                        </div>

                        {{-- Register Form --}}
                        <form method="POST" action="{{ route('register') }}" class="space-y-2.5 sm:space-y-3" x-on:submit="$dispatch('auth-submitting')">
                            @csrf

                            <div>
                                <label for="reg_name" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">
                                    Nama Lengkap
                                </label>
                                <div class="relative">
                                    <input id="reg_name"
                                           type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           autocomplete="name"
                                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none"
                                           placeholder="Budi Santoso">
                                    <i class="fa-regular fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500"/>
                            </div>

                            <div>
                                <label for="reg_email" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">
                                    Alamat Email
                                </label>
                                <div class="relative">
                                    <input id="reg_email"
                                           type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autocomplete="username"
                                           class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none"
                                           placeholder="nama@email.com">
                                    <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500"/>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div>
                                    <label for="reg_password" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">
                                        Kata Sandi
                                    </label>
                                    <div class="relative">
                                        <input id="reg_password"
                                               type="password"
                                               name="password"
                                               required
                                               autocomplete="new-password"
                                               class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl pl-9 pr-9 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none"
                                               placeholder="Min. 8 char">
                                        <i class="fa-solid fa-key absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                        <button type="button"
                                                onclick="const i=document.getElementById('reg_password'); i.type=i.type==='password'?'text':'password';"
                                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer p-1">
                                            <i class="fa-regular fa-eye-slash text-xs"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500"/>
                                </div>

                                <div>
                                    <label for="reg_password_confirmation" class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1 block">
                                        Konfirmasi Sandi
                                    </label>
                                    <div class="relative">
                                        <input id="reg_password_confirmation"
                                               type="password"
                                               name="password_confirmation"
                                               required
                                               autocomplete="new-password"
                                               class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 rounded-xl pl-9 pr-9 py-2.5 text-slate-800 text-sm placeholder-slate-400 transition-all outline-none"
                                               placeholder="Ulangi sandi">
                                        <i class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                        <button type="button"
                                                onclick="const i=document.getElementById('reg_password_confirmation'); i.type=i.type==='password'?'text':'password';"
                                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer p-1">
                                            <i class="fa-regular fa-eye-slash text-xs"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500"/>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-cyan-600 to-cyan-500 hover:from-cyan-500 hover:to-cyan-400 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-cyan-600/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer text-xs tracking-wider uppercase mt-1">
                                <span>Daftar Akun Sekarang</span>
                                <i class="fa-solid fa-sparkles text-xs"></i>
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="relative my-3">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-3 text-slate-400 font-bold text-[10px] tracking-wider">atau daftar dengan</span></div>
                        </div>

                        {{-- Google Sign In Button --}}
                        <a href="{{ route('auth.google') }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-bold py-2 px-4 rounded-xl border border-slate-200 hover:border-slate-300 shadow-xs hover:shadow transition-all flex items-center justify-center gap-3 text-xs group">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                            </svg>
                            <span>Daftar dengan Google</span>
                        </a>

                        {{-- Switch Mode Trigger (3D Rotate Back to Login) --}}
                        <div class="mt-3.5 pt-2.5 border-t border-slate-100 text-center">
                            <p class="text-xs text-slate-500">
                                Sudah memiliki akun NitipDong? 
                                <button type="button" 
                                        @click="setMode('login')" 
                                        class="text-cyan-700 font-bold hover:text-cyan-600 hover:underline cursor-pointer inline-flex items-center gap-1.5 ml-1">
                                    <i class="fa-solid fa-rotate-left text-xs text-cyan-600"></i>
                                    <span>Masuk sekarang</span>
                                </button>
                            </p>
                        </div>

                    </div>

                </div>

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
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-950/85 backdrop-blur-md text-white select-none">
    <div class="relative flex items-center justify-center mb-6">
        <div class="w-16 h-16 rounded-full border-4 border-cyan-500/20 border-t-cyan-400 animate-spin shadow-[0_0_20px_rgba(6,182,212,0.35)]"></div>
        <div class="w-8 h-8 rounded-lg overflow-hidden absolute animate-pulse">
            <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
        </div>
    </div>
    <h3 class="text-base font-extrabold text-white tracking-tight animate-pulse">Memproses Akun Anda</h3>
    <p class="text-xs text-slate-400 mt-1">Mohon tunggu sebentar...</p>
</div>

@stack('scripts')
</body>
</html>
