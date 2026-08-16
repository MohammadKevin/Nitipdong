<x-app-layout>
    <div class="bg-[#f5f5f5] min-h-screen py-6">
        <div class="max-w-[900px] mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-[#06b6d4] transition-colors">Pesanan Saya</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-slate-600">Detail Pembayaran</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

                {{-- Left: Bank Transfer Info --}}
                <div class="lg:col-span-3 space-y-4">

                    {{-- Countdown / Urgency --}}
                    <div class="bg-white shadow-sm p-5 flex items-start gap-4">
                        <div class="w-10 h-10 bg-[#ecfeff] text-[#06b6d4] rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 text-sm">Selesaikan Pembayaran Sebelum Batas Waktu</p>
                            <p class="text-xs text-slate-500 mt-0.5">Pesanan #{{ $order->invoice_number }} akan otomatis dibatalkan jika belum dibayar.</p>
                            <div class="mt-3 inline-flex items-center gap-2 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-sm">
                                <span class="text-amber-700 font-bold text-sm">23 : 45 : 12</span>
                                <span class="text-amber-600 text-xs">tersisa</span>
                            </div>
                        </div>
                    </div>

                    {{-- Transfer To --}}
                    <div class="bg-white shadow-sm">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-800 text-sm">Transfer ke Rekening Berikut</h3>
                        </div>
                        <div class="p-5 space-y-3">
                            {{-- Bank Option 1 --}}
                            <div class="flex items-center justify-between border border-slate-200 hover:border-[#06b6d4] rounded-sm p-4 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-9 bg-blue-700 text-white flex items-center justify-center font-extrabold text-sm rounded-sm">BCA</div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm tracking-widest">123 456 7890</p>
                                        <p class="text-xs text-slate-500 mt-0.5">a.n. PT BelanjaIn Indonesia</p>
                                    </div>
                                </div>
                                <button type="button"
                                    onclick="navigator.clipboard.writeText('1234567890'); this.innerText='Tersalin!'; setTimeout(() => this.innerText='Salin', 2000)"
                                    class="text-xs text-[#06b6d4] font-semibold border border-[#06b6d4] px-3 py-1 rounded-sm hover:bg-[#ecfeff] transition-colors">
                                    Salin
                                </button>
                            </div>
                            {{-- Bank Option 2 --}}
                            <div class="flex items-center justify-between border border-slate-200 hover:border-[#06b6d4] rounded-sm p-4 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-9 bg-[#f97316] text-white flex items-center justify-center font-extrabold text-sm rounded-sm">BNI</div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm tracking-widest">098 765 4321</p>
                                        <p class="text-xs text-slate-500 mt-0.5">a.n. PT BelanjaIn Indonesia</p>
                                    </div>
                                </div>
                                <button type="button"
                                    onclick="navigator.clipboard.writeText('0987654321'); this.innerText='Tersalin!'; setTimeout(() => this.innerText='Salin', 2000)"
                                    class="text-xs text-[#06b6d4] font-semibold border border-[#06b6d4] px-3 py-1 rounded-sm hover:bg-[#ecfeff] transition-colors">
                                    Salin
                                </button>
                            </div>
                        </div>

                        {{-- Step by Step Guide --}}
                        <div class="mx-5 mb-5 bg-[#f9f9f9] border border-slate-100 rounded-sm p-4">
                            <p class="text-xs font-semibold text-slate-600 mb-3">Cara Pembayaran Transfer Bank:</p>
                            <ol class="space-y-2 text-xs text-slate-600">
                                <li class="flex items-start gap-2"><span class="w-4 h-4 bg-[#06b6d4] text-white rounded-full flex items-center justify-center font-bold shrink-0 text-[10px]">1</span>Buka aplikasi m-Banking atau kunjungi ATM terdekat.</li>
                                <li class="flex items-start gap-2"><span class="w-4 h-4 bg-[#06b6d4] text-white rounded-full flex items-center justify-center font-bold shrink-0 text-[10px]">2</span>Pilih menu <strong>Transfer</strong> dan masukkan nomor rekening tujuan di atas.</li>
                                <li class="flex items-start gap-2"><span class="w-4 h-4 bg-[#06b6d4] text-white rounded-full flex items-center justify-center font-bold shrink-0 text-[10px]">3</span>Masukkan nominal transfer sesuai total tagihan (termasuk angka unik jika ada).</li>
                                <li class="flex items-start gap-2"><span class="w-4 h-4 bg-[#06b6d4] text-white rounded-full flex items-center justify-center font-bold shrink-0 text-[10px]">4</span>Ambil screenshot / foto bukti transfer.</li>
                                <li class="flex items-start gap-2"><span class="w-4 h-4 bg-[#06b6d4] text-white rounded-full flex items-center justify-center font-bold shrink-0 text-[10px]">5</span>Upload bukti transfer di formulir sebelah kanan ini.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Right: Order Summary + Upload Form --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Order Summary --}}
                    <div class="bg-white shadow-sm">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-800 text-sm">Rincian Pesanan</h3>
                        </div>
                        <div class="p-5 space-y-2 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>No. Invoice</span>
                                <span class="font-medium text-slate-800">{{ $order->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Total Produk</span>
                                <span class="font-medium text-slate-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Ongkos Kirim</span>
                                <span class="font-medium text-emerald-600">Gratis</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Layanan</span>
                                <span class="font-medium text-slate-800">Rp1.000</span>
                            </div>
                            <div class="border-t border-slate-100 pt-3 mt-3 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Total Bayar</span>
                                <span class="text-xl font-bold text-[#06b6d4]">Rp{{ number_format($order->total_amount + 1000, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Proof --}}
                    <div class="bg-white shadow-sm">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-800 text-sm">Upload Bukti Transfer</h3>
                        </div>
                        <form action="{{ route('customer.order.confirm_payment', $order) }}" method="POST" enctype="multipart/form-data" class="p-5">
                            @csrf
                            <div x-data="{ preview: null }" class="space-y-4">
                                {{-- Dropzone --}}
                                <label for="payment_proof"
                                    class="flex flex-col items-center gap-3 border-2 border-dashed border-slate-300 hover:border-[#06b6d4] rounded-sm p-6 cursor-pointer transition-colors group">
                                    <template x-if="!preview">
                                        <div class="text-center">
                                            <svg class="w-10 h-10 text-slate-300 group-hover:text-[#06b6d4] mx-auto mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="text-sm text-slate-500">Klik atau seret file ke sini</p>
                                            <p class="text-xs text-slate-400 mt-1">PNG, JPG, JPEG — Maks. 2MB</p>
                                        </div>
                                    </template>
                                    <template x-if="preview">
                                        <div class="text-center">
                                            <img :src="preview" class="max-h-32 mx-auto rounded-sm object-contain mb-2">
                                            <p class="text-xs text-emerald-600 font-medium">✓ Gambar dipilih. Klik untuk mengganti.</p>
                                        </div>
                                    </template>
                                    <input id="payment_proof" name="payment_proof" type="file" class="hidden" required accept="image/*"
                                        @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); }">
                                </label>
                                @error('payment_proof')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror

                                {{-- Actions --}}
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('customer.dashboard') }}"
                                        class="flex-1 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-sm transition-colors text-center">
                                        Bayar Nanti
                                    </a>
                                    <button type="submit"
                                        class="flex-1 py-2.5 bg-[#06b6d4] hover:bg-[#0891b2] text-white text-sm font-semibold rounded-sm transition-colors">
                                        Konfirmasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
