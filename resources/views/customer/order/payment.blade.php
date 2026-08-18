<x-app-layout>
    <div class="page-container py-5">
        <div class="max-w-2xl mx-auto mb-4">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Instruksi Pembayaran</h1>
            <p class="text-xs text-slate-400 mt-0.5">Selesaikan transfer pembayaran manual dan unggah bukti struk transfer</p>
        </div>

        @if(session('success'))
            <div class="max-w-2xl mx-auto mb-4 flex items-center gap-2.5 px-4 py-3 bg-cyan-50 border border-cyan-200 text-cyan-900 rounded-lg text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-cyan-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-2xl mx-auto space-y-4">
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-card space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] text-slate-400 font-mono uppercase block">Nomor Invoice Pesanan</span>
                        <span class="font-bold text-slate-900 text-base font-mono">#{{ $order->invoice_number }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block uppercase">Total Transfer</span>
                        <span class="font-extrabold text-cyan-700 text-lg sm:text-xl">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3"
                     x-data="{
                        copiedBca: false,
                        copiedBni: false,
                        copyNumber(val, type) {
                            navigator.clipboard.writeText(val);
                            if(type === 'bca') { this.copiedBca = true; setTimeout(() => this.copiedBca = false, 2000); }
                            if(type === 'bni') { this.copiedBni = true; setTimeout(() => this.copiedBni = false, 2000); }
                        }
                     }">
                    <p class="text-xs font-semibold text-slate-800">Pilihan Rekening Resmi BelanjaIn:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 bg-white rounded-lg border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Bank BCA</span>
                                <p class="font-mono font-bold text-slate-900 text-xs mt-1.5">8820-1928-3721</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">a.n. PT BelanjaIn Indonesia</p>
                            </div>
                            <button type="button" @click="copyNumber('882019283721', 'bca')" class="text-slate-400 hover:text-cyan-700 p-2" title="Salin Rekening">
                                <i :class="copiedBca ? 'fa-solid fa-check text-cyan-600' : 'fa-regular fa-copy'"></i>
                            </button>
                        </div>

                        <div class="p-3.5 bg-white rounded-lg border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200">Bank BNI</span>
                                <p class="font-mono font-bold text-slate-900 text-xs mt-1.5">0987-6543-2100</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">a.n. PT BelanjaIn Indonesia</p>
                            </div>
                            <button type="button" @click="copyNumber('098765432100', 'bni')" class="text-slate-400 hover:text-cyan-700 p-2" title="Salin Rekening">
                                <i :class="copiedBni ? 'fa-solid fa-check text-cyan-600' : 'fa-regular fa-copy'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('customer.order.confirm_payment', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-1">
                    @csrf
                    <div>
                        <label for="payment_proof" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Unggah Bukti Struk Transfer <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="payment_proof" id="payment_proof" required accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-800 hover:file:bg-cyan-100 border border-slate-200 rounded-md p-1">
                        @error('payment_proof')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3 flex items-center justify-between">
                        <a href="{{ route('customer.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-cyan-700">
                            Bayar Nanti (Ke Riwayat)
                        </a>
                        <button type="submit" class="btn-primary text-xs h-9.5 px-5.5 bg-cyan-700 hover:bg-cyan-800">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
