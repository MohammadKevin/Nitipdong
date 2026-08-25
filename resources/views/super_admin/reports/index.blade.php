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
            <!-- Direct PDF Download Button (No new tab) -->
            <button type="button" id="btn-download-pdf" onclick="downloadReportPDF()"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs border border-slate-200 shadow-xs hover:border-slate-300 transition-all cursor-pointer" title="Langsung unduh dokumen laporan sebagai file PDF">
                <i class="fa-solid fa-file-pdf text-rose-600 text-sm" id="pdf-icon"></i>
                <span id="pdf-btn-text">Unduh PDF</span>
            </button>

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

    <!-- HIDDEN CONTAINER FOR HIGH-RES CLIENT-SIDE PDF GENERATION -->
    <div style="display: none;" id="pdf-export-content">
        <div style="padding: 24px; font-family: Arial, Helvetica, sans-serif; color: #0f172a; background: #ffffff;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f172a; padding-bottom: 14px; margin-bottom: 16px;">
                <div>
                    <h2 style="font-size: 22px; font-weight: 800; margin: 0; color: #0f172a; letter-spacing: -0.5px;">NITIPDONG PLATFORM</h2>
                    <p style="font-size: 11px; color: #64748b; margin: 3px 0 0 0;">Enterprise Marketplace &amp; Multi-Vendor E-Commerce Platform</p>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; padding: 3px 10px; background: #f1f5f9; font-size: 9.5px; font-weight: 700; text-transform: uppercase; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 4px; color: #334155;">DOKUMEN RESMI</span>
                    <p style="font-size: 10px; color: #64748b; margin: 0;">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <!-- Title & Period -->
            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 16px; font-size: 11px;">
                <div>
                    <strong>Laporan: </strong><span style="color: #0f172a;">Rekapitulasi Penjualan &amp; Pembagian Komisi Platform (15%)</span>
                </div>
                <div>
                    <strong>Periode: </strong><span style="color: #1d4ed8; font-weight: 700;">{{ $periodText ?? 'Semua Waktu Historis' }}</span>
                </div>
            </div>

            <!-- 4 KPI Boxes -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 16px;">
                <div style="padding: 10px 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <span style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block;">Gross Volume (GMV)</span>
                    <span style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 2px; display: block;">Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}</span>
                </div>
                <div style="padding: 10px 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px;">
                    <span style="font-size: 9px; font-weight: 700; color: #065f46; text-transform: uppercase; display: block;">Laba Komisi Platform (15%)</span>
                    <span style="font-size: 14px; font-weight: 800; color: #065f46; margin-top: 2px; display: block;">Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}</span>
                </div>
                <div style="padding: 10px 12px; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 6px;">
                    <span style="font-size: 9px; font-weight: 700; color: #6b21a8; text-transform: uppercase; display: block;">Hak Penjual (85%)</span>
                    <span style="font-size: 14px; font-weight: 800; color: #6b21a8; margin-top: 2px; display: block;">Rp {{ number_format($totalSellerEarnings, 0, ',', '.') }}</span>
                </div>
                <div style="padding: 10px 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <span style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block;">Total Transaksi</span>
                    <span style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 2px; display: block;">{{ number_format($totalOrdersCount, 0, ',', '.') }} Pesanan</span>
                </div>
            </div>

            <!-- Full Data Table with Structured Grid -->
            <table style="width: 100%; border-collapse: collapse; font-size: 10px; border: 1px solid #cbd5e1; margin-bottom: 16px;">
                <thead>
                    <tr style="background: #0f172a; color: #ffffff;">
                        <th style="padding: 7px 8px; text-align: center; width: 35px; border: 1px solid #1e293b; font-weight: 700;">No</th>
                        <th style="padding: 7px 8px; text-align: left; width: 140px; border: 1px solid #1e293b; font-weight: 700;">No Invoice &amp; Waktu</th>
                        <th style="padding: 7px 8px; text-align: left; width: 140px; border: 1px solid #1e293b; font-weight: 700;">Merchant / Toko</th>
                        <th style="padding: 7px 8px; text-align: left; width: 130px; border: 1px solid #1e293b; font-weight: 700;">Pembeli</th>
                        <th style="padding: 7px 8px; text-align: left; border: 1px solid #1e293b; font-weight: 700;">Item Produk Terjual</th>
                        <th style="padding: 7px 8px; text-align: right; width: 115px; border: 1px solid #1e293b; font-weight: 700;">Nilai GMV (Rp)</th>
                        <th style="padding: 7px 8px; text-align: right; width: 110px; border: 1px solid #1e293b; font-weight: 700;">Komisi 15% (Rp)</th>
                        <th style="padding: 7px 8px; text-align: right; width: 110px; border: 1px solid #1e293b; font-weight: 700;">Hak Toko 85% (Rp)</th>
                        <th style="padding: 7px 8px; text-align: center; width: 75px; border: 1px solid #1e293b; font-weight: 700;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allOrders as $idx => $order)
                    @php
                        $fee = round($order->total_amount * 0.15);
                        $sellerNet = $order->total_amount - $fee;
                        $itemsSummary = $order->orderItems ? $order->orderItems->map(fn($it) => ($it->product->name ?? 'Item') . " ({$it->quantity}x)")->join(', ') : '-';
                    @endphp
                    <tr style="{{ $idx % 2 == 1 ? 'background: #f8fafc;' : 'background: #ffffff;' }}">
                        <td style="padding: 6px 8px; text-align: center; border: 1px solid #cbd5e1;">{{ $idx + 1 }}</td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; font-weight: 700;">
                            {{ $order->invoice_number }}<br>
                            <span style="font-size: 9px; font-weight: 400; color: #64748b;">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">{{ $order->store->name ?? '-' }}</td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">{{ $order->user->name ?? '-' }}</td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9.5px; color: #475569;">{{ $itemsSummary }}</td>
                        <td style="padding: 6px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: 700;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td style="padding: 6px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: 700; color: #065f46; background: #ecfdf5;">{{ number_format($fee, 0, ',', '.') }}</td>
                        <td style="padding: 6px 8px; text-align: right; border: 1px solid #cbd5e1;">{{ number_format($sellerNet, 0, ',', '.') }}</td>
                        <td style="padding: 6px 8px; text-align: center; border: 1px solid #cbd5e1; font-weight: 700; font-size: 9px;">{{ strtoupper($order->status ?? '-') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td style="padding: 16px 8px; text-align: center; border: 1px solid #cbd5e1; color: #94a3b8;">-</td>
                        <td style="padding: 16px 8px; text-align: center; border: 1px solid #cbd5e1; color: #94a3b8;" colspan="4">
                            <strong style="color: #475569; font-size: 11px;">Belum ada data transaksi pada periode ini</strong><br>
                            <span style="font-size: 9px; color: #94a3b8;">Seluruh rekapitulasi data penjualan dan komisi akan tercatat otomatis di tabel ini.</span>
                        </td>
                        <td style="padding: 16px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: 700; color: #94a3b8;">Rp 0</td>
                        <td style="padding: 16px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: 700; color: #065f46; background: #ecfdf5;">Rp 0</td>
                        <td style="padding: 16px 8px; text-align: right; border: 1px solid #cbd5e1; color: #94a3b8;">Rp 0</td>
                        <td style="padding: 16px 8px; text-align: center; border: 1px solid #cbd5e1; color: #94a3b8; font-weight: 700; font-size: 9px;">NIHIL</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: 800;">
                        <td colspan="5" style="padding: 8px 10px; text-align: right; border: 1px solid #cbd5e1; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL KESELURUHAN LAPORAN:</td>
                        <td style="padding: 8px 10px; text-align: right; border: 1px solid #cbd5e1;">Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}</td>
                        <td style="padding: 8px 10px; text-align: right; border: 1px solid #cbd5e1; color: #065f46;">Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}</td>
                        <td style="padding: 8px 10px; text-align: right; border: 1px solid #cbd5e1;">Rp {{ number_format($totalSellerEarnings, 0, ',', '.') }}</td>
                        <td style="border: 1px solid #cbd5e1;"></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Verification Footer -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid #cbd5e1; padding-top: 12px; font-size: 9.5px; color: #64748b;">
                <div>
                    <p style="margin: 0;">Laporan keuangan resmi di-generate oleh sistem analitik NitipDong Platform v2.4</p>
                    <p style="margin: 2px 0 0 0; font-family: monospace;">Kode Verifikasi: {{ strtoupper(substr(md5(now() . $totalGrossRevenue), 0, 16)) }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-weight: 700; color: #0f172a;">Otoritas Super Administrator NitipDong</p>
                    <p style="margin: 2px 0 0 0;">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        async function downloadReportPDF() {
            const btn = document.getElementById('btn-download-pdf');
            const btnText = document.getElementById('pdf-btn-text');
            const icon = document.getElementById('pdf-icon');
            const originalText = btnText.innerText;

            btnText.innerText = 'Mengunduh PDF...';
            icon.className = 'fa-solid fa-circle-notch fa-spin text-rose-600 text-sm';
            btn.disabled = true;

            const element = document.getElementById('pdf-export-content');
            const periodName = '{{ $period ?? "periode" }}';
            const todayStr = new Date().toISOString().slice(0, 10);
            const fileName = 'Laporan_Keuangan_NitipDong_' + periodName + '_' + todayStr + '.pdf';

            // Clone element to visible screen area for html2canvas pixel capture
            const clone = element.cloneNode(true);
            clone.id = 'pdf-export-clone';
            clone.style.display = 'block';
            clone.style.position = 'fixed';
            clone.style.top = '0px';
            clone.style.left = '0px';
            clone.style.width = '1080px';
            clone.style.backgroundColor = '#ffffff';
            clone.style.zIndex = '999999';
            clone.style.opacity = '1';
            clone.style.pointerEvents = 'none';
            document.body.appendChild(clone);

            const opt = {
                margin:       [6, 6, 6, 6],
                filename:     fileName,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { 
                    scale: 2, 
                    useCORS: true, 
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: 1200
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            try {
                await html2pdf().set(opt).from(clone).save();
                btnText.innerText = 'Selesai!';
                icon.className = 'fa-solid fa-circle-check text-emerald-600 text-sm';
            } catch (err) {
                console.error(err);
                btnText.innerText = 'Gagal';
                icon.className = 'fa-solid fa-triangle-exclamation text-rose-600 text-sm';
            } finally {
                if (document.body.contains(clone)) {
                    document.body.removeChild(clone);
                }
                setTimeout(() => {
                    btnText.innerText = originalText;
                    icon.className = 'fa-solid fa-file-pdf text-rose-600 text-sm';
                    btn.disabled = false;
                }, 2000);
            }
        }
    </script>
    @endpush
</x-super-admin-layout>
