<x-super-admin-layout>
    <x-slot name="title">
        Daftar Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Toko & Merchant</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh toko dan seller yang terdaftar di platform BelanjaIn.</p>
        </div>

        <form action="{{ route('super_admin.stores.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama toko..."
                       class="input text-xs pl-8 h-9 rounded-md">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </div>
            <button type="submit" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800">Cari</button>
            @if($search)
                <a href="{{ route('super_admin.stores.index') }}" class="btn-secondary text-xs h-9 px-3 rounded-md">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5 font-semibold">ID</th>
                        <th class="py-3.5 px-5 font-semibold">Nama Toko</th>
                        <th class="py-3.5 px-5 font-semibold">Pemilik Akun</th>
                        <th class="py-3.5 px-5 font-semibold">Status Operasional</th>
                        <th class="py-3.5 px-5 font-semibold">Tanggal Daftar</th>
                        <th class="py-3.5 px-5 font-semibold text-right">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stores as $store)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono text-slate-400">#{{ $store->id }}</td>
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                @if($store->logo)
                                    <img src="{{ asset('storage/' . $store->logo) }}" class="w-8 h-8 rounded-md object-cover shrink-0 border border-slate-200" alt="Logo">
                                @else
                                    <div class="w-8 h-8 rounded-md bg-cyan-50 text-cyan-800 flex items-center justify-center font-bold border border-cyan-200 text-xs">{{ substr($store->name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $store->name }}</h4>
                                    <p class="text-[11px] text-slate-400 truncate max-w-[180px]">{{ $store->address ?? 'Alamat belum diatur' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($store->user->name ?? 'User') }}&background=0891b2&color=fff&size=50" class="w-5 h-5 rounded-full object-cover shrink-0" alt="Avatar">
                                <span class="text-slate-700 font-medium">{{ $store->user->name ?? 'User Terhapus' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-5">
                            @if($store->status === 'active' || $store->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold text-[10px] border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui (Aktif)
                                </span>
                            @elseif($store->status === 'pending')
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold text-[10px] border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Verifikasi
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-semibold text-[10px] border border-rose-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditangguhkan (Banned)
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-slate-400">{{ $store->created_at->translatedFormat('d M Y') }}</td>
                        <td class="py-3.5 px-5 text-right">
                            <form action="{{ route('super_admin.stores.toggle_ban', $store) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status toko ini?');">
                                @csrf
                                @if($store->status === 'approved' || $store->status === 'pending')
                                    <button type="submit" class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-[10px] transition-colors border border-rose-200">
                                        Suspend Toko
                                    </button>
                                @else
                                    <button type="submit" class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-[10px] transition-colors border border-emerald-200">
                                        Pulihkan (Unban)
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">Tidak ada toko yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($stores->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $stores->links() }}
        </div>
        @endif
    </div>
</x-super-admin-layout>
