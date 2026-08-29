<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem — NitipDong</title>
    <meta name="description" content="NitipDong sedang dalam pemeliharaan terjadwal untuk peningkatan performa dan fitur baru.">
    <link rel="icon" type="image/svg+xml" href="/icon-app-web-terbaru/nitipdong-icon-mark.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

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
                        card: '0 4px 6px -1px rgba(19,42,46,0.04), 0 24px 60px -12px rgba(14,138,138,0.16)',
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
            overflow-x: hidden;
        }

        /* Ambient glowing background blobs */
        .blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(40px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.85;
        }
        .blob-teal {
            width: 520px; height: 520px;
            top: -160px; right: -120px;
            background: radial-gradient(circle at 35% 35%, #D8F2F1 0%, #EAF6F5 55%, transparent 75%);
        }
        .blob-peach {
            width: 480px; height: 480px;
            bottom: -160px; left: -120px;
            background: radial-gradient(circle at 60% 40%, #FCE9DF 0%, #FBF1EA 55%, transparent 75%);
        }

        @keyframes drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(0, -8px) rotate(2deg); }
        }
        .drift { animation: drift 6s ease-in-out infinite; }

        @keyframes softPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(14,138,138,0.22); }
            50% { box-shadow: 0 0 0 16px rgba(14,138,138,0); }
        }
        .pulse-ring { animation: softPulse 2.8s ease-out infinite; }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(0.85); }
        }
        .dot-blink { animation: dotBlink 1.6s ease-in-out infinite; }

        /* Progress shimmer */
        @keyframes shimmer {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(220%); }
        }
        .shimmer::after {
            content: '';
            position: absolute;
            inset: 0;
            width: 45%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.75), transparent);
            animation: shimmer 2.2s infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .drift, .pulse-ring, .dot-blink, .shimmer::after { animation: none !important; }
        }
    </style>
