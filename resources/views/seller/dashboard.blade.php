<x-seller-layout>
    <x-slot name="title">
        Dashboard Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    @php
        $pendingCount = isset($orders) ? $orders->where('status', 'pending')->count() : 0;
        $processingCount = isset($orders) ? $orders->where('status', 'processing')->count() : 0;
        $totalIncome = isset($orders) ? $orders->where('status', 'completed')->sum('total_amount') : 0;
    @endphp

    <div class="bg-gradient-to-r from-[#152238] to-[#1E293B] rounded-2xl p-6 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-6 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-2xl text-emerald-400 shrink-0">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight font-display">
                        Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                    </h1>
                    <p class="text-xs text-slate-300 mt-1">
                        Selamat datang di panel kelola toko <span class="font-semibold text-emerald-400">{{ $store->name ?? 'Toko Saya' }}</span>.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('seller.products.create') }}" class="px-4 py-2.5 bg-[#12A57F] hover:bg-[#0f8b6a] text-white rounded-xl text-xs font-semibold shadow-md shadow-[#12A57F]/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Produk
                </a>
                <a href="{{ route('seller.orders.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-2 border border-white/10">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Pesanan ({{ $pendingCount }})
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-[#12A57F] flex items-center justify-center text-xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Produk</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $products->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Pesanan</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ isset($orders) ? $orders->count() : 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Perlu Diproses</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $pendingCount }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Pendapatan</p>
                    <h4 class="text-lg font-bold text-slate-800 font-display mt-0.5">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('seller.products.create') }}" class="bg-white p-4 rounded-2xl border border-[#EFEBDF] hover:border-[#12A57F] hover:shadow-sm transition-all group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#12A57F] flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-plus"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800 group-hover:text-[#12A57F] transition-colors">Tambah Produk</p>
                <p class="text-[10px] text-slate-400">Buat listing baru</p>
            </div>
        </a>

        <a href="{{ route('seller.products.index') }}" class="bg-white p-4 rounded-2xl border border-[#EFEBDF] hover:border-[#12A57F] hover:shadow-sm transition-all group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800 group-hover:text-[#12A57F] transition-colors">Katalog Toko</p>
                <p class="text-[10px] text-slate-400">Kelola semua produk</p>
            </div>
        </a>

        <a href="{{ route('seller.orders.index') }}" class="bg-white p-4 rounded-2xl border border-[#EFEBDF] hover:border-[#12A57F] hover:shadow-sm transition-all group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800 group-hover:text-[#12A57F] transition-colors">Pesanan Masuk</p>
                <p class="text-[10px] text-slate-400">Update status kirim</p>
            </div>
        </a>

        <a href="{{ route('seller.vouchers.index') }}" class="bg-white p-4 rounded-2xl border border-[#EFEBDF] hover:border-[#12A57F] hover:shadow-sm transition-all group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800 group-hover:text-[#12A57F] transition-colors">Voucher Toko</p>
                <p class="text-[10px] text-slate-400">Kupon promosi</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
            <div class="p-5 border-b border-[#F0EEE6] flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Produk Terbaru</h3>
                <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-[#12A57F] hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                        <tr>
                            <th class="px-5 py-3">Produk</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3 text-center">Stok</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5F3EE]">
                        @forelse($products->take(5) as $prod)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                        @if($prod->image)
                                            <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover" alt="{{ $prod->name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 text-sm">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('product.show', $prod) }}" target="_blank" class="font-bold text-slate-800 truncate block max-w-[180px] text-xs hover:text-[#12A57F]">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">{{ $prod->category->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-800">
                                Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $prod->stock > 5 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $prod->stock }} Pcs
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <a href="{{ route('seller.products.edit', $prod) }}" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors inline-block" title="Edit">
                                    <i class="fa-solid fa-pen text-[11px]"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                                Belum ada produk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
            <div class="p-5 border-b border-[#F0EEE6] flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Pesanan Terbaru</h3>
                <a href="{{ route('seller.orders.index') }}" class="text-xs font-semibold text-[#12A57F] hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                        <tr>
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5F3EE]">
                        @if(isset($orders) && $orders->count() > 0)
                            @forelse($orders->take(5) as $ord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-semibold text-slate-700 text-xs">
                                    {{ $ord->invoice_number }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-medium text-slate-800 block truncate max-w-[120px]">{{ $ord->user->name ?? 'Pembeli' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $ord->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @php
                                        $statusStyles = [
                                            'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusStyles[$ord->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($ord->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-slate-800">
                                    Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                                    Belum ada pesanan masuk
                                </td>
                            </tr>
                            @endforelse
                        @else
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">
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