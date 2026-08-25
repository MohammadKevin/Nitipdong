<x-admin-layout>
    <x-slot name="title">
        Persetujuan Toko - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Persetujuan Toko &amp; Merchant
    </x-slot>

    <!-- HEADER BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Persetujuan Toko &amp; Merchant
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Tinjau dan verifikasi permohonan pembukaan toko resmi dari pengguna NitipDong.</p>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Menunggu Review</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($pendingCount, 0, ',', '.') }} Toko</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Toko Disetujui</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($approvedCount, 0, ',', '.') }} Toko</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Pengajuan Ditolak</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($rejectedCount, 0, ',', '.') }} Toko</h4>
            </div>
        </div>
    </div>

    <!-- TABLE PENDING STORES -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wider">Daftar Pengajuan Toko (Pending)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Toko yang memerlukan persetujuan dan verifikasi identitas resmi</p>
            </div>
            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.approvals.index') : route('admin.dashboard') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="w-full sm:w-64 h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400" placeholder="Cari nama toko...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Nama Toko</th>
                        <th class="px-5 py-3.5">Pengaju Akun</th>
                        <th class="px-5 py-3.5">Deskripsi Toko</th>
                        <th class="px-5 py-3.5 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingStores as $store)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-4 text-slate-500 text-xs">
                            <span class="font-medium text-slate-700">{{ $store->created_at->translatedFormat('d M Y') }}</span><br>
                            <span class="text-[11px] text-slate-400">{{ $store->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-900">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($store->name, 0, 2)) }}
                                </div>
                                <span class="truncate">{{ $store->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($store->user->name) }}&background=0f172a&color=fff&size=50" class="w-6 h-6 rounded-full object-cover shrink-0 border border-slate-200" alt="User">
                                <div>
                                    <span class="text-slate-800 font-medium block leading-tight">{{ $store->user->name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $store->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-500 max-w-xs">
                            <p class="truncate text-xs">{{ $store->description ?? 'Tidak ada deskripsi' }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.approvals.approve', $store) : route('admin.stores.approve', $store) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition-colors border border-emerald-200/80 inline-flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.approvals.reject', $store) : route('admin.stores.reject', $store) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-xs transition-colors border border-rose-200/80 inline-flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 block">Semua Pengajuan Toko Telah Ditinjau</span>
                            <p class="text-xs text-slate-400 mt-1">Tidak ada antrean persetujuan toko baru saat ini.</p>
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
