@php
    $method = strtolower($order->payment_method ?: 'qris');
    $isQris = str_contains($method, 'qris');
    $isBca = str_contains($method, 'bca');
    $isBri = str_contains($method, 'bri');
    $isBni = str_contains($method, 'bni');
    $isMandiri = str_contains($method, 'mandiri') || str_contains($method, 'echannel');
    $isManual = str_contains($method, 'manual');

    $bankName = 'BCA';
    $bankColor = 'blue';
    $bankCode = '014';
    $vaNumber = $charge['va_number'] ?? ('880199' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));

    if ($isBri) {
        $bankName = 'BRI (BRIVA)';
        $bankColor = 'cyan';
        $bankCode = '002';
        $vaNumber = $charge['va_number'] ?? ('880499' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    } elseif ($isBca) {
        $bankName = 'BCA';
        $bankColor = 'blue';
        $bankCode = '014';
        $vaNumber = $charge['va_number'] ?? ('880199' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    } elseif ($isBni) {
        $bankName = 'BNI';
        $bankColor = 'orange';
        $bankCode = '009';
        $vaNumber = $charge['va_number'] ?? ('880399' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    } elseif ($isMandiri) {
        $bankName = 'Mandiri';
        $bankColor = 'amber';
        $bankCode = '008';
        $vaNumber = $charge['va_number'] ?? ('880299' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    }

    $qrString = $charge['qr_string'] ?? ($charge['qris_data'] ?? ('00020101021226680016ID.CO.NITIPDONG.WWW011893600999' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) . '5204541153033605802ID5918NITIPDONG6007JAKARTA62070703A016304' . strtoupper(substr(md5($order->invoice_number), 0, 4))));
    $qrImageUrl = $charge['qris_image_url'] ?? ('https://api.qrserver.com/v1/create-qr-code/?size=360x360&margin=10&data=' . urlencode($qrString));

    $itemsSubtotal = $order->orderItems->sum(fn($item) => $item->price * $item->quantity);
    if ($itemsSubtotal <= 0) {
        $itemsSubtotal = $order->total_amount - ($order->shipping_cost ?? 0) + ($order->discount_amount ?? 0);
    }

    $expiresAt = $order->expires_at ?? ($order->created_at ? $order->created_at->copy()->addHours(24) : now()->addHours(24));
    $remainingSeconds = max(0, (int) now()->diffInSeconds($expiresAt, false));
    $initialH = str_pad((string) floor($remainingSeconds / 3600), 2, '0', STR_PAD_LEFT);
    $initialM = str_pad((string) floor(($remainingSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
    $initialS = str_pad((string) ($remainingSeconds % 60), 2, '0', STR_PAD_LEFT);
    $initialFormattedTimer = "{$initialH}:{$initialM}:{$initialS}";
@endphp

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <style>
        @keyframes strokeCheck {
            0% { stroke-dashoffset: 48px; opacity: 0; }
            100% { stroke-dashoffset: 0px; opacity: 1; }
        }
        @keyframes strokeCircle {
            0% { stroke-dashoffset: 166px; transform: rotate(-90deg) scale(0.85); opacity: 0; }
            100% { stroke-dashoffset: 0px; transform: rotate(-90deg) scale(1); opacity: 1; }
        }
        @keyframes popSuccess {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop-success {
            animation: popSuccess 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-circle-draw {
            stroke-dasharray: 166px;
            stroke-dashoffset: 166px;
            transform-origin: center;
            animation: strokeCircle 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
        }
        .animate-check-draw {
            stroke-dasharray: 48px;
            stroke-dashoffset: 48px;
            animation: strokeCheck 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.45s forwards;
        }
    </style>
@endpush

<x-app-layout>
    <div class="bg-slate-50/60 min-h-screen py-6 sm:py-8"
         x-data="{
            copied: false,
            toastMessage: '',
            isChecking: false,
            isPaidSuccess: false,
            redirectCountdown: 4,
            countdownInterval: null,
            isChangeModalOpen: false,
            guideTab: 'mbanking',
            newPaymentMethod: '{{ $order->payment_method ?: 'qris' }}',
            remainingSeconds: {{ $remainingSeconds }},
            formattedTimer: '{{ $initialFormattedTimer }}',
            pollInterval: null,
            init() {
                // Countdown timer 24 Jam
                setInterval(() => {
                    if (this.remainingSeconds > 0) {
                        this.remainingSeconds--;
                        const h = String(Math.floor(this.remainingSeconds / 3600)).padStart(2, '0');
                        const m = String(Math.floor((this.remainingSeconds % 3600) / 60)).padStart(2, '0');
                        const s = String(this.remainingSeconds % 60).padStart(2, '0');
                        this.formattedTimer = `${h}:${m}:${s}`;
                    }
                }, 1000);

                // Auto-polling status pembayaran tiap 2.5 detik
                this.pollInterval = setInterval(() => {
                    if (this.isPaidSuccess) return;
                    fetch('/api/v1/orders/{{ $order->id }}/payment-status')
                        .then(res => res.json())
                        .then(data => {
                            if (data.is_paid) {
                                this.triggerPaymentSuccess();
                            }
                        })
                        .catch(() => {});
                }, 2500);
            },
            triggerPaymentSuccess() {
                if (this.isPaidSuccess) return;
                this.isPaidSuccess = true;
                if (this.pollInterval) clearInterval(this.pollInterval);

                // Confetti blast celebration
                try {
                    if (typeof confetti === 'function') {
                        confetti({
                            particleCount: 90,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                        setTimeout(() => {
                            confetti({
                                particleCount: 50,
                                angle: 60,
                                spread: 55,
                                origin: { x: 0.1, y: 0.7 }
                            });
                            confetti({
                                particleCount: 50,
                                angle: 120,
                                spread: 55,
                                origin: { x: 0.9, y: 0.7 }
                            });
                        }, 350);
                    }
                } catch (e) {}

                // Auto redirect timer
                this.redirectCountdown = 4;
                this.countdownInterval = setInterval(() => {
                    this.redirectCountdown--;
                    if (this.redirectCountdown <= 0) {
                        clearInterval(this.countdownInterval);
                        this.goToOrders();
                    }
                }, 1000);
            },
            goToOrders() {
                window.location.href = '{{ route('customer.dashboard') }}?payment=success';
            },
            copyText(text, label) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text);
                } else {
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                }
                this.toastMessage = `${label} berhasil disalin!`;
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            },
            checkStatusNow() {
                this.isChecking = true;
                fetch('/api/v1/orders/{{ $order->id }}/payment-status')
                    .then(res => res.json())
                    .then(data => {
                        this.isChecking = false;
                        if (data.is_paid) {
                            this.triggerPaymentSuccess();
                        } else {
                            this.toastMessage = 'Pembayaran belum terdeteksi. Silakan selesaikan pembayaran.';
                            this.copied = true;
                            setTimeout(() => this.copied = false, 3000);
                        }
                    })
                    .catch(() => {
                        this.isChecking = false;
                        this.toastMessage = 'Gagal memeriksa status. Coba lagi dalam beberapa saat.';
                        this.copied = true;
                        setTimeout(() => this.copied = false, 3000);
                    });
            }
         }">

        <div class="page-container">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-6">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-600 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-slate-400"></i> Beranda
                </a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-600 transition-colors">Pesanan Saya</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-900 font-bold">Pembayaran</span>
            </nav>

            <!-- Checkout Stepper Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 mb-6 shadow-xs">
                <div class="flex items-center justify-between max-w-2xl mx-auto relative">
                    <!-- Progress Line Background -->
                    <div class="absolute top-1/2 left-8 right-8 -translate-y-1/2 h-0.5 bg-slate-100 z-0"></div>
                    <div class="absolute top-1/2 left-8 w-1/2 -translate-y-1/2 h-0.5 bg-cyan-600 z-0"></div>

                    <!-- Step 1 -->
                    <div class="relative z-10 flex flex-col items-center gap-1.5 text-center">
                        <div class="w-8 h-8 rounded-full bg-cyan-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-cyan-50">
                            <i class="fa-solid fa-check text-[11px]"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700">Buat Pesanan</span>
                    </div>

                    <!-- Step 2 (Active) -->
                    <div class="relative z-10 flex flex-col items-center gap-1.5 text-center">
                        <div class="w-8 h-8 rounded-full bg-cyan-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-cyan-100">
                            <i class="fa-solid fa-credit-card text-[11px]"></i>
                        </div>
                        <span class="text-[11px] font-bold text-cyan-700">Pembayaran</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative z-10 flex flex-col items-center gap-1.5 text-center">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold">
                            3
                        </div>
                        <span class="text-[11px] font-medium text-slate-400">Diproses</span>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative z-10 flex flex-col items-center gap-1.5 text-center">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold">
                            4
                        </div>
                        <span class="text-[11px] font-medium text-slate-400">Selesai</span>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50/80 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-semibold shadow-xs animate-fade-up">
                    <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 text-sm shadow-xs">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(isset($otherPendingOrders) && $otherPendingOrders->isNotEmpty())
                <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-xs shadow-xs animate-fade-up">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 text-sm shadow-xs mt-0.5">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-extrabold text-slate-900 text-sm">Pesanan Multi-Toko Berhasil Dibuat</h3>
                                <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold text-[10px]">
                                    {{ $otherPendingOrders->count() + 1 }} Toko Berbeda
                                </span>
                            </div>
                            <p class="text-slate-600 mt-1 leading-relaxed">
                                Karena keranjang Anda berisi produk dari toko berbeda, sistem membuat invoice terpisah untuk masing-masing penjual. Selesaikan pembayaran pesanan ini, kemudian lanjutkan untuk pesanan toko lainnya:
                            </p>
                            <div class="mt-3 space-y-2">
                                <div class="p-2.5 rounded-xl bg-white border border-cyan-200 flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-2 h-2 rounded-full bg-cyan-600 shrink-0"></span>
                                        <span class="font-mono font-bold text-slate-900">#{{ $order->invoice_number }}</span>
                                        <span class="text-slate-500 truncate">({{ $order->store->name ?? 'Toko' }})</span>
                                    </div>
                                    <span class="font-extrabold text-cyan-700 whitespace-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }} <span class="font-normal text-[10px] text-cyan-600">(Sedang Dibayar)</span></span>
                                </div>
                                @foreach($otherPendingOrders as $other)
                                    <div class="p-2.5 rounded-xl bg-white/80 border border-slate-200 flex items-center justify-between gap-3 hover:border-amber-300 transition-colors">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                            <span class="font-mono font-bold text-slate-900">#{{ $other->invoice_number }}</span>
                                            <span class="text-slate-500 truncate">({{ $other->store->name ?? 'Toko' }})</span>
                                        </div>
                                        <div class="flex items-center gap-2.5 shrink-0">
                                            <span class="font-bold text-slate-800">Rp {{ number_format($other->total_amount, 0, ',', '.') }}</span>
                                            <a href="{{ route('customer.order.payment', $other) }}" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-amber-950 font-bold rounded-lg text-[11px] transition-colors shadow-2xs">
                                                Bayar &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Layout Grid: 7 Cols Left (Payment details) + 5 Cols Right (Order Summary & Security) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Left Column: Payment Details & Instructions -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- Urgency & Total Tagihan Header Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs relative overflow-hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-800 text-[11px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Menunggu Pembayaran
                                </span>
                                <h1 class="text-lg font-black text-slate-900 mt-2 tracking-tight">Selesaikan Pembayaran Anda</h1>
                            </div>
                            
                            <!-- Live Countdown Pill -->
                            <div class="sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl sm:rounded-none border sm:border-0 border-slate-100">
                                <span class="text-[11px] font-medium text-slate-500 block mb-1">Sisa Waktu Pembayaran</span>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 font-mono font-bold text-sm tracking-wide">
                                    <i class="fa-regular fa-clock text-rose-500"></i>
                                    <span x-text="formattedTimer">23:59:59</span>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Display Container -->
                        <div class="pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-xs font-semibold text-slate-500 block mb-1">Total Pembayaran:</span>
                                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-sans">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>
                            </div>

                            <button type="button" @click="copyText('{{ (int) $order->total_amount }}', 'Nominal tagihan')"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-cyan-50 border border-slate-200 hover:border-cyan-200 text-slate-700 hover:text-cyan-700 font-bold text-xs transition-all active:scale-95 shadow-xs">
                                <i class="fa-regular fa-copy text-slate-500 hover:text-cyan-600"></i>
                                <span>Salin Jumlah</span>
                            </button>
                        </div>
                    </div>

                    <!-- Payment Method Detail Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                        
                        <!-- Header with Method Switcher -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 text-base shadow-xs">
                                    @if($isQris)
                                        <i class="fa-solid fa-qrcode text-red-600"></i>
                                    @elseif($isManual)
                                        <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i>
                                    @else
                                        <i class="fa-solid fa-building-columns text-cyan-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Metode Pembayaran</span>
                                    <h2 class="text-sm font-bold text-slate-900">
                                        @if($isQris)
                                            QRIS (GPN - Semua E-Wallet &amp; Bank)
                                        @elseif($isManual)
                                            Transfer Bank Manual (BCA)
                                        @else
                                            Virtual Account {{ $bankName }}
                                        @endif
                                    </h2>
                                </div>
                            </div>

                            <button type="button" @click="isChangeModalOpen = true"
                                    class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-cyan-50 border border-slate-200 hover:border-cyan-300 text-slate-700 hover:text-cyan-700 font-bold text-xs transition-all flex items-center gap-1.5 active:scale-95 shadow-xs">
                                <i class="fa-solid fa-wallet text-cyan-600 text-[11px]"></i>
                                <span>Ganti</span>
                            </button>
                        </div>

                        <!-- 1. QRIS CONTENT -->
                        @if($isQris)
                            <div class="space-y-6">
                                
                                <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-6 text-center shadow-xs">
                                    
                                    <!-- QRIS Badge Header -->
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-200/80 text-red-700 text-xs font-bold mb-4">
                                        <span class="font-black tracking-wider">QRIS</span>
                                        <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                        <span class="text-[11px] font-semibold text-red-600">Standar Nasional Bank Indonesia</span>
                                    </div>

                                    <div class="text-xs text-slate-500 mb-4">
                                        Merchant: <strong class="text-slate-800 font-bold">{{ $order->store->name ?? 'NitipDong Official' }}</strong>
                                        <span class="text-slate-300 mx-1.5">•</span>
                                        NMID: <span class="font-mono text-slate-600">ID1020038927492</span>
                                    </div>

                                    <!-- QR Image with Scanning Guide Frame -->
                                    <div class="relative inline-block mx-auto mb-4 group">
                                        <div class="p-4 bg-white rounded-2xl border-2 border-slate-200 shadow-md inline-block relative">
                                            <!-- Corner Accents -->
                                            <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-cyan-600 rounded-tl"></div>
                                            <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-cyan-600 rounded-tr"></div>
                                            <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-cyan-600 rounded-bl"></div>
                                            <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-cyan-600 rounded-br"></div>

                                            <img src="{{ $qrImageUrl }}" alt="QRIS Code Tagihan #{{ $order->invoice_number }}" class="w-64 h-64 sm:w-72 sm:h-72 object-contain mx-auto block rounded-lg">
                                        </div>
                                    </div>

                                    <!-- QR Actions (Download QR & Instructions) -->
                                    <div class="flex flex-wrap items-center justify-center gap-3 max-w-sm mx-auto mb-4">
                                        <a href="{{ $qrImageUrl }}" download="QRIS-NitipDong-{{ $order->invoice_number }}.png" target="_blank"
                                           class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 active:scale-95 shadow-xs">
                                            <i class="fa-solid fa-download text-slate-500"></i> Unduh QR Code
                                        </a>
                                        <button type="button" @click="copyText('{{ $qrString }}', 'String Kode QRIS')"
                                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 active:scale-95 shadow-xs">
                                            <i class="fa-regular fa-copy text-slate-500"></i> Salin Kode QR
                                        </button>
                                    </div>

                                    <p class="text-xs text-slate-600 leading-relaxed max-w-md mx-auto">
                                        Pindai QR di atas menggunakan aplikasi e-wallet atau mobile banking apa saja yang mendukung QRIS.
                                    </p>

                                    <!-- Supported Wallets & Apps Pill Strip -->
                                    <div class="mt-4 pt-4 border-t border-slate-200/60 flex flex-wrap items-center justify-center gap-2">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block w-full mb-1">Didukung oleh:</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">GoPay</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">OVO</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">DANA</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">ShopeePay</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">BCA Mobile</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">Livin' Mandiri</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">BRImo</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">BNI Mobile</span>
                                    </div>
                                </div>

                                <!-- QRIS Payment Guide Accordion -->
                                <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-4">
                                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-info text-cyan-600"></i> Cara Pembayaran QRIS:
                                    </h3>
                                    <ol class="space-y-2 text-xs text-slate-600 pl-4 list-decimal leading-relaxed">
                                        <li>Buka aplikasi e-wallet (GoPay, OVO, DANA, ShopeePay) atau Mobile Banking (BCA, Mandiri, BRI, BNI).</li>
                                        <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                                        <li>Arahkan kamera ke kode QR di atas atau unggah gambar QR dari galeri Anda.</li>
                                        <li>Pastikan nama merchant tertera <strong>{{ $order->store->name ?? 'NitipDong' }}</strong> dan total tagihan <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>.</li>
                                        <li>Masukkan PIN transaksi Anda untuk menyelesaikan pembayaran.</li>
                                        <li>Status pesanan akan terverifikasi secara otomatis dalam beberapa detik!</li>
                                    </ol>
                                </div>

                            </div>

                        <!-- 2. VIRTUAL ACCOUNT CONTENT -->
                        @elseif(!$isManual)
                            <div class="space-y-5">
                                
                                <div class="relative rounded-2xl p-5 sm:p-6 bg-slate-900 text-white shadow-lg overflow-hidden w-full max-w-[420px]">

                                    {{-- Tekstur security print, halus --}}
                                    <div class="absolute inset-0 opacity-[0.04] pointer-events-none"
                                         style="background-image: repeating-linear-gradient(115deg, #fff 0 1px, transparent 1px 10px);"></div>

                                    {{-- Baris atas: chip EMV + status badge --}}
                                    <div class="relative z-10 flex items-start justify-between">
                                        <div class="w-10 h-7 rounded-md bg-gradient-to-br from-amber-300 to-amber-500 relative overflow-hidden">
                                            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(90deg, transparent 0 3px, rgba(0,0,0,0.15) 3px 4px);"></div>
                                            <div class="absolute inset-x-0 top-1/2 h-px bg-black/15"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-300 bg-emerald-500/15 border border-emerald-400/30 px-2 py-0.5 rounded-md">
                                            Verifikasi Otomatis
                                        </span>
                                    </div>

                                    {{-- Nama bank --}}
                                    <div class="relative z-10 mt-3 text-xs font-bold text-slate-300 tracking-wide flex items-center gap-1.5">
                                        <i class="fa-solid fa-building-columns text-cyan-400"></i> Virtual Account {{ $bankName }}
                                    </div>

                                    {{-- Nomor VA, dikelompokkan 4 digit kayak kartu asli --}}
                                    <div class="relative z-10 mt-4">
                                        <div class="text-[10px] text-slate-500 mb-1.5 uppercase tracking-wider">Nomor Virtual Account</div>
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="font-mono text-base sm:text-lg font-black text-white tracking-[0.06em] leading-relaxed break-all select-all">
                                                {{ implode(' ', str_split($vaNumber, 4)) }}
                                            </span>
                                            <button type="button" @click="copyText('{{ $vaNumber }}', 'Nomor Virtual Account')"
                                                    aria-label="Salin nomor virtual account"
                                                    class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all active:scale-95 shrink-0 border border-white/10 focus-visible:ring-2 focus-visible:ring-cyan-400 focus-visible:outline-none">
                                                <i class="fa-regular fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Baris bawah: total tagihan --}}
                                    <div class="relative z-10 mt-5 pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                                        <span>Total Tagihan</span>
                                        <span class="font-bold text-white text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-blue-50/60 border border-blue-200/80 rounded-xl text-xs text-blue-900 flex items-start gap-2.5">
                                    <i class="fa-solid fa-shield-halved text-blue-600 mt-0.5 shrink-0"></i>
                                    <p class="leading-relaxed text-[11px]">
                                        Transfer tepat sesuai nominal tagihan. Pembayaran melalui Virtual Account akan terverifikasi secara instan tanpa perlu mengunggah bukti transfer.
                                    </p>
                                </div>

                                <!-- Tabbed Payment Guides -->
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <div class="bg-slate-50 border-b border-slate-200 flex text-xs font-bold text-slate-600">
                                        <button type="button" @click="guideTab = 'mbanking'"
                                                class="flex-1 py-3 px-4 text-center border-b-2 transition-colors flex items-center justify-center gap-2"
                                                :class="guideTab === 'mbanking' ? 'border-cyan-600 text-cyan-700 bg-white' : 'border-transparent hover:text-slate-900'">
                                            <i class="fa-solid fa-mobile-screen"></i> Mobile Banking
                                        </button>
                                        <button type="button" @click="guideTab = 'ibanking'"
                                                class="flex-1 py-3 px-4 text-center border-b-2 transition-colors flex items-center justify-center gap-2"
                                                :class="guideTab === 'ibanking' ? 'border-cyan-600 text-cyan-700 bg-white' : 'border-transparent hover:text-slate-900'">
                                            <i class="fa-solid fa-laptop"></i> Internet Banking
                                        </button>
                                        <button type="button" @click="guideTab = 'atm'"
                                                class="flex-1 py-3 px-4 text-center border-b-2 transition-colors flex items-center justify-center gap-2"
                                                :class="guideTab === 'atm' ? 'border-cyan-600 text-cyan-700 bg-white' : 'border-transparent hover:text-slate-900'">
                                            <i class="fa-solid fa-credit-card"></i> ATM
                                        </button>
                                    </div>

                                    <div class="p-5 bg-white text-xs text-slate-600 leading-relaxed">
                                        <!-- M-Banking Guide -->
                                        <div x-show="guideTab === 'mbanking'" class="space-y-2">
                                            <p class="font-bold text-slate-800 mb-2">Petunjuk Pembayaran via Mobile Banking:</p>
                                            <ol class="list-decimal list-inside space-y-1.5 pl-1">
                                                <li>Buka aplikasi Mobile Banking {{ $bankName }} Anda dan lakukan Login.</li>
                                                <li>Pilih menu <strong>Transfer</strong> &gt; <strong>Virtual Account</strong>.</li>
                                                <li>Masukkan Nomor Virtual Account: <strong class="font-mono text-slate-900">{{ $vaNumber }}</strong></li>
                                                <li>Pastikan nama merchant tertera <strong>NitipDong</strong> dan nominal tagihan <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>.</li>
                                                <li>Konfirmasi transaksi dan masukkan PIN akun Anda.</li>
                                                <li>Transaksi selesai, simpan notifikasi sebagai bukti transaksi.</li>
                                            </ol>
                                        </div>

                                        <!-- Internet Banking Guide -->
                                        <div x-show="guideTab === 'ibanking'" class="space-y-2" x-cloak>
                                            <p class="font-bold text-slate-800 mb-2">Petunjuk Pembayaran via Internet Banking:</p>
                                            <ol class="list-decimal list-inside space-y-1.5 pl-1">
                                                <li>Login ke portal Internet Banking {{ $bankName }} Anda.</li>
                                                <li>Pilih menu <strong>Transfer Dana</strong> &gt; <strong>Transfer ke Virtual Account</strong>.</li>
                                                <li>Masukkan Nomor Virtual Account: <strong class="font-mono text-slate-900">{{ $vaNumber }}</strong></li>
                                                <li>Periksa rincian pembayaran, lalu masukkan respon Token / OTP pengaman.</li>
                                                <li>Tekan <strong>Kirim / Konfirmasi</strong> untuk menyelesaikan transaksi.</li>
                                            </ol>
                                        </div>

                                        <!-- ATM Guide -->
                                        <div x-show="guideTab === 'atm'" class="space-y-2" x-cloak>
                                            <p class="font-bold text-slate-800 mb-2">Petunjuk Pembayaran via Mesin ATM:</p>
                                            <ol class="list-decimal list-inside space-y-1.5 pl-1">
                                                <li>Masukkan Kartu ATM {{ $bankName }} dan 6 digit PIN Anda.</li>
                                                <li>Pilih menu <strong>Transaksi Lainnya</strong> &gt; <strong>Transfer</strong> &gt; <strong>Ke Rekening Virtual Account</strong>.</li>
                                                <li>Masukkan Nomor Virtual Account: <strong class="font-mono text-slate-900">{{ $vaNumber }}</strong></li>
                                                <li>Periksa kesesuaian nama penerima dan jumlah nominal tagihan.</li>
                                                <li>Pilih <strong>Ya / Lanjutkan</strong> untuk membayar dan simpan struk transaksi.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        <!-- 3. MANUAL BANK TRANSFER CONTENT -->
                        @else
                            <div class="space-y-5">
                                <div class="p-4 bg-amber-50/70 border border-amber-200/80 rounded-2xl text-xs text-amber-900 flex items-start gap-2.5">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600 mt-0.5 shrink-0 text-sm"></i>
                                    <p class="leading-relaxed">
                                        Lakukan transfer manual ke rekening bank resmi kami, kemudian unggah foto struk / tangkapan layar bukti transfer untuk diverifikasi oleh penjual.
                                    </p>
                                </div>

                                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                                    <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                                        <span class="text-slate-500">Bank Tujuan:</span>
                                        <span class="font-bold text-slate-900">BCA (Bank Central Asia)</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                                        <span class="text-slate-500">Nomor Rekening:</span>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-black text-slate-900 text-sm">123-456-7890</span>
                                            <button type="button" @click="copyText('1234567890', 'Nomor Rekening')" class="text-cyan-700 hover:text-cyan-800 text-xs font-bold">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">Atas Nama:</span>
                                        <span class="font-bold text-slate-900">PT NitipDong Indonesia</span>
                                    </div>
                                </div>

                                <form action="{{ route('customer.order.confirm_payment', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Foto Bukti Transfer</label>
                                        <input type="file" name="payment_proof" required accept="image/*"
                                               class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer border border-slate-200 rounded-xl p-2 bg-white">
                                        <span class="text-[11px] text-slate-400 mt-1 block">Format: JPG, PNG, WEBP (Maks. 2MB)</span>
                                    </div>
                                    <button type="submit" class="btn-primary w-full h-11 text-xs font-bold shadow-sm">
                                        <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> Kirim Bukti Pembayaran
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>

                    <!-- Action Bar & Auto-Detection Status -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 text-xs text-slate-500 w-full sm:w-auto">
                            <i class="fa-solid fa-arrows-rotate text-emerald-600 text-[11px] shrink-0 fa-spin"></i>
                            <span class="text-[11px]">Sistem otomatis memeriksa pembayaran secara realtime...</span>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="button" @click="checkStatusNow()" :disabled="isChecking"
                                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
                                <template x-if="!isChecking">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-arrows-rotate"></i> Cek Status
                                    </span>
                                </template>
                                <template x-if="isChecking">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Order Summary & Trust Guarantees -->
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-24">

                    <!-- Order Summary Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Invoice</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-xs font-bold text-slate-800 select-all">#{{ $order->invoice_number }}</span>
                                    <button type="button" @click="copyText('{{ $order->invoice_number }}', 'Nomor Invoice')"
                                            class="text-slate-400 hover:text-cyan-700 text-xs transition-colors" title="Salin Invoice">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-cyan-50 text-cyan-700 font-bold text-[10px] border border-cyan-200">
                                {{ count($order->orderItems) }} Produk
                            </span>
                        </div>

                        <!-- Store Header -->
                        @if($order->store)
                            <div class="flex items-center gap-2.5 pt-1">
                                <div class="w-7 h-7 rounded-lg bg-cyan-50 border border-cyan-200 flex items-center justify-center text-cyan-700 text-xs font-bold">
                                    <i class="fa-solid fa-store text-[11px]"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $order->store->name }}</h4>
                                    <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-[9px]"></i> {{ $order->store->city ?? 'Indonesia' }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Product Items List -->
                        <div class="space-y-3 pt-2 max-h-60 overflow-y-auto pr-1">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden shrink-0">
                                        @if($item->product && $item->product->image_url)
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 text-sm">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 text-xs">
                                        <h5 class="font-bold text-slate-800 truncate">{{ $item->product->name ?? 'Produk Pesanan' }}</h5>
                                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center justify-between">
                                            <span>{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            <span class="font-semibold text-slate-800">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Breakdown -->
                        <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Subtotal Produk:</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 flex items-center gap-1">
                                    Ongkos Kirim
                                    <span class="text-[10px] font-bold text-slate-400">({{ strtoupper($order->shipping_courier ?: 'Kurir') }})</span>
                                </span>
                                <span class="font-semibold text-slate-800">
                                    {{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}
                                </span>
                            </div>

                            @if(($order->discount_amount ?? 0) > 0)
                                <div class="flex items-center justify-between text-emerald-700 font-semibold">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-ticket text-[10px]"></i> Diskon Voucher:
                                    </span>
                                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-sm">
                                <span class="font-bold text-slate-900">Total Tagihan:</span>
                                <span class="font-black text-base text-cyan-700">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                    </div>

                    <!-- Security and Buyer Protection Guarantee -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-3.5">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-shield-check text-emerald-600"></i> Jaminan Belanja Aman
                        </h4>

                        <div class="space-y-3 text-xs text-slate-600">
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <p class="text-[11px] leading-relaxed">
                                    <strong class="text-slate-800">Proteksi Rekening Bersama (Escrow):</strong> Dana Anda aman ditampung hingga paket Anda terima sesuai pesanan.
                                </p>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <p class="text-[11px] leading-relaxed">
                                    <strong class="text-slate-800">Pengiriman Terlacak:</strong> Pantau posisi kurir pengiriman secara live setelah pesanan dikirim.
                                </p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-center">
                            <a href="{{ route('customer.dashboard') }}" class="text-[11px] font-bold text-cyan-700 hover:text-cyan-800 transition-colors inline-flex items-center gap-1">
                                <i class="fa-solid fa-headset"></i> Butuh bantuan pesanan? Hubungi CS
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- Change Payment Method Modal -->
        <div x-show="isChangeModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-slate-950/60 backdrop-blur-xs" @click="isChangeModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-100 relative z-10 animate-fade-up">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm font-bold">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Pilih Metode Pembayaran</h3>
                        </div>
                        <button type="button" @click="isChangeModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <form action="{{ route('customer.order.change_payment_method', $order) }}" method="POST" class="mt-4 space-y-4">
                        @csrf

                        <!-- 1. E-Wallet / QRIS Section -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Instan &amp; Semua E-Wallet</span>
                            <label class="flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition-all"
                                   :class="newPaymentMethod === 'qris' ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="qris" x-model="newPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">QRIS (GoPay, OVO, DANA, ShopeePay, M-Banking)</div>
                                        <div class="text-[11px] text-slate-500">Scan QR Code instan, verifikasi otomatis</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-red-50 text-red-600 font-bold text-[10px] border border-red-200">QRIS</span>
                            </label>
                        </div>

                        <!-- 2. Virtual Account Section -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Virtual Account (Otomatis 24 Jam)</span>
                            <div class="space-y-2">
                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="newPaymentMethod === 'va_bca' ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="va_bca" x-model="newPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">BCA Virtual Account</div>
                                            <div class="text-[11px] text-slate-500">Verifikasi otomatis tanpa bukti transfer</div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-200">BCA</span>
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="newPaymentMethod === 'va_bri' ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="va_bri" x-model="newPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">BRI Virtual Account (BRIVA)</div>
                                            <div class="text-[11px] text-slate-500">Verifikasi otomatis tanpa bukti transfer</div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-cyan-50 text-cyan-700 font-bold text-[10px] border border-cyan-200">BRI</span>
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="newPaymentMethod === 'va_mandiri' ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="va_mandiri" x-model="newPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">Mandiri Virtual Account</div>
                                            <div class="text-[11px] text-slate-500">Verifikasi otomatis tanpa bukti transfer</div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200">Mandiri</span>
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                       :class="newPaymentMethod === 'va_bni' ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="va_bni" x-model="newPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">BNI Virtual Account</div>
                                            <div class="text-[11px] text-slate-500">Verifikasi otomatis tanpa bukti transfer</div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-700 font-bold text-[10px] border border-orange-200">BNI</span>
                                </label>
                            </div>
                        </div>

                        <!-- 3. Manual Bank Transfer Section -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Transfer Manual</span>
                            <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="newPaymentMethod === 'manual' ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="manual" x-model="newPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Transfer Bank Manual</div>
                                        <div class="text-[11px] text-slate-500">Transfer ke rekening BCA + unggah foto struk bukti</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[10px]">Manual</span>
                            </label>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="w-full btn-primary h-11 text-xs font-bold shadow-sm">
                                Konfirmasi &amp; Gunakan Metode Ini
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- PAYMENT SUCCESS CELEBRATION MODAL OVERLAY -->
        <div x-show="isPaidSuccess"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             x-cloak>
            
            <!-- Dark Backdrop with Blur -->
            <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md"></div>

            <!-- Card Content with Pop Animation -->
            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 text-center animate-pop-success overflow-hidden">
                
                <!-- Background Accent Glow -->
                <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-48 h-48 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Animated SVG Checkmark -->
                <div class="relative w-20 h-20 mx-auto mb-5 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-emerald-100/80 animate-ping opacity-25"></div>
                    <svg class="w-20 h-20 text-emerald-500 drop-shadow-sm" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="25" stroke="currentColor" stroke-width="3" class="text-emerald-500 animate-circle-draw" stroke-linecap="round" />
                        <path d="M17 28.5L24.5 36L39 21.5" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 animate-check-draw" />
                    </svg>
                </div>

                <!-- Title & Status Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs font-bold mb-3">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Pembayaran Berhasil
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Terima Kasih!</h2>
                <p class="text-xs text-slate-600 leading-relaxed mt-1 mb-5">
                    Pembayaran Anda telah berhasil diverifikasi oleh sistem. Pesanan sedang diteruskan ke penjual untuk segera dikemas.
                </p>

                <!-- Receipt Detail Box -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-left space-y-2.5 mb-6 text-xs">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Nomor Invoice:</span>
                        <span class="font-mono font-bold text-slate-900">#{{ $order->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Metode Pembayaran:</span>
                        <span class="font-bold text-slate-800 capitalize">{{ str_replace('_', ' ', $order->payment_method ?: 'QRIS') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Waktu Transaksi:</span>
                        <span class="font-medium text-slate-700">{{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="pt-2.5 border-t border-slate-200 flex items-center justify-between font-bold">
                        <span class="text-slate-700">Total Dibayar:</span>
                        <span class="text-emerald-700 font-black text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Auto Redirect Countdown Progress -->
                <div class="mb-5">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium mb-1.5">
                        <span>Mengalihkan otomatis...</span>
                        <span class="font-bold text-slate-700 font-mono"><span x-text="redirectCountdown">4</span>s</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 transition-all duration-1000 ease-linear rounded-full"
                             :style="`width: ${(redirectCountdown / 4) * 100}%`"></div>
                    </div>
                </div>

                <!-- Action CTAs -->
                <div class="space-y-2">
                    <button type="button" @click="goToOrders()"
                            class="w-full btn-primary h-11 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white rounded-xl flex items-center justify-center gap-2 shadow-sm active:scale-98 transition-all">
                        <span>Lihat Detail Pesanan Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </button>
                    
                    <a href="{{ route('customer.dashboard') }}"
                       class="inline-block text-xs font-semibold text-slate-500 hover:text-cyan-700 py-1 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>

            </div>
        </div>

        <!-- Floating Toast Notification -->
        <div x-show="copied" x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-6 right-6 z-50 px-4 py-3 bg-slate-900/95 backdrop-blur-xs text-white text-xs font-bold rounded-2xl shadow-2xl flex items-center gap-2.5 border border-slate-700/60"
             x-cloak>
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
            <span x-text="toastMessage">Teks berhasil disalin!</span>
        </div>

    </div>
</x-app-layout>
