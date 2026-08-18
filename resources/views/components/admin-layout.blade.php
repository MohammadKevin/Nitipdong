<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard - ' . config('app.name', 'BelanjaIn') }}</title>

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
    <div class="h-screen bg-[#F6F4EF] text-[#3E4658] overflow-hidden" style="font-family:'Inter',sans-serif;">
        <div class="flex h-screen w-full">

            <aside class="w-64 h-full shrink-0 bg-[#152238] text-slate-300 flex flex-col justify-between py-6 px-4 overflow-y-auto">
                <div>
                    <div class="flex items-center gap-2.5 px-2 mb-9">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                            <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-9 h-9 rounded-lg shadow-md object-cover">
                            <div>
                                <p class="font-bold text-white text-[15px] leading-none" style="font-family:'Poppins',sans-serif;">BelanjaIn</p>
                                <p class="text-[10px] text-slate-400 mt-1">Admin Panel</p>
                            </div>
                        </a>
                    </div>

                    <nav class="flex flex-col gap-1">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold px-3 mb-2">Menu Utama</p>

                        <a href="{{ route('admin.dashboard') }}"
                           class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('admin.dashboard'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M4.5 9.75V21h15V9.75"/></svg>
                            Persetujuan Toko
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('admin.products.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Moderasi Produk
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('admin.categories.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Kategori Produk
                        </a>
                        <a href="{{ route('admin.flash_sales.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.flash_sales.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('admin.flash_sales.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Flash Sale
                        </a>
                        <a href="{{ route('chat.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('chat.*') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('chat.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Pesan
                        </a>

                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold px-3 mt-6 mb-2">Lainnya</p>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/5 hover:text-white transition-colors">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-2.5 px-2 py-2.5 rounded-xl bg-white/5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=12A57F&color=fff" class="w-8 h-8 rounded-lg" alt="User">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Admin</p>
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
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 flex gap-3 shadow-sm" role="alert">
                        <svg class="w-5 h-5 shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-medium">{{ session('error') }}</p>
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
