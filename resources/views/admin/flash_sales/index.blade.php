<x-admin-layout>
    <x-slot name="title">
        Kelola Flash Sale - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Flash Sale Platform
    </x-slot>

    <!-- HEADER / ACTION BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Kelola Flash Sale Platform
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atur jadwal promosi kilat, kurasi produk toko, dan tetapkan harga khusus Flash Sale.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.create') : route('admin.flash_sales.create') }}" class="h-9 px-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Buat Flash Sale Baru</span>
            </a>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Sedang Berlangsung</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($runningCount, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Akan Datang (Terjadwal)</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($upcomingCount, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Event</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($totalEvents, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>
    </div>

    <!-- FLASH SALES TABLE -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200/90 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                @php
                    $routeIndex = auth()->user()->role === 'super_admin' ? 'super_admin.flash_sales.index' : 'admin.flash_sales.index';
                @endphp
                <a href="{{ route($routeIndex) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Semua
                </a>
                <a href="{{ route($routeIndex, ['status' => 'running']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'running' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Sedang Berlangsung
                </a>
                <a href="{{ route($routeIndex, ['status' => 'upcoming']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'upcoming' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Akan Datang
                </a>
                <a href="{{ route($routeIndex, ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'ended' ? 'bg-slate-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Telah Berakhir
                </a>
            </div>

            <form action="{{ route($routeIndex) }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="w-full sm:w-64 h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400" placeholder="Cari nama event...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Nama Event</th>
                        <th class="px-5 py-3.5">Status Waktu</th>
                        <th class="px-5 py-3.5">Periode Jadwal</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Produk</th>
                        <th class="px-5 py-3.5 text-center">Status Tayang</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($flashSales as $fs)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-900 text-xs block">{{ $fs->name }}</span>
                            <span class="text-[11px] text-slate-400">ID: #{{ $fs->id }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($fs->is_running)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Sedang Aktif
                                </span>
                            @elseif($fs->is_upcoming)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Akan Datang
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            <span class="font-medium text-slate-800">{{ $fs->start_time->translatedFormat('d M Y, H:i') }}</span>
                            <span class="text-slate-400"> &rarr; </span>
                            <span class="font-medium text-slate-800">{{ $fs->end_time->translatedFormat('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $fs->items_count ?? $fs->items->count() }} Produk
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($fs->is_active)
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.show', $fs) : route('admin.flash_sales.show', $fs) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200 text-xs font-semibold inline-flex items-center gap-1" title="Kelola Produk Flash Sale">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                    </svg>
                                    <span>Kelola Item</span>
                                </a>
                                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.edit', $fs) : route('admin.flash_sales.edit', $fs) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors border border-slate-200" title="Edit Jadwal">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 block">Tidak Ada Sesi Flash Sale</span>
                            <p class="text-xs text-slate-400 mt-1">Buat sesi promosi kilat baru untuk meningkatkan transaksi merchant.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($flashSales->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $flashSales->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
