<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard - ' . config('app.name', 'SakserShop') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="{{ asset('img/saksershop-logo.png') }}">
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div class="h-screen bg-slate-50 text-slate-800 overflow-hidden">
        <div class="flex h-screen w-full">

            <aside class="w-64 h-full shrink-0 bg-slate-900 text-slate-300 flex flex-col justify-between py-5 px-3.5 overflow-y-auto border-r border-slate-800">
                <div>
                    <div class="flex items-center gap-2.5 px-3 py-1 mb-6">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                            <div class="w-8 h-8 rounded-lg overflow-hidden border border-cyan-400/30 bg-slate-950 flex items-center justify-center shrink-0">
                                <img src="{{ asset('img/saksershop-logo.png') }}" alt="SakserShop Logo" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-white text-sm leading-tight tracking-tight">Sakser<span class="text-cyan-400 font-extrabold">Shop</span></p>
                                    <span class="text-[10px] font-semibold text-cyan-400 bg-cyan-950/60 border border-cyan-800/50 px-1.5 py-0.5 rounded tracking-normal">Admin Panel</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <nav class="flex flex-col gap-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 mt-2 mb-2">Moderasi & Kontrol</p>

                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-store w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Persetujuan Toko
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('admin.products.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-boxes-stacked w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('admin.products.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Moderasi Produk
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('admin.categories.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-tags w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('admin.categories.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Kategori Produk
                        </a>

                        <a href="{{ route('admin.flash_sales.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('admin.flash_sales.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-bolt w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('admin.flash_sales.*') ? 'text-cyan-400' : 'text-amber-400' }}"></i>
                            Flash Sale Platform
                        </a>

                        <a href="{{ route('chat.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs transition-colors group {{ request()->routeIs('chat.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border-l-2 border-cyan-400 pl-2.5 rounded-r-md' : 'font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                            <i class="fa-regular fa-comment-dots w-4 h-4 text-xs flex items-center justify-center {{ request()->routeIs('chat.*') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
                            Pesan Chat
                        </a>

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 mt-5 mb-2">Akun</p>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 transition-colors group">
                            <i class="fa-solid fa-user-gear w-4 h-4 text-xs flex items-center justify-center text-slate-400 group-hover:text-slate-200"></i>
                            Pengaturan Profil
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-slate-800/50 border border-slate-800">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-md object-cover shrink-0" alt="User">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-200 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Admin Operasional</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-rose-400 hover:bg-slate-800 p-1.5 rounded-md transition-colors" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="flex-1 p-5 sm:p-6 lg:p-8 pb-28 sm:pb-36 flex flex-col gap-6 overflow-y-auto relative scroll-smooth">
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
