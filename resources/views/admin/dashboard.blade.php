<x-admin-layout>
    <x-slot name="title">
        Persetujuan Toko - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Persetujuan Toko & Moderasi Merchant
    </x-slot>

    <!-- HEADER BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                Persetujuan Toko & Moderasi Merchant
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Tinjau dan verifikasi permohonan pembukaan toko baru dari pengguna NitipDong.</p>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-base border border-amber-200 shrink-0 font-mono-num">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Menunggu Review</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($pendingCount, 0, ',', '.') }} Toko</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-base border border-blue-200 shrink-0 font-mono-num">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Toko Disetujui</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($approvedCount, 0, ',', '.') }} Toko</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-base border border-rose-200 shrink-0 font-mono-num">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Pengajuan Ditolak</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($rejectedCount, 0, ',', '.') }} Toko</h4>
            </div>
        </div>
    </div>

    <!-- TABLE PENDING STORES -->
    <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider font-mono-num">Daftar Pengajuan Toko (Pending)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Toko yang membutuhkan verifikasi dokumen dan nama resmi</p>
            </div>
            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.approvals.index') : route('admin.dashboard') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="w-56 h-8.5 pl-8 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono-num transition-colors" placeholder="Cari nama toko...">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 font-semibold">Nama Toko</th>
                        <th class="px-5 py-3 font-semibold">Pengaju Akun</th>
                        <th class="px-5 py-3 font-semibold">Deskripsi Toko</th>
                        <th class="px-5 py-3 font-semibold text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingStores as $store)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 text-slate-500 font-mono-num text-[11px]">
                            {{ $store->created_at->translatedFormat('d M Y') }}<br>
                            <span class="text-[10px] text-slate-400 font-mono-num">{{ $store->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-900">
                            {{ $store->name }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($store->user->name) }}&background=0f172a&color=fff&size=50" class="w-6 h-6 rounded-full object-cover shrink-0 border border-slate-200" alt="User">
                                <span class="text-slate-700 font-medium">{{ $store->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 max-w-xs">
                            <p class="truncate">{{ $store->description ?? 'Tidak ada deskripsi' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.stores.approve', $store) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition-colors border border-emerald-200 inline-flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('admin.stores.reject', $store) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-md bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-xs transition-colors border border-rose-200 inline-flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-circle-check text-2xl text-blue-600 mb-2 block"></i>
                            <span class="text-xs font-semibold text-slate-700">Semua pengajuan toko telah ditinjau</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada antrean persetujuan toko baru saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pendingStores->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $pendingStores->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
