<x-super-admin-layout>
    <x-slot name="title">
        Manajemen Payout & Penarikan Dana - {{ config('app.name', 'SakserShop') }}
    </x-slot>

    <div class="space-y-6" x-data="{
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
        {{-- Header Banner --}}
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-card border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm border border-emerald-200">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Payout & Penarikan Dana Toko</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">Verifikasi dan transfer pencairan saldo dompet ke rekening bank pemilik toko.</p>
            </div>
            <div>
                <a href="{{ route('super_admin.reports.revenue.export') }}" class="btn-primary text-xs h-9.5 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-2 shadow-xs transition-all cursor-pointer">
                    <i class="fa-solid fa-file-excel text-xs"></i>
                    <span>Ekspor Laporan Keuntungan (5%)</span>
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Metrics Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-card">
                <span class="text-xs text-slate-500 font-medium">Permohonan Pending</span>
                <h3 class="text-xl font-extrabold {{ $pendingCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">
                    {{ $pendingCount }} Pengajuan
                </h3>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-card">
                <span class="text-xs text-slate-500 font-medium">Payout Disetujui</span>
                <h3 class="text-xl font-extrabold text-emerald-700 mt-1">{{ $approvedCount }} Transaksi</h3>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-card">
                <span class="text-xs text-slate-500 font-medium">Total Dana Ditransfer</span>
                <h3 class="text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($totalPaidOut, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-card">
                <span class="text-xs text-slate-500 font-medium">Permohonan Ditolak</span>
                <h3 class="text-xl font-extrabold text-rose-600 mt-1">{{ $rejectedCount }} Ditolak</h3>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'all']) }}"
                       class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $status === 'all' ? 'bg-cyan-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Semua
                    </a>
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'pending']) }}"
                       class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $status === 'pending' ? 'bg-cyan-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'approved']) }}"
                       class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $status === 'approved' ? 'bg-cyan-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('super_admin.withdrawals.index', ['status' => 'rejected']) }}"
                       class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $status === 'rejected' ? 'bg-cyan-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Ditolak
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">ID & Waktu</th>
                            <th class="py-3.5 px-5">Toko & Pemilik</th>
                            <th class="py-3.5 px-5">Jumlah Payout</th>
                            <th class="py-3.5 px-5">Rekening Tujuan</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-slate-900">#WD-{{ $w->id }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $w->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-900 block">{{ $w->store->name ?? 'Toko' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $w->store->user->email ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-5 font-extrabold text-sm text-slate-900">
                                Rp {{ number_format($w->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-800">{{ $w->bank_name }}</span>
                                <span class="block font-mono text-[11px] text-slate-600 font-semibold">{{ $w->account_number }}</span>
                                <span class="block text-[10px] text-slate-400">a.n. {{ $w->account_holder }}</span>
                            </td>
                            <td class="py-4 px-5">
                                @if($w->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Payout
                                    </span>
                                @elseif($w->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Selesai Ditransfer
                                    </span>
                                    @if($w->proof_url)
                                        <a href="{{ asset('storage/' . $w->proof_url) }}" target="_blank" class="block text-[10px] text-cyan-700 hover:underline mt-0.5">
                                            Bukti Transfer
                                        </a>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if($w->status === 'pending')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                @click="openApprove({{ $w->toJson() }}, '{{ route('super_admin.withdrawals.approve', $w) }}')"
                                                class="btn-primary text-xs h-7 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold cursor-pointer">
                                            Approve Transfer
                                        </button>
                                        <button type="button"
                                                @click="openReject({{ $w->toJson() }}, '{{ route('super_admin.withdrawals.reject', $w) }}')"
                                                class="text-xs h-7 px-2.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold cursor-pointer">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Selesai</span>
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
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Konfirmasi Transfer Payout
                </h3>
                <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Toko:</span>
                            <strong class="text-slate-900" x-text="selectedWd?.store?.name"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nominal Transfer:</span>
                            <strong class="text-emerald-700 text-sm">Rp <span x-text="Number(selectedWd?.amount || 0).toLocaleString('id-ID')"></span></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Rekening Tujuan:</span>
                            <span class="font-mono text-slate-800 font-bold"><span x-text="selectedWd?.bank_name"></span> - <span x-text="selectedWd?.account_number"></span> (<span x-text="selectedWd?.account_holder"></span>)</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Unggah Bukti Transfer Bank (Opsional)</label>
                        <input type="file" name="proof" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Admin (Opsional)</label>
                        <textarea name="admin_note" rows="2" placeholder="Contoh: Transfer via KlikBCA No. Ref 9281928"
                                  class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:border-emerald-600"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showApproveModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-xs">Konfirmasi Selesai</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Reject Payout --}}
        <div x-show="showRejectModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showRejectModal = false"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-rose-600"></i> Tolak Penarikan Dana
                </h3>
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <p class="text-slate-600">Saldo sebesar <strong>Rp <span x-text="Number(selectedWd?.amount || 0).toLocaleString('id-ID')"></span></strong> akan otomatis dikembalikan ke dompet toko.</p>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alasan Penolakan</label>
                        <textarea name="admin_note" rows="3" required placeholder="Contoh: Nomor rekening tidak cocok dengan nama pemilik buku tabungan..."
                                  class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:border-rose-600"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-xs">Tolak Penarikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
