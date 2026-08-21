<x-seller-layout>
    <x-slot name="title">
        Dashboard Toko - {{ config('app.name', 'SakserShop') }}
    </x-slot>

    @php
        $pendingCount = isset($orders) ? $orders->where('status', 'pending')->count() : 0;
        $processingCount = isset($orders) ? $orders->where('status', 'processing')->count() : 0;
        $totalIncome = isset($orders) ? $orders->where('status', 'completed')->sum('total_amount') : 0;
    @endphp

    {{-- Header Banner Toko (Professional E-Commerce Seller Center Style) --}}
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-card border border-slate-200/80 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-cyan-200 shadow-2xs">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[9px] text-white shadow-2xs" title="Toko Aktif">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            Halo, {{ Auth::user()->name }}!
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Toko Aktif & Resmi
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                        Kelola katalog, pantau pengiriman pesanan, dan kembangkan bisnis toko <strong class="text-slate-800">{{ $store->name ?? 'Toko Saya' }}</strong> Anda.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('seller.products.create') }}" class="btn-primary text-xs h-10 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-2 shadow-xs transition-all">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>Tambah Produk</span>
                </a>
                <a href="{{ route('seller.orders.index') }}" class="btn-secondary text-xs h-10 px-4 rounded-xl border border-slate-200 hover:border-slate-300 font-semibold flex items-center gap-2 text-slate-700 transition-all">
                    <i class="fa-solid fa-box text-[11px] text-slate-500"></i>
                    <span>Pesanan Masuk</span>
                    @if($pendingCount > 0)
                        <span class="px-1.5 py-0.2 bg-rose-500 text-white text-[10px] font-bold rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ url('/') }}" target="_blank" class="p-2.5 text-slate-400 hover:text-cyan-700 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all" title="Buka Halaman Depan">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4 hover:shadow-card-hover transition-all">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg border border-cyan-200 shrink-0">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Produk</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $products->count() }} Listing</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4 hover:shadow-card-hover transition-all">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg border border-blue-200 shrink-0">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Pesanan</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ isset($orders) ? $orders->count() : 0 }} Transaksi</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4 hover:shadow-card-hover transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg border border-amber-200 shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Perlu Diproses</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $pendingCount }} Invoice</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4 hover:shadow-card-hover transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg border border-emerald-200 shrink-0">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Pendapatan</p>
                <h4 class="text-base font-extrabold text-slate-900 mt-0.5 truncate">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    {{-- Store Location & Shipping Origin Overview Card --}}
    <div class="bg-gradient-to-r from-cyan-950 via-slate-900 to-slate-950 text-white rounded-2xl p-5 sm:p-6 shadow-card border border-cyan-500/20 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/20 border border-cyan-400/30 text-cyan-300 flex items-center justify-center text-xl shrink-0 shadow-inner">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="font-extrabold text-sm text-white">Alamat & Lokasi Asal Gudang Toko</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <i class="fa-solid fa-truck-fast"></i> Gratis Ongkir 1 Kota
                    </span>
                </div>
                <p class="text-xs text-slate-300 mt-1 flex items-center gap-1.5 flex-wrap">
                    <span>Kota Pengiriman: <strong class="text-cyan-300 font-bold">{{ $store->city ?: ($store->effective_city ?: 'Jakarta Pusat') }}</strong></span>
                    <span class="text-slate-500">•</span>
                    <span class="text-slate-400 truncate max-w-md">{{ $store->address ?: 'Alamat belum diatur lengkap' }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('seller.settings.edit') }}" class="btn-primary text-xs h-10 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold flex items-center justify-center gap-2 shadow-xs shrink-0 cursor-pointer">
            <i class="fa-solid fa-pen-to-square text-xs"></i>
            <span>Ubah Alamat & Pinpoint Toko</span>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <a href="{{ route('seller.products.create') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm border border-cyan-200 shrink-0">
                <i class="fa-solid fa-plus"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors whitespace-normal">Tambah Produk</p>
                <p class="text-[10px] text-slate-400">Listing baru</p>
            </div>
        </a>

        <a href="{{ route('seller.products.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center text-sm border border-slate-200 shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors whitespace-normal">Katalog Toko</p>
                <p class="text-[10px] text-slate-400">Stok & harga</p>
            </div>
        </a>

        <a href="{{ route('seller.orders.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm border border-amber-200 shrink-0">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors whitespace-normal">Pesanan Masuk</p>
                <p class="text-[10px] text-slate-400">Kirim produk</p>
            </div>
        </a>

        <a href="{{ route('seller.settings.edit') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm border border-cyan-200 shrink-0">
                <i class="fa-solid fa-shop"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors whitespace-normal">Pengaturan Toko</p>
                <p class="text-[10px] text-slate-400">Alamat & profil</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Produk Terbaru</h3>
                <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-cyan-700 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3 text-center">Stok</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products->take(5) as $prod)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                        @if($prod->image)
                                            <img src="{{ $prod->image_url }}" class="w-full h-full object-cover" alt="{{ $prod->name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('product.show', $prod) }}" target="_blank" class="font-bold text-slate-900 truncate block max-w-[150px] text-xs hover:text-cyan-700">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">{{ $prod->category->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $prod->stock > 5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $prod->stock }} Pcs
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('seller.products.edit', $prod) }}" class="p-1 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded border border-slate-200 inline-block" title="Edit">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                Belum ada produk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Pesanan Terbaru</h3>
                <a href="{{ route('seller.orders.index') }}" class="text-xs font-semibold text-cyan-700 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if(isset($orders) && $orders->count() > 0)
                            @forelse($orders->take(5) as $ord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 text-xs">
                                    #{{ $ord->invoice_number }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-800 block truncate max-w-[120px]">{{ $ord->user->name ?? 'Pembeli' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $ord->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusStyles = [
                                            'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'processing' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                            'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusStyles[$ord->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($ord->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">
                                    Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada pesanan masuk
                                </td>
                            </tr>
                            @endforelse
                        @else
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                Belum ada pesanan masuk
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-seller-layout>
