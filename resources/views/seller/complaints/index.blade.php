<x-seller-layout>
    <x-slot name="title">
        Pusat Komplain & Retur - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="space-y-6" x-data="{
        showRespondModal: false,
        selectedComplaint: null,
        decision: 'approve',
        responseNote: '',
        actionUrl: '',
        openRespond(complaint, url) {
            this.selectedComplaint = complaint;
            this.actionUrl = url;
            this.decision = 'approve';
            this.responseNote = '';
            this.showRespondModal = true;
        }
    }">
        {{-- Header Banner --}}
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-card border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm border border-rose-200">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pusat Komplain & Pengembalian</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">Tinjau klaim dan kendala produk dari pembeli untuk menjaga reputasi dan kepuasan pelanggan toko Anda.</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Complaints List Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-question text-slate-400 text-sm"></i>
                    <h3 class="font-bold text-sm text-slate-900">Daftar Pengajuan Komplain</h3>
                </div>
                <span class="text-xs text-slate-400">{{ $complaints->total() }} Komplain</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">Pesanan & Pembeli</th>
                            <th class="py-3.5 px-5">Alasan & Kendala</th>
                            <th class="py-3.5 px-5">Foto Bukti</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($complaints as $c)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-slate-900">#{{ $c->order->invoice_number }}</span>
                                <span class="block text-[11px] text-slate-500 font-semibold mt-0.5">{{ $c->user->name }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $c->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-5 max-w-sm">
                                <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[10px] font-bold mb-1">
                                    {{ $c->reason }}
                                </span>
                                <p class="text-xs text-slate-700 leading-relaxed">{{ $c->description }}</p>
                                @if($c->seller_response)
                                    <div class="mt-2 p-2 rounded-lg bg-slate-50 border border-slate-200 text-[11px]">
                                        <strong class="text-slate-700">Respon Anda:</strong>
                                        <p class="text-slate-600 mt-0.5">{{ $c->seller_response }}</p>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                @if($c->photo_url)
                                    <a href="{{ asset('storage/' . $c->photo_url) }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden border border-slate-200 hover:opacity-80 transition-opacity">
                                        <img src="{{ asset('storage/' . $c->photo_url) }}" alt="Bukti" class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">Tidak ada foto</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                @if($c->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Perlu Ditanggapi
                                    </span>
                                @elseif($c->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Disetujui (Refund)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if($c->status === 'pending')
                                    <button type="button"
                                            @click="openRespond({{ $c->toJson() }}, '{{ route('seller.complaints.respond', $c) }}')"
                                            class="btn-primary text-xs h-7.5 px-3 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white font-semibold cursor-pointer">
                                        Tanggapi Klaim
                                    </button>
                                @else
                                    <span class="text-[11px] text-slate-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-shield-check text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-semibold text-slate-600">Tidak Ada Komplain Masuk</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Semua pesanan berjalan lancar tanpa ada komplain dari pembeli.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($complaints->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Tanggapi Komplain --}}
        <div x-show="showRespondModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showRespondModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-reply text-cyan-600"></i> Tanggapi Komplain Pembeli
                    </h3>
                    <button @click="showRespondModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="actionUrl" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Keputusan Penjual</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2 transition-all"
                                   :class="decision === 'approve' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                <input type="radio" name="decision" value="approve" x-model="decision" class="text-emerald-600">
                                <span>Setujui Retur / Refund</span>
                            </label>
                            <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2 transition-all"
                                   :class="decision === 'reject' ? 'border-rose-500 bg-rose-50 text-rose-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                <input type="radio" name="decision" value="reject" x-model="decision" class="text-rose-600">
                                <span>Tolak Komplain</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan / Respon untuk Pembeli</label>
                        <textarea name="seller_response" x-model="responseNote" rows="3" required
                                  placeholder="Berikan penjelasan respon Anda atau instruksi pengembalian barang..."
                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showRespondModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs cursor-pointer">
                            Kirim Keputusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-seller-layout>
