@php
    $rawMethod = $order->payment_method ?: 'qris';
    if (str_starts_with($rawMethod, 'va_')) {
        $defaultTab = 'va';
    } elseif ($rawMethod === 'manual_transfer' || $rawMethod === 'manual') {
        $defaultTab = 'manual';
    } else {
        $defaultTab = 'qris';
    }
@endphp

<x-app-layout>
    {{-- Midtrans Snap Script (Sandbox) --}}
    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-nNuy0AuFjI35ym6k"></script>
    @endpush

    <div class="page-container py-5"
         x-data="{
            activeTab: '{{ $defaultTab }}',
            selectedVaBank: '{{ str_starts_with($rawMethod, 'va_') ? $rawMethod : 'va_bca' }}',
            vaNumbers: {
                'va_bca': '880199{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}',
                'va_mandiri': '880299{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}',
                'va_bni': '880399{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}',
                'va_bri': '880499{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}',
            },
            vaBankNames: {
                'va_bca': 'Bank Central Asia (BCA)',
                'va_mandiri': 'Bank Mandiri',
                'va_bni': 'Bank Negara Indonesia (BNI)',
                'va_bri': 'Bank Rakyat Indonesia (BRI / BRIVA)',
            },
            copied: false,
            isLoadingMidtrans: false,
            copyText(text) {
                navigator.clipboard.writeText(text);
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            },
            payWithMidtrans() {
                this.isLoadingMidtrans = true;
                fetch('{{ route('customer.order.midtrans_snap_token', $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.isLoadingMidtrans = false;
                    const token = data.snap_token || data.token;
                    if (data.status === 'success' && token) {
                        if (window.snap) {
                            window.snap.pay(token, {
                                onSuccess: function(result) {
                                    window.location.href = '{{ route('customer.dashboard') }}?payment=success';
                                },
                                onPending: function(result) {
                                    window.location.href = '{{ route('customer.dashboard') }}?payment=pending';
                                },
                                onError: function(result) {
                                    if (window.toast) {
                                        window.toast.error('Pembayaran gagal atau dibatalkan.');
                                    } else {
                                        alert('Pembayaran gagal atau dibatalkan!');
                                    }
                                },
                                onClose: function() {
                                    if (window.toast) {
                                        window.toast.info('Popup pembayaran ditutup. Anda dapat melanjutkan pembayaran nanti.');
                                    }
                                }
                            });
                        } else {
                            if (window.toast) {
                                window.toast.error('Midtrans Snap SDK gagal dimuat. Silakan periksa koneksi internet Anda.');
                            } else {
                                alert('Midtrans Snap SDK gagal dimuat. Silakan periksa koneksi internet Anda.');
                            }
                        }
                    } else {
                        const errMsg = data.message || 'Gagal memproses transaksi Midtrans Snap.';
                        if (window.toast) {
                            window.toast.error(errMsg);
                        } else {
                            alert(errMsg);
                        }
                    }
                })
                .catch(err => {
                    this.isLoadingMidtrans = false;
                    const errText = 'Terjadi kesalahan jaringan: ' + err.message;
                    if (window.toast) {
                        window.toast.error(errText);
                    } else {
                        alert(errText);
                    }
                });
            }
         }">
        <div class="max-w-2xl mx-auto mb-4">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Selesaikan Pembayaran</h1>
            <p class="text-xs text-slate-500 mt-0.5">Selesaikan pembayaran sesuai metode yang Anda pilih untuk memproses pesanan secara instan</p>
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
            {{-- Primary Midtrans Snap Instant Action Box --}}
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-950 text-white rounded-2xl p-5 sm:p-6 shadow-xl border border-cyan-500/30 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-amber-300"></i> Midtrans Snap Gateway
                        </span>
                    </div>
                    <h3 class="font-extrabold text-sm text-white mt-1">Bayar Otomatis 24 Jam Instan</h3>
                    <p class="text-xs text-slate-300">Buka pop-up resmi Midtrans untuk bayar via QRIS, BCA, Mandiri, BRI, BNI, GoPay, OVO, ShopeePay, atau Minimarket.</p>
                </div>
                <button type="button" @click="payWithMidtrans()"
                        :disabled="isLoadingMidtrans"
                        class="px-6 py-3.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-cyan-900/40 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 active:scale-98 shrink-0">
                    <span x-show="!isLoadingMidtrans" class="flex items-center gap-2">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Buka Pop-up Midtrans</span>
                    </span>
                    <span x-show="isLoadingMidtrans" x-cloak class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Memuat Snap...</span>
                    </span>
                </button>
            </div>

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
                    <button type="button" @click="activeTab = 'qris'"
                            :class="activeTab === 'qris' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-qrcode text-xs"></i>
                        <span>QRIS Instan</span>
                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[9px] font-bold rounded">Otomatis</span>
                    </button>
                    <button type="button" @click="activeTab = 'va'"
                            :class="activeTab === 'va' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-building-columns text-xs"></i>
                        <span>Virtual Account (BCA, Mandiri, BNI, BRI)</span>
                    </button>
                    <button type="button" @click="activeTab = 'manual'"
                            :class="activeTab === 'manual' ? 'border-cyan-600 text-cyan-800 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="pb-2.5 px-3 text-xs flex items-center gap-1.5 transition-all shrink-0 cursor-pointer">
                        <i class="fa-solid fa-receipt text-xs"></i>
                        <span>Transfer Bank Manual</span>
                    </button>
                </div>

                {{-- 1. TAB QRIS INSTAN (LANGSUNG MUNCUL JIKA PILIH QRIS) --}}
                <div x-show="activeTab === 'qris'" class="space-y-4 pt-1">
                    <div class="text-center bg-slate-50 border border-slate-200/80 rounded-2xl p-5 sm:p-6 space-y-4 shadow-xs">
                        <div class="inline-block p-4 bg-white rounded-2xl shadow-sm border border-slate-200">
                            <img src="{{ $charge['qr_image_url'] ?? ('https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' . urlencode('NITIPDONG-QRIS-' . $order->invoice_number . '-' . $order->total_amount)) }}"
                                 alt="QRIS Code" class="w-56 h-56 sm:w-64 sm:h-64 mx-auto rounded-lg">
                        </div>
                        
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-900 rounded-full text-xs font-extrabold">
                                <i class="fa-solid fa-bolt text-amber-500"></i> QRIS Standar Bank Indonesia (ASPI)
                            </span>
                            <p class="text-xs text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                                Pindai QR Code di atas menggunakan aplikasi mobile banking (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, DANA, ShopeePay, LinkAja).
                            </p>
                        </div>

                        <div class="p-3.5 bg-white rounded-xl border border-slate-200 max-w-sm mx-auto text-xs space-y-1.5 text-left">
                            <div class="flex justify-between text-slate-500">
                                <span>Merchant Resmi:</span>
                                <span class="font-bold text-slate-800">NitipDong Official</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Total Tagihan:</span>
                                <span class="font-extrabold text-cyan-800 text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500 text-[11px] pt-1.5 border-t border-slate-100">
                                <span>Status Verifikasi:</span>
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-circle-notch fa-spin text-[10px]"></i> Menunggu Pembayaran
                                </span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="button" @click="payWithMidtrans()"
                                    class="w-full sm:w-auto px-6 py-2.5 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 mx-auto cursor-pointer">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                <span>Buka Popup QRIS Midtrans</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. TAB VIRTUAL ACCOUNT (LANGSUNG MUNCUL DENGAN BANK TERPILIH) --}}
                <div x-show="activeTab === 'va'" x-cloak class="space-y-4 pt-1">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                        {{-- Bank Selector Tabs --}}
                        <div>
                            <span class="text-xs font-semibold text-slate-600 block mb-2">Pilih Bank Virtual Account:</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button type="button" @click="selectedVaBank = 'va_bca'"
                                        :class="selectedVaBank === 'va_bca' ? 'bg-blue-600 text-white border-blue-600 shadow-xs font-bold' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 font-medium'"
                                        class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>BCA</span>
                                </button>
                                <button type="button" @click="selectedVaBank = 'va_mandiri'"
                                        :class="selectedVaBank === 'va_mandiri' ? 'bg-amber-600 text-white border-amber-600 shadow-xs font-bold' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 font-medium'"
                                        class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>Mandiri</span>
                                </button>
                                <button type="button" @click="selectedVaBank = 'va_bni'"
                                        :class="selectedVaBank === 'va_bni' ? 'bg-orange-600 text-white border-orange-600 shadow-xs font-bold' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 font-medium'"
                                        class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>BNI</span>
                                </button>
                                <button type="button" @click="selectedVaBank = 'va_bri'"
                                        :class="selectedVaBank === 'va_bri' ? 'bg-cyan-700 text-white border-cyan-700 shadow-xs font-bold' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 font-medium'"
                                        class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>BRI</span>
                                </button>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200">
                            <div class="flex items-center justify-between pb-2">
                                <span class="text-xs font-bold text-slate-800" x-text="vaBankNames[selectedVaBank]"></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-100 text-cyan-800">
                                    Verifikasi Otomatis
                                </span>
                            </div>

                            <span class="text-xs font-semibold text-slate-700 block mb-1">Nomor Virtual Account:</span>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-white border border-slate-300 rounded-xl px-4 py-2.5 font-mono font-extrabold text-base sm:text-lg text-slate-900 tracking-wider"
                                     x-text="vaNumbers[selectedVaBank]">
                                </div>
                                <button type="button" @click="copyText(vaNumbers[selectedVaBank])"
                                        class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-2xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                                    <i :class="copied ? 'fa-solid fa-check text-emerald-400' : 'fa-regular fa-copy'"></i>
                                    <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-[11px] text-slate-500 pt-2 border-t border-slate-200">
                            <p class="font-semibold text-slate-700">Panduan Pembayaran Virtual Account:</p>
                            <ol class="list-decimal list-inside space-y-0.5 ml-1 leading-relaxed">
                                <li>Buka Mobile Banking atau ATM bank yang Anda pilih di atas.</li>
                                <li>Pilih menu <strong>Transfer / Pembayaran</strong> &gt; <strong>Virtual Account</strong>.</li>
                                <li>Masukkan nomor Virtual Account di atas dan pastikan nominal sesuai (Rp {{ number_format($order->total_amount, 0, ',', '.') }}).</li>
                                <li>Konfirmasi transaksi. Status pesanan akan otomatis terverifikasi tanpa perlu upload struk!</li>
                            </ol>
                        </div>

                        <div class="pt-2">
                            <button type="button" @click="payWithMidtrans()"
                                    class="w-full sm:w-auto px-6 py-2.5 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 mx-auto cursor-pointer">
                                <i class="fa-solid fa-building-columns text-xs"></i>
                                <span>Buka Popup Virtual Account Midtrans</span>
                            </button>
                        </div>
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
                        <p class="text-xs font-bold text-slate-800">Pilihan Rekening Resmi NitipDong:</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Bank BCA</span>
                                    <p class="font-mono font-bold text-slate-900 text-xs mt-1.5">8820-1928-3721</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">a.n. PT NitipDong Indonesia</p>
                                </div>
                                <button type="button" @click="copyNumber('882019283721', 'bca')" class="text-slate-400 hover:text-cyan-700 p-2 cursor-pointer" title="Salin Rekening">
                                    <i :class="copiedBca ? 'fa-solid fa-check text-cyan-600' : 'fa-regular fa-copy'"></i>
                                </button>
                            </div>

                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200">Bank BNI</span>
                                    <p class="font-mono font-bold text-slate-900 text-xs mt-1.5">0987-6543-2100</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">a.n. PT NitipDong Indonesia</p>
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
