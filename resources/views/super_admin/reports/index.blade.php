<x-super-admin-layout>
    <x-slot name="title">
        Laporan Keuangan & Ekspor Transaksi - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Laporan Keuangan & Ekspor Platform
    </x-slot>

    <!-- HEADER / ACTION BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Laporan Keuangan & Ekspor Transaksi
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Analisis pendapatan komisi 15%, volume GMV, dan pembagian hasil penjualan seluruh merchant marketplace.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Print / PDF Button -->
            <a href="{{ route('super_admin.reports.print', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs border border-slate-200 shadow-xs hover:border-slate-300 transition-all cursor-pointer" title="Cetak langsung atau simpan sebagai dokumen PDF">
                <i class="fa-solid fa-file-pdf text-rose-600 text-sm"></i>
                <span>Cetak / PDF</span>
            </a>

            <!-- Download Formatted Excel .xls Button -->
            <a href="{{ route('super_admin.reports.revenue.export', array_merge(request()->query(), ['format' => 'excel'])) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs hover:shadow transition-all cursor-pointer" title="Format tabel Microsoft Excel berkolom rapi">
                <i class="fa-solid fa-file-excel text-white text-sm"></i>
                <span>Unduh Excel (.xls)</span>
            </a>
        </div>
    </div>

    <!-- FILTER & CONTROL PANEL -->
    <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs p-4 sm:p-5 space-y-4" x-data="{ customDate: {{ $period === 'custom' || ($startDate && $endDate && !in_array($period, ['today', '7days', 'this_month', 'last_month', 'this_year', 'all'])) ? 'true' : 'false' }} }">
        
        <!-- Preset Period Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider font-mono-num flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-days text-blue-600"></i> Pilih Periode Cepat:
            </span>
            
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 font-mono-num text-xs">
                <a href="{{ route('super_admin.reports.index', array_merge(request()->except(['period', 'start_date', 'end_date', 'page']), ['period' => 'today'])) }}"
                   class="px-2.5 py-1 rounded-md transition-colors font-semibold shrink-0 {{ $period === 'today' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-slate-50 border border-slate-200' }}">
                    Hari Ini
                </a>
                <a href="{{ route('super_admin.reports.index', array_merge(request()->except(['period', 'start_date', 'end_date', 'page']), ['period' => '7days'])) }}"
                   class="px-2.5 py-1 rounded-md transition-colors font-semibold shrink-0 {{ $period === '7days' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-slate-50 border border-slate-200' }}">
                    7 Hari
                </a>
                <a href="{{ route('super_admin.reports.index', array_merge(request()->except(['period', 'start_date', 'end_date', 'page']), ['period' => 'this_month'])) }}"
                   class="px-2.5 py-1 rounded-md transition-colors font-semibold shrink-0 {{ $period === 'this_month' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-slate-50 border border-slate-200' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('super_admin.reports.index', array_merge(request()->except(['period', 'start_date', 'end_date', 'page']), ['period' => 'last_month'])) }}"
                   class="px-2.5 py-1 rounded-md transition-colors font-semibold shrink-0 {{ $period === 'last_month' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-slate-50 border border-slate-200' }}">
                    Bulan Lalu
                </a>
                <a href="{{ route('super_admin.reports.index', array_merge(request()->except(['period', 'start_date', 'end_date', 'page']), ['period' => 'this_year'])) }}"
                   class="px-2.5 py-1 rounded-md transition-colors font-semibold shrink-0 {{ $period === 'this_year' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-slate-50 border border-slate-200' }}">
                    Tahun Ini
                </a>
                <a href="{{ route('super_admin.reports.index', array_merge(request()->except(['period', 'start_date', 'end_date', 'page']), ['period' => 'all'])) }}"
                   class="px-2.5 py-1 rounded-md transition-colors font-semibold shrink-0 {{ $period === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-slate-50 border border-slate-200' }}">
                    Semua Waktu
                </a>
            </div>
        </div>

        <!-- Detailed Filter Form -->
        <form action="{{ route('super_admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <input type="hidden" name="period" value="custom">

            <!-- Start Date -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1 uppercase text-[10px] tracking-wider font-mono-num">
                    Dari Tanggal
                </label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full h-8.5 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- End Date -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1 uppercase text-[10px] tracking-wider font-mono-num">
                    Sampai Tanggal
                </label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full h-8.5 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Store Filter -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1 uppercase text-[10px] tracking-wider font-mono-num">
                    Filter Toko
                </label>
                <select name="store_id" class="w-full h-8.5 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">-- Semua Merchant --</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ (string)$storeId === (string)$st->id ? 'selected' : '' }}>
                            {{ $st->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1 uppercase text-[10px] tracking-wider font-mono-num">
                    Status Transaksi
                </label>
                <select name="status" class="w-full h-8.5 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Diproses Toko</option>
                    <option value="shipped" {{ $status === 'shipped' ? 'selected' : '' }}>Sedang Dikirim</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>-- Semua Status --</option>
                </select>
            </div>

            <!-- Search & Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 h-8.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('super_admin.reports.index') }}" class="h-8.5 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs flex items-center justify-center border border-slate-200 transition-colors" title="Reset Filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 4 SUMMARY CARDS FOR FILTERED DATA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- Total GMV -->
        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Gross Volume (GMV)</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate" title="Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}">
                    Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <!-- Komisi Platform 15% -->
        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Laba Komisi Platform (15%)</p>
                <h4 class="text-lg sm:text-xl font-bold text-emerald-700 mt-0.5 font-mono-num truncate" title="Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}">
                    Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <!-- Hak Penjual 85% -->
        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 border border-purple-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Hak Penjual (85%)</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate" title="Rp {{ number_format($totalSellerEarnings, 0, ',', '.') }}">
                    Rp {{ number_format($totalSellerEarnings, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Transaksi Terdata</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">
                    {{ number_format($totalOrdersCount, 0, ',', '.') }} Pesanan
                </h4>
            </div>
        </div>

    </div>

    <!-- PREVIEW DATA TABLE -->
    <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider font-mono-num">
                    Tabel Rekapitulasi Penjualan &amp; Bagi Hasil
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if($startDate && $endDate)
                        Periode: <strong class="text-slate-700 font-mono-num">{{ Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s/d <strong class="text-slate-700 font-mono-num">{{ Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
                    @else
                        Periode: <strong class="text-slate-700">Semua Data Historis</strong>
                    @endif
                </p>
            </div>
            
            <span class="text-xs font-semibold text-slate-700 font-mono-num bg-white px-2.5 py-1 rounded-md border border-slate-200 shrink-0">
                {{ number_format($totalOrdersCount, 0, ',', '.') }} Data Ditemukan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No Invoice & Waktu</th>
                        <th class="px-4 py-3 font-semibold">Merchant / Toko</th>
                        <th class="px-4 py-3 font-semibold">Pembeli</th>
                        <th class="px-4 py-3 font-semibold">Item Belanja</th>
                        <th class="px-4 py-3 font-semibold text-right">Nilai GMV (Rp)</th>
                        <th class="px-4 py-3 font-semibold text-right">Komisi 15% (Rp)</th>
                        <th class="px-4 py-3 font-semibold text-right">Bagian Toko 85% (Rp)</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    @php
                        $fee = round($order->total_amount * 0.15);
                        $sellerNet = $order->total_amount - $fee;
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="font-mono-num font-bold text-slate-900 text-xs block">{{ $order->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400 font-mono-num">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-semibold text-slate-900 block truncate max-w-[140px]">{{ $order->store->name ?? '-' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono-num truncate max-w-[140px] block">{{ $order->store->user->email ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-slate-700 font-medium block truncate max-w-[120px]">{{ $order->user->name ?? '-' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono-num truncate max-w-[120px] block">{{ $order->user->phone ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="text-[11px] text-slate-600 max-w-[180px]">
                                @foreach($order->orderItems->take(2) as $item)
                                    <p class="truncate">&bull; {{ $item->product->name ?? 'Item' }} <span class="text-slate-400 font-mono-num">({{ $item->quantity }}x)</span></p>
                                @endforeach
                                @if($order->orderItems->count() > 2)
                                    <span class="text-[10px] text-blue-600 font-semibold font-mono-num">+{{ $order->orderItems->count() - 2 }} produk lainnya</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono-num font-bold text-slate-900">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono-num font-bold text-emerald-700">
                            Rp {{ number_format($fee, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono-num text-slate-700">
                            Rp {{ number_format($sellerNet, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($order->status === 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono-num">
                                    Selesai
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 font-mono-num">
                                    Dikirim
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 font-mono-num">
                                    Diproses
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 font-mono-num">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-file-invoice-dollar text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada data transaksi yang sesuai dengan filter periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if($orders->count() > 0)
                <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900 font-mono-num text-xs">
                    <tr>
                        <td colspan="4" class="px-4 py-3 uppercase text-[10px] tracking-wider text-slate-600">
                            Subtotal Halaman Ini:
                        </td>
                        <td class="px-4 py-3 text-right text-slate-900 font-bold">
                            Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-emerald-700 font-bold">
                            Rp {{ number_format(round($orders->sum('total_amount') * 0.15), 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-slate-800 font-bold">
                            Rp {{ number_format($orders->sum('total_amount') - round($orders->sum('total_amount') * 0.15), 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($orders->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</x-super-admin-layout>
