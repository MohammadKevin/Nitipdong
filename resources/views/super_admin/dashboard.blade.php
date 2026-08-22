<x-super-admin-layout>
    <x-slot name="title">
        Super Admin Dashboard - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                Ringkasan Metrik & Keuntungan Platform
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring perputaran transaksi dan pendapatan komisi 5% marketplace.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('super_admin.withdrawals.index') }}" class="btn-secondary text-xs h-9 px-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 font-semibold flex items-center gap-1.5 text-slate-700 transition-colors">
                <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i>
                <span>Kelola Payout</span>
            </a>
            <a href="{{ route('super_admin.reports.revenue.export') }}" class="btn-primary text-xs h-9 px-3.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-1.5 shadow-xs transition-colors">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Ekspor Laporan</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-card flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-200">
                    Keuntungan Platform (5%)
                </span>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                    <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Laba Bersih
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-cyan-800">Rp {{ number_format($totalKeuntunganPlatform, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">5% komisi dari seluruh transaksi sukses</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-card flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Gross Volume (GMV)</span>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                    Total Penjualan
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900">Rp {{ number_format($grossVolume, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Total transaksi berhasil marketplace</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-card flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Pesanan Baru</span>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-200">
                    <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Bulan Ini
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900">{{ number_format($pesananBaru, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ number_format($activeOrders, 0, ',', '.') }} pesanan sedang berjalan</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-card flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Pengguna</span>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                    Terverifikasi
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900">{{ number_format($totalPengguna, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $totalToko }} toko resmi terdaftar</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-xl border border-slate-200/80 shadow-card flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Tren Keuntungan Platform (Komisi 5%)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pendapatan laba bersih platform 7 hari terakhir</p>
                </div>
                <span class="text-xs font-semibold text-cyan-700 bg-cyan-50 px-2.5 py-1 rounded border border-cyan-200">
                    Update Real-Time
                </span>
            </div>

            <div class="h-64 w-full flex items-end justify-between gap-3 pt-6 px-2">
                @php
                    $dummyHeights = [45, 60, 52, 78, 65, 90, 84];
                    $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                @endphp
                @foreach($dummyHeights as $idx => $height)
                    <div class="flex-1 flex flex-col items-center gap-2 group">
                        <div class="w-full bg-cyan-100 group-hover:bg-cyan-600 rounded-t transition-all duration-300 relative" style="height: {{ $height }}%">
                            <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded font-mono pointer-events-none transition-opacity whitespace-nowrap">
                                Rp {{ number_format($height * 2500, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">{{ $days[$idx] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-xl border border-slate-200/80 shadow-card flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-100 mb-3">
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Komposisi Pengguna</h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribusi peran di NitipDong</p>
            </div>

            <div class="space-y-4 py-2">
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700">Customer Aktif</span>
                        <span class="text-slate-900">85%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-cyan-600 h-full rounded-full" style="width: 85%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700">Official Merchant / Seller</span>
                        <span class="text-slate-900">12%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: 12%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700">Admin & Tim Operasional</span>
                        <span class="text-slate-900">3%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-purple-500 h-full rounded-full" style="width: 3%"></div>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 text-xs text-slate-600">
                Platform menambahkan komisi 5% secara otomatis pada setiap produk untuk mendukung infrastruktur operasional.
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Aktivitas Transaksi Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar invoice pesanan yang masuk ke platform</p>
            </div>
            <a href="{{ route('super_admin.stores.index') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800">
                Kelola Toko &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Invoice</th>
                        <th class="px-5 py-3.5">Merchant / Toko</th>
                        <th class="px-5 py-3.5">Total Belanja</th>
                        <th class="px-5 py-3.5">Komisi Platform (5%)</th>
                        <th class="px-5 py-3.5">Status Pesanan</th>
                        <th class="px-5 py-3.5">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5 font-mono font-bold text-slate-900">#{{ $order->invoice_number }}</td>
                        <td class="px-5 py-3.5 font-medium text-slate-800">{{ $order->store->name ?? 'NitipDong Store' }}</td>
                        <td class="px-5 py-3.5 font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 font-bold text-cyan-800">Rp {{ number_format(round($order->total_amount * 0.05), 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5">
                            @if($order->status === 'pending')
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold text-[10px] border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-amber-500"></span> Pending
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="px-2.5 py-0.5 rounded-full bg-cyan-50 text-cyan-800 font-semibold text-[10px] border border-cyan-200 inline-flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-cyan-600"></span> Diproses
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-semibold text-[10px] border border-purple-200 inline-flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-purple-600"></span> Dikirim
                                </span>
                            @elseif($order->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold text-[10px] border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-emerald-600"></span> Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-semibold text-[10px] border border-rose-200 inline-flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-rose-600"></span> Batal
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-400">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada data transaksi pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-super-admin-layout>