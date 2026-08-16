<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BelanjaIn - Platform E-Commerce Modern</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-200 selection:text-emerald-900">

    <!-- Topbar / Navbar -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-2 group">
                        <div class="bg-gradient-to-br from-emerald-500 to-indigo-600 text-white p-2 rounded-xl group-hover:scale-105 transition-transform duration-300 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-indigo-600">BelanjaIn</span>
                    </a>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                    <div class="relative w-full group">
                        <input type="text" class="w-full bg-slate-100 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 rounded-full pl-12 pr-4 py-2.5 text-sm transition-all shadow-inner" placeholder="Cari produk, toko, atau kategori...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Nav -->
                <div class="hidden md:flex items-center space-x-6">
                    <!-- Cart Real Dynamic Indicator -->
                    @auth
                        @php 
                            $cartCount = auth()->user()->carts()->count(); 
                        @endphp
                        <a href="{{ route('customer.cart.index') }}" class="relative text-slate-500 hover:text-emerald-600 transition-colors" title="Keranjang Belanja">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            @if($cartCount > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-[10px] font-bold text-white shadow-sm">{{ $cartCount }}</span>
                            @endif
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="relative text-slate-500 hover:text-emerald-600 transition-colors" title="Keranjang Belanja">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </a>
                    @endauth

                    <div class="w-px h-6 bg-slate-200"></div>

                    <!-- Auth Links -->
                    @if (Route::has('login'))
                        @auth
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors focus:outline-none">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10B981&color=fff" class="w-8 h-8 rounded-full border border-slate-200" alt="Avatar">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 overflow-hidden z-50">
                                    @if(Auth::user()->role === 'super_admin')
                                        <a href="{{ route('super_admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Dashboard Super Admin</a>
                                    @elseif(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Dashboard Admin</a>
                                    @elseif(Auth::user()->role === 'seller')
                                        <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Toko Saya</a>
                                    @else
                                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Dashboard Saya</a>
                                    @endif
                                    
                                    <a href="{{ route('chat.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Pesan Masuk</a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Pengaturan Profil</a>
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-semibold bg-emerald-600 text-white px-4 py-2 rounded-full hover:bg-emerald-700 hover:shadow-md transition-all">Daftar</a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-500 hover:text-slate-700 focus:outline-none p-2">
                        <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Search Bar -->
            <div class="md:hidden pb-4 pt-2">
                <div class="relative w-full">
                    <input type="text" class="w-full bg-slate-100 border-transparent focus:bg-white focus:border-emerald-500 rounded-full pl-10 pr-4 py-2 text-sm" placeholder="Cari produk...">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-collapse class="md:hidden border-t border-slate-100 bg-white">
            <div class="px-4 pt-2 pb-6 space-y-2">
                @if (Route::has('login'))
                    @auth
                        <div class="flex items-center space-x-3 py-3 border-b border-slate-100 mb-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" class="w-10 h-10 rounded-full" alt="Avatar">
                            <div>
                                <p class="font-medium text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('customer.dashboard') }}" class="block py-2 text-slate-600 font-medium">Dashboard</a>
                        <a href="{{ route('customer.cart.index') }}" class="block py-2 text-slate-600 font-medium">Keranjang Belanja</a>
                        <a href="{{ route('chat.index') }}" class="block py-2 text-slate-600 font-medium">Pesan Masuk</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left py-2 text-rose-600 font-medium">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block py-2 text-slate-600 font-medium">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block py-2 text-emerald-600 font-medium">Daftar Akun Baru</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Notifikasi Flash -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden border-b border-slate-100">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-emerald-50 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-50 opacity-50 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="max-w-2xl">
                    <span class="inline-block py-1 px-3 rounded-full bg-emerald-100 text-emerald-700 text-sm font-semibold mb-4 tracking-wide shadow-sm">
                        🎉 Platform Jual Beli Terpercaya
                    </span>
                    <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
                        Belanja Nyaman, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-indigo-600">Harga Teman.</span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Temukan ribuan produk berkualitas dari seller terverifikasi. Transaksi aman, pengiriman cepat, dan terhubung langsung dengan penjual.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#katalog-produk" class="bg-emerald-600 text-white px-8 py-3.5 rounded-full font-semibold hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-300">
                            Mulai Belanja
                        </a>
                        <a href="#kategori" class="bg-white text-slate-700 border border-slate-200 px-8 py-3.5 rounded-full font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all duration-300 flex items-center gap-2">
                            Lihat Kategori
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="mt-10 flex items-center gap-6 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            100% Original
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H14a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-5a1 1 0 00-.293-.707l-3-3A1 1 0 0016 5h-3V4a1 1 0 00-1-1H4a1 1 0 00-1 1z"></path></svg>
                            Pengiriman Aman
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                            Direct Chat Seller
                        </div>
                    </div>
                </div>
                
                <!-- Hero Visual Banner -->
                <div class="relative hidden lg:block">
                    <div class="grid grid-cols-2 gap-4">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Gadget" class="rounded-2xl shadow-xl w-full h-64 object-cover transform translate-y-8 hover:-translate-y-2 transition-transform duration-500">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Shoes" class="rounded-2xl shadow-xl w-full h-64 object-cover hover:-translate-y-2 transition-transform duration-500">
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Headphone" class="rounded-2xl shadow-xl w-full h-48 object-cover col-span-2 transform -translate-y-4 hover:-translate-y-6 transition-transform duration-500">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section (Dynamic from Database) -->
    <section id="kategori" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-1">Kategori Pilihan</h2>
                    <p class="text-slate-500 text-sm">Jelajahi produk berdasarkan minatmu</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($categories as $category)
                    <a href="#katalog-produk" class="group flex flex-col items-center justify-center p-5 bg-slate-50 rounded-2xl hover:bg-emerald-50 hover:shadow-md transition-all duration-300 border border-slate-100 hover:border-emerald-200">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300 mb-3 text-emerald-600 font-bold text-lg">
                            🏷️
                        </div>
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-emerald-700">{{ $category->name }}</span>
                    </a>
                @empty
                    <div class="col-span-full text-center py-4 text-slate-400 text-sm">Kategori belum tersedia.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Dynamic Product Catalog Section -->
    <section id="katalog-produk" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                        🛍️ Produk Terbaru
                    </h2>
                    <p class="text-slate-500 text-sm">Pilihan produk berkualitas dari mitra toko BelanjaIn</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group overflow-hidden flex flex-col justify-between p-4">
                        <div>
                            <!-- Foto Produk -->
                            <div class="relative w-full h-48 bg-slate-100 rounded-2xl overflow-hidden mb-4">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">Foto Tidak Tersedia</div>
                                @endif
                                <div class="absolute top-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                    {{ $product->category->name ?? 'Produk' }}
                                </div>
                            </div>

                            <!-- Info Produk -->
                            <p class="text-xs text-slate-500 mb-1 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium text-slate-700">{{ $product->store->name ?? 'Toko BelanjaIn' }}</span>
                            </p>
                            <h3 class="font-semibold text-slate-800 text-sm line-clamp-2 mb-2 group-hover:text-emerald-600 transition-colors">
                                {{ $product->name }}
                            </h3>
                            
                            <div class="mb-3">
                                <p class="text-lg font-extrabold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-400">Tersedia {{ $product->stock }} stok</p>
                            </div>
                        </div>

                        <!-- Tombol Aksi Tambah Keranjang & Direct Chat -->
                        <div class="mt-2 pt-3 border-t border-slate-100 flex items-center gap-2">
                            <!-- Add to Cart Form -->
                            <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2.5 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Keranjang
                                </button>
                            </form>

                            <!-- Chat Seller Button -->
                            @auth
                                <form action="{{ route('chat.start', $product->store->user_id ?? 1) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors" title="Chat Pemilik Toko">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors" title="Login untuk Chat Toko">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-100">
                        <p class="text-slate-500 font-medium">Belum ada produk aktif yang ditampilkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Seller CTA Banner -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-indigo-900 to-emerald-900 rounded-3xl overflow-hidden shadow-2xl relative">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-emerald-500 opacity-20 rounded-full blur-2xl"></div>
                
                <div class="grid md:grid-cols-2 relative z-10">
                    <div class="p-10 md:p-16 flex flex-col justify-center">
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 leading-tight">Punya Produk? <br>Buka Tokomu Sendiri di BelanjaIn</h2>
                        <p class="text-emerald-100 mb-8 text-lg">Jangkau jutaan pembeli di seluruh Indonesia dan nikmati kemudahan kelola produk serta pesanan secara profesional.</p>
                        <div class="flex">
                            @auth
                                <a href="{{ route('customer.store.register') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white font-bold py-3.5 px-8 rounded-full shadow-lg hover:shadow-emerald-500/50 transition-all duration-300">
                                    Buka Toko Sekarang
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white font-bold py-3.5 px-8 rounded-full shadow-lg hover:shadow-emerald-500/50 transition-all duration-300">
                                    Daftar & Jualan Sekarang
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="hidden md:block bg-[url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')] bg-cover bg-center">
                        <div class="w-full h-full bg-gradient-to-r from-indigo-900 to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div>
                    <a href="/" class="flex items-center gap-2 mb-6">
                        <div class="bg-gradient-to-br from-emerald-500 to-indigo-600 text-white p-2 rounded-xl shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-indigo-600">BelanjaIn</span>
                    </a>
                    <p class="text-slate-500 text-sm mb-6">Platform E-Commerce multi-vendor terpercaya di Indonesia.</p>
                </div>
                
                <div>
                    <h3 class="font-bold text-slate-900 mb-4">Navigasi Cepat</h3>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="#katalog-produk" class="hover:text-emerald-600 transition-colors">Semua Produk</a></li>
                        <li><a href="#kategori" class="hover:text-emerald-600 transition-colors">Kategori</a></li>
                        <li><a href="{{ route('customer.dashboard') }}" class="hover:text-emerald-600 transition-colors">Akun Saya</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold text-slate-900 mb-4">Seller & Merchant</h3>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="{{ route('customer.store.register') }}" class="hover:text-emerald-600 transition-colors">Daftar Buka Toko</a></li>
                        <li><a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard Toko</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-slate-900 mb-4">Keamanan Transaksi</h3>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-slate-50 border border-slate-100 p-2 rounded flex items-center justify-center">
                            <span class="font-bold text-xs text-slate-400">PCI DSS</span>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-2 rounded flex items-center justify-center">
                            <span class="font-bold text-xs text-slate-400">SSL 256-bit</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-200 pt-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} BelanjaIn. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Floating Chat Widget Component -->
    <x-chat-widget />

</body>
</html>