</head>
<body class="min-h-screen relative text-ink antialiased flex flex-col justify-between">

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
            : ('Sistem sedang dalam pemeliharaan terjadwal' . ($windowStart && $windowEnd ? " pada pukul {$windowStart} – {$windowEnd} WIB." : '.') . ' Kami sedang melakukan peningkatan performa & sistem keamanan.');
    @endphp

    <!-- Header Navigation -->
    <header class="relative z-10 w-full px-5 sm:px-8 py-5">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-soft text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <path d="M3 6h18"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-xl tracking-tight text-ink">NitipDong</span>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full bg-teal-50 border border-teal-500/20 text-[10.5px] font-bold text-teal-700 tracking-wide uppercase">
                        System Maintenance
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-500/20 text-amber-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 dot-blink"></span>
                    <span>Status Server: Pemeliharaan</span>
                </div>
                <div class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/90 backdrop-blur border border-ink/[0.06] shadow-xs">
                    <svg class="w-3.5 h-3.5 text-ink/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span id="liveClock" class="font-mono text-xs font-semibold text-ink/75 tabular-nums">--.--.-- WIB</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content: Landscape Card -->
    <main class="relative z-10 flex items-center justify-center px-4 sm:px-6 py-4 sm:py-8 flex-1">
        <div class="w-full max-w-5xl">
            <div class="bg-white rounded-[32px] shadow-card border border-white/80 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12">
                    
                    <!-- Left Section: Main Info & Actions (Landscape Left) -->
                    <div class="lg:col-span-6 p-6 sm:p-8 lg:p-10 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-ink/[0.06] bg-gradient-to-b from-white via-white to-teal-50/20">
                        <div>
                            <!-- Header Icon & Status Pill -->
                            <div class="flex items-center gap-4 mb-6">
                                <div class="relative w-16 h-16 shrink-0">
                                    <div class="absolute inset-0 rounded-2xl bg-teal-100/90 drift"></div>
                                    <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 pulse-ring flex items-center justify-center shadow-md">
                                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-500/25 text-amber-600 text-[11px] font-extrabold tracking-wide uppercase mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dot-blink"></span>
                                        Pemeliharaan Terjadwal
                                    </div>
                                    <div class="text-[12px] font-semibold text-ink/40 flex items-center gap-1">
                                        <span>Status: HTTP 503</span>
                                        <span>&bull;</span>
                                        <span>NitipDong Web</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink leading-tight tracking-tight mb-3">
                                {{ $title }} 🛠️
                            </h1>

                            <!-- Message Box -->
                            <div class="bg-teal-50/70 border border-teal-500/15 rounded-2xl p-4 sm:p-5 mb-5">
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-xl bg-teal-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    </div>
                                    <p class="text-[13.5px] leading-relaxed text-ink/80 font-medium">
                                        {{ $message }}
                                    </p>
                                </div>
                            </div>

                            @if($windowStart && $windowEnd)
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50/90 border border-amber-500/20 text-amber-800 text-xs font-semibold mb-6">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span>Estimasi Waktu: <strong>{{ $windowStart }} – {{ $windowEnd }} WIB</strong></span>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 pt-3">
                            <button id="btnCheckStatus" onclick="checkServerStatus(true)" class="w-full py-3.5 px-6 rounded-2xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] text-white font-bold text-[14px] transition-all duration-150 shadow-soft hover:shadow-lg hover:shadow-teal-600/20 flex items-center justify-center gap-2.5">
                                <svg id="refreshIcon" class="w-4.5 h-4.5 transition-transform duration-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 0 1 15.3-6.4L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.3 6.4L3 16"/><path d="M3 21v-5h5"/>
                                </svg>
                                <span id="btnCheckText">Coba Muat Ulang Halaman</span>
                            </button>

                            <div class="flex items-center gap-2.5">
                                <a href="https://wa.me/6282131588846?text=Halo%20Admin%20NitipDong,%20saya%20ingin%20bertanya%20terkait%20layanan%20website" target="_blank" rel="noopener noreferrer" class="flex-1 py-3 px-4 rounded-2xl border border-ink/10 hover:bg-ink/[0.03] text-ink/75 font-semibold text-[13px] transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-leaf-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/>
                                    </svg>
                                    <span>Hubungi CS WhatsApp</span>
                                </a>
                                <a href="/" class="py-3 px-4 rounded-2xl border border-ink/10 hover:bg-ink/[0.03] text-ink/60 hover:text-ink/85 font-semibold text-[13px] transition-colors flex items-center justify-center gap-1.5" title="Beranda">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </a>
                            </div>

                            <div id="statusToast" class="hidden py-2.5 px-4 rounded-xl text-[12px] font-semibold transition-all"></div>
                        </div>
                    </div>

                    <!-- Right Section: System Process Details (Landscape Right) -->
                    <div class="lg:col-span-6 p-6 sm:p-8 lg:p-10 flex flex-col justify-between bg-slate-50/60">
                        <div>
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <h2 class="text-[13px] font-extrabold text-ink uppercase tracking-wider">Aktivitas Pembaruan</h2>
                                    <p class="text-[12px] text-ink/50 mt-0.5">Peningkatan performa &amp; stabilitas platform</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-500/20 text-[11px] font-bold">
                                    3 Proses Aktif
                                </span>
                            </div>

                            <!-- Feature List -->
                            <div class="space-y-3 mb-6">
                                <!-- Process 1 -->
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-ink/[0.06] shadow-xs flex items-center gap-3.5 hover:border-teal-500/30 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8Z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-0.5">
                                            <h3 class="text-[13px] font-bold text-ink truncate">Kecepatan &amp; Respons Server</h3>
                                            <span class="text-[11px] font-bold text-leaf-500 flex items-center gap-1 shrink-0">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                Optimal
                                            </span>
                                        </div>
                                        <p class="text-[11.5px] text-ink/55 leading-normal">Penyempurnaan cache &amp; optimasi query database.</p>
                                    </div>
                                </div>

                                <!-- Process 2 -->
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-ink/[0.06] shadow-xs flex items-center gap-3.5 hover:border-teal-500/30 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2 4 5v6c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V5l-8-3Z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-0.5">
                                            <h3 class="text-[13px] font-bold text-ink truncate">Keamanan Akun &amp; Transaksi</h3>
                                            <span class="text-[11px] font-bold text-leaf-500 flex items-center gap-1 shrink-0">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                Terlindungi
                                            </span>
                                        </div>
                                        <p class="text-[11.5px] text-ink/55 leading-normal">Pembaruan enkripsi checkout &amp; proteksi data akun.</p>
                                    </div>
                                </div>

                                <!-- Process 3 -->
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-ink/[0.06] shadow-xs flex items-center gap-3.5 hover:border-amber-500/30 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.6 12.3 12.3 3.9a2 2 0 0 0-1.4-.6H5a2 2 0 0 0-2 2v5.9c0 .5.2 1 .6 1.4l8.4 8.4a2 2 0 0 0 2.8 0l5.8-5.8a2 2 0 0 0 0-2.9Z"/><circle cx="7.5" cy="7.5" r="1.2" fill="currentColor" stroke="none"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-0.5">
                                            <h3 class="text-[13px] font-bold text-ink truncate">Sinkronisasi Fitur &amp; Promo</h3>
                                            <span class="text-[11px] font-bold text-amber-600 flex items-center gap-1 shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dot-blink"></span>
                                                Berjalan
                                            </span>
                                        </div>
                                        <p class="text-[11.5px] text-ink/55 leading-normal">Sinkronisasi katalog promo diskon &amp; pelacakan kurir.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Progress & Security Guarantee -->
                        <div class="space-y-3.5 pt-2">
                            <!-- Countdown & Progress Bar -->
                            <div class="p-4 rounded-2xl bg-white border border-ink/[0.06] shadow-xs">
                                <div class="flex justify-between items-center text-xs font-semibold mb-2">
                                    <span class="text-ink/65 flex items-center gap-1.5 text-[11.5px]">
                                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-ping"></span>
                                        Pemeriksaan status otomatis
                                    </span>
                                    <span class="font-mono text-teal-700 bg-teal-50 px-2 py-0.5 rounded-md font-bold text-[11px]" id="checkCountdown">cek ulang 10s</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-ink/[0.06] overflow-hidden">
                                    <div class="relative h-full w-4/5 rounded-full bg-gradient-to-r from-teal-400 to-teal-600 shimmer overflow-hidden"></div>
                                </div>
                            </div>

                            <!-- Security Guarantee Banner -->
                            <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-leaf-500/[0.08] border border-leaf-500/20 text-ink/80 text-[12px]">
                                <svg class="w-4.5 h-4.5 text-leaf-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span class="font-medium leading-snug">Data transaksi, saldo akun, dan riwayat pesanan Anda dijamin aman.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left text-[11.5px] text-ink/40 font-medium mt-5 px-3">
                <span>&copy; {{ date('Y') }} NitipDong Platform &bull; All Rights Reserved</span>
                <span>NitipDong Web System &bull; Versi {{ env('APP_MOBILE_LATEST_VERSION', '2.0.2') }} Major</span>
            </div>
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
            if (clockEl) clockEl.textContent = `${h}.${m}.${s} WIB`;
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
                        btnText.textContent = 'Coba Muat Ulang Halaman';
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

