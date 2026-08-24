<x-super-admin-layout>
    <x-slot name="title">
        Executive Dashboard - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Executive Overview & Platform Analytics
    </x-slot>

    <!-- HERO HEADER & ACTION BAR -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gradient-to-br from-[#0A1128] via-[#0D1F3C] to-[#083344] p-6 sm:p-7 rounded-2xl border border-cyan-500/20 shadow-xl shadow-cyan-950/20 text-white relative overflow-hidden">
        
        <!-- Decorative Ambient Light -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-cyan-500/10 border border-cyan-400/30 text-[11px] font-semibold text-cyan-300 mb-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                <span>Pusat Kendali Keuangan & Operasional</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                Selamat Datang, <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 via-cyan-400 to-white">{{ auth()->user()->name }}</span> 👋
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl font-normal">
                Ringkasan performa penjualan marketplace, laba bersih komisi 5%, dan aktivitas transaksi real-time.
            </p>
        </div>

        <div class="relative z-10 flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('super_admin.withdrawals.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 border border-slate-700/80 text-xs font-semibold flex items-center gap-2 transition-all hover:border-cyan-500/40 shadow-sm">
                <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i>
                <span>Tinjau Payout</span>
                @if($pendingWithdrawalsCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-slate-950 font-extrabold text-[10px] font-mono-num">
                        {{ $pendingWithdrawalsCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('super_admin.reports.revenue.export') }}" 
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-white font-bold text-xs flex items-center gap-2 shadow-lg shadow-cyan-600/30 transition-all">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Ekspor Laporan Keuangan</span>
            </a>
        </div>
    </div>

    <!-- 4 CORE EXECUTIVE METRIC CARDS (CYAN DESIGN SYSTEM) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- CARD 1: HERO KEUNTUNGAN PLATFORM -->
        <div class="relative bg-gradient-to-br from-cyan-950/90 via-cyan-900/90 to-[#0A1128] text-white p-5 rounded-2xl border border-cyan-500/30 shadow-lg shadow-cyan-950/15 flex flex-col justify-between overflow-hidden group hover:border-cyan-400/50 transition-all duration-300">
            <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-cyan-400/10 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
            
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-cyan-300 bg-cyan-950/90 px-2.5 py-1 rounded-lg border border-cyan-700/50 flex items-center gap-1.5">
                        <i class="fa-solid fa-hand-holding-dollar text-[11px] text-cyan-400"></i>
                        Komisi Platform (5%)
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-300 bg-emerald-950/80 px-2 py-0.5 rounded-md border border-emerald-500/30">
                        <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Laba Bersih
                    </span>
                </div>

                <div class="mt-4">
                    <p class="text-[11px] text-cyan-200/80 font-medium">Total Akumulasi Pendapatan</p>
                    <h3 class="text-2xl font-extrabold text-white tracking-tight mt-0.5 font-mono-num">
                        Rp {{ number_format($totalKeuntunganPlatform, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-cyan-800/40 flex items-center justify-between text-[11px]">
                <span class="text-slate-300">Bulan Ini:</span>
                <span class="font-bold text-cyan-300 font-mono-num">Rp {{ number_format($thisMonthProfit, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CARD 2: GROSS MERCHANDISE VOLUME (GMV) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center text-slate-600">
                            <i class="fa-solid fa-coins text-xs text-cyan-600"></i>
                        </div>
                        Gross Volume (GMV)
                    </span>
                    <span class="inline-flex items-center text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                        Total Sukses
                    </span>
                </div>

                <div class="mt-4">
                    <p class="text-[11px] text-slate-400 font-medium">Total Omset Perputaran</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5 font-mono-num">
                        Rp {{ number_format($grossVolume, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Rata-rata Order (AOV):</span>
                <span class="font-bold text-slate-800 font-mono-num">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CARD 3: PESANAN BARU & AKTIF -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-md bg-cyan-50 flex items-center justify-center text-cyan-600">
                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                        </div>
                        Volume Pesanan
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded-md border border-cyan-200">
                        Bulan Ini
                    </span>
                </div>

                <div class="mt-4">
                    <p class="text-[11px] text-slate-400 font-medium">Pesanan Masuk</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5 font-mono-num">
                        {{ number_format($pesananBaru, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Order</span>
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Sedang Berjalan:</span>
                <span class="font-bold text-cyan-700 font-mono-num flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                    {{ number_format($activeOrders, 0, ',', '.') }} Transaksi
                </span>
            </div>
        </div>

        <!-- CARD 4: EKOSISTEM PENGGUNA & TOKO -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-md bg-purple-50 flex items-center justify-center text-purple-600">
                            <i class="fa-solid fa-users-gear text-xs"></i>
                        </div>
                        Ekosistem Platform
                    </span>
                    <span class="inline-flex items-center text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">
                        Terverifikasi
                    </span>
                </div>

                <div class="mt-4">
                    <p class="text-[11px] text-slate-400 font-medium">Total Akun Pengguna</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5 font-mono-num">
                        {{ number_format($totalPengguna, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">User</span>
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Toko Terdaftar:</span>
                <div class="flex items-center gap-1.5">
                    <span class="font-bold text-slate-800 font-mono-num">{{ $totalToko }} Toko</span>
                    @if($pendingStoresCount > 0)
                        <span class="text-[9px] font-bold bg-amber-50 text-amber-700 px-1.5 py-0.2 rounded border border-amber-200 font-mono-num">
                            +{{ $pendingStoresCount }} Review
                        </span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- REVENUE CHART & DISTRIBUTION SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
        
        <!-- MAIN CHART: REAL-TIME REVENUE & GMV (CHART.JS) -->
        <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Tren Pendapatan & Volume Transaksi</h3>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded-full border border-cyan-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                            Live Data
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Analisis pendapatan komisi 5% dan gross merchandise volume secara berkala</p>
                </div>

                <!-- Interactive Period Tabs -->
                <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold" id="chart-period-tabs">
                    <button type="button" onclick="loadChartData('day')" class="period-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all" data-period="day">7 Hari</button>
                    <button type="button" onclick="loadChartData('week')" class="period-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all active bg-white text-cyan-700 shadow-xs" data-period="week">Mingguan</button>
                    <button type="button" onclick="loadChartData('month')" class="period-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all" data-period="month">Bulan Ini</button>
                    <button type="button" onclick="loadChartData('year')" class="period-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all" data-period="year">Tahunan</button>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative w-full h-72 sm:h-80 pt-4">
                <canvas id="revenueChart"></canvas>
                <div id="chart-loader" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center hidden">
                    <div class="flex items-center gap-2 text-cyan-700 text-xs font-semibold">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                        <span>Memuat data analitik...</span>
                    </div>
                </div>
            </div>

            <!-- Chart Footnote / Summary -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-cyan-500"></span>
                        <span class="font-medium text-slate-700">Komisi Platform (5%)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                        <span class="font-medium text-slate-500">Gross Volume (GMV)</span>
                    </div>
                </div>
                <span class="text-[11px] text-slate-400 font-mono-num">Auto-calculated from Completed Invoices</span>
            </div>
        </div>

        <!-- RIGHT SIDE: ORDER STATUS BREAKDOWN -->
        <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Status Transaksi Keseluruhan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribusi status {{ number_format($totalPesanan, 0, ',', '.') }} pesanan</p>
            </div>

            <div class="space-y-3.5 my-auto py-3">
                @php
                    $safeTotal = $totalPesanan > 0 ? $totalPesanan : 1;
                    $compPct = round(($completedOrders / $safeTotal) * 100);
                    $shipPct = round(($shippedOrders / $safeTotal) * 100);
                    $procPct = round(($processingOrders / $safeTotal) * 100);
                    $pendPct = round(($pendingOrders / $safeTotal) * 100);
                    $cancPct = round(($cancelledOrders / $safeTotal) * 100);
                @endphp

                <!-- Selesai -->
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Pesanan Selesai
                        </span>
                        <span class="text-slate-900 font-mono-num">{{ $completedOrders }} ({{ $compPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $compPct }}%"></div>
                    </div>
                </div>

                <!-- Dikirim -->
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Sedang Dikirim
                        </span>
                        <span class="text-slate-900 font-mono-num">{{ $shippedOrders }} ({{ $shipPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-cyan-500 h-full rounded-full transition-all duration-500" style="width: {{ $shipPct }}%"></div>
                    </div>
                </div>

                <!-- Diproses -->
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                            Diproses Toko
                        </span>
                        <span class="text-slate-900 font-mono-num">{{ $processingOrders }} ({{ $procPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-sky-500 h-full rounded-full transition-all duration-500" style="width: {{ $procPct }}%"></div>
                    </div>
                </div>

                <!-- Pending -->
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Menunggu Konfirmasi
                        </span>
                        <span class="text-slate-900 font-mono-num">{{ $pendingOrders }} ({{ $pendPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $pendPct }}%"></div>
                    </div>
                </div>

                <!-- Batal / Ditolak -->
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Dibatalkan / Gagal
                        </span>
                        <span class="text-slate-900 font-mono-num">{{ $cancelledOrders }} ({{ $cancPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-500" style="width: {{ $cancPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Operational Callout -->
            <div class="p-3.5 bg-gradient-to-r from-cyan-50/70 to-slate-50 rounded-xl border border-cyan-100 text-xs text-slate-600 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info text-cyan-600 mt-0.5 text-xs shrink-0"></i>
                <p class="leading-relaxed">
                    Setiap transaksi sukses secara otomatis dipotong komisi <strong class="text-cyan-800">5%</strong> untuk kas platform sebelum dicairkan ke saldo toko mitra.
                </p>
            </div>
        </div>

    </div>

    <!-- RECENT TRANSACTIONS ENTERPRISE LEDGER -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden" x-data="{ tableSearch: '' }">
        
        <!-- Table Header & Filter Bar -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#FAFCFF]">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-sm sm:text-base text-slate-900 tracking-tight">Aktivitas Transaksi Terbaru</h3>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-mono-num font-bold">
                        {{ $recentOrders->count() }} Invoice Terkini
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi pesanan live yang tercatat di sistem platform</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" 
                           x-model="tableSearch" 
                           placeholder="Filter invoice / toko..." 
                           class="w-48 sm:w-64 h-9 pl-8 pr-3 text-xs rounded-xl bg-white border border-slate-200 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-400">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>

                <a href="{{ route('super_admin.stores.index') }}" 
                   class="px-3 py-2 rounded-xl text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 transition-colors shrink-0">
                    Kelola Toko &rarr;
                </a>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Nomor Invoice</th>
                        <th class="px-6 py-4">Merchant / Toko</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4">Total Belanja (GMV)</th>
                        <th class="px-6 py-4">Komisi Platform (5%)</th>
                        <th class="px-6 py-4">Status Pesanan</th>
                        <th class="px-6 py-4">Waktu Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-cyan-50/30 transition-colors" 
                        x-show="!tableSearch || '{{ strtolower($order->invoice_number) }}'.includes(tableSearch.toLowerCase()) || '{{ strtolower($order->store->name ?? '') }}'.includes(tableSearch.toLowerCase()) || '{{ strtolower($order->user->name ?? '') }}'.includes(tableSearch.toLowerCase())">
                        
                        <!-- Invoice ID -->
                        <td class="px-6 py-4 font-mono-num font-bold text-slate-900">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 group-hover:border-cyan-300">
                                #{{ $order->invoice_number }}
                            </span>
                        </td>

                        <!-- Store Name -->
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-cyan-50 border border-cyan-200 flex items-center justify-center text-[11px] text-cyan-700 font-bold shrink-0">
                                    <i class="fa-solid fa-store text-[10px]"></i>
                                </div>
                                <span class="truncate max-w-[140px]">{{ $order->store->name ?? 'NitipDong Store' }}</span>
                            </div>
                        </td>

                        <!-- Buyer Name -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name ?? 'Customer') }}&background=0e7490&color=fff&size=50" 
                                     class="w-6 h-6 rounded-full object-cover shrink-0 border border-slate-200" 
                                     alt="Buyer">
                                <span class="text-slate-700 font-medium truncate max-w-[120px]">{{ $order->user->name ?? 'Customer' }}</span>
                            </div>
                        </td>

                        <!-- GMV Amount -->
                        <td class="px-6 py-4 font-bold text-slate-900 font-mono-num">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>

                        <!-- Platform 5% Cut -->
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 font-bold text-cyan-800 bg-cyan-50 px-2.5 py-1 rounded-lg border border-cyan-200 font-mono-num">
                                <i class="fa-solid fa-plus text-[9px] text-cyan-600"></i>
                                Rp {{ number_format(round($order->total_amount * 0.05), 0, ',', '.') }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
                            @if($order->status === 'pending')
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 font-bold text-[10px] border border-cyan-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-600 animate-pulse"></span> Diproses
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 font-bold text-[10px] border border-sky-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span> Dikirim
                                </span>
                            @elseif($order->status === 'completed')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Batal
                                </span>
                            @endif
                        </td>

                        <!-- Timestamp -->
                        <td class="px-6 py-4 text-slate-500 font-mono-num text-[11px]">
                            {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2 block"></i>
                            <span class="font-semibold text-slate-600">Belum ada aktivitas transaksi pesanan</span>
                            <p class="text-xs text-slate-400 mt-0.5">Transaksi baru yang masuk akan otomatis tampil di tabel ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        let revenueChartInstance = null;

        function initChart(labels, commissionData, gmvData) {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;

            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }

            const canvas = ctx.getContext('2d');
            const cyanGradient = canvas.createLinearGradient(0, 0, 0, 300);
            cyanGradient.addColorStop(0, 'rgba(6, 182, 212, 0.35)');
            cyanGradient.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

            revenueChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Komisi Platform (5%)',
                            data: commissionData,
                            borderColor: '#06b6d4',
                            borderWidth: 2.5,
                            backgroundColor: cyanGradient,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#0891b2',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#06b6d4',
                            pointHoverBorderColor: '#ffffff',
                            yAxisID: 'y'
                        },
                        {
                            label: 'Gross Volume (GMV)',
                            data: gmvData,
                            borderColor: '#cbd5e1',
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            titleFont: { size: 11, family: 'Inter' },
                            bodyColor: '#ffffff',
                            bodyFont: { size: 12, weight: 'bold', family: 'JetBrains Mono' },
                            padding: 10,
                            borderRadius: 10,
                            borderColor: 'rgba(6, 182, 212, 0.3)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y || 0;
                                    return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11, family: 'Inter' }
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                color: '#0891b2',
                                font: { size: 10, family: 'JetBrains Mono' },
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: false,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }

        function loadChartData(period = 'day') {
            // Update Tab Active Style
            document.querySelectorAll('#chart-period-tabs .period-btn').forEach(btn => {
                if (btn.dataset.period === period) {
                    btn.className = 'period-btn px-3 py-1.5 rounded-lg font-bold bg-white text-cyan-700 shadow-xs';
                } else {
                    btn.className = 'period-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all';
                }
            });

            const loader = document.getElementById('chart-loader');
            if (loader) loader.classList.remove('hidden');

            fetch(`{{ route('super_admin.dashboard.chart-data') }}?period=${period}`)
                .then(res => res.json())
                .then(data => {
                    const commission = data.data || [];
                    const gmv = data.gmv || commission.map(c => c * 20);
                    initChart(data.labels, commission, gmv);
                })
                .catch(err => {
                    console.error('Failed to load chart data:', err);
                })
                .finally(() => {
                    if (loader) loader.classList.add('hidden');
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadChartData('day');
        });
    </script>
    @endpush
</x-super-admin-layout>