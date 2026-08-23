<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem — NitipDong</title>
    <meta name="description" content="NitipDong sedang dalam pemeliharaan terjadwal untuk peningkatan performa dan fitur baru.">
    <link rel="icon" type="image/png" href="/img/nitipdong-logo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#132A2E',
                        teal: {
                            50: '#EEFAFA',
                            100: '#D8F2F1',
                            400: '#28B4B0',
                            500: '#0E8A8A',
                            600: '#0C7373',
                            700: '#0A5C5C',
                        },
                        amber: {
                            50: '#FFF6EC',
                            500: '#E88A2B',
                            600: '#D8791D',
                        },
                        leaf: {
                            500: '#3AAE6B',
                        },
                        sand: '#FBF7F1',
                        peach: '#FCE9DF',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        soft: '0 1px 2px rgba(19,42,46,0.04), 0 12px 32px -12px rgba(19,42,46,0.12)',
                        card: '0 1px 3px rgba(19,42,46,0.05), 0 24px 48px -20px rgba(14,138,138,0.18)',
                    }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F5F8F8;
        }

        /* Soft ambient blobs, like the reference screenshot */
        .blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(2px);
            pointer-events: none;
            z-index: 0;
        }
        .blob-teal {
            width: 460px; height: 460px;
            top: -180px; right: -140px;
            background: radial-gradient(circle at 35% 35%, #D8F2F1 0%, #EAF6F5 55%, transparent 75%);
        }
        .blob-peach {
            width: 380px; height: 380px;
            bottom: -160px; left: -140px;
            background: radial-gradient(circle at 60% 40%, #FCE9DF 0%, #FBF1EA 55%, transparent 75%);
        }

        @keyframes drift {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(0, -10px); }
        }
        .drift { animation: drift 6s ease-in-out infinite; }

        @keyframes softPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(14,138,138,0.18); }
            50% { box-shadow: 0 0 0 14px rgba(14,138,138,0); }
        }
        .pulse-ring { animation: softPulse 2.6s ease-out infinite; }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }
        .dot-blink { animation: dotBlink 1.6s ease-in-out infinite; }

        /* Progress shimmer, kept subtle */
        @keyframes shimmer {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(220%); }
        }
        .shimmer::after {
            content: '';
            position: absolute;
            inset: 0;
            width: 40%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
            animation: shimmer 2.2s infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .drift, .pulse-ring, .dot-blink, .shimmer::after { animation: none !important; }
        }
    </style>
