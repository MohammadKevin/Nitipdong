<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Seller Center - ' . config('app.name', 'BelanjaIn') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" type="image/jpeg" href="{{ asset('img/icon.jpg') }}">
</head>
<body class="font-sans antialiased">
    @php
        $sellerStore = auth()->user()->store;
        $pendingOrders = $sellerStore ? \App\Models\Order::where('store_id', $sellerStore->id)->where('status', 'pending')->count() : 0;
        $activeVouchers = $sellerStore ? \App\Models\Voucher::where('store_id', $sellerStore->id)->count() : 0;
    @endphp

    <div class="h-screen bg-[#F6F4EF] text-[#3E4658] overflow-hidden" style="font-family:'Inter',sans-serif;">
        <div class="flex h-screen w-full">

            <aside class="w-64 h-full shrink-0 bg-[#152238] text-slate-300 flex flex-col justify-between py-6 px-4 overflow-y-auto">
                <div>
                    <div class="flex items-center gap-2.5 px-2 mb-8">
                        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 group">
                            <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-9 h-9 rounded-lg shadow-md object-cover">
                            <div>
                                <p class="font-bold text-white text-[15px] leading-none" style="font-family:'Poppins',sans-serif;">BelanjaIn</p>
                                <p class="text-[10px] text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                    Seller Center
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="px-2 py-3 mb-5 rounded-xl bg-white/5 border border-white/5">
                        <div class="flex items-center gap-2.5">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($sellerStore->name ?? auth()->user()->name) }}&background=12A57F&color=fff" class="w-8 h-8 rounded-lg shadow-sm" alt="Store">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-white truncate">{{ $sellerStore->name ?? 'Toko Saya' }}</p>
                                <span class="inline-flex items-center gap-1 text-[9px] text-emerald-300 font-medium">
                                    <i class="fa-solid fa-shield-check text-[9px]"></i> Official Seller
                                </span>
                            </div>
                        </div>
                    </div>

                    <nav class="flex flex-col gap-1">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold px-3 mb-2">Menu Toko</p>

                        <a href="{{ route('seller.dashboard') }}"
                           class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('seller.dashboard'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M4.5 9.75V21h15V9.75"/></svg>
                            Dashboard Toko
                        </a>

                        <a href="{{ route('seller.products.index') }}"
                           class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Katalog Produk
                        </a>

                        <a href="{{ route('seller.products.create') }}"
                           class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('seller.products.create') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('seller.products.create'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Produk
                        </a>

                        <a href="{{ route('seller.orders.index') }}"
                           class="relative flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('seller.orders.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                @if(request()->routeIs('seller.orders.*'))
                                    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                                @endif
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Pesanan Masuk
                            </div>
                            @if($pendingOrders > 0)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-400 text-slate-900 shadow-sm">{{ $pendingOrders }}</span>
                            @endif
                        </a>

                        <a href="{{ route('seller.vouchers.index') }}"
                           class="relative flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('seller.vouchers.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                @if(request()->routeIs('seller.vouchers.*'))
                                    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                                @endif
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                Voucher Toko
                            </div>
                            @if($activeVouchers > 0)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/30 text-emerald-300 border border-emerald-500/40">{{ $activeVouchers }}</span>
                            @endif
                        </a>

                        <a href="{{ route('chat.index') }}"
                           class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('chat.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('chat.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Pesan & Chat
                        </a>

                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold px-3 mt-6 mb-2">Lainnya</p>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/5 hover:text-white transition-colors">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan Profil
                        </a>

                        <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-300 transition-colors">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Lihat Marketplace
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-2.5 px-2 py-2.5 rounded-xl bg-white/5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=12A57F&color=fff" class="w-8 h-8 rounded-lg" alt="User">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Seller Toko</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white" title="Keluar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="flex-1 p-6 lg:p-8 flex flex-col gap-6 overflow-y-auto relative">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 flex gap-3 shadow-sm" role="alert">
                        <svg class="w-5 h-5 shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-medium text-xs">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 flex gap-3 shadow-sm" role="alert">
                        <svg class="w-5 h-5 shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-medium text-xs">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
    
    <x-chat-widget />
</body>
</html>
