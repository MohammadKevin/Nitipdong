<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Control Center - ' . config('app.name', 'NitipDong') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">

    <!-- Chart.js for High-End Interactive Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
            font-feature-settings: "tnum" 1;
        }
        /* Custom Cyan Scrollbar */
        .cyan-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .cyan-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        .cyan-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(6, 182, 212, 0.3);
            border-radius: 9999px;
        }
        .cyan-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(6, 182, 212, 0.6);
        }
    </style>
</head>
<body class="h-full bg-slate-900 font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
        
        <!-- Mobile Sidebar Overlay Backdrop -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden">
        </div>

        <!-- ULTRA-SLEEK MODERN CYAN-SLATE SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0A1128] text-slate-300 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-cyan-950/80 shadow-2xl shadow-cyan-950/20">
            
            <!-- Sidebar Header & Logo -->
            <div class="flex flex-col h-full">
                <div class="p-5 pb-4 border-b border-slate-800/80 bg-[#060D20]/60">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 via-cyan-600 to-cyan-800 p-0.5 shadow-lg shadow-cyan-500/25 group-hover:shadow-cyan-400/40 transition-all duration-300">
                                <div class="w-full h-full bg-[#0A1128] rounded-[10px] flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('img/saksershop-logo.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-white text-base tracking-tight leading-none">Nitip<span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-cyan-500">Dong</span></span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                    <span class="text-[10px] font-bold tracking-wider uppercase text-cyan-400/90 font-mono-num">Super Control</span>
                                </div>
                            </div>
                        </a>

                        <!-- Close Button (Mobile Only) -->
                        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                            <i class="fa-solid fa-xmark text-base"></i>
                        </button>
                    </div>

                    <!-- Live System Engine Status Pill -->
                    <div class="mt-4 px-3 py-2 rounded-lg bg-gradient-to-r from-cyan-950/60 to-slate-900/80 border border-cyan-500/20 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                            </span>
                            <span class="text-[11px] font-semibold text-slate-200">Engine v2.4</span>
                        </div>
                        <span class="text-[10px] font-mono-num font-bold text-cyan-400 bg-cyan-950 px-1.5 py-0.5 rounded border border-cyan-800/60">LIVE PROD</span>
                    </div>
                </div>

                <!-- Navigation Links (Scrollable) -->
                <div class="flex-1 overflow-y-auto cyan-scrollbar px-3.5 py-4 space-y-6">
                    
                    <!-- Section: CORE ANALYTICS -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-cyan-400/70 px-3 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-[9px]"></i>
                            <span>Analitik & Utama</span>
                        </p>
                        <nav class="space-y-1">
                            <a href="{{ route('super_admin.dashboard') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('super_admin.dashboard') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('super_admin.dashboard') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-chart-line text-xs"></i>
                                </div>
                                <span class="flex-1">Executive Dashboard</span>
                                @if(request()->routeIs('super_admin.dashboard'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                @endif
                            </a>
                        </nav>
                    </div>

                    <!-- Section: OPERASIONAL MARKETPLACE -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-cyan-400/70 px-3 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-store text-[9px]"></i>
                            <span>Operasional Platform</span>
                        </p>
                        <nav class="space-y-1">
                            <a href="{{ route('super_admin.stores.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('super_admin.stores.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('super_admin.stores.*') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-shop text-xs"></i>
                                </div>
                                <span class="flex-1">Manajemen Toko</span>
                            </a>

                            <a href="{{ route('admin.categories.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-tags text-xs"></i>
                                </div>
                                <span class="flex-1">Kategori Produk</span>
                            </a>

                            <a href="{{ route('admin.flash_sales.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.flash_sales.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.flash_sales.*') ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-amber-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-bolt text-xs"></i>
                                </div>
                                <span class="flex-1">Flash Sale Platform</span>
                                <span class="text-[9px] font-bold bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded border border-amber-500/30">PROMO</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Section: KEUANGAN & PAYOUT -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-cyan-400/70 px-3 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-wallet text-[9px]"></i>
                            <span>Keuangan & Payout</span>
                        </p>
                        <nav class="space-y-1">
                            <a href="{{ route('super_admin.withdrawals.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('super_admin.withdrawals.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('super_admin.withdrawals.*') ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-emerald-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-money-bill-transfer text-xs"></i>
                                </div>
                                <span class="flex-1">Payout Toko</span>
                            </a>

                            <a href="{{ route('super_admin.reports.revenue.export') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group text-slate-400 hover:text-cyan-300 hover:bg-slate-800/50">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800">
                                    <i class="fa-solid fa-file-excel text-xs"></i>
                                </div>
                                <span class="flex-1">Ekspor Laporan</span>
                                <i class="fa-solid fa-download text-[10px] text-slate-500 group-hover:text-cyan-300"></i>
                            </a>
                        </nav>
                    </div>

                    <!-- Section: USER & SECURITY -->
                    <div>
                        <p class="text-[10px] font-bold tracking-wider uppercase text-cyan-400/70 px-3 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-[9px]"></i>
                            <span>Pengguna & Keamanan</span>
                        </p>
                        <nav class="space-y-1">
                            <a href="{{ route('super_admin.users.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('super_admin.users.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('super_admin.users.*') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-users text-xs"></i>
                                </div>
                                <span class="flex-1">Daftar Pengguna</span>
                            </a>

                            <a href="{{ route('super_admin.admins.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('super_admin.admins.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('super_admin.admins.*') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-user-shield text-xs"></i>
                                </div>
                                <span class="flex-1">Admin Operasional</span>
                            </a>

                            <a href="{{ route('chat.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('chat.*') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-regular fa-comment-dots text-xs"></i>
                                </div>
                                <span class="flex-1">Pusat Pesan / Chat</span>
                            </a>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/5 text-cyan-300 border-l-4 border-cyan-400 shadow-sm shadow-cyan-500/10 pl-2.5' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('profile.edit') ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-800/80 text-slate-400 group-hover:text-cyan-300 group-hover:bg-slate-800' }}">
                                    <i class="fa-solid fa-sliders text-xs"></i>
                                </div>
                                <span class="flex-1">Pengaturan Profil</span>
                            </a>
                        </nav>
                    </div>

                </div>

                <!-- Bottom User Profile Card -->
                <div class="p-3.5 border-t border-slate-800/80 bg-[#060D20]/80">
                    <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center gap-3">
                        <div class="relative shrink-0">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0891b2&color=fff' }}" 
                                 class="w-9 h-9 rounded-lg object-cover ring-1 ring-cyan-500/40" 
                                 alt="{{ auth()->user()->name }}">
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-cyan-400 border-2 border-slate-900"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-block text-[9px] font-bold uppercase tracking-wider text-cyan-400 bg-cyan-950/80 border border-cyan-800/40 px-1.5 py-0.2 rounded font-mono-num">
                                SUPER ADMIN
                            </span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" 
                                    class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors" 
                                    title="Keluar dari Akun">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden">
            
            <!-- STICKY TOP HEADER -->
            <header class="h-16 shrink-0 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between z-30 shadow-xs">
                
                <!-- Left: Hamburger (Mobile) & Breadcrumbs -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-cyan-600 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-bars-staggered text-base"></i>
                    </button>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 font-medium hidden sm:inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-cube text-[11px] text-cyan-600"></i>
                            Platform Control
                        </span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-bold text-slate-800 tracking-tight flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-600"></span>
                            {{ $pageTitle ?? 'Super Admin Control Center' }}
                        </span>
                    </div>
                </div>

                <!-- Right: Status Indicators & Quick Actions -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    
                    <!-- Live Server Clock WIB -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 font-mono-num">
                        <i class="fa-regular fa-clock text-cyan-600"></i>
                        <span id="super-admin-clock" class="font-semibold text-slate-700">--:--:-- WIB</span>
                    </div>

                    <!-- Quick Payout Shortcut -->
                    <a href="{{ route('super_admin.withdrawals.index') }}" 
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-cyan-800 bg-cyan-50/80 hover:bg-cyan-100 border border-cyan-200 transition-colors">
                        <i class="fa-solid fa-money-bill-transfer text-cyan-600"></i>
                        <span>Payout Review</span>
                    </a>

                    <!-- Fast Export Report -->
                    <a href="{{ route('super_admin.reports.revenue.export') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 shadow-sm shadow-cyan-600/20 transition-colors">
                        <i class="fa-solid fa-file-excel text-xs"></i>
                        <span class="hidden sm:inline">Ekspor Data</span>
                    </a>
                </div>
            </header>

            <!-- SCROLLABLE PAGE BODY -->
            <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 space-y-6 pb-24 scroll-smooth">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-chat-popup />
    <x-chat-widget />
    <x-toast-notifier />

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
