<x-seller-layout>
    <x-slot name="title">
        Dashboard Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    @php
        $pendingCount = isset($orders) ? $orders->where('status', 'pending')->count() : 0;
        $processingCount = isset($orders) ? $orders->where('status', 'processing')->count() : 0;
        $totalIncome = isset($orders) ? $orders->where('status', 'completed')->sum('total_amount') : 0;
    @endphp

    <div class="bg-slate-950 rounded-xl p-6 text-white relative overflow-hidden shadow-card border border-slate-800">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 relative z-10">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-lg bg-cyan-950 border border-cyan-400/30 flex items-center justify-center text-xl text-cyan-400 shrink-0">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">
                        Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                    </h1>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Selamat datang di panel kelola toko <span class="font-bold text-cyan-400">{{ $store->name ?? 'Toko Saya' }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('seller.products.create') }}" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    Tambah Produk
                </a>
                <a href="{{ route('seller.orders.index') }}" class="btn-secondary text-xs h-9 px-3.5 rounded-md bg-white/10 text-white hover:bg-white/20 border-white/10 flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt text-[10px]"></i>
                    Pesanan ({{ $pendingCount }})
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg border border-cyan-200 shrink-0">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Produk</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $products->count() }} Listing</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-lg border border-blue-200 shrink-0">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Pesanan</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ isset($orders) ? $orders->count() : 0 }} Transaksi</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-lg border border-amber-200 shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Perlu Diproses</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $pendingCount }} Invoice</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg border border-emerald-200 shrink-0">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Pendapatan</p>
                <h4 class="text-base font-extrabold text-slate-900 mt-0.5 truncate">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <a href="{{ route('seller.products.create') }}" class="bg-white p-3.5 rounded-xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm border border-cyan-200 shrink-0">
                <i class="fa-solid fa-plus"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">Tambah Produk</p>
                <p class="text-[10px] text-slate-400">Listing baru</p>
            </div>
        </a>

        <a href="{{ route('seller.products.index') }}" class="bg-white p-3.5 rounded-xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-slate-50 text-slate-700 flex items-center justify-center text-sm border border-slate-200 shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">Katalog Toko</p>
                <p class="text-[10px] text-slate-400">Stok & harga</p>
            </div>
        </a>

        <a href="{{ route('seller.orders.index') }}" class="bg-white p-3.5 rounded-xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-amber-50 text-amber-700 flex items-center justify-center text-sm border border-amber-200 shrink-0">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">Pesanan Masuk</p>
                <p class="text-[10px] text-slate-400">Kirim produk</p>
            </div>
        </a>

        <a href="{{ route('seller.vouchers.index') }}" class="bg-white p-3.5 rounded-xl border border-slate-200/80 hover:border-cyan-600 shadow-card hover:shadow-card-hover transition-all group flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-purple-50 text-purple-700 flex items-center justify-center text-sm border border-purple-200 shrink-0">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">Voucher Promo</p>
                <p class="text-[10px] text-slate-400">Diskon toko</p>
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
                                            <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover" alt="{{ $prod->name }}">
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