<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BelanjaIn - Situs Jual Beli Online Terlengkap, Mudah & Aman</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('img/icon.jpg') }}">
</head>
<body class="bg-gradient-to-b from-slate-50 via-cyan-50/30 to-white text-slate-800 antialiased">

    <!-- Header dengan Glass Effect -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/50 shadow-sm">
        <!-- Top Navigation Bar -->
        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white">
            <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-xs py-2">
                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-cyan-100 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Seller Centre
                    </a>
                    <span class="w-px h-3 bg-white/30"></span>
                    <a href="#" class="hover:text-cyan-100 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download App
                    </a>
                </div>

                <!-- Nav Actions -->
                <div class="ml-auto flex items-center gap-3">
                    @auth
                        @php $cartCount = auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0; @endphp
                        <a href="{{ route('customer.cart.index') }}" class="relative flex items-center justify-center w-9 h-9 rounded-lg hover:bg-white/10 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center shadow-lg">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-colors">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0ea5e9&color=fff&size=80&bold=true" class="w-7 h-7 rounded-full ring-2 ring-white/30" alt="Avatar">
                                <span class="hidden md:block text-sm font-semibold">{{ Str::words(Auth::user()->name, 1, '') }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if(Auth::user()->role === 'super_admin')
                                    <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Super Admin Panel
                                    </a>
                                @elseif(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Admin Panel
                                    </a>
                                @elseif(Auth::user()->role === 'seller')
                                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        Toko Saya
                                    </a>
                                @else
                                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        Pesanan Saya
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Edit Profil
                                </a>
                                <div class="border-t border-slate-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">@csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-1.5 text-sm font-semibold hover:text-cyan-100 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="px-5 py-1.5 bg-white text-cyan-600 text-sm font-bold rounded-lg hover:bg-cyan-50 transition-all shadow-md hover:shadow-lg">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Main Search & Logo Bar -->
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center gap-6">
            <a href="/" class="flex items-center gap-3 shrink-0 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl blur-sm opacity-75 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative bg-gradient-to-br from-cyan-500 to-blue-600 p-2.5 rounded-2xl shadow-lg">
                        <img src="{{ asset('img/icon.jpg') }}" alt="Logo" class="w-8 h-8 object-cover rounded-lg">
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-bold tracking-tight bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">BelanjaIn</span>
                    <span class="text-[10px] text-slate-500 -mt-1">Belanja Praktis, Hemat Maksimal</span>
                </div>
            </a>

            <div class="flex-1 max-w-3xl">
                <form action="#" method="GET" class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity"></div>
                    <div class="relative flex items-center bg-slate-100 rounded-2xl border-2 border-transparent hover:border-cyan-500/30 focus-within:border-cyan-500 transition-all shadow-sm">
                        <input type="text" name="q" class="flex-1 bg-transparent border-none focus:ring-0 text-sm px-5 py-3.5 placeholder-slate-400" placeholder="Cari produk impianmu...">
                        <button type="submit" class="m-1.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-8 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4">
                <div class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 text-sm shadow-md">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Hero Banner Section with Modern Design -->
        <section class="max-w-7xl mx-auto px-4 pt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Main Banner -->
                <div class="lg:col-span-2 relative rounded-3xl overflow-hidden shadow-xl group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-700/85 via-blue-700/70 to-transparent z-10"></div>
                    <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop" class="w-full h-[320px] object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Mega Sale Banner">
                    <div class="absolute inset-0 z-20 flex flex-col justify-center px-12">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-full mb-4 w-fit border border-white/30">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                            MEGA SALE 2026
                        </span>
                        <h2 class="text-white text-4xl font-black mb-3 drop-shadow-lg leading-tight">Diskon Besar<br/>Hingga 80%</h2>
                        <p class="text-white/85 text-base mb-6 drop-shadow">Ribuan produk pilihan dengan harga terbaik</p>
                        <a href="#" class="inline-flex items-center gap-2 bg-white text-cyan-600 px-8 py-3 rounded-xl font-bold hover:shadow-xl transition-all w-fit hover:bg-cyan-50">
                            Belanja Sekarang
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Side Banners -->
                <div class="hidden lg:grid grid-rows-2 gap-4">
                    <div class="relative rounded-2xl overflow-hidden shadow-lg group cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-br from-violet-700/80 to-purple-900/80 z-10"></div>
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" alt="Sneakers Promo">
                        <div class="absolute inset-0 z-20 flex flex-col justify-center px-6">
                            <p class="text-white text-xl font-black drop-shadow">Sneakers</p>
                            <p class="text-white/80 text-sm drop-shadow">Up to 50% OFF</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-white/90 text-xs font-semibold">
                                Lihat Koleksi
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </span>
                        </div>
                    </div>
                    <div class="relative rounded-2xl overflow-hidden shadow-lg group cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-600/80 to-red-700/80 z-10"></div>
                        <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" alt="Gadgets Promo">
                        <div class="absolute inset-0 z-20 flex flex-col justify-center px-6">
                            <p class="text-white text-xl font-black drop-shadow">Gadgets</p>
                            <p class="text-white/80 text-sm drop-shadow">New Arrival 2026</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-white/90 text-xs font-semibold">
                                Cek Sekarang
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Menu Icons dengan Horizontal Scroll -->
            <div class="bg-white p-5 mt-6 shadow-lg rounded-2xl" data-carousel>
                <button data-carousel-prev class="scroll-arrow scroll-arrow-left">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div data-carousel-container class="scroll-container" style="cursor: grab;">
                    <!-- Gratis Ongkir -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Gratis Ongkir</span>
                    </a>
                    <!-- Flash Sale -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Flash Sale</span>
                    </a>
                    <!-- Supermarket -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Supermarket</span>
                    </a>
                    <!-- 100% Ori -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">100% Ori</span>
                    </a>
                    <!-- Voucher -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Voucher</span>
                    </a>
                    <!-- Elektronik -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Elektronik</span>
                    </a>
                    <!-- Fashion -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-pink-400 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7l-5 5 5 5M17 7l5 5-5 5M14 4l-4 16"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Fashion</span>
                    </a>
                    <!-- Home & Living -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Home</span>
                    </a>
                    <!-- Olahraga -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" fill="none"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Olahraga</span>
                    </a>
                    <!-- Lainnya -->
                    <a href="#" class="category-item product-card" style="min-width: 100px;">
                        <div class="w-14 h-14 bg-gradient-to-br from-violet-400 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform hover:scale-110">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        </div>
                        <span class="text-[11px] text-center font-semibold text-slate-700 leading-tight mt-1">Lainnya</span>
                    </a>
                </div>

                <button data-carousel-next class="scroll-arrow scroll-arrow-right">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>

        <!-- Categories Section -->
        @if(isset($categories) && count($categories) > 0)
        <section class="max-w-7xl mx-auto px-4 mt-8">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-cyan-500 to-blue-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-slate-800">Kategori Pilihan</h2>
                </div>
                <a href="#" class="text-cyan-600 text-sm font-semibold flex items-center gap-1 hover:gap-2 transition-all">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden p-6">
                <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                    @php
                    $categoryIcons = [
                        'Elektronik' => '<svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                        'Pakaian'    => '<svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7l-5 5 5 5M17 7l5 5-5 5M14 4l-4 16"/></svg>',
                        'Makanan'    => '<svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                        'Otomotif'   => '<svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17h8M3 17l1-4 2-4h12l2 4 1 4M5 17v2a1 1 0 001 1h1a1 1 0 001-1v-2M15 17v2a1 1 0 001 1h1a1 1 0 001-1v-2"/></svg>',
                    ];
                    $categoryBg = [
                        'Elektronik' => 'from-indigo-50 to-blue-50',
                        'Pakaian'    => 'from-pink-50 to-rose-50',
                        'Makanan'    => 'from-orange-50 to-amber-50',
                        'Otomotif'   => 'from-slate-50 to-gray-100',
                    ];
                    $defaultIcon = '<svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
                    @endphp
                    @foreach ($categories as $category)
                        <a href="#" class="flex flex-col items-center gap-3 p-4 rounded-xl hover:bg-gradient-to-br hover:from-cyan-50 hover:to-blue-50 transition-all group">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $categoryBg[$category->name] ?? 'from-cyan-50 to-blue-50' }} flex items-center justify-center group-hover:scale-110 transition-transform shadow-md">
                                {!! $categoryIcons[$category->name] ?? $defaultIcon !!}
                            </div>
                            <span class="text-xs text-center font-semibold text-slate-700 leading-tight">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Flash Sale dengan Horizontal Scroll -->
        <section class="max-w-7xl mx-auto px-4 mt-8">
            <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <h3 class="font-black text-white text-2xl flex items-center gap-2">
                            <svg class="w-8 h-8 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                            Flash Sale
                        </h3>
                        <div class="flex items-center gap-2" x-data="timer()" x-init="countdown()">
                            <span class="bg-white text-orange-600 text-sm font-black px-3 py-2 rounded-lg shadow-lg" x-text="hours.toString().padStart(2, '0')">04</span>
                            <span class="font-black text-white text-xl">:</span>
                            <span class="bg-white text-orange-600 text-sm font-black px-3 py-2 rounded-lg shadow-lg" x-text="minutes.toString().padStart(2, '0')">48</span>
                            <span class="font-black text-white text-xl">:</span>
                            <span class="bg-white text-orange-600 text-sm font-black px-3 py-2 rounded-lg shadow-lg" x-text="seconds.toString().padStart(2, '0')">39</span>
                        </div>
                    </div>
                    <a href="#" class="bg-white text-orange-600 px-6 py-2.5 rounded-xl font-bold hover:shadow-xl transition-all flex items-center gap-2">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>

                <!-- Flash Sale Products Carousel -->
                <div data-carousel class="relative">
                    <button data-carousel-prev class="scroll-arrow scroll-arrow-left" style="background: rgba(255, 255, 255, 0.95);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div data-carousel-container class="scroll-container" style="cursor: grab;">
                        @for($i = 0; $i < 12; $i++)
                        <div class="bg-white rounded-xl p-3 hover:shadow-2xl transition-all product-card" style="min-width: 180px;">
                            <div class="aspect-square bg-slate-100 rounded-lg mb-2 shimmer"></div>
                            <div class="h-4 bg-slate-100 rounded mb-2 shimmer"></div>
                            <div class="h-6 bg-gradient-to-r from-orange-100 to-red-100 rounded shimmer"></div>
                        </div>
                        @endfor
                    </div>

                    <button data-carousel-next class="scroll-arrow scroll-arrow-right" style="background: rgba(255, 255, 255, 0.95);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Promo Banner Strip -->
        <section class="max-w-7xl mx-auto px-4 mt-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Promo 1: Fashion -->
                <div class="relative rounded-2xl overflow-hidden shadow-lg group cursor-pointer h-40">
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-600/80 to-rose-700/70 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1470&auto=format&fit=crop" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" alt="Fashion Sale">
                    <div class="absolute inset-0 z-20 flex items-center px-7 gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7l-5 5 5 5M17 7l5 5-5 5M14 4l-4 16"/></svg>
                        </div>
                        <div>
                            <p class="text-white font-black text-lg leading-tight">Fashion Week</p>
                            <p class="text-white/80 text-sm">Koleksi terbaru 2026</p>
                        </div>
                    </div>
                </div>
                <!-- Promo 2: Electronics -->
                <div class="relative rounded-2xl overflow-hidden shadow-lg group cursor-pointer h-40">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700/80 to-indigo-800/70 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1550009158-9ebf69173e03?q=80&w=1401&auto=format&fit=crop" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" alt="Electronics Sale">
                    <div class="absolute inset-0 z-20 flex items-center px-7 gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-white font-black text-lg leading-tight">Elektronik</p>
                            <p class="text-white/80 text-sm">Gadget terkini, harga bersaing</p>
                        </div>
                    </div>
                </div>
                <!-- Promo 3: Food & Fresh -->
                <div class="relative rounded-2xl overflow-hidden shadow-lg group cursor-pointer h-40">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-teal-700/70 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1374&auto=format&fit=crop" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" alt="Fresh Food">
                    <div class="absolute inset-0 z-20 flex items-center px-7 gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3l14 9-14 9V3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-2.21 0-4 1.343-4 3s1.79 3 4 3 4-1.343 4-3-1.79-3-4-3z"/></svg>
                        </div>
                        <div>
                            <p class="text-white font-black text-lg leading-tight">Fresh & Food</p>
                            <p class="text-white/80 text-sm">Gratis ongkir setiap hari</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rekomendasi / Product Catalog -->
        <section class="max-w-7xl mx-auto px-4 mt-8 mb-16">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-cyan-500 to-blue-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-slate-800">Rekomendasi Untukmu</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @forelse ($products as $product)
                    <a href="{{ route('product.show', $product) }}" class="product-card group">
                        <div class="relative aspect-square bg-slate-100 overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                    <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            <!-- Badge -->
                            @if($product->badge)
                                <span class="store-badge">{{ strtoupper($product->badge) }}</span>
                            @elseif($product->discount_percentage > 0)
                                <span class="store-badge">{{ $product->discount_percentage }}% OFF</span>
                            @endif

                            <!-- Favorite Button -->
                            <button class="absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:scale-110 shadow-lg">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                        </div>
                        <div class="p-3">
                            <h3 class="font-normal text-slate-700 text-sm line-clamp-2 mb-2 leading-snug group-hover:text-cyan-600 transition-colors">{{ $product->name }}</h3>

                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-3 h-3 {{ $i < floor($product->rating) ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-xs text-slate-500 ml-1">({{ $product->rating }})</span>
                            </div>

                            <div class="flex flex-col gap-1">
                                @if($product->discount_percentage > 0)
                                    <p class="price-tag">Rp{{ number_format($product->getDiscountedPrice(), 0, ',', '.') }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-400 line-through">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                        <span class="text-xs font-bold text-red-500">-{{ $product->discount_percentage }}%</span>
                                    </div>
                                @else
                                    <p class="price-tag">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                @endif
                            </div>

                            @if($product->sold_count > 0)
                                <p class="text-xs text-slate-500 mt-2">{{ number_format($product->sold_count) }} Terjual</p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <p class="text-slate-400 text-lg font-medium">Belum ada produk tersedia</p>
                    </div>
                @endforelse
            </div>

            @if(count($products) > 0)
            <div class="mt-8 text-center">
                <button class="bg-white border-2 border-cyan-500 hover:bg-cyan-50 text-cyan-600 px-16 py-4 rounded-2xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center gap-2 mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Muat Lebih Banyak
                </button>
            </div>
            @endif
        </section>
    </main>

    <!-- Footer dengan Design Modern -->
    <footer class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-slate-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-12 h-12 rounded-2xl overflow-hidden shadow-xl">
                            <img src="{{ asset('img/icon.jpg') }}" class="w-full h-full object-cover">
                        </div>
                        <span class="font-black text-2xl text-white">BelanjaIn</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-400 mb-5">Platform e-commerce terpercaya di Indonesia. Belanja mudah, aman, dan terpercaya dengan ribuan produk pilihan.</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-all hover:scale-110 shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white flex items-center justify-center transition-all hover:scale-110 shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white flex items-center justify-center transition-all hover:scale-110 shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-white mb-5 text-sm uppercase tracking-wider">Tentang Kami</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Tentang BelanjaIn</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Karir</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Blog</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-white mb-5 text-sm uppercase tracking-wider">Layanan Pelanggan</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Cara Pembelian</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Lacak Pesanan</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Pengembalian Barang</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-white mb-5 text-sm uppercase tracking-wider">Hubungi Kami</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-cyan-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>support@belanjain.com</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-cyan-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>0800-1234-5678<br/>(24/7 Customer Service)</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-700 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
                <p class="text-slate-400">&copy; 2026 BelanjaIn. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="text-slate-400">Metode Pembayaran:</span>
                    <div class="flex gap-2">
                        <div class="w-12 h-8 bg-white rounded flex items-center justify-center text-xs font-bold text-slate-700">VISA</div>
                        <div class="w-12 h-8 bg-white rounded flex items-center justify-center text-xs font-bold text-blue-600">OVO</div>
                        <div class="w-12 h-8 bg-white rounded flex items-center justify-center text-xs font-bold text-emerald-600">BCA</div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <x-chat-widget />

    <script>
        function timer() {
            return {
                hours: 4,
                minutes: 48,
                seconds: 39,
                countdown() {
                    setInterval(() => {
                        if (this.seconds > 0) {
                            this.seconds--;
                        } else if (this.minutes > 0) {
                            this.minutes--;
                            this.seconds = 59;
                        } else if (this.hours > 0) {
                            this.hours--;
                            this.minutes = 59;
                            this.seconds = 59;
                        }
                    }, 1000);
                }
            }
        }
    </script>
</body>
</html>
