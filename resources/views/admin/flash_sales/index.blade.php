<x-admin-layout>
    <x-slot name="title">
        Kelola Flash Sale - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-bolt text-amber-500"></i>
                Kelola Flash Sale Platform
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atur jadwal promosi kilat, kurasi produk toko, dan tetapkan harga khusus Flash Sale.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.flash_sales.create') }}" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Buat Flash Sale Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg border border-amber-200 shrink-0">
                <i class="fa-solid fa-fire"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Sedang Berlangsung</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($runningCount, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg border border-cyan-200 shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Akan Datang (Terjadwal)</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($upcomingCount, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center text-lg border border-purple-200 shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Semua Sesi</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalEvents, 0, ',', '.') }} Sesi</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('admin.flash_sales.index') }}" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ empty($status) ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'running']) }}" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ $status === 'running' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200' }}">
                    Sedang Berlangsung
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'upcoming']) }}" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ $status === 'upcoming' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200' }}">
                    Akan Datang
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ $status === 'ended' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200' }}">
                    Telah Berakhir
                </a>
            </div>

            <form action="{{ route('admin.flash_sales.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="input text-xs pl-8 pr-4 h-8.5 rounded-md w-full sm:w-64" placeholder="Cari nama event...">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Nama Event</th>
                        <th class="px-5 py-3.5 font-semibold">Status Event</th>
                        <th class="px-5 py-3.5 font-semibold">Periode Waktu</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Jumlah Produk</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Status Aktif</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($flashSales as $fs)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('admin.flash_sales.show', $fs) }}" class="font-bold text-slate-900 text-xs hover:text-cyan-700 transition-colors block">
                                {{ $fs->name }}
                            </a>
                            <span class="text-[10px] text-slate-400 font-mono">ID: #{{ $fs->id }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($fs->is_running)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Live Flash Sale
                                </span>
                            @elseif($fs->is_upcoming)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                    <i class="fa-regular fa-clock text-[9px]"></i> Terjadwal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            <p class="font-medium">{{ $fs->start_time->translatedFormat('d M Y, H:i') }}</p>
                            <p class="text-[11px] text-slate-400">s/d {{ $fs->end_time->translatedFormat('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fa-solid fa-box text-[9px]"></i>
                                {{ $fs->items_count }} Produk
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form action="{{ route('admin.flash_sales.toggle', $fs) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border transition-colors {{ $fs->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                    {{ $fs->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.flash_sales.show', $fs) }}" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-md transition-colors border border-slate-200" title="Kelola Produk Flash Sale">
                                    <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                </a>
                                <a href="{{ route('admin.flash_sales.edit', $fs) }}" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-md transition-colors border border-slate-200" title="Edit Jadwal">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('admin.flash_sales.destroy', $fs) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi flash sale ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200" title="Hapus Flash Sale">
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
