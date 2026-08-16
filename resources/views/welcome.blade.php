<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BelanjaIn - Situs Jual Beli Online Terlengkap, Mudah & Aman</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5 { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('img/icon.jpg') }}">
</head>
<body class="bg-[#f0fdfa] text-slate-800 antialiased">

    <!-- BelanjaIn Header -->
    <header class="bg-white border-b border-slate-100" style="box-shadow:0 1px 8px rgba(0,0,0,0.06);">
        <!-- Top Navigation Bar -->
        <div class="max-w-[1200px] mx-auto px-4 flex justify-between items-center text-[13px] py-1">
            <div class="flex items-center gap-4">
                <a href="{{ route('seller.dashboard') }}" class="hover:text-cyan-600 transition-colors">Seller Centre</a>
                <span class="w-px h-3 bg-slate-200"></span>
                <a href="#" class="hover:text-cyan-600 transition-colors">Download</a>
            </div>
            
            <!-- Nav Actions -->
            <div class="ml-auto flex items-center gap-2">
                @auth
                    @php $cartCount = auth()->user()->role === 'customer' ? auth()->user()->carts()->count() : 0; @endphp
                    <a href="{{ route('customer.cart.index') }}" class="relative flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-cyan-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-slate-50 transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=06b6d4&color=fff&size=80" class="w-8 h-8 rounded-full ring-2 ring-cyan-100" alt="Avatar">
                            <span class="hidden md:block text-sm font-semibold text-slate-800" style="font-family:'Outfit',sans-serif;">{{ Str::words(Auth::user()->name, 1, '') }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-50">
                                <p class="text-sm font-semibold text-slate-800" style="font-family:'Outfit',sans-serif;">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                            </div>
                            @if(Auth::user()->role === 'super_admin')
                                <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">Super Admin Panel</a>
                            @elseif(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">Admin Panel</a>
                            @elseif(Auth::user()->role === 'seller')
                                <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">Toko Saya</a>
                            @else
                                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">Pesanan Saya</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-cyan-50 hover:text-cyan-600">Edit Profil</a>
                            <div class="border-t border-slate-50">
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-50">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-cyan-600 hover:text-cyan-700" style="font-family:'Outfit',sans-serif;">Masuk</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm" style="font-family:'Outfit',sans-serif;">Daftar</a>
                @endauth
            </div>
        </div>

        <!-- Main Search & Logo Bar -->
        <div class="max-w-[1200px] mx-auto px-4 py-4 flex items-center gap-8">
            <a href="/" class="flex items-center gap-2 shrink-0">
                <div class="bg-cyan-500 p-2 rounded-xl">
                    <img src="{{ asset('img/icon.jpg') }}" alt="Logo" class="w-8 h-8 object-cover rounded-md">
                </div>
                <span class="text-2xl font-bold tracking-tight text-slate-900" style="font-family:'Outfit',sans-serif;">BelanjaIn</span>
            </a>

            <div class="flex-1 flex flex-col gap-1">
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <input type="text" class="w-full bg-transparent border-none focus:ring-0 text-sm px-4 py-2" placeholder="Cari produk...">
                    <button class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- Notifikasi Flash -->
        @if(session('success'))
            <div class="max-w-[1200px] mx-auto mt-4 px-4">
                <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Banner Section -->
        <section class="max-w-[1200px] mx-auto px-4 pt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 relative rounded-2xl overflow-hidden shadow-sm bg-slate-200">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=2070&auto=format&fit=crop" class="w-full h-[250px] object-cover" alt="Banner">
                </div>
                <div class="hidden lg:grid grid-rows-2 gap-4">
                    <div class="rounded-2xl overflow-hidden shadow-sm bg-slate-200">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2070&auto=format&fit=crop" class="w-full h-[121px] object-cover" alt="Promo 1">
                    </div>
                    <div class="rounded-2xl overflow-hidden shadow-sm bg-slate-200">
                        <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=2071&auto=format&fit=crop" class="w-full h-[121px] object-cover" alt="Promo 2">
                    </div>
                </div>
            </div>
            
            <!-- Quick Menu Icons -->
            <div class="flex justify-between items-center bg-white p-4 mt-6 shadow-sm rounded-xl overflow-x-auto">
                <a href="#" class="flex flex-col items-center gap-2 min-w-[80px]">
                    <div class="w-11 h-11 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                    </div>
                    <span class="text-[11px] text-center font-medium">Gratis Ongkir</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 min-w-[80px]">
                    <div class="w-11 h-11 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-[11px] text-center font-medium">Flash Sale</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 min-w-[80px]">
                    <div class="w-11 h-11 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-[11px] text-center font-medium">Supermarket</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 min-w-[80px]">
                    <div class="w-11 h-11 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <span class="text-[11px] text-center font-medium">100% Ori</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 min-w-[80px]">
                    <div class="w-11 h-11 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <span class="text-[11px] text-center font-medium">Voucher</span>
                </a>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-[1200px] mx-auto px-4 mt-6">
            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="p-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-800 uppercase tracking-wide">Kategori</h2>
                </div>
                <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-10">
                    @forelse ($categories ?? [] as $category)
                        <a href="#" class="flex flex-col items-center gap-2 p-4 border-r border-b border-slate-100 hover:bg-slate-50 transition-colors group">
                            <div class="w-12 h-12 rounded-full bg-cyan-50 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <span class="text-2xl">{{ ['Elektronik'=>'💻','Pakaian'=>'👕','Makanan'=>'🍔','Otomotif'=>'🚗'][$category->name] ?? '📦' }}</span>
                            </div>
                            <span class="text-[11px] text-center font-medium text-slate-700 leading-tight">{{ $category->name }}</span>
                        </a>
                    @empty
                        <div class="col-span-full p-4 text-center text-sm text-slate-400">Belum ada kategori</div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Flash Sale -->
        <section class="max-w-[1200px] mx-auto px-4 mt-6">
            <div class="bg-white shadow-sm rounded-xl">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-cyan-600 text-lg" style="font-family:'Outfit',sans-serif;">⚡ Flash Sale</span>
                        <div class="flex items-center gap-1">
                            <span class="bg-cyan-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-lg">04</span><span class="font-bold text-slate-600">:</span>
                            <span class="bg-cyan-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-lg">48</span><span class="font-bold text-slate-600">:</span>
                            <span class="bg-cyan-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-lg">39</span>
                        </div>
                    </div>
                    <a href="#" class="text-cyan-600 text-sm font-medium flex items-center hover:text-cyan-700">
                        Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Rekomendasi / Product Catalog -->
        <section class="max-w-[1200px] mx-auto px-4 mt-6 mb-12">
            <div class="bg-white shadow-sm rounded-xl flex items-center justify-center sticky top-[72px] z-30 mb-2">
                <div class="flex-1 py-3.5 border-b-2 border-cyan-500 text-center text-cyan-600 font-semibold text-sm" style="font-family:'Outfit',sans-serif;">
                    REKOMENDASI UNTUKMU
                </div>
                <div class="flex-1 py-3.5 text-center text-slate-400 font-medium text-sm cursor-not-allowed" style="font-family:'Outfit',sans-serif;">
                    PRODUK TERLARIS
                </div>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @forelse ($products as $product)
                    <a href="{{ route('product.show', $product) }}" class="bg-white border border-slate-100 rounded-xl overflow-hidden hover:border-cyan-400 hover:shadow-lg transition-all group flex flex-col h-full">
                        <div class="relative w-full aspect-square bg-slate-50 overflow-hidden shrink-0">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">No Image</div>
                            @endif
                        </div>
                        <div class="p-3 flex flex-col flex-1 justify-between">
                            <h3 class="font-normal text-slate-800 text-[12px] line-clamp-2 mb-2 leading-snug">{{ $product->name }}</h3>
                            <p class="text-[16px] font-bold text-cyan-600">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400">Belum ada produk.</div>
                @endforelse
            </div>
            
            @if(count($products) > 0)
            <div class="mt-6 text-center">
                <button class="bg-white border border-slate-200 hover:border-cyan-500 hover:text-cyan-600 text-slate-600 px-12 py-2 rounded-xl font-medium transition-all">
                    Lihat Lainnya
                </button>
            </div>
            @endif
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 pt-10 pb-6 mt-12 text-sm text-slate-500">
        <div class="max-w-[1200px] mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg overflow-hidden"><img src="{{ asset('img/icon.jpg') }}" class="w-full h-full object-cover"></div>
                        <span class="font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">Belanja<span class="text-cyan-500">In</span></span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400">Platform jual beli online terpercaya. Belanja mudah, aman, dan nyaman.</p>
                </div>
                <div>
                    <h3 class="font-bold text-slate-700 mb-4 text-xs uppercase tracking-wider" style="font-family:'Outfit',sans-serif;">Layanan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Bantuan & FAQ</a></li>
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Metode Pembayaran</a></li>
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Gratis Ongkir</a></li>
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-slate-700 mb-4 text-xs uppercase tracking-wider" style="font-family:'Outfit',sans-serif;">Perusahaan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-cyan-600 transition-colors">Karir</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-slate-700 mb-4 text-xs uppercase tracking-wider" style="font-family:'Outfit',sans-serif;">Ikuti Kami</h3>
                    <div class="flex gap-2">
                        <a href="#" class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 hover:bg-cyan-100 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 hover:bg-cyan-100 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row justify-between items-center gap-2">
                <p class="text-xs">&copy; 2026 BelanjaIn. Hak Cipta Dilindungi.</p>
                <p class="text-xs">🇮🇩 Indonesia</p>
            </div>
        </div>
    </footer>
    
    <x-chat-widget />
</body>
</html>