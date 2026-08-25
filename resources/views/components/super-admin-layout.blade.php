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
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] text-slate-300 flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800 shrink-0">
            
            <div class="flex flex-col h-full">
                
                <!-- HEADER / BRANDING -->
                <div class="h-16 px-4 border-b border-slate-800 flex items-center justify-between bg-[#0B1324]">
                    <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-xs shrink-0">
                            <img src="{{ asset('img/saksershop-logo.png') }}" alt="Logo" class="w-5 h-5 object-contain">
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-white text-sm tracking-tight leading-none">Nitip<span class="text-blue-400">Dong</span></span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                                <span class="text-xs text-slate-400 leading-none">Super Admin Platform</span>
                            </div>
                        </div>
                    </a>

                    <!-- Mobile Dismiss -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- NAVIGATION ITEMS -->
                <div class="flex-1 overflow-y-auto slate-scrollbar px-3 py-5 space-y-6">
                    
                    <!-- SECTION 1: ANALITIK & UTAMA -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Analitik &amp; Utama
                        </p>
                        <nav class="space-y-1">
                            @php $active = request()->routeIs('super_admin.dashboard'); @endphp
                            <a href="{{ route('super_admin.dashboard') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                                </svg>
                                <span class="flex-1 truncate">Executive Dashboard</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 2: OPERASIONAL PLATFORM -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Operasional Platform
                        </p>
                        <nav class="space-y-1">
                            <!-- Manajemen Toko -->
                            @php $active = request()->routeIs('super_admin.stores.*'); @endphp
                            <a href="{{ route('super_admin.stores.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/>
                                </svg>
                                <span class="flex-1 truncate">Manajemen Toko</span>
                            </a>

                            <!-- Persetujuan Toko -->
                            @php $active = request()->routeIs('super_admin.approvals.*') || request()->routeIs('admin.dashboard'); @endphp
                            <a href="{{ route('super_admin.approvals.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>
                                </svg>
                                <span class="flex-1 truncate">Persetujuan Toko</span>
                            </a>

                            <!-- Moderasi Produk -->
                            @php $active = request()->routeIs('super_admin.products.*') || request()->routeIs('admin.products.*'); @endphp
                            <a href="{{ route('super_admin.products.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                                </svg>
                                <span class="flex-1 truncate">Moderasi Produk</span>
                            </a>

                            <!-- Kategori Produk -->
                            @php $active = request()->routeIs('super_admin.categories.*') || request()->routeIs('admin.categories.*'); @endphp
                            <a href="{{ route('super_admin.categories.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/>
                                </svg>
                                <span class="flex-1 truncate">Kategori Produk</span>
                            </a>

                            <!-- Flash Sale Platform -->
                            @php $active = request()->routeIs('super_admin.flash_sales.*') || request()->routeIs('admin.flash_sales.*'); @endphp
                            <a href="{{ route('super_admin.flash_sales.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                </svg>
                                <span class="flex-1 truncate">Flash Sale Platform</span>
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">PROMO</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 3: KEUANGAN & PAYOUT -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Keuangan &amp; Payout
                        </p>
                        <nav class="space-y-1">
                            <!-- Payout Toko -->
                            @php $active = request()->routeIs('super_admin.withdrawals.*'); @endphp
                            <a href="{{ route('super_admin.withdrawals.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                                </svg>
                                <span class="flex-1 truncate">Payout Toko</span>
                            </a>

                            <!-- Laporan & Ekspor -->
                            @php $active = request()->routeIs('super_admin.reports.*'); @endphp
                            <a href="{{ route('super_admin.reports.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-blue-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/>
                                </svg>
                                <span class="flex-1 truncate">Laporan &amp; Ekspor</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 4: PENGGUNA & KEAMANAN -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Pengguna &amp; Keamanan
                        </p>
                        <nav class="space-y-1">
                            <!-- Daftar Pengguna -->
                            @php $active = request()->routeIs('super_admin.users.*'); @endphp
                            <a href="{{ route('super_admin.users.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span class="flex-1 truncate">Daftar Pengguna</span>
                            </a>

                            <!-- Admin Operasional -->
                            @php $active = request()->routeIs('super_admin.admins.*'); @endphp
                            <a href="{{ route('super_admin.admins.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>
                                </svg>
                                <span class="flex-1 truncate">Admin Operasional</span>
                            </a>

                            <!-- Pusat Pesan / Chat -->
                            @php $active = request()->routeIs('chat.*'); @endphp
                            <a href="{{ route('chat.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <span class="flex-1 truncate">Pusat Pesan / Chat</span>
                            </a>

                            <!-- Pengaturan Profil -->
                            @php $active = request()->routeIs('profile.edit'); @endphp
                            <a href="{{ route('profile.edit') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/>
                                </svg>
                                <span class="flex-1 truncate">Pengaturan Profil</span>
                            </a>
                        </nav>
                    </div>

                </div>

                <!-- FOOTER / USER PROFILE CARD -->
                <div class="p-3 border-t border-slate-800 bg-[#0B1324]/60">
                    <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 transition-colors flex items-center gap-3">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=2563eb&color=fff' }}" 
                             class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-700 shrink-0" 
                             alt="{{ auth()->user()->name }}">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-200 truncate leading-snug">{{ auth()->user()->name }}</p>
                            <span class="text-xs text-slate-400 block truncate">Super Admin Platform</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors" 
                                    title="Keluar">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" x2="9" y1="12" y2="12"/>
                                </svg>
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
