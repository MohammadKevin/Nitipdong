@if(auth()->check() && auth()->user()->role === 'super_admin')
    <x-super-admin-layout :title="$title ?? null" :pageTitle="$pageTitle ?? null">
        {{ $slot }}
    </x-super-admin-layout>
@else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel - ' . config('app.name', 'NitipDong') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">

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

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0B0D10] text-[#C7CBD1] flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 border-r border-[#1B1F26] shrink-0">

    <div class="flex flex-col h-full">

        <!-- HEADER / BRANDING -->
        <div class="h-16 px-4 border-b border-[#1B1F26] flex items-center justify-between bg-[#0E1116]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-sm border border-[#262B33] bg-[#12151A] flex items-center justify-center shrink-0 relative">
                    <img src="{{ asset('img/saksershop-logo.png') }}" alt="Logo" class="w-5 h-5 object-contain opacity-90">
                    <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-[#B8441A] ring-2 ring-[#0E1116]"></span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-bold text-[#EDEEF0] text-sm tracking-tight leading-none">Nitip<span class="text-[#B8441A]">Dong</span></span>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#4C9A6A] shrink-0"></span>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-[#5B6270] leading-none">Pusat Kendali</span>
                    </div>
                </div>
            </a>

            <!-- Mobile Dismiss -->
            <button @click="sidebarOpen = false" class="lg:hidden text-[#5B6270] hover:text-[#EDEEF0] p-1 rounded-sm hover:bg-white/5 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- NAVIGATION ITEMS -->
        <div class="flex-1 overflow-y-auto slate-scrollbar px-3 py-5 space-y-7">

            <!-- SECTION 1: MODERASI & KONTROL -->
            <div>
                <div class="flex items-center gap-2 px-1 mb-2.5">
                    <span class="font-mono text-[10px] text-[#4A505B]">I</span>
                    <span class="text-[10px] font-medium tracking-wider text-[#5B6270] uppercase">Moderasi &amp; Kontrol</span>
                    <span class="flex-1 h-px bg-[#1B1F26]"></span>
                </div>
                <nav class="space-y-1">
                    @php $active = request()->routeIs('admin.dashboard'); @endphp
                    <a href="{{ route('admin.dashboard') }}"
                       class="group flex items-center gap-2.5 pl-2.5 pr-3 py-2 border-l-2 transition-colors {{ $active ? 'border-[#B8441A] bg-[#B8441A]/[0.06]' : 'border-transparent hover:border-[#2A2F38] hover:bg-white/[0.02]' }}">
                        <span class="w-6 h-6 shrink-0 grid place-items-center rounded-sm border font-mono text-[10px] font-semibold {{ $active ? 'border-[#B8441A]/50 bg-[#B8441A]/10 text-[#D9682F]' : 'border-[#262B33] text-[#5B6270] group-hover:text-[#8A90A0] group-hover:border-[#333944]' }}">PT</span>
                        <span class="flex-1 truncate text-xs {{ $active ? 'text-[#EDEEF0] font-medium' : 'text-[#8A90A0] group-hover:text-[#C7CBD1]' }}">Persetujuan Toko</span>
                        @if($active)<span class="w-1.5 h-1.5 rounded-full bg-[#B8441A] shrink-0"></span>@endif
                    </a>

                    @php $active = request()->routeIs('admin.products.*'); @endphp
                    <a href="{{ route('admin.products.index') }}"
                       class="group flex items-center gap-2.5 pl-2.5 pr-3 py-2 border-l-2 transition-colors {{ $active ? 'border-[#B8441A] bg-[#B8441A]/[0.06]' : 'border-transparent hover:border-[#2A2F38] hover:bg-white/[0.02]' }}">
                        <span class="w-6 h-6 shrink-0 grid place-items-center rounded-sm border font-mono text-[10px] font-semibold {{ $active ? 'border-[#B8441A]/50 bg-[#B8441A]/10 text-[#D9682F]' : 'border-[#262B33] text-[#5B6270] group-hover:text-[#8A90A0] group-hover:border-[#333944]' }}">MP</span>
                        <span class="flex-1 truncate text-xs {{ $active ? 'text-[#EDEEF0] font-medium' : 'text-[#8A90A0] group-hover:text-[#C7CBD1]' }}">Moderasi Produk</span>
                        @if($active)<span class="w-1.5 h-1.5 rounded-full bg-[#B8441A] shrink-0"></span>@endif
                    </a>
                </nav>
            </div>

            <!-- SECTION 2: KATALOG & PROMOSI -->
            <div>
                <div class="flex items-center gap-2 px-1 mb-2.5">
                    <span class="font-mono text-[10px] text-[#4A505B]">II</span>
                    <span class="text-[10px] font-medium tracking-wider text-[#5B6270] uppercase">Katalog &amp; Promosi</span>
                    <span class="flex-1 h-px bg-[#1B1F26]"></span>
                </div>
                <nav class="space-y-1">
                    @php $active = request()->routeIs('admin.categories.*'); @endphp
                    <a href="{{ route('admin.categories.index') }}"
                       class="group flex items-center gap-2.5 pl-2.5 pr-3 py-2 border-l-2 transition-colors {{ $active ? 'border-[#B8441A] bg-[#B8441A]/[0.06]' : 'border-transparent hover:border-[#2A2F38] hover:bg-white/[0.02]' }}">
                        <span class="w-6 h-6 shrink-0 grid place-items-center rounded-sm border font-mono text-[10px] font-semibold {{ $active ? 'border-[#B8441A]/50 bg-[#B8441A]/10 text-[#D9682F]' : 'border-[#262B33] text-[#5B6270] group-hover:text-[#8A90A0] group-hover:border-[#333944]' }}">KP</span>
                        <span class="flex-1 truncate text-xs {{ $active ? 'text-[#EDEEF0] font-medium' : 'text-[#8A90A0] group-hover:text-[#C7CBD1]' }}">Kategori Produk</span>
                        @if($active)<span class="w-1.5 h-1.5 rounded-full bg-[#B8441A] shrink-0"></span>@endif
                    </a>

                    @php $active = request()->routeIs('admin.flash_sales.*'); @endphp
                    <a href="{{ route('admin.flash_sales.index') }}"
                       class="group flex items-center gap-2.5 pl-2.5 pr-3 py-2 border-l-2 transition-colors {{ $active ? 'border-[#B8441A] bg-[#B8441A]/[0.06]' : 'border-transparent hover:border-[#2A2F38] hover:bg-white/[0.02]' }}">
                        <span class="w-6 h-6 shrink-0 grid place-items-center rounded-sm border font-mono text-[10px] font-semibold {{ $active ? 'border-[#B8441A]/50 bg-[#B8441A]/10 text-[#D9682F]' : 'border-[#262B33] text-[#5B6270] group-hover:text-amber-400 group-hover:border-amber-500/40' }}">FS</span>
                        <span class="flex-1 truncate text-xs {{ $active ? 'text-[#EDEEF0] font-medium' : 'text-[#8A90A0] group-hover:text-[#C7CBD1]' }}">Flash Sale Platform</span>
                        <span class="text-[9px] font-mono uppercase tracking-wide px-1.5 py-0.5 rounded-sm border border-amber-500/30 text-amber-400 shrink-0">Promo</span>
                    </a>
                </nav>
            </div>

            <!-- SECTION 3: KOMUNIKASI & AKUN -->
            <div>
                <div class="flex items-center gap-2 px-1 mb-2.5">
                    <span class="font-mono text-[10px] text-[#4A505B]">III</span>
                    <span class="text-[10px] font-medium tracking-wider text-[#5B6270] uppercase">Komunikasi &amp; Akun</span>
                    <span class="flex-1 h-px bg-[#1B1F26]"></span>
                </div>
                <nav class="space-y-1">
                    @php $active = request()->routeIs('chat.*'); @endphp
                    <a href="{{ route('chat.index') }}"
                       class="group flex items-center gap-2.5 pl-2.5 pr-3 py-2 border-l-2 transition-colors {{ $active ? 'border-[#B8441A] bg-[#B8441A]/[0.06]' : 'border-transparent hover:border-[#2A2F38] hover:bg-white/[0.02]' }}">
                        <span class="w-6 h-6 shrink-0 grid place-items-center rounded-sm border font-mono text-[10px] font-semibold {{ $active ? 'border-[#B8441A]/50 bg-[#B8441A]/10 text-[#D9682F]' : 'border-[#262B33] text-[#5B6270] group-hover:text-[#8A90A0] group-hover:border-[#333944]' }}">PC</span>
                        <span class="flex-1 truncate text-xs {{ $active ? 'text-[#EDEEF0] font-medium' : 'text-[#8A90A0] group-hover:text-[#C7CBD1]' }}">Pesan Chat</span>
                        @if($active)<span class="w-1.5 h-1.5 rounded-full bg-[#B8441A] shrink-0"></span>@endif
                    </a>

                    @php $active = request()->routeIs('profile.edit'); @endphp
                    <a href="{{ route('profile.edit') }}"
                       class="group flex items-center gap-2.5 pl-2.5 pr-3 py-2 border-l-2 transition-colors {{ $active ? 'border-[#B8441A] bg-[#B8441A]/[0.06]' : 'border-transparent hover:border-[#2A2F38] hover:bg-white/[0.02]' }}">
                        <span class="w-6 h-6 shrink-0 grid place-items-center rounded-sm border font-mono text-[10px] font-semibold {{ $active ? 'border-[#B8441A]/50 bg-[#B8441A]/10 text-[#D9682F]' : 'border-[#262B33] text-[#5B6270] group-hover:text-[#8A90A0] group-hover:border-[#333944]' }}">PP</span>
                        <span class="flex-1 truncate text-xs {{ $active ? 'text-[#EDEEF0] font-medium' : 'text-[#8A90A0] group-hover:text-[#C7CBD1]' }}">Pengaturan Profil</span>
                        @if($active)<span class="w-1.5 h-1.5 rounded-full bg-[#B8441A] shrink-0"></span>@endif
                    </a>
                </nav>
            </div>

        </div>

        <!-- FOOTER / USER PROFILE CARD (ID badge) -->
        <div class="p-3 border-t border-[#1B1F26] bg-[#0E1116]">
            <div class="relative p-2.5 rounded-sm border border-dashed border-[#2A2F38] bg-[#12151A] flex items-center gap-3">
                <div class="w-8 h-8 rounded-sm overflow-hidden border border-[#262B33] shrink-0">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=12151A&color=B8441A' }}"
                         class="w-full h-full object-cover" alt="{{ auth()->user()->name }}">
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-[#EDEEF0] truncate leading-snug">{{ auth()->user()->name }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#4C9A6A] shrink-0"></span>
                        <span class="text-[9px] font-mono uppercase tracking-wider text-[#5B6270]">Sesi Aktif</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="p-1.5 rounded-sm text-[#5B6270] hover:text-[#D9682F] hover:bg-white/5 transition-colors"
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
                        <span class="text-slate-400 font-medium hidden sm:inline">Admin Panel</span>
                        <span class="text-slate-300 hidden sm:inline">/</span>
                        <span class="font-semibold text-slate-800 tracking-tight flex items-center gap-1.5">
                            {{ $pageTitle ?? 'Operasional & Moderasi' }}
                        </span>
                    </div>
                </div>

                <!-- Right: Timestamp & Info -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200 text-xs text-slate-600 font-mono-num">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="admin-clock" class="font-semibold text-slate-700">--:--:-- WIB</span>
                    </div>
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
            const clockEl = document.getElementById('admin-clock');
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
@endif
