<x-admin-layout>
    <x-slot name="title">
        Moderasi Produk - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Moderasi & Kontrol Produk
    </x-slot>

    <!-- HEADER / ACTION BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Moderasi & Kontrol Produk
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau dan kelola seluruh etalase katalog produk merchant di platform NitipDong.</p>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Total Produk</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($totalProducts ?? 0, 0, ',', '.') }} Produk</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Produk Aktif (Tayang)</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($activeProducts ?? 0, 0, ',', '.') }} Produk</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Produk Di-Takedown</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($takedownProducts ?? 0, 0, ',', '.') }} Produk</h4>
            </div>
        </div>
    </div>

    <!-- PRODUCTS LEDGER TABLE CARD -->
    <div class="bg-white rounded-lg shadow-xs border border-slate-200/90 overflow-hidden">
        
        <!-- SEARCH & FILTER BAR -->
        <div class="p-4 border-b border-slate-100 flex flex-col lg:flex-row justify-between lg:items-center gap-3 bg-slate-50/50">
            
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto pb-1 lg:pb-0 font-mono-num">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.index', array_merge(request()->except(['status', 'page']))) : route('admin.products.index', array_merge(request()->except(['status', 'page']))) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ empty($status) ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Semua ({{ number_format($totalProducts ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) : route('admin.products.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'active' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Aktif ({{ number_format($activeProducts ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.index', array_merge(request()->except(['status', 'page']), ['status' => 'takedown'])) : route('admin.products.index', array_merge(request()->except(['status', 'page']), ['status' => 'takedown'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'takedown' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Takedown ({{ number_format($takedownProducts ?? 0, 0, ',', '.') }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.index') : route('admin.products.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
                @if(!empty($status))
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="relative flex-1 lg:w-72">
                    <input type="text" name="search" value="{{ $search }}" class="w-full h-8.5 pl-8 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono-num transition-colors placeholder:text-slate-400" placeholder="Cari produk atau toko...">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>
                <button type="submit" class="h-8.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs">Cari</button>
                @if($search || $status)
                    <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.index') : route('admin.products.index') }}" class="h-8.5 px-3 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors border border-slate-200">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Produk</th>
                        <th class="px-5 py-3 font-semibold">Harga & Stok</th>
                        <th class="px-5 py-3 font-semibold">Merchant / Toko</th>
                        <th class="px-5 py-3 font-semibold">Kategori</th>
                        <th class="px-5 py-3 font-semibold text-center">Status</th>
                        <th class="px-5 py-3 font-semibold text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ !$product->is_active ? 'bg-rose-50/30' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-2xs">
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-sm">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate max-w-[180px]">{{ $product->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono-num mt-0.5">ID: #{{ $product->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-900 font-mono-num">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-slate-500 font-mono-num mt-0.5">Stok: {{ $product->stock }} pcs</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Toko') }}&background=0f172a&color=fff&size=50" class="w-5 h-5 rounded-full object-cover shrink-0 border border-slate-200" alt="Store">
                                <span class="text-slate-700 font-medium truncate max-w-[130px]">{{ $product->store->name ?? 'Toko' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200 font-mono-num">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Takedown
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center">
                                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.toggle_status', $product) : route('admin.products.toggle_status', $product) }}" method="POST">
                                    @csrf
                                    @if($product->is_active)
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-md transition-colors border border-rose-200 text-xs font-semibold flex items-center gap-1 shadow-2xs cursor-pointer" title="Nonaktifkan (Takedown)" onclick="return confirm('Anda yakin ingin memblokir/takedown produk ini?')">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                            <span>Takedown</span>
                                        </button>
                                    @else
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-md transition-colors border border-emerald-200 text-xs font-semibold flex items-center gap-1 shadow-2xs cursor-pointer" title="Aktifkan Kembali" onclick="return confirm('Anda yakin ingin mengaktifkan kembali produk ini?')">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>Aktifkan</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-box-open text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada produk yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
