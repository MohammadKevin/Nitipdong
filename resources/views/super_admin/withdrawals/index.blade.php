<x-super-admin-layout>
    <x-slot name="title">
        Manajemen Payout & Penarikan Dana - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Payout & Penarikan Toko
    </x-slot>

    <div class="space-y-5" x-data="{
        showApproveModal: false,
        showRejectModal: false,
        selectedWd: null,
        actionUrl: '',
        openApprove(wd, url) {
            this.selectedWd = wd;
            this.actionUrl = url;
            this.showApproveModal = true;
        },
        openReject(wd, url) {
            this.selectedWd = wd;
            this.actionUrl = url;
            this.showRejectModal = true;
        }
    }">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Payout & Penarikan Dana Toko</h1>
                <p class="text-xs text-slate-500 mt-0.5">Verifikasi dan transfer pencairan saldo dompet ke rekening bank pemilik toko.</p>
            </div>
            <div>
                <a href="{{ route('super_admin.reports.revenue.export') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition-colors">
                    <i class="fa-solid fa-file-excel text-[11px]"></i>
                    <span>Ekspor Laporan Payout</span>
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-2.5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-900 rounded-lg text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Metrics Summary Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/70 flex items-center justify-center text-base shrink-0 font-mono-num">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono-num truncate">Permohonan Pending</span>
                    <h3 class="text-lg sm:text-xl font-bold {{ $pendingCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-0.5 font-mono-num truncate">
                        {{ number_format($pendingCount, 0, ',', '.') }} Pengajuan
                    </h3>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/70 flex items-center justify-center text-base shrink-0 font-mono-num">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono-num truncate">Payout Disetujui</span>
                    <h3 class="text-lg sm:text-xl font-bold text-emerald-700 mt-0.5 font-mono-num truncate">{{ number_format($approvedCount, 0, ',', '.') }} Transaksi</h3>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/70 flex items-center justify-center text-base shrink-0 font-mono-num">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono-num truncate">Total Dana Ditransfer</span>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate" title="Rp {{ number_format($totalPaidOut, 0, ',', '.') }}">Rp {{ number_format($totalPaidOut, 0, ',', '.') }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/70 flex items-center justify-center text-base shrink-0 font-mono-num">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono-num truncate">Permohonan Ditolak</span>
                    <h3 class="text-lg sm:text-xl font-bold text-rose-600 mt-0.5 font-mono-num truncate">{{ number_format($rejectedCount, 0, ',', '.') }} Ditolak</h3>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap bg-slate-50/50">
                <div class="flex items-center gap-1 overflow-x-auto text-xs font-mono-num">
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'all']) }}"
                       class="px-3 py-1 rounded-md font-semibold transition-colors {{ $status === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                        Semua
                    </a>
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'pending']) }}"
                       class="px-3 py-1 rounded-md font-semibold transition-colors {{ $status === 'pending' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                        Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'approved']) }}"
                       class="px-3 py-1 rounded-md font-semibold transition-colors {{ $status === 'approved' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'rejected']) }}"
                       class="px-3 py-1 rounded-md font-semibold transition-colors {{ $status === 'rejected' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                        Ditolak
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider font-mono-num">
                        <tr>
                            <th class="py-3 px-5">ID & Waktu</th>
                            <th class="py-3 px-5">Toko & Pemilik</th>
                            <th class="py-3 px-5">Jumlah Payout</th>
                            <th class="py-3 px-5">Rekening Tujuan</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <span class="font-mono-num font-semibold text-slate-900">#WD-{{ $w->id }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono-num mt-0.5">{{ $w->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="font-semibold text-slate-900 block">{{ $w->store->name ?? 'Toko' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono-num">{{ $w->store->user->email ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-bold text-sm text-slate-900 font-mono-num">
                                Rp {{ number_format($w->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="font-semibold text-slate-800">{{ $w->bank_name }}</span>
                                <span class="block font-mono-num text-[11px] text-slate-600 font-semibold">{{ $w->account_number }}</span>
                                <span class="block text-[10px] text-slate-400">a.n. {{ $w->account_holder }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                @if($w->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 font-mono-num">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Payout
                                    </span>
                                @elseif($w->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono-num">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Selesai Ditransfer
                                    </span>
                                    @if($w->proof_url)
                                        <a href="{{ asset('storage/' . $w->proof_url) }}" target="_blank" class="block text-[10px] text-blue-600 hover:underline mt-0.5">
                                            Bukti Transfer
                                        </a>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 font-mono-num">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                @if($w->status === 'pending')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                @click="openApprove({{ $w->toJson() }}, '{{ route('super_admin.withdrawals.approve', $w) }}')"
                                                class="text-xs h-7 px-3 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-xs cursor-pointer">
                                            Setujui
                                        </button>
                                        <button type="button"
                                                @click="openReject({{ $w->toJson() }}, '{{ route('super_admin.withdrawals.reject', $w) }}')"
                                                class="text-xs h-7 px-2.5 rounded-md border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold cursor-pointer">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 font-mono-num">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-money-bill-transfer text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-semibold text-slate-600">Tidak Ada Permohonan Penarikan Dana</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($withdrawals->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Approve Payout --}}
        <div x-show="showApproveModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showApproveModal = false"
                 class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Konfirmasi Transfer Payout
                </h3>
                <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Toko:</span>
                            <strong class="text-slate-900" x-text="selectedWd?.store?.name"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nominal Transfer:</span>
                            <strong class="text-emerald-700 text-sm font-mono-num">Rp <span x-text="Number(selectedWd?.amount || 0).toLocaleString('id-ID')"></span></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Rekening Tujuan:</span>
                            <span class="font-mono-num text-slate-800 font-bold"><span x-text="selectedWd?.bank_name"></span> - <span x-text="selectedWd?.account_number"></span> (<span x-text="selectedWd?.account_holder"></span>)</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Unggah Bukti Transfer Bank (Opsional)</label>
                        <input type="file" name="proof" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Admin (Opsional)</label>
                        <textarea name="admin_note" rows="2" placeholder="Contoh: Transfer via KlikBCA No. Ref 9281928"
                                  class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:border-emerald-600"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showApproveModal = false" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-xs">Konfirmasi Selesai</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Reject Payout --}}
        <div x-show="showRejectModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showRejectModal = false"
                 class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-rose-600"></i> Tolak Penarikan Dana
                </h3>
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <p class="text-slate-600">Saldo sebesar <strong class="font-mono-num">Rp <span x-text="Number(selectedWd?.amount || 0).toLocaleString('id-ID')"></span></strong> akan otomatis dikembalikan ke dompet toko.</p>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alasan Penolakan</label>
                        <textarea name="admin_note" rows="3" required placeholder="Contoh: Nomor rekening tidak cocok dengan nama pemilik buku tabungan..."
                                  class="w-full rounded-lg border border-slate-300 text-xs p-2.5 focus:border-rose-600"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-xs">Tolak Penarikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
