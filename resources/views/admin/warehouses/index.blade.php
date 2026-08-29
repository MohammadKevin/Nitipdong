<x-admin-layout>
    <x-slot name="title">
        Kelola Gudang Hub NDX - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Gudang Hub DC NitipDongExpress
    </x-slot>

    @php
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $prefix = $isSuperAdmin ? 'super_admin' : 'admin';
    @endphp

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pb-1">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                    Gudang Hub DC NitipDongExpress (NDX)
                </h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-cyan-700 bg-cyan-50 border border-cyan-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-600"></span>
                    Logistik Internal
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Kelola jaringan gudang sortir dan pusat distribusi ekspedisi NitipDongExpress di seluruh Indonesia.</p>
        </div>
        
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="{{ route($prefix . '.warehouses.create') }}" class="h-9 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold text-xs flex items-center gap-2 shadow-xs transition-colors shrink-0 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Gudang Hub</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-200/70 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-warehouse text-base"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded-full border border-cyan-200/70">
                        Semua Hub DC
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Gudang Hub</p>
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">
                        {{ number_format($totalWarehouses, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">Gudang</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Jaringan logistik:</span>
                <span class="font-medium text-slate-800">Nasional</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/70 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/70">
                        Beroperasi
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hub Aktif</p>
                    <h3 class="text-2xl font-bold text-emerald-700 tracking-tight mt-1">
                        {{ number_format($activeWarehouses, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">Aktif</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Status operasional:</span>
                <span class="font-medium text-emerald-700">Siap Sortir & Pickup</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/70 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-truck-fast text-base"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/70">
                        NDX Coverage
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Layanan Pengiriman</p>
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight mt-1">
                        Reguler · Express · Same Day
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Integrasi sistem:</span>
                <span class="font-medium text-slate-800">100% Otomatis</span>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <h2 class="text-sm font-bold text-slate-900">Daftar Gudang Hub DC ({{ $warehouses->total() }})</h2>
            
            <form method="GET" action="{{ route($prefix . '.warehouses.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari kode, nama, kota..."
                           class="w-56 sm:w-72 pl-9 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <button type="submit" class="h-8 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Nama Hub</th>
                        <th class="px-5 py-3.5">Kota & Provinsi</th>
                        <th class="px-5 py-3.5">Alamat Lengkap</th>
                        <th class="px-5 py-3.5">Koordinat (Lat, Lng)</th>
                        <th class="px-5 py-3.5">Kontak & PIC</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($warehouses as $wh)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono font-bold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-200 text-[11px] inline-block">
                                {{ $wh->code }}
                            </span>
                            <span class="font-bold text-slate-900 block mt-1 text-xs">{{ $wh->name }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-slate-800 block">{{ $wh->city }}</span>
                            <span class="text-[11px] text-slate-500">{{ $wh->province }}</span>
                        </td>
                        <td class="px-5 py-3.5 max-w-xs">
                            <p class="text-[11px] text-slate-600 line-clamp-2" title="{{ $wh->address }}">{{ $wh->address }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-[10px] text-slate-500">
                            {{ number_format($wh->lat, 4) }}, {{ number_format($wh->lng, 4) }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-slate-800 block">{{ $wh->pic_name ?: '-' }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $wh->phone ?: '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form action="{{ route($prefix . '.warehouses.toggle', $wh) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="cursor-pointer px-2.5 py-0.5 rounded-full text-[10px] font-bold border transition-all {{ $wh->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}"
                                        title="Klik untuk ubah status">
                                    {{ $wh->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route($prefix . '.warehouses.edit', $wh) }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors" title="Edit Data">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                </a>
                                <form action="{{ route($prefix . '.warehouses.destroy', $wh) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gudang hub ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer" title="Hapus">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            Tidak ada gudang hub yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $warehouses->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
