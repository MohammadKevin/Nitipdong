<x-super-admin-layout>
    <x-slot name="title">
        Executive Dashboard - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Executive Overview & Financial Metrics
    </x-slot>

    <!-- CURATED ENTERPRISE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                    Ringkasan Eksekutif & Analisis Finansial
                </h1>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200/80 font-mono-num">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    Q3 {{ now()->year }}
                </span>
            </div>
            <p class="text-xs text-slate-500 max-w-2xl">
                Monitoring real-time perputaran transaksi marketplace, pendapatan laba komisi 5%, dan status operasional toko.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <a href="{{ route('super_admin.withdrawals.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-xs transition-colors">
                <i class="fa-solid fa-money-bill-transfer text-emerald-600 text-[11px]"></i>
                <span>Tinjau Payout</span>
                @if($pendingWithdrawalsCount > 0)
                    <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold text-[10px] font-mono-num">
                        {{ $pendingWithdrawalsCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('super_admin.reports.revenue.export') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition-colors">
                <i class="fa-solid fa-file-excel text-[11px]"></i>
                <span>Ekspor Laporan</span>
            </a>
        </div>
    </div>

    <!-- CONNECTED KPI METRIC GRID (Enterprise Linear Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- METRIC 1: KOMISI PLATFORM (5%) -->
        <div class="bg-white p-4.5 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                    <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 font-mono-num">Komisi Platform (5%)</span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60 font-mono-num">
                        Laba Bersih
                    </span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight font-mono-num">
                        Rp {{ number_format($totalKeuntunganPlatform, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Bulan Ini:</span>
                <span class="font-bold text-slate-800 font-mono-num">Rp {{ number_format($thisMonthProfit, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- METRIC 2: GROSS MERCHANDISE VOLUME (GMV) -->
        <div class="bg-white p-4.5 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                    <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 font-mono-num">Gross Volume (GMV)</span>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded font-mono-num">
                        Total Sukses
                    </span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight font-mono-num">
                        Rp {{ number_format($grossVolume, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Rata-rata Order (AOV):</span>
                <span class="font-bold text-slate-800 font-mono-num">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- METRIC 3: VOLUME PESANAN -->
        <div class="bg-white p-4.5 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                    <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 font-mono-num">Volume Pesanan</span>
                    <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200/60 font-mono-num">
                        Bulan Ini
                    </span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight font-mono-num">
                        {{ number_format($pesananBaru, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Order</span>
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Pesanan Berjalan:</span>
                <span class="font-bold text-blue-700 font-mono-num">{{ number_format($activeOrders, 0, ',', '.') }} Aktif</span>
            </div>
        </div>

        <!-- METRIC 4: EKOSISTEM PENGGUNA & TOKO -->
        <div class="bg-white p-4.5 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                    <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 font-mono-num">Pengguna Terdaftar</span>
                    <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded font-mono-num">
                        Akun
                    </span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight font-mono-num">
                        {{ number_format($totalPengguna, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">User</span>
                    </h3>
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Toko Resmi:</span>
                <div class="flex items-center gap-1.5 font-mono-num">
                    <span class="font-bold text-slate-800">{{ $totalToko }} Mitra</span>
                    @if($pendingStoresCount > 0)
                        <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-1 py-0.2 rounded border border-amber-200">
                            +{{ $pendingStoresCount }} Review
                        </span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- DATA ANALYTICS & STATUS DISTRIBUTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- ANALYTICAL REVENUE & GMV CHART -->
        <div class="lg:col-span-8 bg-white p-5 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Tren Finansial Platform</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Analisis pendapatan komisi 5% dan gross merchandise volume</p>
                </div>

                <!-- Minimalist Enterprise Period Tabs -->
                <div class="inline-flex p-0.5 bg-slate-100 rounded-lg text-xs font-medium font-mono-num" id="chart-period-tabs">
                    <button type="button" onclick="loadChartData('day')" class="period-btn px-2.5 py-1 rounded-md text-slate-600 hover:text-slate-900 transition-colors" data-period="day">7 Hari</button>
                    <button type="button" onclick="loadChartData('week')" class="period-btn px-2.5 py-1 rounded-md text-slate-600 hover:text-slate-900 transition-colors active bg-white text-blue-700 font-bold shadow-xs" data-period="week">Mingguan</button>
                    <button type="button" onclick="loadChartData('month')" class="period-btn px-2.5 py-1 rounded-md text-slate-600 hover:text-slate-900 transition-colors" data-period="month">Bulan Ini</button>
                    <button type="button" onclick="loadChartData('year')" class="period-btn px-2.5 py-1 rounded-md text-slate-600 hover:text-slate-900 transition-colors" data-period="year">Tahunan</button>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="relative w-full h-64 sm:h-72 pt-3">
                <canvas id="revenueChart"></canvas>
                <div id="chart-loader" class="absolute inset-0 bg-white/80 flex items-center justify-center hidden">
                    <div class="flex items-center gap-2 text-slate-600 text-xs font-medium">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm text-blue-600"></i>
                        <span>Memuat data analitik...</span>
                    </div>
                </div>
            </div>

            <!-- Footnote Legend -->
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span>
                        <span class="font-medium text-slate-700">Komisi Platform (5%)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-xs bg-slate-300"></span>
                        <span class="font-medium text-slate-500">Gross Volume (GMV)</span>
                    </div>
                </div>
                <span class="text-[11px] text-slate-400 font-mono-num">Real-Time Invoiced Data</span>
            </div>
        </div>

        <!-- TRANSACTION STATUS DISTRIBUTION -->
        <div class="lg:col-span-4 bg-white p-5 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Status Transaksi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Distribusi dari {{ number_format($totalPesanan, 0, ',', '.') }} total order</p>
                </div>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded font-mono-num">
                    100%
                </span>
            </div>

            <div class="space-y-3 my-auto py-2">
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
                    <div class="flex justify-between text-xs mb-1 font-medium">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-xs bg-emerald-600"></span>
                            Pesanan Selesai
                        </span>
                        <span class="text-slate-900 font-mono-num font-semibold">{{ $completedOrders }} ({{ $compPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-xs h-1.5 overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-xs transition-all duration-300" style="width: {{ $compPct }}%"></div>
                    </div>
                </div>

                <!-- Dikirim -->
                <div>
                    <div class="flex justify-between text-xs mb-1 font-medium">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-xs bg-blue-600"></span>
                            Sedang Dikirim
                        </span>
                        <span class="text-slate-900 font-mono-num font-semibold">{{ $shippedOrders }} ({{ $shipPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-xs h-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-xs transition-all duration-300" style="width: {{ $shipPct }}%"></div>
                    </div>
                </div>

                <!-- Diproses -->
                <div>
                    <div class="flex justify-between text-xs mb-1 font-medium">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-xs bg-sky-500"></span>
                            Diproses Toko
                        </span>
                        <span class="text-slate-900 font-mono-num font-semibold">{{ $processingOrders }} ({{ $procPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-xs h-1.5 overflow-hidden">
                        <div class="bg-sky-500 h-full rounded-xs transition-all duration-300" style="width: {{ $procPct }}%"></div>
                    </div>
                </div>

                <!-- Pending -->
                <div>
                    <div class="flex justify-between text-xs mb-1 font-medium">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-xs bg-amber-500"></span>
                            Menunggu Konfirmasi
                        </span>
                        <span class="text-slate-900 font-mono-num font-semibold">{{ $pendingOrders }} ({{ $pendPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-xs h-1.5 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-xs transition-all duration-300" style="width: {{ $pendPct }}%"></div>
                    </div>
                </div>

                <!-- Batal -->
                <div>
                    <div class="flex justify-between text-xs mb-1 font-medium">
                        <span class="text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-xs bg-rose-500"></span>
                            Dibatalkan / Gagal
                        </span>
                        <span class="text-slate-900 font-mono-num font-semibold">{{ $cancelledOrders }} ({{ $cancPct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-xs h-1.5 overflow-hidden">
                        <div class="bg-rose-500 h-full rounded-xs transition-all duration-300" style="width: {{ $cancPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Operational Notice -->
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200/80 text-xs text-slate-600">
                <p class="leading-relaxed">
                    Setiap transaksi sukses secara otomatis dipotong komisi <strong class="text-slate-900 font-semibold">5%</strong> untuk kas operasional platform.
                </p>
            </div>
        </div>

    </div>

    <!-- RECENT TRANSACTIONS ENTERPRISE LEDGER TABLE -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden" x-data="{ tableSearch: '' }">
        
        <!-- Table Header & Fast Search -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-sm text-slate-900 tracking-tight">Aktivitas Transaksi Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar invoice dan status pesanan masuk real-time</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <input type="text" 
                           x-model="tableSearch" 
                           placeholder="Filter invoice / toko..." 
                           class="w-48 sm:w-56 h-8.5 pl-7 pr-3 text-xs rounded-lg bg-white border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400 font-mono-num">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
                </div>

                <a href="{{ route('super_admin.stores.index') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition-colors shrink-0">
                    Kelola Toko &rarr;
                </a>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="px-5 py-3">Nomor Invoice</th>
                        <th class="px-5 py-3">Merchant / Toko</th>
                        <th class="px-5 py-3">Pembeli</th>
                        <th class="px-5 py-3">Total Belanja (GMV)</th>
                        <th class="px-5 py-3">Komisi Platform (5%)</th>
                        <th class="px-5 py-3">Status Pesanan</th>
                        <th class="px-5 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-slate-50/70 transition-colors" 
                        x-show="!tableSearch || '{{ strtolower($order->invoice_number) }}'.includes(tableSearch.toLowerCase()) || '{{ strtolower($order->store->name ?? '') }}'.includes(tableSearch.toLowerCase()) || '{{ strtolower($order->user->name ?? '') }}'.includes(tableSearch.toLowerCase())">
                        
                        <!-- Invoice Code -->
                        <td class="px-5 py-3.5 font-mono-num font-semibold text-slate-900">
                            #{{ $order->invoice_number }}
                        </td>

                        <!-- Store Name -->
                        <td class="px-5 py-3.5 font-medium text-slate-800">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-store text-slate-400 text-xs"></i>
                                <span class="truncate max-w-[150px]">{{ $order->store->name ?? 'NitipDong Store' }}</span>
                            </div>
                        </td>

                        <!-- Buyer Name -->
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name ?? 'Customer') }}&background=0f172a&color=fff&size=50" 
                                     class="w-5 h-5 rounded-full object-cover shrink-0" 
                                     alt="Buyer">
                                <span class="text-slate-700 font-medium truncate max-w-[120px]">{{ $order->user->name ?? 'Customer' }}</span>
                            </div>
                        </td>

                        <!-- Total Belanja -->
                        <td class="px-5 py-3.5 font-bold text-slate-900 font-mono-num">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>

                        <!-- Komisi 5% -->
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1 font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60 font-mono-num">
                                Rp {{ number_format(round($order->total_amount * 0.05), 0, ',', '.') }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-5 py-3.5">
                            @if($order->status === 'pending')
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Diproses
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-700 font-bold text-[10px] border border-sky-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span> Dikirim
                                </span>
                            @elseif($order->status === 'completed')
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Selesai
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Batal
                                </span>
                            @endif
                        </td>

                        <!-- Timestamp -->
                        <td class="px-5 py-3.5 text-slate-400 font-mono-num text-[11px]">
                            {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                            <i class="fa-solid fa-receipt text-2xl text-slate-300 mb-2 block"></i>
                            <span class="font-medium text-slate-600">Belum ada data transaksi pesanan</span>
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
            const blueGradient = canvas.createLinearGradient(0, 0, 0, 260);
            blueGradient.addColorStop(0, 'rgba(37, 99, 235, 0.12)');
            blueGradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

            revenueChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Komisi Platform (5%)',
                            data: commissionData,
                            borderColor: '#2563eb',
                            borderWidth: 2,
                            backgroundColor: blueGradient,
                            fill: true,
                            tension: 0.15,
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 1.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Gross Volume (GMV)',
                            data: gmvData,
                            borderColor: '#94a3b8',
                            borderWidth: 1.5,
                            borderDash: [3, 3],
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.15,
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
                            padding: 8,
                            borderRadius: 6,
                            borderColor: '#334155',
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
                                color: '#64748b',
                                font: { size: 11, family: 'JetBrains Mono' }
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
                                color: '#2563eb',
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
            document.querySelectorAll('#chart-period-tabs .period-btn').forEach(btn => {
                if (btn.dataset.period === period) {
                    btn.className = 'period-btn px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-xs';
                } else {
                    btn.className = 'period-btn px-2.5 py-1 rounded-md text-slate-600 hover:text-slate-900 transition-colors';
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