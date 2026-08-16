<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pembayaran Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Selesaikan Pembayaran Anda</h3>
                        <p class="text-sm text-slate-500">Invoice: <span class="font-medium text-slate-700">{{ $order->invoice_number }}</span></p>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 mb-8 border border-slate-100">
                    <p class="text-sm text-slate-600 mb-2">Total Pembayaran</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mb-4">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>

                    <div class="border-t border-slate-200 pt-4 mt-4">
                        <p class="text-sm font-medium text-slate-800 mb-2">Transfer ke Rekening Berikut:</p>
                        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-8 bg-blue-600 text-white flex items-center justify-center font-bold text-xs rounded">BCA</div>
                                <div>
                                    <p class="font-bold text-slate-800">123 456 7890</p>
                                    <p class="text-xs text-slate-500">a.n. PT BelanjaIn Indonesia</p>
                                </div>
                            </div>
                            <button type="button" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold" onclick="navigator.clipboard.writeText('1234567890'); alert('Nomor rekening disalin!')">Salin</button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('customer.order.confirm_payment', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload Bukti Transfer</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl bg-white hover:bg-slate-50 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600 justify-center">
                                    <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                        <span>Upload file</span>
                                        <input id="payment_proof" name="payment_proof" type="file" class="sr-only" required accept="image/*">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, JPEG hingga 2MB</p>
                            </div>
                        </div>
                        @error('payment_proof')
                            <p class="text-rose-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Nanti Saja</a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 rounded-xl transition-all">Konfirmasi Pembayaran</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
