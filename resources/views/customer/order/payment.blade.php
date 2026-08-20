<x-app-layout>
    <div class="page-container py-5"
         x-data="{
            activeTab: '{{ $charge['type'] ?? ($order->payment_method ?: 'qris') }}',
            copied: false,
            copyText(text) {
                navigator.clipboard.writeText(text);
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            }
         }">
        <div class="max-w-2xl mx-auto mb-4">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Selesaikan Pembayaran</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pilih channel pembayaran di bawah untuk memproses pesanan Anda secara instan</p>
        </div>

        @if(session('success'))
            <div class="max-w-2xl mx-auto mb-4 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-2xl mx-auto mb-4 flex items-center gap-2.5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="max-w-2xl mx-auto space-y-4">
            {{-- Order Summary Header --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] text-slate-400 font-mono uppercase block">Nomor Invoice Pesanan</span>
                        <span class="font-bold text-slate-900 text-base font-mono">#{{ $order->invoice_number }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block uppercase font-medium">Total Tagihan</span>
                        <span class="font-extrabold text-cyan-800 text-lg sm:text-xl">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Payment Method Tabs --}}
                <div class="flex border-b border-slate-200 gap-2 overflow-x-auto">
                    <button type="button" @click="activeTab = 'duitku'"
                            :class="activeTab === 'duitku' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-wallet text-cyan-600 text-xs"></i>
                        <span>Duitku Gateway (Sandbox)</span>
                        <span class="px-1.5 py-0.2 bg-cyan-100 text-cyan-800 text-[9px] font-bold rounded">Live API</span>
                    </button>
                    <button type="button" @click="activeTab = 'qris'"
                            :class="activeTab === 'qris' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-qrcode text-xs"></i>
                        <span>QRIS Instan</span>
                    </button>
                    <button type="button" @click="activeTab = 'va'"
                            :class="activeTab.startsWith('va') || activeTab === 'va' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-building-columns text-xs"></i>
                        <span>Virtual Account</span>
                    </button>
                    <button type="button" @click="activeTab = 'manual'"
                            :class="activeTab === 'manual' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-receipt text-xs"></i>
                        <span>Transfer Manual</span>
                    </button>
                </div>

                {{-- 0. TAB DUITKU PAYMENT GATEWAY (SANDBOX) --}}
                <div x-show="activeTab === 'duitku'" class="space-y-4 pt-1"
                     x-data="{
                        isLoadingDuitku: false,
                        payWithDuitku() {
                            this.isLoadingDuitku = true;
                            fetch('{{ route('customer.order.duitku_create', $order) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.isLoadingDuitku = false;
                                if (data.status === 'success' && data.payment_url) {
                                    // Redirect ke Payment Page Duitku
                                    window.location.href = data.payment_url;
                                } else {
                                    alert(data.message || 'Gagal memproses transaksi Duitku Sandbox.');
                                }
                            })
                            .catch(err => {
                                this.isLoadingDuitku = false;
                                alert('Terjadi kesalahan jaringan: ' + err.message);
                            });
                        }
                     }">
                    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-950 text-white rounded-2xl p-5 sm:p-6 space-y-4 shadow-xl border border-cyan-500/30 relative overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-36 h-36 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 border border-cyan-400/40 text-cyan-300 flex items-center justify-center text-lg shadow-inner">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-white">Duitku Payment Gateway</h3>
                                    <p class="text-[11px] text-cyan-200/80">Sandbox Mode Testing (Merchant: DS34393)</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                Official Gateway
                            </span>
                        </div>

                        <p class="text-xs text-slate-300 leading-relaxed">
                            Bayar dengan mudah menggunakan puluhan metode pembayaran terverifikasi otomatis:
                        </p>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                            <div class="p-2.5 bg-white/10 backdrop-blur-xs rounded-xl border border-white/10 flex items-center gap-2">
                                <i class="fa-solid fa-qrcode text-cyan-400"></i>
                                <span class="font-medium text-slate-200">QRIS (Semua E-Wallet)</span>
                            </div>
                            <div class="p-2.5 bg-white/10 backdrop-blur-xs rounded-xl border border-white/10 flex items-center gap-2">
                                <i class="fa-solid fa-building-columns text-blue-400"></i>
                                <span class="font-medium text-slate-200">BCA, Mandiri, BRI, BNI</span>
                            </div>
                            <div class="p-2.5 bg-white/10 backdrop-blur-xs rounded-xl border border-white/10 flex items-center gap-2">
                                <i class="fa-solid fa-mobile-screen-button text-emerald-400"></i>
                                <span class="font-medium text-slate-200">OVO, Dana, ShopeePay</span>
                            </div>
                            <div class="p-2.5 bg-white/10 backdrop-blur-xs rounded-xl border border-white/10 flex items-center gap-2">
                                <i class="fa-solid fa-store text-amber-400"></i>
                                <span class="font-medium text-slate-200">Indomaret & Alfamart</span>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase">Total yang Akan Dibayar:</span>
                                <span class="font-black text-lg text-cyan-300">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>

                            <button type="button" @click="payWithDuitku()"
                                    :disabled="isLoadingDuitku"
                                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-cyan-900/40 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isLoadingDuitku" class="flex items-center gap-2">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    <span>Buka Halaman Pembayaran Duitku</span>
                                </span>
                                <span x-show="isLoadingDuitku" x-cloak class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                    <span>Menghubungkan ke Duitku...</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 1. TAB QRIS --}}
                <div x-show="activeTab === 'qris'" class="space-y-4 pt-1">
                    <div class="text-center bg-slate-50 border border-slate-200/80 rounded-xl p-5 space-y-3">
                        <div class="inline-block p-3 bg-white rounded-xl shadow-xs border border-slate-200">
                            <img src="{{ $charge['qr_image_url'] ?? ('https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode('BELANJAIN-QRIS-' . $order->invoice_number . '-' . $order->total_amount)) }}"
                                 alt="QRIS Code" class="w-48 h-48 sm:w-52 sm:h-52 mx-auto rounded-lg">
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-cyan-100 text-cyan-900 rounded-full text-[11px] font-bold">
                                <i class="fa-solid fa-bolt text-amber-500"></i> QRIS Dinamis Otomatis
                            </span>
                            <p class="text-xs text-slate-500 mt-2">
                                Pindai QR Code di atas menggunakan aplikasi perbankan (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, Dana, ShopeePay, LinkAja).
                            </p>
                        </div>
                    </div>

                    {{-- Sandbox Instant Simulation Button --}}
                    <div class="p-3.5 bg-cyan-50/60 border border-cyan-200 rounded-xl flex items-center justify-between gap-3">
                        <div class="text-xs text-cyan-900">
                            <span class="font-bold block">Mode Simulasi Pembayaran Instan:</span>
                            <span class="text-[11px] text-cyan-700">Gunakan simulator untuk memvalidasi pembayaran QRIS secara langsung tanpa scan manual.</span>
                        </div>
                        <form action="{{ route('customer.order.simulate_payment', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-lg shadow-2xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                                <i class="fa-solid fa-bolt text-amber-300"></i>
                                <span>Bayar Sekarang (Instan)</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 2. TAB VIRTUAL ACCOUNT --}}
                <div x-show="activeTab.startsWith('va') || activeTab === 'va'" x-cloak class="space-y-4 pt-1">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-5 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Bank Tujuan VA</span>
                                <span class="font-bold text-slate-900 text-sm">
                                    {{ $charge['bank_name'] ?? 'Virtual Account BelanjaIn' }}
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-100 text-cyan-800">
                                Verifikasi Otomatis
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-slate-600 block mb-1">Nomor Virtual Account (VA):</span>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-white border border-slate-300 rounded-xl px-4 py-2.5 font-mono font-extrabold text-base text-slate-900 tracking-wider">
                                    {{ $charge['va_number'] ?? ('8800' . str_pad($order->id, 8, '0', STR_PAD_LEFT)) }}
                                </div>
                                <button type="button" @click="copyText('{{ $charge['va_number'] ?? ('8800' . str_pad($order->id, 8, '0', STR_PAD_LEFT)) }}')"
                                        class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-2xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                                    <i :class="copied ? 'fa-solid fa-check text-emerald-400' : 'fa-regular fa-copy'"></i>
                                    <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-[11px] text-slate-500 pt-2">
                            <p class="font-semibold text-slate-700">Panduan Pembayaran Virtual Account:</p>
                            <ol class="list-decimal list-inside space-y-0.5 ml-1">
                                <li>Buka Mobile Banking atau ATM bank Anda.</li>
                                <li>Pilih menu <strong>Transfer / Pembayaran</strong> &gt; <strong>Virtual Account</strong>.</li>
                                <li>Masukkan nomor Virtual Account di atas dan pastikan nominal sesuai.</li>
                                <li>Konfirmasi transaksi. Status pesanan akan otomatis berubah tanpa perlu upload struk!</li>
                            </ol>
                        </div>
                    </div>

                    {{-- Sandbox Simulation Button for VA --}}
                    <div class="p-3.5 bg-cyan-50/60 border border-cyan-200 rounded-xl flex items-center justify-between gap-3">
                        <div class="text-xs text-cyan-900">
                            <span class="font-bold block">Simulasi Transfer Virtual Account:</span>
                            <span class="text-[11px] text-cyan-700">Klik untuk mensimulasikan notifikasi pembayaran VA masuk dari bank.</span>
                        </div>
                        <form action="{{ route('customer.order.simulate_payment', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-lg shadow-2xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                                <i class="fa-solid fa-building-columns"></i>
                                <span>Simulasi VA Lunas</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 3. TAB MANUAL TRANSFER & UPLOAD STRUK --}}
                <div x-show="activeTab === 'manual'" x-cloak class="space-y-4 pt-1">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3"
                         x-data="{
                            copiedBca: false,
                            copiedBni: false,
                            copyNumber(val, type) {
                                navigator.clipboard.writeText(val);
                                if(type === 'bca') { this.copiedBca = true; setTimeout(() => this.copiedBca = false, 2000); }
                                if(type === 'bni') { this.copiedBni = true; setTimeout(() => this.copiedBni = false, 2000); }
                            }
                         }">
                        <p class="text-xs font-bold text-slate-800">Pilihan Rekening Resmi BelanjaIn:</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Bank BCA</span>
                                    <p class="font-mono font-bold text-slate-900 text-xs mt-1.5">8820-1928-3721</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">a.n. PT BelanjaIn Indonesia</p>
                                </div>
                                <button type="button" @click="copyNumber('882019283721', 'bca')" class="text-slate-400 hover:text-cyan-700 p-2 cursor-pointer" title="Salin Rekening">
                                    <i :class="copiedBca ? 'fa-solid fa-check text-cyan-600' : 'fa-regular fa-copy'"></i>
                                </button>
                            </div>

                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200">Bank BNI</span>
                                    <p class="font-mono font-bold text-slate-900 text-xs mt-1.5">0987-6543-2100</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">a.n. PT BelanjaIn Indonesia</p>
                                </div>
                                <button type="button" @click="copyNumber('098765432100', 'bni')" class="text-slate-400 hover:text-cyan-700 p-2 cursor-pointer" title="Salin Rekening">
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
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-800 hover:file:bg-cyan-100 border border-slate-200 rounded-xl p-1">
                            @error('payment_proof')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex items-center justify-between">
                            <a href="{{ route('customer.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-cyan-700">
                                Bayar Nanti (Ke Riwayat)
                            </a>
                            <button type="submit" class="btn-primary text-xs h-9.5 px-5.5 bg-cyan-700 hover:bg-cyan-800 cursor-pointer">
                                Unggah & Konfirmasi
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Bottom Navigation --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('customer.dashboard') }}" class="text-slate-500 hover:text-cyan-700 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Kembali ke Dashboard Saya</span>
                    </a>
                    <span class="text-[11px] text-slate-400">
                        Batas Waktu Pembayaran: <strong class="text-slate-700">{{ now()->addDay()->format('d M Y, 23:59') }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
