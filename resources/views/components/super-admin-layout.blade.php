<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Platform - ' . config('app.name', 'NitipDong') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">

    <!-- Chart.js CDN for Analytical Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

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
                    <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-xs">
                            <img src="{{ asset('img/saksershop-logo.png') }}" alt="Logo" class="w-5 h-5 object-contain">
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-white text-sm tracking-tight leading-none">Nitip<span class="text-blue-400 font-bold">Dong</span></span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-[9px] font-semibold text-slate-400 font-mono-num tracking-wide">LIVE PROD • Engine v2.4</span>
                            </div>
                        </div>
                    </a>

                    <!-- Mobile Dismiss -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- NAVIGATION ITEMS -->
                <div class="flex-1 overflow-y-auto slate-scrollbar px-3 py-4 space-y-5">
                    
                    <!-- CATEGORY 1: ANALITIK & UTAMA -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Analitik & Utama
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('super_admin.dashboard') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.dashboard') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-chart-pie w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.dashboard') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Executive Dashboard</span>
                            </a>
                        </nav>
                    </div>

                    <!-- CATEGORY 2: OPERASIONAL PLATFORM -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Operasional Platform
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('super_admin.stores.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.stores.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-store w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.stores.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Manajemen Toko</span>
                            </a>

                            <a href="{{ route('super_admin.approvals.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.approvals.*') || request()->routeIs('admin.dashboard') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-clipboard-check w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.approvals.*') || request()->routeIs('admin.dashboard') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Persetujuan Toko</span>
                            </a>

                            <a href="{{ route('super_admin.products.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.products.*') || request()->routeIs('admin.products.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-boxes-stacked w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.products.*') || request()->routeIs('admin.products.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Moderasi Produk</span>
                            </a>

                            <a href="{{ route('super_admin.categories.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.categories.*') || request()->routeIs('admin.categories.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-tags w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.categories.*') || request()->routeIs('admin.categories.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Kategori Produk</span>
                            </a>

                            <a href="{{ route('super_admin.flash_sales.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.flash_sales.*') || request()->routeIs('admin.flash_sales.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-bolt w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.flash_sales.*') || request()->routeIs('admin.flash_sales.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}"></i>
                                <span class="flex-1">Flash Sale Platform</span>
                                <span class="text-[9px] font-bold bg-amber-500/15 text-amber-400 px-1.5 py-0.2 rounded font-mono-num">PROMO</span>
                            </a>
                        </nav>
                    </div>

                    <!-- CATEGORY 3: KEUANGAN & PAYOUT -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Keuangan & Payout
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('super_admin.withdrawals.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.withdrawals.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-money-bill-transfer w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.withdrawals.*') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400' }}"></i>
                                <span class="flex-1">Payout Toko</span>
                            </a>

                            <a href="{{ route('super_admin.reports.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.reports.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.reports.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                                <span class="flex-1">Laporan & Ekspor</span>
                            </a>
                        </nav>
                    </div>

                    <!-- CATEGORY 4: PENGGUNA & KEAMANAN -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 px-3 mb-1 font-mono-num">
                            Pengguna & Keamanan
                        </p>
                        <nav class="space-y-0.5">
                            <a href="{{ route('super_admin.users.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.users.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-users w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.users.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Daftar Pengguna</span>
                            </a>

                            <a href="{{ route('super_admin.admins.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('super_admin.admins.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-user-shield w-4 h-4 text-center text-xs {{ request()->routeIs('super_admin.admins.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Admin Operasional</span>
                            </a>

                            <a href="{{ route('chat.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('chat.*') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-regular fa-comment-dots w-4 h-4 text-center text-xs {{ request()->routeIs('chat.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Pusat Pesan / Chat</span>
                            </a>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('profile.edit') ? 'bg-blue-600/15 text-blue-400 font-semibold border-l-2 border-blue-500 pl-2.5' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium' }}">
                                <i class="fa-solid fa-sliders w-4 h-4 text-center text-xs {{ request()->routeIs('profile.edit') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span class="flex-1">Pengaturan Profil</span>
                            </a>
                        </nav>
                    </div>

                </div>

                <!-- FOOTER SIDEBAR -->
                <div class="p-3 border-t border-slate-800/90 bg-[#0B1324]">
                    <div class="p-2 rounded-lg bg-slate-900 border border-slate-800 flex items-center gap-2.5">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=2563eb&color=fff' }}" 
                             class="w-7 h-7 rounded-md object-cover ring-1 ring-slate-700 shrink-0" 
                             alt="{{ auth()->user()->name }}">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-200 truncate leading-tight">{{ auth()->user()->name }}</p>
                            <span class="text-[10px] text-slate-400 font-mono-num">Super Admin Platform</span>
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
                        <span class="text-slate-400 font-medium hidden sm:inline">Platform Control</span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-semibold text-slate-800 tracking-tight flex items-center gap-1.5">
                            {{ $pageTitle ?? 'Executive Overview & Platform Analytics' }}
                        </span>
                    </div>
                </div>

                <!-- Right: Timestamp & Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    <!-- Live Server Clock WIB -->
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200 text-xs text-slate-600 font-mono-num">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="super-admin-clock" class="font-semibold text-slate-700">--:--:-- WIB</span>
                    </div>

                    <!-- Secondary Outline Button: Tinjau Payout -->
                    <a href="{{ route('super_admin.withdrawals.index') }}" 
                       class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-md text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition-colors shrink-0">
                        <i class="fa-solid fa-money-bill-transfer text-emerald-600 text-[11px]"></i>
                        <span class="hidden md:inline">Tinjau Payout</span>
                        <span class="md:hidden">Payout</span>
                    </a>

                    <!-- Primary Solid Button: Ekspor Laporan Keuangan -->
                    <a href="{{ route('super_admin.reports.index') }}"
                       class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition-colors shrink-0">
                        <i class="fa-solid fa-file-invoice-dollar text-[11px]"></i>
                        <span class="hidden md:inline">Laporan & Ekspor</span>
                        <span class="md:hidden">Laporan</span>
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
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const clockEl = document.getElementById('super-admin-clock');
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
