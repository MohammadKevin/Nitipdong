<x-admin-layout>
    <x-slot name="title">
        Resolusi Komplain &amp; Sengketa - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Pusat Resolusi Komplain
    </x-slot>

    <!-- HEADER BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Pusat Resolusi Komplain &amp; Sengketa
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Mediasi kendala pesanan, verifikasi bukti unboxing, dan penentuan pengembalian dana transaksi.</p>
        </div>
    </div>

    <!-- 4 SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Komplain</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($totalCount, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Menunggu Mediasi</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($pendingCount, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Disetujui (Refund)</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($approvedCount, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Ditolak</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($rejectedCount, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- MAIN CARD: FILTER & TABLE -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden" x-data="{ selectedComplaint: null, showResolveModal: false, showPhotoModal: false, activePhoto: '' }">
        
        <!-- Filter Tabs & Search -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <!-- Filter Status Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                <a href="{{ route('admin.complaints.index', ['status' => 'all', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'all' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Semua ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.complaints.index', ['status' => 'pending', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Menunggu ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.complaints.index', ['status' => 'approved', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('admin.complaints.index', ['status' => 'rejected', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Ditolak ({{ $rejectedCount }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.complaints.index') }}" method="GET" class="relative">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" value="{{ $search }}" class="w-full lg:w-72 h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400" placeholder="Cari invoice, pembeli, toko...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </form>
        </div>

        <!-- Table Complaints -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Invoice &amp; Tanggal</th>
                        <th class="px-5 py-3.5">Pihak Sengketa</th>
                        <th class="px-5 py-3.5">Alasan &amp; Bukti</th>
                        <th class="px-5 py-3.5">Status Mediasi</th>
                        <th class="px-5 py-3.5 text-center">Aksi Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($complaints as $c)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- Invoice & Date -->
                        <td class="px-5 py-4 align-top">
                            <span class="font-bold text-slate-900 block text-xs">#{{ $c->order->invoice_number ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">{{ $c->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                            <span class="text-[11px] font-semibold text-blue-600 mt-1 block">Rp {{ number_format($c->order->total_amount ?? 0, 0, ',', '.') }}</span>
                        </td>

                        <!-- Pihak Sengketa -->
                        <td class="px-5 py-4 align-top">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">Buyer</span>
                                    <span class="font-medium text-slate-800 truncate">{{ $c->user->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">Toko</span>
                                    <span class="font-medium text-slate-800 truncate">{{ $c->store->name ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Alasan & Bukti -->
                        <td class="px-5 py-4 align-top max-w-xs">
                            <p class="font-semibold text-slate-800 text-xs">{{ $c->reason }}</p>
                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $c->description }}</p>
                            
                            @if($c->photo_url)
                            <button type="button" 
                                    @click="activePhoto = '{{ asset('storage/' . $c->photo_url) }}'; showPhotoModal = true"
                                    class="mt-2 text-[11px] font-semibold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                                Lihat Foto Bukti
                            </button>
                            @endif
                        </td>

                        <!-- Status Mediasi -->
                        <td class="px-5 py-4 align-top">
                            @if($c->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Mediasi
                                </span>
                            @elseif($c->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Disetujui (Refund)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Ditolak
                                </span>
                            @endif

                            @if($c->admin_notes)
                            <div class="mt-2 p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px] text-slate-600">
                                <span class="font-bold text-slate-700 block">Catatan Admin:</span>
                                {{ $c->admin_notes }}
                            </div>
                            @endif
                        </td>

                        <!-- Aksi Keputusan -->
                        <td class="px-5 py-4 align-top text-center">
                            @if($c->status === 'pending')
                            <button type="button" 
                                    @click="selectedComplaint = {{ json_encode($c) }}; showResolveModal = true"
                                    class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                </svg>
                                Beri Keputusan
                            </button>
                            @else
                            <span class="text-xs text-slate-400 italic">Sengketa Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 block">Tidak Ada Komplain Ditemukan</span>
                            <p class="text-xs text-slate-400 mt-1">Semua transaksi platform berjalan lancar tanpa sengketa.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($complaints->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $complaints->links() }}
        </div>
        @endif

        <!-- MODAL PUTUSAN MEDIASI -->
        <div x-show="showResolveModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.away="showResolveModal = false" 
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Keputusan Mediasi Sengketa</h3>
                    <button @click="showResolveModal = false" class="text-slate-400 hover:text-slate-700 text-sm font-bold">✕</button>
                </div>

                <form :action="`/admin/complaints/${selectedComplaint?.id}/resolve`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Pilih Keputusan Resmi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 cursor-pointer flex items-center gap-2.5">
                                <input type="radio" name="decision" value="approve" checked class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <p class="text-xs font-bold text-emerald-800">Setujui Refund</p>
                                    <p class="text-[10px] text-emerald-600">Kembalikan dana ke pembeli</p>
                                </div>
                            </label>
                            <label class="p-3 rounded-xl border border-rose-200 bg-rose-50/50 hover:bg-rose-50 cursor-pointer flex items-center gap-2.5">
                                <input type="radio" name="decision" value="reject" class="text-rose-600 focus:ring-rose-500">
                                <div>
                                    <p class="text-xs font-bold text-rose-800">Tolak Komplain</p>
                                    <p class="text-[10px] text-rose-600">Teruskan dana ke penjual</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Mediasi Admin (Wajib)</label>
                        <textarea name="admin_notes" rows="4" required class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder:text-slate-400" placeholder="Tuliskan pertimbangan dan alasan keputusan mediasi..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="showResolveModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-colors">
                            Kirim Keputusan Resmi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL PHOTO LIGHTBOX -->
        <div x-show="showPhotoModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="showPhotoModal = false" class="max-w-2xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl p-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Foto Bukti Unboxing / Barang</span>
                    <button @click="showPhotoModal = false" class="text-slate-400 hover:text-slate-700 text-sm font-bold">✕</button>
                </div>
                <div class="max-h-[70vh] overflow-auto flex items-center justify-center bg-slate-900 rounded-xl p-2">
                    <img :src="activePhoto" alt="Bukti Komplain" class="max-h-[65vh] object-contain rounded-lg">
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