</head>
<body class="min-h-screen relative text-ink antialiased">

    <div class="blob blob-teal"></div>
    <div class="blob blob-peach"></div>

    @php
        $msgData = [];
        $paths = [
            storage_path('framework/maintenance_message.json'),
            storage_path('framework/maintenance_web.json'),
            storage_path('framework/down'),
        ];
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $raw = @file_get_contents($p);
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $msgData = array_merge($msgData, $decoded);
                }
            }
        }

        $customMessage = $msgData['message'] ?? env('APP_MAINTENANCE_MESSAGE') ?? (isset($exception) && method_exists($exception, 'getMessage') ? $exception->getMessage() : null);
        $title = $msgData['title'] ?? env('APP_MAINTENANCE_TITLE') ?? 'Mode Pemeliharaan & Pengembangan';
        $windowStart = $msgData['window_start'] ?? env('APP_MAINTENANCE_START');
        $windowEnd = $msgData['window_end'] ?? env('APP_MAINTENANCE_END');
        $message = !empty($customMessage) && !str_starts_with($customMessage, 'The application is in maintenance mode')
            ? $customMessage
            : ('System sedang dalam pemeliharaan terjadwal' . ($windowStart && $windowEnd ? " pada pukul {$windowStart} – {$windowEnd} WIB." : '.') . ' Silakan coba kembali setelah jam tersebut.');
    @endphp

    <!-- Header -->
    <header class="relative z-10 w-full px-5 sm:px-8 py-5">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-soft">
                    <svg class="w-4.5 h-4.5 text-white" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <path d="M3 6h18"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <span class="font-extrabold text-lg tracking-tight text-ink">NitipDong</span>
            </div>
            <div id="liveClock" class="font-mono text-[11px] text-ink/40 tabular-nums">--.--.--</div>
        </div>
    </header>

    <!-- Main -->
    <main class="relative z-10 flex items-start sm:items-center justify-center px-5 py-6 sm:py-14 min-h-[calc(100vh-140px)]">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-[28px] shadow-card p-6 sm:p-8 text-center">

                <!-- Icon badge -->
                <div class="relative w-20 h-20 mx-auto mb-6">
                    <div class="absolute inset-0 rounded-3xl bg-teal-100 drift"></div>
                    <div class="relative w-20 h-20 rounded-3xl bg-gradient-to-br from-teal-500 to-teal-600 pulse-ring flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Status pill -->
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-500/25 text-amber-600 text-[11px] font-extrabold tracking-wide uppercase mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dot-blink"></span>
                    Pemeliharaan Sistem
                </div>

                <!-- Title -->
                <h1 class="text-[22px] sm:text-2xl font-extrabold text-ink leading-snug mb-3">
                    {{ $title }} 🛠️
                </h1>

                <!-- Message card -->
                <div class="bg-teal-50/70 rounded-2xl px-5 py-4 mb-6">
                    <p class="text-[13.5px] leading-relaxed text-ink/70 font-medium">
                        {{ $message }}
                    </p>
                </div>

                <!-- Checklist -->
                <div class="rounded-2xl border border-ink/[0.06] divide-y divide-ink/[0.06] mb-6 text-left overflow-hidden">
                    <div class="flex items-center gap-3 px-4 py-3.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8Z"/></svg>
                        </div>
                        <span class="text-[13.5px] font-semibold text-ink/80 flex-1">Peningkatan Kecepatan &amp; Respons Server</span>
                        <svg class="w-5 h-5 text-leaf-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8.5 12.5 2.4 2.4 4.6-5.4"/></svg>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-3.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V5l-8-3Z"/></svg>
                        </div>
                        <span class="text-[13.5px] font-semibold text-ink/80 flex-1">Optimalisasi Keamanan Transaksi &amp; Akun</span>
                        <svg class="w-5 h-5 text-leaf-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8.5 12.5 2.4 2.4 4.6-5.4"/></svg>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-3.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 12.3 12.3 3.9a2 2 0 0 0-1.4-.6H5a2 2 0 0 0-2 2v5.9c0 .5.2 1 .6 1.4l8.4 8.4a2 2 0 0 0 2.8 0l5.8-5.8a2 2 0 0 0 0-2.9Z"/><circle cx="7.5" cy="7.5" r="1.2" fill="currentColor" stroke="none"/></svg>
                        </div>
                        <span class="text-[13.5px] font-semibold text-ink/80 flex-1">Sinkronisasi Promo &amp; Fitur Belanja Terbaru</span>
                        <svg class="w-5 h-5 text-leaf-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8.5 12.5 2.4 2.4 4.6-5.4"/></svg>
                    </div>
                </div>

                <!-- Progress -->
                <div class="mb-6">
                    <div class="flex justify-between items-center text-[11px] font-semibold text-ink/40 mb-1.5">
                        <span>Sedang berjalan</span>
                        <span class="font-mono" id="checkCountdown">cek ulang 10s</span>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-ink/[0.06] overflow-hidden">
                        <div class="relative h-full w-3/4 rounded-full bg-gradient-to-r from-teal-400 to-teal-600 shimmer overflow-hidden"></div>
                    </div>
                </div>

                <!-- Actions -->
                <button id="btnCheckStatus" onclick="checkServerStatus(true)" class="w-full py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] text-white font-bold text-[14px] transition-all duration-150 shadow-soft flex items-center justify-center gap-2 mb-3">
                    <svg id="refreshIcon" class="w-4 h-4 transition-transform duration-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 0 1 15.3-6.4L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.3 6.4L3 16"/><path d="M3 21v-5h5"/>
                    </svg>
                    <span id="btnCheckText">Coba Muat Ulang Aplikasi</span>
                </button>

                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20NitipDong,%20saya%20ingin%20bertanya%20terkait%20layanan%20website" target="_blank" rel="noopener noreferrer" class="w-full py-3 rounded-2xl border border-ink/10 hover:bg-ink/[0.03] text-ink/70 font-semibold text-[13.5px] transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-leaf-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/>
                    </svg>
                    Hubungi CS
                </a>

                <div id="statusToast" class="hidden mt-3 py-2.5 px-4 rounded-xl text-[12px] font-semibold transition-all"></div>
            </div>

            <p class="text-center text-[11px] text-ink/35 font-medium mt-5">
                NitipDong Mobile &bull; Versi 1.1.4
            </p>
        </div>
    </main>

    <script>
        // Live clock (WIB)
        function updateClock() {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const wib = new Date(utc + (3600000 * 7));
            const h = String(wib.getHours()).padStart(2, '0');
            const m = String(wib.getMinutes()).padStart(2, '0');
            const s = String(wib.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.textContent = `${h}.${m}.${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Server status polling
        let countdown = 10;
        const countdownEl = document.getElementById('checkCountdown');
        const btnText = document.getElementById('btnCheckText');
        const refreshIcon = document.getElementById('refreshIcon');
        const toast = document.getElementById('statusToast');

        async function checkServerStatus(manual = false) {
            if (manual) {
                btnText.textContent = 'Memeriksa...';
                refreshIcon.classList.add('animate-spin');
            }

            try {
                const response = await fetch('/api/v1/system/status', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });

                if (response.status === 200) {
                    const data = await response.json();
                    if (data.is_maintenance === false) {
                        showToast('Server aktif kembali. Mengalihkan ke halaman utama…', 'success');
                        setTimeout(() => { window.location.href = '/'; }, 1200);
                        return;
                    }
                }

                if (manual) {
                    showToast('Sistem masih dalam pemeliharaan. Coba lagi sebentar.', 'warning');
                }
            } catch (err) {
                if (manual) {
                    showToast('Belum dapat terhubung. Coba lagi sebentar.', 'warning');
                }
            } finally {
                if (manual) {
                    setTimeout(() => {
                        btnText.textContent = 'Coba Muat Ulang Aplikasi';
                        refreshIcon.classList.remove('animate-spin');
                    }, 600);
                }
                countdown = 10;
            }
        }

        function showToast(msg, type) {
            if (!toast) return;
            toast.classList.remove('hidden', 'bg-leaf-500/10', 'text-leaf-500', 'bg-amber-50', 'text-amber-600');
            toast.classList.add(type === 'success' ? 'bg-leaf-500/10' : 'bg-amber-50', type === 'success' ? 'text-leaf-500' : 'text-amber-600');
            toast.textContent = msg;
            toast.classList.remove('hidden');
        }

        setInterval(() => {
            countdown--;
            if (countdownEl) countdownEl.textContent = `cek ulang ${countdown}s`;
            if (countdown <= 0) {
                countdown = 10;
                checkServerStatus(false);
            }
        }, 1000);
    </script>
</body>
</html>