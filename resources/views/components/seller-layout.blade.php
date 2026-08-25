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
        $activeComplaints = $sellerStore ? \App\Models\OrderComplaint::where('store_id', $sellerStore->id)->where('status', 'pending')->count() : 0;
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
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] text-slate-300 flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800 shrink-0">
            
            <div class="flex flex-col h-full">
                
                <!-- HEADER / BRANDING -->
                <div class="h-16 px-4 border-b border-slate-800 flex items-center justify-between bg-[#0B1324]">
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-xs shrink-0 p-1">
                            <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-white text-sm tracking-tight leading-none">Nitip<span class="text-blue-400">Dong</span></span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span>
                                <span class="text-xs text-slate-400 leading-none">Seller Center</span>
                            </div>
                        </div>
                    </a>

                    <!-- Mobile Dismiss -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- NAVIGATION ITEMS -->
                <div class="flex-1 overflow-y-auto slate-scrollbar px-3 py-5 space-y-6">
                    
                    <!-- SECTION 1: OPERASIONAL TOKO -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Operasional Toko
                        </p>
                        <nav class="space-y-1">
                            <!-- Dashboard Toko -->
                            @php $active = request()->routeIs('seller.dashboard'); @endphp
                            <a href="{{ route('seller.dashboard') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                                </svg>
                                <span class="flex-1 truncate">Dashboard Toko</span>
                            </a>

                            <!-- Pesanan Masuk -->
                            @php $active = request()->routeIs('seller.orders.*'); @endphp
                            <a href="{{ route('seller.orders.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                                </svg>
                                <span class="flex-1 truncate">Pesanan Masuk</span>
                                @if($pendingOrders > 0)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-mono-num">{{ $pendingOrders }}</span>
                                @endif
                            </a>

                            <!-- Katalog Produk -->
                            @php $active = request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit'); @endphp
                            <a href="{{ route('seller.products.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                                </svg>
                                <span class="flex-1 truncate">Katalog Produk</span>
                            </a>

                            <!-- Tambah Produk Baru -->
                            @php $active = request()->routeIs('seller.products.create'); @endphp
                            <a href="{{ route('seller.products.create') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                <span class="flex-1 truncate">Tambah Produk Baru</span>
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 2: KEUANGAN & PROMO -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Keuangan &amp; Promo
                        </p>
                        <nav class="space-y-1">
                            <!-- Dompet & Saldo Toko -->
                            @php $active = request()->routeIs('seller.wallet.*'); @endphp
                            <a href="{{ route('seller.wallet.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                                </svg>
                                <span class="flex-1 truncate">Dompet &amp; Saldo Toko</span>
                            </a>

                            <!-- Voucher Promo -->
                            @php $active = request()->routeIs('seller.vouchers.*'); @endphp
                            <a href="{{ route('seller.vouchers.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                                </svg>
                                <span class="flex-1 truncate">Voucher Promo</span>
                                @if($activeVouchers > 0)
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 font-mono-num">{{ $activeVouchers }}</span>
                                @endif
                            </a>

                            <!-- Pusat Komplain -->
                            @php $active = request()->routeIs('seller.complaints.*'); @endphp
                            <a href="{{ route('seller.complaints.index') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-rose-400' : 'text-slate-400 group-hover:text-rose-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/>
                                </svg>
                                <span class="flex-1 truncate">Pusat Komplain</span>
                                @if($activeComplaints > 0)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500 text-white font-mono-num">{{ $activeComplaints }}</span>
                                @endif
                            </a>
                        </nav>
                    </div>

                    <!-- SECTION 3: KOMUNIKASI & PENGATURAN -->
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">
                            Komunikasi &amp; Pengaturan
                        </p>
                        <nav class="space-y-1">
                            <!-- Chat Pembeli -->
                            @php $active = request()->routeIs('seller.chat.cus*'); @endphp
                            <a href="{{ route('seller.chat.cus') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <span class="flex-1 truncate">Chat Pembeli</span>
                            </a>

                            <!-- Chat Admin -->
                            @php $active = request()->routeIs('seller.chat.admin*'); @endphp
                            <a href="{{ route('seller.chat.admin') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M18 18.72a9 9 0 0 0 3-6.72c0-5-4-9-9-9s-9 4-9 9a9 9 0 0 0 3 6.72"/><path d="M13 14H7a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z"/>
                                </svg>
                                <span class="flex-1 truncate">Bantuan Admin</span>
                            </a>

                            <!-- Pengaturan Toko -->
                            @php $active = request()->routeIs('seller.settings.*'); @endphp
                            <a href="{{ route('seller.settings.edit') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/>
                                </svg>
                                <span class="flex-1 truncate">Pengaturan Toko</span>
                            </a>

                            <!-- Profil Akun -->
                            @php $active = request()->routeIs('profile.*'); @endphp
                            <a href="{{ route('profile.edit') }}"
                               class="relative flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors group {{ $active ? 'bg-slate-800/80 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium' }}">
                                @if($active)
                                    <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-r-full bg-blue-500"></span>
                                @endif
                                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/>
                                </svg>
                                <span class="flex-1 truncate">Profil Akun</span>
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
                            <span class="text-xs text-slate-400 block truncate">Pemilik Toko</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors" 
                                    title="Keluar">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
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
            <header class="h-16 shrink-0 bg-white border-b border-slate-200/90 px-4 sm:px-6 flex items-center justify-between z-30 shadow-xs">
                
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 font-medium hidden sm:inline">Seller Center</span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-semibold text-slate-800 tracking-tight flex items-center gap-1.5">
                            {{ $pageTitle ?? 'Operasional & Manajemen Toko' }}
                        </span>
                    </div>
                </div>

                <!-- Right: STORE IDENTITY + WIB CLOCK + MARKETPLACE LINK -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    
                    <!-- NAMA STORE DI ATAS KANAN -->
                    @if($sellerStore)
                    <a href="{{ route('seller.settings.edit') }}" 
                       class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs group"
                       title="Pengaturan Toko">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden">
                            @if($sellerStore->avatar_url ?? false)
                                <img src="{{ $sellerStore->avatar_url }}" alt="{{ $sellerStore->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($sellerStore->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="hidden md:flex flex-col text-left min-w-0">
                            <span class="text-xs font-bold text-slate-800 truncate max-w-[160px] group-hover:text-blue-600 transition-colors">{{ $sellerStore->name }}</span>
                            <span class="text-[10px] text-emerald-600 font-semibold leading-none flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Toko Resmi Aktif
                            </span>
                        </div>
                    </a>
                    @endif

                    <!-- LIVE WIB CLOCK -->
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 font-mono-num">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="seller-clock" class="font-semibold text-slate-700">--:--:-- WIB</span>
                    </div>

                    <!-- LIHAT MARKETPLACE -->
                    <a href="/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition-colors">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/>
                        </svg>
                        <span class="hidden sm:inline">Lihat Marketplace</span>
                    </a>
                </div>
            </header>

            <!-- SCROLLABLE WORKSPACE -->
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

    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const clockEl = document.getElementById('seller-clock');
            if (!clockEl) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Jakarta' });
            clockEl.textContent = `${timeStr} WIB`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    @stack('scripts')
</body>
</html>
