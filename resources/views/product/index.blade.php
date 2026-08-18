<x-app-layout>
    <div class="page-container py-6">
        <div class="mb-5">
            <nav class="flex text-xs text-slate-400 mb-2 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
                <a href="/" class="hover:text-emerald-600 transition-colors">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <a href="{{ url('/products') }}" class="hover:text-emerald-600 transition-colors">Katalog</a>
                @if($activeCategory)
                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                    <span class="text-slate-700 font-medium">{{ $activeCategory->name }}</span>
                @elseif(request('q'))
                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                    <span class="text-slate-700 font-medium">Pencarian: "{{ request('q') }}"</span>
                @endif
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        @if(request('q'))
                            Hasil Pencarian: "<span class="text-emerald-600">{{ request('q') }}</span>"
                        @elseif($activeCategory)
                            {{ $activeCategory->name }}
                        @else
                            Semua Produk Pilihan
                        @endif
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Menampilkan {{ $products->total() }} produk berkualitas
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6" x-data="{ filterMobile: false }">
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs sticky top-24 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="font-bold text-xs uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-sliders text-emerald-600"></i> Filter Produk
                        </span>
                        @if(request()->hasAny(['category', 'min_price', 'max_price', 'discount_only', 'sort']))
                            <a href="{{ url('/products') }}" class="text-[11px] font-semibold text-rose-500 hover:underline">
                                Reset
                            </a>
                        @endif
                    </div>

                    <form action="{{ url('/products') }}" method="GET" class="space-y-4">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">Kategori</label>
                            <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                                <a href="{{ url('/products' . (request('q') ? '?q='.request('q') : '')) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ !request('category') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span>Semua Kategori</span>
                                </a>
                                @foreach($categories as $cat)
                                    <a href="{{ url('/products?'.http_build_query(array_merge(request()->except('category','page'), ['category' => $cat->slug]))) }}"
                                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ request('category') == $cat->slug ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span class="truncate">{{ $cat->name }}</span>
                                        @if(request('category') == $cat->slug)
                                            <i class="fa-solid fa-check text-[10px] text-emerald-600"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <label class="block text-xs font-semibold text-slate-700 mb-2">Rentang Harga (Rp)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}"
                                       class="input text-xs px-2.5 py-1.5">
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}"
                                       class="input text-xs px-2.5 py-1.5">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <label class="block text-xs font-semibold text-slate-700">Penawaran</label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600">
                                <input type="checkbox" name="discount_only" value="1" {{ request('discount_only') ? 'checked' : '' }}
                                       class="w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                <span>Hanya Produk Diskon</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600">
                                <input type="checkbox" name="flash_sale" value="1" {{ request('flash_sale') ? 'checked' : '' }}
                                       class="w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                <span>Sedang Flash Sale</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full btn-primary text-xs py-2">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="text-xs text-slate-500">
                        Urutkan berdasarkan kebutuhan Anda
                    </div>

                    <form method="GET" action="{{ url('/products') }}" class="flex items-center gap-2">
                        @foreach(request()->except('sort', 'page') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <span class="text-xs text-slate-400 shrink-0">Urutkan:</span>
                        <select name="sort" onchange="this.form.submit()" class="text-xs py-1.5 pl-3 pr-8 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 font-medium">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                        </select>
                    </form>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @forelse($products as $prod)
                    <div class="product-card group">
                        <a href="{{ route('product.show', $prod) }}" class="block">
                            <div class="product-img-box">
                                @if($prod->image)
                                    <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $prod->name }}" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                @endif

                                @if($prod->has_discount)
                                    <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white shadow-xs">
                                        -{{ $prod->discount_percentage_effective }}%
                                    </span>
                                @endif

                                @if($prod->is_in_flash_sale)
                                    <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-400 text-slate-950 flex items-center gap-1 shadow-xs">
                                        <i class="fa-solid fa-bolt"></i> Flash Sale
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="p-3.5 flex flex-col justify-between flex-1 gap-2">
                            <div>
                                <div class="flex items-center gap-1 text-[10px] text-slate-400 mb-1 truncate">
                                    <i class="fa-solid fa-store text-[9px] text-emerald-600"></i>
                                    <span class="truncate">{{ $prod->store->name ?? 'Toko Resmi' }}</span>
                                </div>

                                <a href="{{ route('product.show', $prod) }}" class="text-xs font-medium text-slate-800 line-clamp-2 hover:text-emerald-600 transition-colors leading-snug">
                                    {{ $prod->name }}
                                </a>
                            </div>

                            <div>
                                <div class="flex flex-col">
                                    <span class="text-sm sm:text-base font-bold text-slate-900">
                                        Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                                    </span>
                                    @if($prod->has_discount)
                                        <span class="text-[10px] text-slate-400 line-through">
                                            Rp {{ number_format($prod->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100 mt-2">
                                    <div class="flex items-center gap-1 text-amber-500 font-semibold">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        <span>5.0</span>
                                    </div>
                                    <span class="text-slate-400 text-[10px]">Terjual 30+</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                        <i class="fa-solid fa-magnifying-glass text-3xl mb-2 text-slate-300"></i>
                        <p class="text-sm font-semibold text-slate-700">Tidak ada produk yang cocok dengan filter</p>
                        <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci atau hapus beberapa filter pencarian.</p>
                        <a href="{{ url('/products') }}" class="mt-3 inline-block btn-secondary text-xs">
                            Hapus Semua Filter
                        </a>
                    </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
