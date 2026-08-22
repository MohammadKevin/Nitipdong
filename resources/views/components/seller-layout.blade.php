<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Seller Center - ' . config('app.name', 'NitipDong') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    @php
        $sellerStore = auth()->user()->store;
        $pendingOrders = $sellerStore ? \App\Models\Order::where('store_id', $sellerStore->id)->where('status', 'pending')->count() : 0;
        $activeVouchers = $sellerStore ? \App\Models\Voucher::where('store_id', $sellerStore->id)->count() : 0;
    @endphp

    <div class="h-screen bg-slate-50 text-slate-800">
        <div class="flex h-screen w-full">

            <aside class="w-64 h-full shrink-0 bg-slate-900 text-slate-300 flex flex-col justify-between py-5 px-3.5 overflow-y-auto border-r border-slate-800">
                <div>
                    <div class="flex items-center gap-2.5 px-3 py-1 mb-5">
                        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 group">
                            <div class="w-8 h-8 rounded-lg overflow-hidden border border-cyan-400/30 bg-slate-950 flex items-center justify-center shrink-0">
                                <img src="{{ asset('img/saksershop-logo.png') }}" alt="NitipDong Logo" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-white text-sm leading-tight tracking-tight">Nitip<span class="text-cyan-400 font-extrabold">Dong</span></p>
                                    <span class="text-[10px] font-semibold text-cyan-400 bg-cyan-950/60 border border-cyan-800/50 px-1.5 py-0.5 rounded tracking-normal">Seller Center</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="px-3 py-2 mb-4 rounded-xl bg-slate-800/50 border border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-lg object-cover shrink-0 border border-slate-700 shadow-2xs" alt="Store">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-slate-200 truncate">{{ $sellerStore->name ?? 'Toko Saya' }}</p>
                                <span class="inline-flex items-center gap-1 text-[9px] text-cyan-400 font-medium">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Merchant Resmi
                                </span>
                            </div>
                        </div>
                    </div>

                    <nav class="flex flex-col gap-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 mt-2 mb-2">Operasional Toko</p>

                        <a href="{{ route('seller.dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.dashboard') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-chart-pie w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.dashboard') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Dashboard Toko
                        </a>

                        <a href="{{ route('seller.products.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-boxes-stacked w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Katalog Produk
                        </a>

                        <a href="{{ route('seller.products.create') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.products.create') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-plus w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.products.create') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Tambah Produk Baru
                        </a>

                        <a href="{{ route('seller.wallet.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.wallet.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-wallet w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.wallet.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Dompet & Saldo Toko
                        </a>

                        <a href="{{ route('seller.complaints.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.complaints.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-triangle-exclamation w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.complaints.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                                Pusat Komplain
                            </div>
                        </a>

                        <a href="{{ route('seller.orders.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.orders.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-receipt w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.orders.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                                Pesanan Masuk
                            </div>
                            @if($pendingOrders > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-400 text-slate-950">{{ $pendingOrders }}</span>
                            @endif
                        </a>

                        <a href="{{ route('seller.vouchers.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.vouchers.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-ticket w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.vouchers.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                                Voucher Promo
                            </div>
                            @if($activeVouchers > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-slate-800 text-slate-300 border border-slate-700">{{ $activeVouchers }}</span>
                            @endif
                        </a>

                        <a href="{{ route('seller.chat.cus') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.chat.cus*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-regular fa-comment-dots w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.chat.cus*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Pesan Chat Pembeli
                        </a>

                        <a href="{{ route('seller.chat.admin') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.chat.admin*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-headset w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.chat.admin*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Pesan Chat Admin
                        </a>

                        <a href="{{ route('seller.settings.edit') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('seller.settings.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-shop w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('seller.settings.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Pengaturan Toko & Alamat
                        </a>

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 mt-5 mb-2">Pintasan</p>
                        <a href="/" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 transition-colors group">
                            <i class="fa-solid fa-arrow-up-right-from-square w-4 h-4 text-xs flex items-center justify-center text-slate-400 group-hover:text-slate-200"></i>
                            Kunjungi Marketplace
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('profile.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-user-gear w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('profile.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Pengaturan Akun User
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-slate-800/50 border border-slate-800">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-md object-cover shrink-0" alt="User">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-200 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Seller Toko</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-rose-400 hover:bg-slate-800 p-1.5 rounded-md transition-colors" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="flex-1 p-5 sm:p-6 lg:p-8 pb-28 sm:pb-36 space-y-6 overflow-y-auto relative scroll-smooth animate-fade-up">
                @if(session('success'))
                    <div class="bg-cyan-50 border border-cyan-200 text-cyan-900 rounded-lg p-3.5 flex gap-2.5 shadow-xs text-xs font-semibold" role="alert">
                        <i class="fa-solid fa-circle-check text-cyan-600 text-sm mt-0.5"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-lg p-3.5 flex gap-2.5 shadow-xs text-xs font-semibold" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <x-chat-popup />
    <x-chat-widget />
    <x-toast-notifier />
    @stack('scripts')
</body>
</html>
