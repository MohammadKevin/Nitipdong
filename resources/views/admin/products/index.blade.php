<x-admin-layout>
    <x-slot name="title">
        Moderasi Produk - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Moderasi &amp; Kontrol Produk
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pb-1">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                    Moderasi &amp; Kontrol Produk
                </h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    Katalog
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Pantau, kurasi kualitas etalase produk merchant, dan lakukan tindakan penalti (takedown) bila melanggar ketentuan.</p>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200/90 shadow-xs shrink-0 self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Total Katalog: <strong class="text-slate-800 font-semibold">{{ number_format($totalProducts ?? 0, 0, ',', '.') }} Item</strong></span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/70 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/70">
                        Katalog Platform
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Produk</p>
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">
                        {{ number_format($totalProducts ?? 0, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">Produk</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Terdaftar di toko:</span>
                <span class="font-medium text-slate-800">{{ number_format($totalProducts ?? 0, 0, ',', '.') }} Item</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/70 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/70">
                        Live Tayang
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Produk Aktif (Tayang)</p>
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">
                        {{ number_format($activeProducts ?? 0, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">Produk</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Dapat dibeli pembeli:</span>
                <span class="font-medium text-emerald-700">{{ number_format($activeProducts ?? 0, 0, ',', '.') }} Aktif</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all sm:col-span-2 lg:col-span-1">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/70 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200/70">
                        Ditangguhkan
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Produk Di-Takedown</p>
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">
                        {{ number_format($takedownProducts ?? 0, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">Produk</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Diblokir dari etalase:</span>
                <span class="font-medium text-rose-700">{{ number_format($takedownProducts ?? 0, 0, ',', '.') }} Item</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden">

        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-slate-50/50">

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                @php
                    $routeIndex = auth()->user()->role === 'super_admin' ? 'super_admin.products.index' : 'admin.products.index';
                @endphp
                <a href="{{ route($routeIndex, array_merge(request()->except(['status', 'page']))) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Semua ({{ number_format($totalProducts ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ route($routeIndex, array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ ($status ?? '') === 'active' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Aktif ({{ number_format($activeProducts ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ route($routeIndex, array_merge(request()->except(['status', 'page']), ['status' => 'takedown'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ ($status ?? '') === 'takedown' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Takedown ({{ number_format($takedownProducts ?? 0, 0, ',', '.') }})
                </a>
            </div>

            <form action="{{ route($routeIndex) }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
                @if(!empty($status))
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="relative flex-1 lg:w-72">
                    <input type="text" name="search" value="{{ $search }}" class="w-full h-9 pl-9 pr-3 text-xs rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400" placeholder="Cari nama produk atau toko...">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <button type="submit" class="h-9 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs cursor-pointer">Cari</button>
                @if($search || $status)
                    <a href="{{ route($routeIndex) }}" class="h-9 px-3 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors border border-slate-200">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">Produk</th>
                        <th class="px-5 py-3.5">Harga &amp; Stok</th>
                        <th class="px-5 py-3.5">Merchant / Toko</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ !$product->is_active ? 'bg-rose-50/30' : '' }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                    <img src="{{ $product->image_url ?? asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate max-w-[220px]">{{ $product->name }}</p>
                                    <span class="text-[11px] text-slate-400">ID: #{{ $product->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Stok: {{ $product->stock }} pcs</p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Toko') }}&background=0f172a&color=fff&size=50" class="w-6 h-6 rounded-full object-cover shrink-0 border border-slate-200" alt="Store">
                                <span class="text-slate-800 font-medium truncate max-w-[150px]">{{ $product->store->name ?? 'Toko' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Takedown
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.products.toggle_status', $product) : route('admin.products.toggle_status', $product) }}" method="POST">
                                    @csrf
                                    @if($product->is_active)
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg transition-colors border border-rose-200 text-xs font-semibold flex items-center gap-1.5 cursor-pointer" title="Nonaktifkan (Takedown)" onclick="return confirm('Anda yakin ingin memblokir/takedown produk ini?')">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                            </svg>
                                            <span>Takedown</span>
                                        </button>
                                    @else
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 text-xs font-semibold flex items-center gap-1.5 cursor-pointer" title="Aktifkan Kembali" onclick="return confirm('Anda yakin ingin mengaktifkan kembali produk ini?')">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            <span>Aktifkan</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-slate-700 block">Tidak Ada Produk Ditemukan</span>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Coba sesuaikan kata kunci pencarian atau filter status moderasi produk.</p>
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
