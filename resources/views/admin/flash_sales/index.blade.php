<x-admin-layout>
    <x-slot name="title">
        Kelola Flash Sale - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-bolt text-amber-500"></i>
                Kelola Flash Sale Platform
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Atur jadwal promosi kilat, kurasi produk dari berbagai toko, dan tetapkan harga khusus Flash Sale.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.flash_sales.create') }}" class="inline-flex items-center gap-2 bg-[#12A57F] hover:bg-[#0f8b6a] text-white px-4 py-2.5 rounded-xl font-semibold text-xs transition-all shadow-md shadow-[#12A57F]/20">
                <i class="fa-solid fa-plus"></i>
                Buat Flash Sale Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Sedang Berlangsung</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ number_format($runningCount, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Akan Datang (Terjadwal)</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ number_format($upcomingCount, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Semua Event</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ number_format($totalEvents, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
        <div class="p-5 border-b border-[#F0EEE6] flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-slate-50/50">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('admin.flash_sales.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ empty($status) ? 'bg-[#12A57F] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'running']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'running' ? 'bg-[#12A57F] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                    Sedang Berlangsung
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'upcoming']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'upcoming' ? 'bg-[#12A57F] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                    Akan Datang
                </a>
                <a href="{{ route('admin.flash_sales.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'ended' ? 'bg-[#12A57F] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                    Telah Berakhir
                </a>
            </div>

            <form action="{{ route('admin.flash_sales.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="bg-white border border-[#E7E3D8] rounded-xl pl-10 pr-4 py-2 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] shadow-sm w-full sm:w-64" placeholder="Cari judul flash sale...">
                <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#12A57F]">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                    <tr>
                        <th class="px-6 py-3.5">Nama Event Flash Sale</th>
                        <th class="px-6 py-3.5">Status & Countdown</th>
                        <th class="px-6 py-3.5">Periode Pelaksanaan</th>
                        <th class="px-6 py-3.5 text-center">Jumlah Produk</th>
                        <th class="px-6 py-3.5 text-center">Status Aktif</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse($flashSales as $fs)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 border border-amber-100 flex items-center justify-center text-lg shadow-sm">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.flash_sales.show', $fs) }}" class="font-bold text-slate-800 text-sm hover:text-[#12A57F] transition-colors block">
                                        {{ $fs->title }}
                                    </a>
                                    <span class="text-[10px] text-slate-400">ID Event: #{{ $fs->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $fs->status_badge['color'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $fs->is_running ? 'bg-emerald-500 animate-pulse' : ($fs->is_upcoming ? 'bg-blue-500' : 'bg-slate-400') }}"></span>
                                {{ $fs->status_badge['label'] }}
                            </span>
                            @if($fs->is_running)
                                <p class="text-[10px] text-emerald-600 font-medium mt-1">Berakhir: {{ $fs->end_time->diffForHumans() }}</p>
                            @elseif($fs->is_upcoming)
                                <p class="text-[10px] text-blue-600 font-medium mt-1">Mulai: {{ $fs->start_time->diffForHumans() }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-medium text-[11px] text-slate-800">
                                    <i class="fa-regular fa-clock text-slate-400 mr-1"></i>
                                    {{ $fs->start_time->translatedFormat('d M Y, H:i') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    s/d {{ $fs->end_time->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.flash_sales.show', $fs) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                <i class="fa-solid fa-box-open text-[10px]"></i>
                                {{ $fs->items_count }} Produk
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.flash_sales.toggle', $fs) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $fs->is_active ? 'bg-[#12A57F]' : 'bg-slate-200' }}" title="Toggle Status Aktif">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $fs->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.flash_sales.show', $fs) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-[#12A57F]/10 text-[#12A57F] hover:bg-[#12A57F] hover:text-white rounded-lg transition-colors font-semibold text-[11px]" title="Kelola Produk Flash Sale">
                                    <i class="fa-solid fa-gear text-xs"></i>
                                    Kelola Produk
                                </a>
                                <a href="{{ route('admin.flash_sales.edit', $fs) }}" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Jadwal Event">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('admin.flash_sales.destroy', $fs) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event Flash Sale \'{{ $fs->title }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors" title="Hapus Event">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-3 text-2xl">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <p class="font-medium text-slate-600 text-sm">Belum ada sesi Flash Sale</p>
                                <p class="text-xs text-slate-400 mt-1">Buat sesi flash sale baru untuk menarik lebih banyak pembeli.</p>
                                <a href="{{ route('admin.flash_sales.create') }}" class="mt-4 inline-flex items-center gap-2 bg-[#12A57F] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#0f8b6a] transition-all">
                                    <i class="fa-solid fa-plus"></i>
                                    Buat Flash Sale Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-[#F0EEE6]">
            {{ $flashSales->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
