<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Seller Center - ' . config('app.name', 'NitipDong') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            letter-spacing: -0.011em;
            background-color: #F8FAFC;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            letter-spacing: -0.02em;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums lining-nums;
            font-feature-settings: "tnum" 1, "lnum" 1;
        }
        .slate-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .slate-scrollbar::-webkit-scrollbar-track {
            background: #0F172A;
        }
        .slate-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        .slate-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="h-full bg-[#F8FAFC] text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
    @php
        $sellerStore = auth()->user()->store;
        $pendingOrders = $sellerStore ? \App\Models\Order::where('store_id', $sellerStore->id)->where('status', 'pending')->count() : 0;
        $activeVouchers = $sellerStore ? \App\Models\Voucher::where('store_id', $sellerStore->id)->count() : 0;
    @endphp

    <div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-xs lg:hidden">
        </div>

        <!-- ENTERPRISE SLATE SIDEBAR (#0F172A) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] text-slate-300 flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800/90 shrink-0">
            
            <div class="flex flex-col h-full">
                
                <!-- HEADER / BRANDING -->
                <div class="h-16 px-4 border-b border-slate-800 flex items-center justify-between bg-[#0B1324]">
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-xs p-1">
                            <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-white text-sm tracking-tight leading-none">Nitip<span class="text-blue-400 font-bold">Dong</span></span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span class="text-[9px] font-semibold text-slate-400 font-mono-num tracking-wide">Merchant • Seller Center</span>
                            </div>
                        </div>
                    </a>

                    <!-- Mobile Dismiss -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- STORE BADGE CARD -->
                <div class="p-3 border-b border-slate-800/80 bg-[#0d172e]">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-lg object-cover shrink-0 border border-slate-700 shadow-2xs" alt="Store">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-200 truncate">{{ $sellerStore->name ?? 'Toko Saya' }}</p>
                            <span class="inline-flex items-center gap-1 text-[9px] text-emerald-400 font-mono-num font-medium">
                                <i class="fa-solid fa-circle-check text-[8px]"></i> Toko Resmi Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- NAVIGATION ITEMS -->
                <div class="flex-1 overflow-y-auto slate-scrollbar px-3 py-4 space-y-5">
                    
                    <!-- OPERASIONAL TOKO -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Operasional Toko
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('seller.dashboard') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.dashboard') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-chart-pie w-4 h-4 text-center text-xs {{ request()->routeIs('seller.dashboard') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Dashboard Toko</span>
                            </a>

                            <a href="{{ route('seller.orders.index') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.orders.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-receipt w-4 h-4 text-center text-xs {{ request()->routeIs('seller.orders.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                    <span>Pesanan Masuk</span>
                                </div>
                                @if($pendingOrders > 0)
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-500 text-slate-950 font-mono-num">{{ $pendingOrders }}</span>
                                @endif
                            </a>

                            <a href="{{ route('seller.products.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-boxes-stacked w-4 h-4 text-center text-xs {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Katalog Produk</span>
                            </a>

                            <a href="{{ route('seller.products.create') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.products.create') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-plus w-4 h-4 text-center text-xs {{ request()->routeIs('seller.products.create') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Tambah Produk Baru</span>
                            </a>
                        </nav>
                    </div>

                    <!-- KEUANGAN & PROMO -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Keuangan & Promo
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('seller.wallet.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.wallet.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-wallet w-4 h-4 text-center text-xs {{ request()->routeIs('seller.wallet.*') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400' }}"></i>
                                <span class="flex-1">Dompet & Saldo Toko</span>
                            </a>

                            <a href="{{ route('seller.vouchers.index') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.vouchers.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-ticket w-4 h-4 text-center text-xs {{ request()->routeIs('seller.vouchers.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                    <span>Voucher Promo</span>
                                </div>
                                @if($activeVouchers > 0)
                                    <span class="px-1.5 py-0.5 text-[9px] font-mono-num rounded bg-slate-800 text-slate-300 border border-slate-700">{{ $activeVouchers }}</span>
                                @endif
                            </a>

                            <a href="{{ route('seller.complaints.index') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.complaints.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-triangle-exclamation w-4 h-4 text-center text-xs {{ request()->routeIs('seller.complaints.*') ? 'text-rose-400' : 'text-slate-400 group-hover:text-rose-400' }}"></i>
                                    <span>Pusat Komplain</span>
                                </div>
                            </a>
                        </nav>
                    </div>

                    <!-- KOMUNIKASI & PENGATURAN -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Komunikasi & Pengaturan
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('seller.chat.cus') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.chat.cus*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-regular fa-comment-dots w-4 h-4 text-center text-xs {{ request()->routeIs('seller.chat.cus*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Chat Pembeli</span>
                            </a>

                            <a href="{{ route('seller.chat.admin') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.chat.admin*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-headset w-4 h-4 text-center text-xs {{ request()->routeIs('seller.chat.admin*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Chat Admin</span>
                            </a>

                            <a href="{{ route('seller.settings.edit') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.settings.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-shop w-4 h-4 text-center text-xs {{ request()->routeIs('seller.settings.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Pengaturan Toko</span>
                            </a>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('profile.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-user-gear w-4 h-4 text-center text-xs {{ request()->routeIs('profile.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Profil Akun</span>
                            </a>
                        </nav>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="p-3 border-t border-slate-800/90 bg-[#0B1324]">
                    <div class="p-2 rounded-lg bg-slate-900 border border-slate-800 flex items-center gap-2.5">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=2563eb&color=fff' }}" 
                             class="w-7 h-7 rounded-md object-cover ring-1 ring-slate-700 shrink-0" 
                             alt="{{ auth()->user()->name }}">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-200 truncate leading-tight">{{ auth()->user()->name }}</p>
                            <span class="text-[10px] text-slate-400 font-mono-num">Merchant Toko</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" 
                                    class="p-1.5 rounded-md text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors" 
                                    title="Keluar">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN LAYOUT WRAPPER -->
        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden">
            
            <!-- STICKY TOP APP HEADER -->
            <header class="h-14 shrink-0 bg-white border-b border-slate-200/90 px-4 sm:px-6 flex items-center justify-between z-30 shadow-xs">
                
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-bars-staggered text-sm"></i>
                    </button>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 font-medium hidden sm:inline">Seller Center</span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-semibold text-slate-800 tracking-tight flex items-center gap-1.5">
                            {{ $pageTitle ?? 'Operasional & Manajemen Toko' }}
                        </span>
                    </div>
                </div>

                <!-- Right: Live Clock & Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200 text-xs text-slate-600 font-mono-num">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="seller-clock" class="font-semibold text-slate-700">--:--:-- WIB</span>
                    </div>

                    <a href="/" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition-colors">
                        <i class="fa-solid fa-store text-blue-600 text-[11px]"></i>
                        <span>Lihat Marketplace</span>
                    </a>
                </div>
            </header>

            <!-- SCROLLABLE WORKSPACE -->
            <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 lg:p-7 space-y-6 pb-24 scroll-smooth">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg p-3 flex gap-2.5 shadow-xs text-xs font-semibold" role="alert">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-lg p-3 flex gap-2.5 shadow-xs text-xs font-semibold" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const clockEl = document.getElementById('seller-clock');
            if (!clockEl) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    @stack('scripts')
</body>
</html>
