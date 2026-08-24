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
            <a href="{{ route('admin.flash_sales.create') }}" class="h-8.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Buat Flash Sale Baru</span>
            </a>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-fire"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Sedang Berlangsung</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($runningCount, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Akan Datang (Terjadwal)</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($upcomingCount, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 border border-purple-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Total Semua Sesi</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($totalEvents, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>
    </div>

    <!-- FLASH SALES LEDGER TABLE -->
    <div class="bg-white rounded-lg shadow-xs border border-slate-200/90 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0 font-mono-num">
                <a href="{{ route('admin.flash_sales.index') }}" class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ empty($status) ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Semua
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'running']) }}" class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ $status === 'running' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Sedang Berlangsung
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'upcoming']) }}" class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ $status === 'upcoming' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Akan Datang
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'ended']) }}" class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ $status === 'ended' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Telah Berakhir
                </a>
            </div>

            <form action="{{ route('admin.flash_sales.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="w-full sm:w-60 h-8.5 pl-8 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono-num transition-colors placeholder:text-slate-400" placeholder="Cari nama event...">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nama Event</th>
                        <th class="px-5 py-3 font-semibold">Status Event</th>
                        <th class="px-5 py-3 font-semibold">Periode Waktu</th>
                        <th class="px-5 py-3 font-semibold text-center">Jumlah Produk</th>
                        <th class="px-5 py-3 font-semibold text-center">Status Aktif</th>
                        <th class="px-5 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($flashSales as $fs)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('admin.flash_sales.show', $fs) }}" class="font-semibold text-slate-900 text-xs hover:text-blue-600 transition-colors block">
                                {{ $fs->name }}
                            </a>
                            <span class="text-[10px] text-slate-400 font-mono-num">ID: #{{ $fs->id }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($fs->is_running)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 font-mono-num">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Live Flash Sale
                                </span>
                            @elseif($fs->is_upcoming)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200 font-mono-num">
                                    <i class="fa-regular fa-clock text-[9px]"></i> Terjadwal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200 font-mono-num">
                                    Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 font-mono-num text-[11px]">
                            <p class="font-semibold text-slate-800">{{ $fs->start_time->translatedFormat('d M Y, H:i') }}</p>
                            <p class="text-[10px] text-slate-400">s/d {{ $fs->end_time->translatedFormat('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 font-mono-num">
                                <i class="fa-solid fa-box text-[9px]"></i>
                                {{ $fs->items_count }} Produk
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form action="{{ route('admin.flash_sales.toggle', $fs) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold border transition-colors cursor-pointer font-mono-num {{ $fs->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                    {{ $fs->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.flash_sales.show', $fs) }}" class="p-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-md transition-colors border border-slate-200 shadow-2xs" title="Kelola Produk Flash Sale">
                                    <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                </a>
                                <a href="{{ route('admin.flash_sales.edit', $fs) }}" class="p-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-md transition-colors border border-slate-200 shadow-2xs" title="Edit Jadwal">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('admin.flash_sales.destroy', $fs) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi flash sale ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200 shadow-2xs cursor-pointer" title="Hapus Flash Sale">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            Tidak ada event flash sale yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $flashSales->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
