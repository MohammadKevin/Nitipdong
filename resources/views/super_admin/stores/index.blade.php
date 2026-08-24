<x-super-admin-layout>
    <x-slot name="title">
        Daftar Toko & Merchant - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Manajemen Toko & Merchant
    </x-slot>

    <!-- HEADER / ACTION BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Manajemen Toko & Merchant
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh toko, mitra penjual, dan status operasional merchant di platform NitipDong.</p>
        </div>
    </div>

    <!-- 4 SUMMARY METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-store"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Total Toko</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($totalStores ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Toko Aktif (Resmi)</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($activeStores ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Menunggu Review</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($pendingStores ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Ditangguhkan (Banned)</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($bannedStores ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- STORES LEDGER TABLE CARD -->
    <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs overflow-hidden">
        
        <!-- SEARCH & FILTER BAR -->
        <div class="p-4 border-b border-slate-100 flex flex-col lg:flex-row justify-between lg:items-center gap-3 bg-slate-50/50">
            
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto pb-1 lg:pb-0 font-mono-num">
                <a href="{{ route('super_admin.stores.index', array_merge(request()->except(['status', 'page']))) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ empty($status) ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Semua ({{ number_format($totalStores ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ route('super_admin.stores.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'active' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Aktif ({{ number_format($activeStores ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ route('super_admin.stores.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Pending ({{ number_format($pendingStores ?? 0, 0, ',', '.') }})
                </a>
                <a href="{{ route('super_admin.stores.index', array_merge(request()->except(['status', 'page']), ['status' => 'banned'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'banned' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Banned ({{ number_format($bannedStores ?? 0, 0, ',', '.') }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('super_admin.stores.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
                @if(!empty($status))
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="relative flex-1 lg:w-72">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama toko, alamat, pemilik..."
                           class="w-full h-8.5 pl-8 pr-3 text-xs rounded-lg bg-white border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400 font-mono-num">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>
                <button type="submit" class="h-8.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs">Cari</button>
                @if($search || $status)
                    <a href="{{ route('super_admin.stores.index') }}" class="h-8.5 px-3 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors border border-slate-200">Reset</a>
                @endif
            </form>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="py-3 px-5 font-semibold">ID</th>
                        <th class="py-3 px-5 font-semibold">Nama Toko & Info</th>
                        <th class="py-3 px-5 font-semibold">Pemilik Akun</th>
                        <th class="py-3 px-5 font-semibold text-center">Status Operasional</th>
                        <th class="py-3 px-5 font-semibold">Tanggal Daftar</th>
                        <th class="py-3 px-5 font-semibold text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stores as $store)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ $store->status === 'rejected' ? 'bg-rose-50/20' : '' }}">
                        <td class="py-3.5 px-5 font-mono-num font-semibold text-slate-400">#{{ $store->id }}</td>
                        
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                @if($store->logo)
                                    <img src="{{ asset('storage/' . $store->logo) }}" class="w-9 h-9 rounded-lg object-cover shrink-0 border border-slate-200 shadow-2xs" alt="Logo">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold border border-blue-200/60 text-xs font-mono-num shrink-0 shadow-2xs">
                                        {{ substr($store->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate max-w-[200px]">{{ $store->name }}</h4>
                                    <p class="text-[11px] text-slate-400 truncate max-w-[220px]">
                                        <i class="fa-solid fa-location-dot text-[9px] mr-1 text-slate-300"></i>{{ $store->address ?? 'Alamat belum diatur' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($store->user->name ?? 'User') }}&background=0f172a&color=fff&size=50" class="w-6 h-6 rounded-full object-cover shrink-0 border border-slate-200" alt="Avatar">
                                <div class="min-w-0">
                                    <span class="text-slate-700 font-medium block truncate max-w-[140px]">{{ $store->user->name ?? 'User Terhapus' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono-num block truncate max-w-[140px]">{{ $store->user->email ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-3.5 px-5 text-center">
                            @if($store->status === 'active' || $store->status === 'approved')
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Disetujui (Aktif)
                                </span>
                            @elseif($store->status === 'pending')
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Verifikasi
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Ditangguhkan (Banned)
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-5 text-slate-400 font-mono-num text-[11px]">
                            {{ $store->created_at ? $store->created_at->translatedFormat('d M Y') : '-' }}
                        </td>

                        <td class="py-3.5 px-5 text-center">
                            <form action="{{ route('super_admin.stores.toggle_ban', $store) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status operasional toko \'{{ $store->name }}\'?');">
                                @csrf
                                @if($store->status === 'approved' || $store->status === 'pending' || $store->status === 'active')
                                    <button type="submit" 
                                            class="px-2.5 py-1 rounded bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-xs transition-colors border border-rose-200 shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                        <span>Suspend Toko</span>
                                    </button>
                                @else
                                    <button type="submit" 
                                            class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition-colors border border-emerald-200 shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Pulihkan (Unban)</span>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-store text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada toko yang sesuai kriteria pencarian.
                        </td>
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
