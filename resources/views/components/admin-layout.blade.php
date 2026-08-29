@php
    $isSuperAdmin = request()->is('super-admin*') 
        || request()->routeIs('super_admin.*') 
        || (auth()->check() && auth()->user()->role === 'super_admin' && !request()->is('admin*'));
@endphp

@if($isSuperAdmin)
    <x-super-admin-layout :title="$title ?? null" :pageTitle="$pageTitle ?? null">
        @if(isset($headerActions))
            <x-slot name="headerActions">
                {{ $headerActions }}
            </x-slot>
        @endif
        {{ $slot }}
    </x-super-admin-layout>
@elseif(isset($pureContent) && $pureContent)
    {{ $slot }}
@else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Operasional - ' . config('app.name', 'NitipDong') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}">

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

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] text-slate-300 flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800 shrink-0">
            
            <div class="flex flex-col h-full">

                <div class="h-16 px-4 border-b border-slate-800 flex items-center justify-between bg-[#0B1324]">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-xs shrink-0 p-1">
                            <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-white text-sm tracking-tight leading-none">Nitip<span class="text-blue-400">Dong</span></span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                                <span class="text-xs text-slate-400 leading-none">Admin Operasional</span>
                            </div>
                        </div>
                    </a>

                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto slate-scrollbar px-3 py-5 space-y-6">

                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Moderasi &amp; Kontrol
                        </p>
                        <nav class="space-y-1">
                            
                            @php $active = request()->routeIs('admin.dashboard') || request()->routeIs('admin.stores.*'); @endphp
                            <a href="{{ route('admin.dashboard') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>
                                </svg>
                                <span class="flex-1 truncate">Persetujuan Toko</span>
                            </a>

                            @php $active = request()->routeIs('admin.products.*'); @endphp
                            <a href="{{ route('admin.products.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                                </svg>
                                <span class="flex-1 truncate">Moderasi Produk</span>
                            </a>

                            @php $active = request()->routeIs('admin.complaints.*'); @endphp
                            <a href="{{ route('admin.complaints.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-rose-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/>
                                </svg>
                                <span class="flex-1 truncate">Resolusi Komplain</span>
                            </a>

                            @php $active = request()->routeIs('admin.orders.*'); @endphp
                            <a href="{{ route('admin.orders.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                                </svg>
                                <span class="flex-1 truncate">Monitoring Pesanan</span>
                            </a>
                        </nav>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Katalog &amp; Promosi
                        </p>
                        <nav class="space-y-1">
                            
                            @php $active = request()->routeIs('admin.categories.*'); @endphp
                            <a href="{{ route('admin.categories.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/>
                                </svg>
                                <span class="flex-1 truncate">Kategori Produk</span>
                            </a>

                            @php $active = request()->routeIs('admin.flash_sales.*'); @endphp
                            <a href="{{ route('admin.flash_sales.index') }}"
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

                            @php $active = request()->routeIs('admin.vouchers.*') || request()->routeIs('super_admin.vouchers.*'); @endphp
                            <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.vouchers.index') : route('admin.vouchers.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                                </svg>
                                <span class="flex-1 truncate">Voucher &amp; Promo</span>
                            </a>

                            @php $active = request()->routeIs('admin.warehouses.*') || request()->routeIs('super_admin.warehouses.*'); @endphp
                            <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.warehouses.index') : route('admin.warehouses.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <i class="fa-solid fa-warehouse w-4 text-center text-xs shrink-0 {{ $active ? 'text-cyan-400' : 'text-slate-400 group-hover:text-cyan-400 transition-colors' }}"></i>
                                <span class="flex-1 truncate">Gudang Hub NDX</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300">NDX</span>
                            </a>
                        </nav>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Komunikasi &amp; Akun
                        </p>
                        <nav class="space-y-1">
                            
                            @php $active = request()->routeIs('chat.*'); @endphp
                            <a href="{{ route('chat.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <span class="flex-1 truncate">Pesan Chat</span>
                            </a>

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

                <div class="p-3 border-t border-slate-800 bg-[#0B1324]/60">
                    <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 transition-colors flex items-center gap-3">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=2563eb&color=fff' }}" 
                             class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-700 shrink-0" 
                             alt="{{ auth()->user()->name }}">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-200 truncate leading-snug">{{ auth()->user()->name }}</p>
                            <span class="text-xs text-slate-400 block truncate">Admin Operasional</span>
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

        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden">

            <header class="h-14 shrink-0 bg-white border-b border-slate-200/90 px-4 sm:px-6 flex items-center justify-between z-30 shadow-xs">

                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 font-medium hidden sm:inline">Admin Operasional</span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-semibold text-slate-800 tracking-tight flex items-center gap-1.5">
                            {{ $pageTitle ?? 'Operasional & Moderasi' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200 text-xs text-slate-600 font-mono-num">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="admin-clock" class="font-semibold text-slate-700">--:--:-- WIB</span>
                    </div>
                    @if(auth()->user()->role === 'super_admin')
                        <a href="{{ route('super_admin.dashboard') }}" class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                            Buka Super Admin &rarr;
                        </a>
                    @endif
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 lg:p-7 space-y-6 pb-24 scroll-smooth">
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 sm:p-4 rounded-xl bg-emerald-50/90 border border-emerald-200 text-emerald-800 flex items-start justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0 font-bold">✓</span>
                        <p class="text-xs font-semibold">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 text-xs p-1 font-bold">✕</button>
                </div>
                @endif

                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 sm:p-4 rounded-xl bg-rose-50/90 border border-rose-200 text-rose-800 flex items-start justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs shrink-0 font-bold">✕</span>
                        <p class="text-xs font-semibold">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-900 text-xs p-1 font-bold">✕</button>
                </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function updateAdminClock() {
            const clockEl = document.getElementById('admin-clock');
            if (!clockEl) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Jakarta' });
            clockEl.textContent = `${timeStr} WIB`;
        }
        setInterval(updateAdminClock, 1000);
        updateAdminClock();
    </script>
    <x-chat-popup />
    <x-toast-notifier />

    @stack('scripts')
</body>
</html>
@endif
