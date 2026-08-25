<x-app-layout>
    <div class="page-container py-5">
        <div class="mb-4">
            <nav class="flex text-xs text-slate-400 mb-1.5 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
                <a href="/" class="hover:text-cyan-700 transition-colors">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                <a href="{{ url('/products') }}" class="hover:text-cyan-700 transition-colors">Katalog</a>
                @if($activeCategory)
                    <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                    <span class="text-slate-700 font-medium">{{ $activeCategory->name }}</span>
                @elseif(request('q'))
                    <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                    <span class="text-slate-700 font-medium">Pencarian: "{{ request('q') }}"</span>
                @endif
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                        @if(request('q'))
                            Hasil Pencarian: "<span class="text-cyan-700">{{ request('q') }}</span>"
                        @elseif($activeCategory)
                            {{ $activeCategory->name }}
                        @else
                            Katalog Produk Esensial
                        @endif
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Menampilkan {{ $products->total() }} kurasi produk terbaik
                    </p>
                </div>
            </div>
        </div>

        <div class="mb-5 overflow-x-auto scrollbar-none pb-1">
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ url('/products' . (request('q') ? '?q='.request('q') : '')) }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold shrink-0 transition-colors flex items-center gap-1.5 {{ !request('category') ? 'bg-cyan-700 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-list text-[10px]"></i>
                    <span>Semua Kategori</span>
                </a>
                @foreach($categories as $cat)
                    <a href="{{ url('/products?'.http_build_query(array_merge(request()->except('category','page'), ['category' => $cat->slug]))) }}"
                       class="px-3.5 py-1.5 rounded-full font-semibold shrink-0 transition-colors flex items-center gap-1.5 {{ request('category') == $cat->slug ? 'bg-cyan-700 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        @if($cat->icon)
                            <i class="{{ $cat->icon }} text-[11px] {{ request('category') == $cat->slug ? 'text-white' : 'text-slate-400' }}"></i>
                        @endif
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-5" x-data="{ filterMobile: false }">
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-card sticky top-20 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="font-bold text-xs uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-sliders text-cyan-600 text-xs"></i> Filter Produk
                        </span>
                        @if(request()->hasAny(['category', 'min_price', 'max_price', 'discount_only', 'flash_sale', 'sort']))
                            <a href="{{ url('/products') }}" class="text-[11px] font-semibold text-rose-600 hover:underline">
                                Reset
                            </a>
                        @endif
                    </div>

                    <form action="{{ url('/products') }}" method="GET" class="space-y-4">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-800 mb-2">Kategori Produk</label>
                            <div class="space-y-1 max-h-52 overflow-y-auto pr-1">
                                <a href="{{ url('/products' . (request('q') ? '?q='.request('q') : '')) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-md text-xs {{ !request('category') ? 'bg-cyan-50 text-cyan-800 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span>Semua Kategori</span>
                                </a>
                                @foreach($categories as $cat)
                                    <a href="{{ url('/products?'.http_build_query(array_merge(request()->except('category','page'), ['category' => $cat->slug]))) }}"
                                       class="flex items-center justify-between px-2.5 py-1.5 rounded-md text-xs {{ request('category') == $cat->slug ? 'bg-cyan-50 text-cyan-800 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span class="truncate">{{ $cat->name }}</span>
                                        @if(request('category') == $cat->slug)
                                            <i class="fa-solid fa-check text-[10px] text-cyan-600"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <label class="block text-xs font-semibold text-slate-800 mb-2">Rentang Harga (Rp)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}"
                                       class="input text-xs px-2.5 py-1 h-8 rounded-md">
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}"
                                       class="input text-xs px-2.5 py-1 h-8 rounded-md">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <label class="block text-xs font-semibold text-slate-800 mb-1">Penawaran & Promo</label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600">
                                <input type="checkbox" name="discount_only" value="1" {{ request('discount_only') ? 'checked' : '' }}
                                       class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500">
                                <span>Hanya Produk Diskon</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600">
                                <input type="checkbox" name="flash_sale" value="1" {{ request('flash_sale') ? 'checked' : '' }}
                                       class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500">
                                <span>Sedang Flash Sale</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full btn-primary text-xs h-8.5 rounded-md bg-cyan-700 hover:bg-cyan-800">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-card mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="text-xs text-slate-500">
                        Hasil pencarian katalog NitipDong
                    </div>

                    <form method="GET" action="{{ url('/products') }}" class="flex items-center gap-2">
                        @foreach(request()->except('sort', 'page') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <span class="text-xs text-slate-400 shrink-0">Urutkan:</span>
                        <select name="sort" onchange="this.form.submit()" class="text-xs py-1 pl-2.5 pr-8 bg-slate-50 border border-slate-200 rounded-md text-slate-700 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 font-medium h-8">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                        </select>
                    </form>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3.5">
                    @forelse($products as $prod)
                    <div class="product-card group">
                        <a href="{{ route('product.show', $prod) }}" class="block">
                            <div class="product-img-frame">
                                @if($prod->image)
                                    <img src="{{ $prod->image_url }}" class="w-full h-full object-cover" alt="{{ $prod->name }}" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                @endif

                                @if($prod->has_discount)
                                    <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                        -{{ $prod->discount_percentage_effective }}%
                                    </span>
                                @endif

                                @if($prod->is_in_flash_sale)
                                    <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[10px] font-bold bg-cyan-900 text-cyan-200 flex items-center gap-1 border border-cyan-700/50">
                                        <i class="fa-solid fa-bolt text-cyan-400"></i> Flash Sale
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="p-3.5 flex flex-col justify-between flex-1 gap-2.5">
                            <div>
                                <div class="flex items-center gap-1 text-[11px] text-slate-400 mb-1 truncate">
                                    <i class="fa-solid fa-store text-[9px] text-cyan-700"></i>
                                    <span class="truncate">{{ $prod->store->name ?? 'Official Store' }}</span>
                                </div>

                                <a href="{{ route('product.show', $prod) }}" class="text-xs sm:text-sm font-normal text-slate-800 line-clamp-2 hover:text-cyan-700 transition-colors leading-snug">
                                    {{ $prod->name }}
                                </a>
                            </div>

                            <div>
                                <div class="flex flex-col">
                                    <span class="text-sm sm:text-base font-bold text-slate-900">
                                        Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                                    </span>
                                    @if($prod->has_discount)
                                        <span class="text-[11px] text-slate-400 line-through">
                                            Rp {{ number_format($prod->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-500 pt-2.5 border-t border-slate-100 mt-2.5">
                                    <div class="flex items-center gap-1 text-amber-500 font-semibold text-[11px]">
                                        <i class="fa-solid fa-star text-[9px]"></i>
                                        <span>{{ number_format($prod->effective_rating, 1) }}</span>
                                    </div>
                                    <span class="text-slate-400 text-[11px]">{{ $prod->formatted_sold_count }} terjual</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                        <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-slate-300"></i>
                        <p class="text-xs font-semibold text-slate-700">Tidak ada produk yang cocok</p>
                        <p class="text-xs text-slate-400 mt-0.5">Ubah kata kunci atau reset filter pencarian.</p>
                        <a href="{{ url('/products') }}" class="mt-3 inline-block btn-secondary text-xs h-8 px-4 rounded-md">
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
