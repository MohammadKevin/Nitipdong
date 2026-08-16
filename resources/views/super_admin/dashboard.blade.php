<x-super-admin-layout>
    <x-slot name="title">
        Super Admin Dashboard - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    {{-- Top bar --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">Selamat datang kembali, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Ini ringkasan performa platform BelanjaIn bulan ini.</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <input type="text" placeholder="Cari toko, pengguna, transaksi..." class="w-full bg-white border border-[#E7E3D8] rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                <svg class="w-4 h-4 text-[#B3ACA0] absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button class="w-10 h-10 shrink-0 rounded-xl bg-white border border-[#E7E3D8] text-[#4B5566] flex items-center justify-center hover:bg-[#F0EEE6] relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="absolute top-2 right-2.5 w-1.5 h-1.5 rounded-full bg-[#F2A93B]"></span>
            </button>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=12A57F&color=fff" class="w-10 h-10 rounded-xl border border-[#E7E3D8] object-cover shrink-0" alt="User">
        </div>
    </div>

    {{-- 3. METRIC CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-2xl border border-[#EFEBDF]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-9 h-9 rounded-lg bg-[#E9F8F2] text-[#12A57F] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0-8v8"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-[#8A93A6] font-medium">Total Pendapatan &middot; Berhasil</p>
            <h3 class="text-xl font-bold text-[#14213D] mt-1" style="font-family:'Poppins',sans-serif;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#EFEBDF]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-9 h-9 rounded-lg bg-[#FDEFEF] text-[#E15554] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9l-5 5-4-4-3 3m12-4h2v2"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-[#8A93A6] font-medium">Transaksi Aktif &middot; Saat Ini</p>
            <h3 class="text-xl font-bold text-[#14213D] mt-1" style="font-family:'Poppins',sans-serif;">{{ number_format($activeOrders, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#EFEBDF]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-9 h-9 rounded-lg bg-[#FFF6E7] text-[#F2A93B] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-[#8A93A6] font-medium">Pesanan Baru &middot; Bulan Ini</p>
            <h3 class="text-xl font-bold text-[#14213D] mt-1" style="font-family:'Poppins',sans-serif;">{{ number_format($pesananBaru, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#EFEBDF]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-9 h-9 rounded-lg bg-[#EAF1FF] text-[#3E6FE0] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-[#8A93A6] font-medium">Total Pengguna &middot; Terdaftar</p>
            <h3 class="text-xl font-bold text-[#14213D] mt-1" style="font-family:'Poppins',sans-serif;">{{ number_format($totalPengguna, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- 4. MIDDLE: RECENT ORDERS + CHART + REPORT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-[#EFEBDF] flex flex-col gap-5">
            <div class="flex justify-between items-center">
                <h4 class="font-semibold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Pesanan Terbaru</h4>
                <a href="#" class="text-xs font-semibold text-[#12A57F] hover:underline">Lihat semua</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                <div class="md:col-span-7 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[#B3ACA0] border-b border-[#F0EEE6] font-medium">
                                <th class="pb-2 font-medium">ID</th>
                                <th class="pb-2 font-medium">Produk</th>
                                <th class="pb-2 font-medium">Jumlah</th>
                                <th class="pb-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F3EE]">
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="py-2.5 font-mono text-[#B3ACA0]">#{{ $order->invoice_number }}</td>
                                <td class="py-2.5 font-medium text-[#3E4658]">{{ Str::limit($order->store->name ?? 'BelanjaIn', 20) }}</td>
                                <td class="py-2.5 font-semibold text-[#14213D]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="py-2.5">
                                    @if($order->status === 'pending')
                                        <span class="px-2 py-0.5 rounded-full bg-[#F3EEFC] text-[#8B5CF6] font-semibold text-[10px]">Menunggu</span>
                                    @elseif($order->status === 'processing')
                                        <span class="px-2 py-0.5 rounded-full bg-[#FFF6E7] text-[#C7860B] font-semibold text-[10px]">Diproses</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="px-2 py-0.5 rounded-full bg-[#EAF1FF] text-[#3E6FE0] font-semibold text-[10px]">Dikirim</span>
                                    @elseif($order->status === 'completed')
                                        <span class="px-2 py-0.5 rounded-full bg-[#E9F8F2] text-[#12A57F] font-semibold text-[10px]">Selesai</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-[#FDEFEF] text-[#E15554] font-semibold text-[10px]">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-[#B3ACA0] text-xs">Belum ada pesanan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- GRAFIK TRANSAKSI — dinamis, narik data asli, auto-refresh --}}
                <div class="md:col-span-5 flex flex-col bg-[#FAF9F5] p-4 rounded-xl border border-[#F0EEE6]">

                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-1.5">
                            <span id="chartLiveDot" class="w-1.5 h-1.5 rounded-full bg-[#12A57F] animate-pulse"></span>
                            <span id="chartUpdatedAt" class="text-[10px] text-[#B3ACA0]">Memuat...</span>
                        </div>

                        {{-- Filter periode --}}
                        <div class="flex items-center gap-1 bg-white border border-[#F0EEE6] rounded-lg p-0.5">
                            <button type="button" data-period="day"   class="chart-period-btn px-2 py-1 rounded-md text-[10px] font-semibold text-[#8A93A6] transition-colors">Hari</button>
                            <button type="button" data-period="week"  class="chart-period-btn px-2 py-1 rounded-md text-[10px] font-semibold bg-[#12A57F] text-white transition-colors">Minggu</button>
                            <button type="button" data-period="month" class="chart-period-btn px-2 py-1 rounded-md text-[10px] font-semibold text-[#8A93A6] transition-colors">Bulan</button>
                            <button type="button" data-period="year"  class="chart-period-btn px-2 py-1 rounded-md text-[10px] font-semibold text-[#8A93A6] transition-colors">Tahun</button>
                        </div>
                    </div>

                    <div class="relative flex-1 min-h-[128px]">
                        {{-- Skeleton loading --}}
                        <div id="chartLoading" class="absolute inset-0 flex items-end justify-between gap-3 px-1 pb-1">
                            <div class="w-full h-1/3 bg-[#EFEBDF] rounded-md animate-pulse"></div>
                            <div class="w-full h-1/2 bg-[#EFEBDF] rounded-md animate-pulse"></div>
                            <div class="w-full h-4/5 bg-[#EFEBDF] rounded-md animate-pulse"></div>
                            <div class="w-full h-2/3 bg-[#EFEBDF] rounded-md animate-pulse"></div>
                        </div>
                        <canvas id="transactionChart" class="relative"></canvas>
                    </div>

                    <div id="chartEmpty" class="hidden text-center text-[10px] text-[#B3ACA0] py-4">Belum ada transaksi pada periode ini</div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-[#EFEBDF] flex flex-col justify-between">
            <h4 class="font-semibold text-sm text-[#14213D] mb-4" style="font-family:'Poppins',sans-serif;">Laporan Total</h4>
            <div class="space-y-3">
                <div class="p-3.5 bg-[#FAF9F5] rounded-xl">
                    <span class="text-[11px] text-[#8A93A6]">Total Pengguna</span>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm font-bold text-[#14213D]">{{ number_format($totalPengguna, 0, ',', '.') }} Akun</p>
                    </div>
                </div>
                <div class="p-3.5 bg-[#FAF9F5] rounded-xl">
                    <span class="text-[11px] text-[#8A93A6]">Total Toko Aktif</span>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm font-bold text-[#14213D]">{{ number_format($totalToko, 0, ',', '.') }} Toko</p>
                    </div>
                </div>
                <div class="p-3.5 bg-[#FAF9F5] rounded-xl">
                    <span class="text-[11px] text-[#8A93A6]">Total Keseluruhan Pesanan</span>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm font-bold text-[#14213D]">{{ number_format($totalPesanan, 0, ',', '.') }} Pesanan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. RECENT TRANSACTIONS --}}
    <div class="bg-white p-6 rounded-2xl border border-[#EFEBDF]">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-semibold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Transaksi Terbaru</h4>
            <a href="#" class="text-xs font-semibold text-[#12A57F] hover:underline">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[#B3ACA0] border-b border-[#F0EEE6] font-medium">
                        <th class="pb-3 font-medium">ID Pesanan</th>
                        <th class="pb-3 font-medium">Nama Pelanggan</th>
                        <th class="pb-3 font-medium">Produk</th>
                        <th class="pb-3 font-medium">Jumlah</th>
                        <th class="pb-3 font-medium">Alamat</th>
                        <th class="pb-3 font-medium">Telepon</th>
                        <th class="pb-3 font-medium">Kurir</th>
                        <th class="pb-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-[#FAF9F5] transition-colors">
                        <td class="py-3 font-mono text-[#B3ACA0]">#{{ $order->invoice_number }}</td>
                        <td class="py-3 font-semibold text-[#14213D]">{{ $order->user->name }}</td>
                        <td class="py-3 text-[#4B5566]">{{ Str::limit($order->store->name ?? '-', 20) }}</td>
                        <td class="py-3 font-semibold text-[#14213D]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3 text-[#8A93A6]">{{ Str::limit($order->shipping_address, 20) }}</td>
                        <td class="py-3 text-[#8A93A6]">{{ $order->user->phone ?? '-' }}</td>
                        <td class="py-3 font-medium text-[#4B5566]">{{ $order->tracking_number ?? 'Belum ada' }}</td>
                        <td class="py-3">
                            @if($order->status === 'pending')
                                <span class="px-2.5 py-1 rounded-full bg-[#F3EEFC] text-[#8B5CF6] font-semibold text-[10px]">Menunggu</span>
                            @elseif($order->status === 'processing')
                                <span class="px-2.5 py-1 rounded-full bg-[#FFF6E7] text-[#C7860B] font-semibold text-[10px]">Diproses</span>
                            @elseif($order->status === 'shipped')
                                <span class="px-2.5 py-1 rounded-full bg-[#EAF1FF] text-[#3E6FE0] font-semibold text-[10px]">Dikirim</span>
                            @elseif($order->status === 'completed')
                                <span class="px-2.5 py-1 rounded-full bg-[#E9F8F2] text-[#12A57F] font-semibold text-[10px]">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-[#FDEFEF] text-[#E15554] font-semibold text-[10px]">Dibatalkan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-[#B3ACA0]">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        const canvas       = document.getElementById('transactionChart');
        const loadingEl    = document.getElementById('chartLoading');
        const emptyEl      = document.getElementById('chartEmpty');
        const updatedAtEl  = document.getElementById('chartUpdatedAt');
        const liveDotEl    = document.getElementById('chartLiveDot');
        const periodBtns   = document.querySelectorAll('.chart-period-btn');

        const chartDataUrl = "{{ route('super_admin.dashboard.chart-data') }}";
        const REFRESH_MS   = 30000; // auto-refresh tiap 30 detik

        let chart = null;
        let currentPeriod = 'week';
        let refreshTimer = null;

        function idr(value) {
            return 'Rp ' + Math.round(value).toLocaleString('id-ID');
        }

        function buildGradient(ctx) {
            const g = ctx.createLinearGradient(0, 0, 0, canvas.clientHeight || 150);
            g.addColorStop(0, 'rgba(18,165,127,0.28)');
            g.addColorStop(1, 'rgba(18,165,127,0)');
            return g;
        }

        function setLoading(state) {
            loadingEl.classList.toggle('hidden', !state);
            canvas.style.opacity = state ? '0' : '1';
        }

        function setLive(state) {
            liveDotEl.classList.toggle('bg-[#12A57F]', state);
            liveDotEl.classList.toggle('bg-[#E15554]', !state);
        }

        async function fetchChartData(period) {
            const res = await fetch(`${chartDataUrl}?period=${period}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!res.ok) throw new Error('Gagal mengambil data grafik');
            return res.json();
        }

        function renderChart(json) {
            const hasData = Array.isArray(json.data) && json.data.some((v) => v > 0);
            emptyEl.classList.toggle('hidden', hasData);

            const ctx = canvas.getContext('2d');

            if (!chart) {
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: json.labels,
                        datasets: [{
                            data: json.data,
                            borderColor: '#12A57F',
                            backgroundColor: buildGradient(ctx),
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#12A57F',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#14213D',
                                padding: 8,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: { label: (c) => idr(c.parsed.y) },
                            },
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#B3ACA0', font: { size: 10 } } },
                            y: { display: false, beginAtZero: true },
                        },
                    },
                });
            } else {
                chart.data.labels = json.labels;
                chart.data.datasets[0].data = json.data;
                chart.data.datasets[0].backgroundColor = buildGradient(ctx);
                chart.update();
            }
        }

        async function loadChart(period, { showSkeleton = true } = {}) {
            if (showSkeleton) setLoading(true);
            try {
                const json = await fetchChartData(period);
                renderChart(json);
                setLive(true);
                updatedAtEl.textContent = 'Update ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                console.error(e);
                setLive(false);
                updatedAtEl.textContent = 'Gagal memuat data';
            } finally {
                setLoading(false);
            }
        }

        function scheduleAutoRefresh() {
            if (refreshTimer) clearInterval(refreshTimer);
            refreshTimer = setInterval(() => loadChart(currentPeriod, { showSkeleton: false }), REFRESH_MS);
        }

        periodBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                if (btn.dataset.period === currentPeriod) return;
                periodBtns.forEach((b) => {
                    b.classList.remove('bg-[#12A57F]', 'text-white');
                    b.classList.add('text-[#8A93A6]');
                });
                btn.classList.add('bg-[#12A57F]', 'text-white');
                btn.classList.remove('text-[#8A93A6]');
                currentPeriod = btn.dataset.period;
                loadChart(currentPeriod);
            });
        });

        loadChart(currentPeriod);
        scheduleAutoRefresh();

        // Pause auto-refresh saat tab tidak aktif, biar hemat request
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(refreshTimer);
            } else {
                loadChart(currentPeriod, { showSkeleton: false });
                scheduleAutoRefresh();
            }
        });
    })();
    </script>
    @endpush
</x-super-admin-layout>