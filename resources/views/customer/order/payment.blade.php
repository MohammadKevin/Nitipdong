<x-app-layout>
    <div class="page-container py-6">
        <nav class="flex text-xs text-slate-400 mb-5 items-center gap-1.5" aria-label="Breadcrumb">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-emerald-600 transition-colors">Pesanan Saya</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-700 font-medium">Pembayaran Pesanan #{{ $order->invoice_number }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs flex items-start gap-4">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">Selesaikan Pembayaran Pesanan</p>
                        <p class="text-xs text-slate-500 mt-0.5">Pesanan #{{ $order->invoice_number }} akan otomatis diproses setelah bukti transfer diverifikasi.</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 pb-3 border-b border-slate-100">Transfer ke Rekening Resmi BelanjaIn</h3>
                    
                    <div class="space-y-3"
                         x-data="{
                            copied: null,
                            copyText(val, key) {
                                navigator.clipboard.writeText(val);
                                this.copied = key;
                                setTimeout(() => this.copied = null, 2000);
                            }
                         }">
                        <div class="flex items-center justify-between border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-8 bg-blue-700 text-white flex items-center justify-center font-extrabold text-xs rounded-md">BCA</div>
                                <div>
                                    <p class="font-mono font-bold text-slate-900 text-sm tracking-wider">123 456 7890</p>
                                    <p class="text-[10px] text-slate-400">a.n. PT BelanjaIn Platform Indonesia</p>
                                </div>
                            </div>
                            <button type="button" @click="copyText('1234567890', 'bca')"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors"
                                    :class="copied === 'bca' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                <span x-text="copied === 'bca' ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-8 bg-orange-600 text-white flex items-center justify-center font-extrabold text-xs rounded-md">BNI</div>
                                <div>
                                    <p class="font-mono font-bold text-slate-900 text-sm tracking-wider">098 765 4321</p>
                                    <p class="text-[10px] text-slate-400">a.n. PT BelanjaIn Platform Indonesia</p>
                                </div>
                            </div>
                            <button type="button" @click="copyText('0987654321', 'bni')"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors"
                                    :class="copied === 'bni' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                <span x-text="copied === 'bni' ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 pb-3 border-b border-slate-100">Upload Bukti Pembayaran</h3>

                    <form action="{{ route('customer.order.payment', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                Foto Struk / Bukti Transfer <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="payment_proof" required accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            @error('payment_proof')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Total Tagihan:</span>
                                <span class="font-bold text-slate-900 text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full btn-primary py-2.5 text-xs sm:text-sm font-semibold flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
