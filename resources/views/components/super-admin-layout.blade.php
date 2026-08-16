<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Dashboard - ' . config('app.name', 'BelanjaIn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');
    </style>
</head>
<body class="font-sans antialiased">
    <div class="h-screen bg-[#F6F4EF] text-[#3E4658] overflow-hidden" style="font-family:'Inter',sans-serif;">
        <div class="flex h-screen w-full">

            {{-- SIDEBAR --}}
            <aside class="w-64 h-full shrink-0 bg-[#152238] text-slate-300 flex flex-col justify-between py-6 px-4 overflow-y-auto">
                <div>
                    {{-- Brand --}}
                    <div class="flex items-center gap-2.5 px-2 mb-9">
                        <div class="w-9 h-9 rounded-lg bg-[#12A57F] flex items-center justify-center text-white font-bold text-sm" style="font-family:'Poppins',sans-serif;">BI</div>
                        <div>
                            <p class="font-bold text-white text-[15px] leading-none" style="font-family:'Poppins',sans-serif;">BelanjaIn</p>
                            <p class="text-[10px] text-slate-400 mt-1">Super Admin Panel</p>
                        </div>
                    </div>

                    {{-- Nav --}}
                    <nav class="flex flex-col gap-1">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold px-3 mb-2">Menu Utama</p>

                        <a href="{{ route('super_admin.dashboard') }}"
                           class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('super_admin.dashboard') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('super_admin.dashboard'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M4.5 9.75V21h15V9.75"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('super_admin.stores.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('super_admin.stores.index') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('super_admin.stores.index'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Daftar Toko
                        </a>
                        <a href="{{ route('super_admin.users.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('super_admin.users.index') ? 'bg-[#12A57F] text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            @if(request()->routeIs('super_admin.users.index'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r bg-white"></span>
                            @endif
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Pengguna
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

                {{-- User mini card --}}
                <div class="flex items-center gap-2.5 px-2 py-2.5 rounded-xl bg-white/5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=12A57F&color=fff" class="w-8 h-8 rounded-lg" alt="User">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Super Admin</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white" title="Keluar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 p-6 lg:p-8 flex flex-col gap-6 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
    
    <!-- Chat Widget Component (Optional for Super Admin) -->
    <x-chat-widget />
</body>
</html>
