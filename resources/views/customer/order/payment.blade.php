@php
    $method = strtolower($order->payment_method ?: 'qris');
    $isQris = str_contains($method, 'qris');
    $isBca = str_contains($method, 'bca');
    $isBri = str_contains($method, 'bri');
    $isBni = str_contains($method, 'bni');
    $isMandiri = str_contains($method, 'mandiri') || str_contains($method, 'echannel');
    $isManual = str_contains($method, 'manual');

    $bankName = 'BCA';
    $vaNumber = $charge['va_number'] ?? ('880199' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));

    if ($isBri) {
        $bankName = 'BRI (BRIVA)';
        $vaNumber = $charge['va_number'] ?? ('880499' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    } elseif ($isBca) {
        $bankName = 'BCA';
        $vaNumber = $charge['va_number'] ?? ('880199' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    } elseif ($isBni) {
        $bankName = 'BNI';
        $vaNumber = $charge['va_number'] ?? ('880399' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    } elseif ($isMandiri) {
        $bankName = 'Mandiri';
        $vaNumber = $charge['va_number'] ?? ('880299' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    }

    $qrImageUrl = $charge['qr_image_url'] ?? ('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode('00020101021226680016ID.CO.NITIPDONG.WWW011893600999' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) . '5204541153033605802ID5918NITIPDONG6007JAKARTA62070703A016304' . strtoupper(substr(md5($order->invoice_number), 0, 4))));
@endphp

<x-app-layout>
    <div class="page-container py-6"
         x-data="{
            copied: false,
            copiedType: '',
            isChecking: false,
            isSimulating: false,
            remainingSeconds: 86400,
            formattedTimer: '23:59:59',
            pollInterval: null,
            init() {
                // Countdown timer
                setInterval(() => {
                    if (this.remainingSeconds > 0) {
                        this.remainingSeconds--;
                        const h = String(Math.floor(this.remainingSeconds / 3600)).padStart(2, '0');
                        const m = String(Math.floor((this.remainingSeconds % 3600) / 60)).padStart(2, '0');
                        const s = String(this.remainingSeconds % 60).padStart(2, '0');
                        this.formattedTimer = `${h}:${m}:${s}`;
                    }
                }, 1000);

                // Auto-polling status pembayaran tiap 3 detik
                this.pollInterval = setInterval(() => {
                    fetch('/api/v1/orders/{{ $order->id }}/payment-status')
                        .then(res => res.json())
                        .then(data => {
                            if (data.is_paid) {
                                clearInterval(this.pollInterval);
                                window.location.href = '{{ route('customer.dashboard') }}?payment=success';
                            }
                        })
                        .catch(() => {});
                }, 3000);
            },
            copyText(text, type) {
                navigator.clipboard.writeText(text);
                this.copied = true;
                this.copiedType = type;
                setTimeout(() => this.copied = false, 2500);
            },
            checkStatusNow() {
                this.isChecking = true;
                fetch('/api/v1/orders/{{ $order->id }}/payment-status')
                    .then(res => res.json())
                    .then(data => {
                        this.isChecking = false;
                        if (data.is_paid) {
                            clearInterval(this.pollInterval);
                            window.location.href = '{{ route('customer.dashboard') }}?payment=success';
                        } else {
                            if (window.toast) window.toast.info('Pembayaran belum terdeteksi. Silakan selesaikan pembayaran.');
                            else alert('Pembayaran belum terdeteksi. Silakan selesaikan pembayaran.');
                        }
                    })
                    .catch(() => {
                        this.isChecking = false;
                    });
            },
            simulatePaid() {
                this.isSimulating = true;
                fetch('/api/v1/orders/{{ $order->id }}/simulate-paid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.isSimulating = false;
                    if (data.success) {
                        clearInterval(this.pollInterval);
                        window.location.href = '{{ route('customer.dashboard') }}?payment=success';
                    }
                })
                .catch(() => {
                    this.isSimulating = false;
                });
            }
         }">

        <div class="max-w-2xl mx-auto mb-5">
            <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-cyan-700 mb-2 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Selesaikan Pembayaran</h1>
            <p class="text-xs text-slate-500 mt-1">Lakukan pembayaran sebelum batas waktu berakhir agar pesanan segera diproses</p>
        </div>

        <div class="max-w-2xl mx-auto space-y-4">

            {{-- 1. COUNTDOWN & TOTAL BANNER --}}
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-cyan-950 text-white rounded-2xl p-5 shadow-lg border border-slate-700/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-300">Batas Waktu Pembayaran</span>
                            <span class="px-2 py-0.5 rounded bg-amber-400/20 border border-amber-400/40 text-amber-300 font-mono font-bold text-xs flex items-center gap-1">
                                <i class="fa-regular fa-clock text-[10px]"></i> <span x-text="formattedTimer">23:59:59</span>
                            </span>
                        </div>
                        <div class="mt-2">
                            <span class="text-xs text-slate-400">Total Tagihan:</span>
                            <div class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                <button type="button" @click="copyText('{{ (int) $order->total_amount }}', 'nominal')"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-cyan-700/60 hover:bg-cyan-600 border border-cyan-400/30 text-cyan-200 font-bold transition-all active:scale-95">
                                    <i class="fa-regular fa-copy"></i> Salin
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-700">
                        <span class="text-[11px] text-slate-400 block">Nomor Invoice:</span>
                        <span class="text-sm font-mono font-bold text-cyan-300">#{{ $order->invoice_number }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. DEDICATED PAYMENT METHOD CARD --}}
            @if($isQris)
                {{-- QRIS DIRECT VIEW --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm text-center">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-700 font-bold text-xs mb-4">
                        <i class="fa-solid fa-qrcode"></i> QRIS NASIONAL (GPN)
                    </div>

                    <div class="flex justify-center mb-4">
                        <div class="p-3 bg-white rounded-2xl border-2 border-slate-200 shadow-md inline-block">
                            <img src="{{ $qrImageUrl }}" alt="QRIS Code" class="w-60 h-60 object-contain mx-auto">
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed mb-4">
                        Buka aplikasi <strong>GoPay, OVO, Dana, ShopeePay, LinkAja</strong> atau Mobile Banking (<strong>BCA, Mandiri, BRI, BNI</strong>), lalu pilih menu <strong>Scan QRIS</strong>.
                    </p>

                    <div class="flex justify-center gap-2">
                        <button type="button" @click="copyText('{{ $charge['qr_string'] ?? $order->invoice_number }}', 'qris')"
                                class="btn-secondary h-9 text-xs px-4">
                            <i class="fa-solid fa-copy mr-1"></i> Salin String QRIS
                        </button>
                    </div>
                </div>
            @elseif(!$isManual)
                {{-- VIRTUAL ACCOUNT DIRECT VIEW (BRI / BCA / BNI / MANDIRI) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Metode Pembayaran</span>
                            <h3 class="text-base font-bold text-slate-900 mt-0.5">Virtual Account {{ $bankName }}</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-xs"></i> Verifikasi Otomatis
                        </span>
                    </div>

                    <div class="py-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor Virtual Account:</label>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="font-mono text-xl sm:text-2xl font-black text-slate-900 tracking-wider select-all">{{ $vaNumber }}</span>
                            <button type="button" @click="copyText('{{ $vaNumber }}', 'va')"
                                    class="btn-primary h-9 text-xs px-4 bg-cyan-700 hover:bg-cyan-800">
                                <i class="fa-solid fa-copy mr-1"></i> Salin No. VA
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">
                            *Transfer sesuai nominal tagihan yang tertera di atas. Sistem akan mendeteksi pembayaran secara otomatis tanpa perlu kirim bukti transfer.
                        </p>
                    </div>

                    {{-- Panduan Transfer Step-by-Step --}}
                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Panduan Pembayaran {{ $bankName }}:</h4>
                        
                        <div class="space-y-2 text-xs text-slate-600">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <strong class="text-slate-800 block mb-1">1. Melalui Mobile Banking (m-Banking):</strong>
                                <ol class="list-decimal list-inside space-y-1 text-[11px] pl-1">
                                    <li>Buka aplikasi Mobile Banking dan pilih menu <strong>Transfer / Pembayaran</strong>.</li>
                                    <li>Pilih opsi <strong>Virtual Account</strong>.</li>
                                    <li>Masukkan Nomor Virtual Account: <strong>{{ $vaNumber }}</strong></li>
                                    <li>Pastikan nama merchant <strong>NitipDong</strong> dan total nominal <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> sudah sesuai.</li>
                                    <li>Masukkan PIN transaksi Anda untuk menyelesaikan pembayaran.</li>
                                </ol>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <strong class="text-slate-800 block mb-1">2. Melalui ATM:</strong>
                                <ol class="list-decimal list-inside space-y-1 text-[11px] pl-1">
                                    <li>Masukkan kartu ATM dan PIN Anda.</li>
                                    <li>Pilih menu <strong>Transaksi Lainnya</strong> &gt; <strong>Transfer / Pembayaran</strong> &gt; <strong>Virtual Account</strong>.</li>
                                    <li>Masukkan Nomor Virtual Account <strong>{{ $vaNumber }}</strong>.</li>
                                    <li>Konfirmasi pembayaran dan simpan struk sebagai referensi.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- MANUAL TRANSFER --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 mb-3">Transfer Bank Manual</h3>
                    <p class="text-xs text-slate-600 mb-4">Silakan transfer ke rekening resmi NitipDong, lalu unggah foto struk bukti transfer Anda di bawah ini:</p>
                    
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl mb-4 text-xs space-y-1">
                        <div><strong>Bank:</strong> BCA (Bank Central Asia)</div>
                        <div><strong>Nomor Rekening:</strong> 123-456-7890</div>
                        <div><strong>Atas Nama:</strong> PT NitipDong Indonesia</div>
                    </div>

                    <form action="{{ route('customer.order.confirm_payment', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Foto Bukti Transfer</label>
                            <input type="file" name="payment_proof" required accept="image/*" class="input text-xs py-2">
                        </div>
                        <button type="submit" class="btn-primary w-full h-10 text-xs">
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            @endif

            {{-- 3. ACTION BUTTONS & SIMULATOR --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <button type="button" @click="checkStatusNow()" :disabled="isChecking"
                        class="w-full sm:w-auto flex-1 btn-primary h-11 text-xs bg-slate-900 hover:bg-slate-800 flex items-center justify-center gap-2">
                    <template x-if="!isChecking">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-rotate"></i> Cek Status Pembayaran
                        </span>
                    </template>
                    <template x-if="isChecking">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa Status...
                        </span>
                    </template>
                </button>

                {{-- Testing Sandbox Demo Button --}}
                <button type="button" @click="simulatePaid()" :disabled="isSimulating"
                        class="w-full sm:w-auto px-4 h-11 text-xs font-bold rounded-xl border border-teal-300 bg-teal-50 text-teal-800 hover:bg-teal-100 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                    <template x-if="!isSimulating">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-teal-600"></i> ⚡ Simulasi Bayar Lunas (Demo)
                        </span>
                    </template>
                    <template x-if="isSimulating">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memproses...
                        </span>
                    </template>
                </button>
            </div>

            {{-- Floating Copy Notification Toast --}}
            <div x-show="copied" x-transition
                 class="fixed bottom-6 right-6 z-50 px-4 py-3 bg-slate-900 text-white text-xs font-bold rounded-xl shadow-2xl flex items-center gap-2"
                 x-cloak>
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span x-text="copiedType === 'nominal' ? 'Nominal tagihan berhasil disalin!' : (copiedType === 'va' ? 'Nomor Virtual Account berhasil disalin!' : 'Teks berhasil disalin!')"></span>
            </div>

        </div>
    </div>
</x-app-layout>
