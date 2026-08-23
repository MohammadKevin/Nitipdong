<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Pemeliharaan Sistem — NitipDong</title>
    <meta name="description" content="Website NitipDong sedang dalam tahap pemeliharaan sistem terjadwal untuk peningkatan performa dan fitur baru.">
    <link rel="icon" type="image/png" href="/img/nitipdong-logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            400: '#4ade80',
                            500: '#10b981',
                            600: '#059669',
                            900: '#064e3b',
                        },
                        navy: {
                            800: '#0d1829',
                            850: '#0a1322',
                            900: '#070d18',
                            950: '#040810',
                        },
                        accent: {
                            cyan: '#06b6d4',
                            sky: '#0ea5e9',
                            amber: '#f59e0b',
                            rose: '#f43f5e',
                            violet: '#8b5cf6',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #060b14;
            color: #f8fafc;
            overflow-x: hidden;
        }

        /* Ambient Glow Blobs */
        .ambient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
            animation: floatGlow 12s ease-in-out infinite alternate;
        }
        .blob-1 {
            width: 420px;
            height: 420px;
            top: -100px;
            left: 10%;
            background: radial-gradient(circle, #0284c7 0%, #0369a1 50%, transparent 80%);
        }
        .blob-2 {
            width: 480px;
            height: 480px;
            bottom: -120px;
            right: 10%;
            background: radial-gradient(circle, #059669 0%, #0d9488 50%, transparent 80%);
            animation-duration: 16s;
        }
        .blob-3 {
            width: 320px;
            height: 320px;
            top: 40%;
            left: 45%;
            background: radial-gradient(circle, #8b5cf6 0%, #6366f1 50%, transparent 80%);
            opacity: 0.2;
            animation-duration: 10s;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(25px, 20px) scale(1.08); }
            100% { transform: translate(-20px, -15px) scale(0.95); }
        }

        /* Subtle Grid Pattern */
        .bg-grid {
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(13, 24, 41, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 40px -10px rgba(14, 165, 233, 0.15);
        }

        .glass-pill {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Gear & Orbital Animation */
        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes spinReverse {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 25px rgba(14, 165, 233, 0.4), inset 0 0 15px rgba(14, 165, 233, 0.2); }
            50% { box-shadow: 0 0 45px rgba(14, 165, 233, 0.7), inset 0 0 25px rgba(14, 165, 233, 0.4); }
        }

        .spin-slow {
            animation: spinSlow 20s linear infinite;
        }
        .spin-reverse {
            animation: spinReverse 15s linear infinite;
        }
        .glow-pulse {
            animation: pulseGlow 3s ease-in-out infinite;
        }

        /* Shimmer Loading Bar */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-shimmer {
            animation: shimmer 2.5s infinite;
        }
    </style>
</head>
<body class="min-h-screen relative flex flex-col justify-between selection:bg-brand-500 selection:text-white bg-grid">
    <!-- Ambient Background Blobs -->
    <div class="ambient-blob blob-1"></div>
    <div class="ambient-blob blob-2"></div>
    <div class="ambient-blob blob-3"></div>

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
        $title = $msgData['title'] ?? env('APP_MAINTENANCE_TITLE') ?? 'Peningkatan Sistem & Pembaruan Fitur 🛠️';
        $message = !empty($customMessage) && !str_starts_with($customMessage, 'The application is in maintenance mode') 
            ? $customMessage 
            : 'Website NitipDong sedang dalam tahap pemeliharaan sistem terjadwal untuk peningkatan performa server, keamanan transaksi, dan peluncuran fitur terbaru. Kami akan segera kembali online!';
    @endphp

    <!-- 1. Top Navbar Header -->
    <header class="relative z-10 w-full px-6 py-5 sm:px-10 border-b border-white/5 bg-navy-950/40 backdrop-blur-md">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-accent-sky p-0.5 shadow-lg shadow-brand-500/20">
                    <div class="w-full h-full bg-navy-900 rounded-[10px] flex items-center justify-center">
                        <img src="/img/nitipdong-logo.png" alt="NitipDong" class="w-6 h-6 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span class="hidden font-black text-brand-400 text-lg">N</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-lg text-white tracking-tight">NitipDong</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400 border border-brand-500/20">v1.1.1</span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium">Marketplace & Jastip Terpercaya</p>
                </div>
            </div>

            <!-- Server Status & Clock Pill -->
            <div class="hidden sm:flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full glass-pill text-xs font-semibold text-slate-300">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span>Status Server: Maintenance</span>
                </div>
                <div id="liveClock" class="text-xs font-mono text-slate-400 bg-white/5 px-3 py-1.5 rounded-full border border-white/5">
                    --:--:-- WIB
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Main Hero Content -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 my-6 sm:my-10">
        <div class="max-w-3xl w-full">
            <div class="glass-card rounded-3xl p-6 sm:p-10 text-center relative overflow-hidden">
                <!-- Top Glowing Border Line -->
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-accent-sky via-brand-400 to-transparent"></div>

                <!-- 3D Orbiting Centerpiece Icon -->
                <div class="relative w-28 h-28 sm:w-36 sm:h-36 mx-auto mb-8 flex items-center justify-center">
                    <!-- Outer Orbit Ring -->
                    <div class="absolute inset-0 rounded-full border border-dashed border-sky-400/30 spin-slow"></div>
                    
                    <!-- Middle Orbit Ring -->
                    <div class="absolute inset-2 rounded-full border border-emerald-400/20 spin-reverse"></div>

                    <!-- Glowing Center Hub -->
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-navy-800 to-navy-900 border border-sky-400/40 glow-pulse flex items-center justify-center shadow-2xl">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                            <path d="M7 8h10M7 12h5" stroke-opacity="0.6"></path>
                        </svg>
                        
                        <!-- Floating Floating Mini Badges -->
                        <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center font-black text-xs shadow-lg shadow-amber-500/30">
                            ⚡
                        </div>
                    </div>
                </div>

                <!-- Mode Tag Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-extrabold tracking-wide uppercase mb-4">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>System Upgrade in Progress</span>
                </div>

                <!-- Dynamic Title -->
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight mb-4 leading-tight">
                    {{ $title }}
                </h1>

                <!-- Dynamic Message Card -->
                <div class="max-w-2xl mx-auto bg-navy-900/80 border border-slate-700/50 rounded-2xl p-5 sm:p-6 mb-8 text-slate-300 text-sm sm:text-base leading-relaxed text-left sm:text-center relative">
                    <div class="flex items-start sm:justify-center gap-3">
                        <span class="text-xl sm:text-2xl flex-shrink-0">📢</span>
                        <p class="font-medium text-slate-200">{{ $message }}</p>
                    </div>
                </div>

                <!-- Progress Shimmer Bar -->
                <div class="max-w-md mx-auto mb-8">
                    <div class="flex justify-between items-center text-xs font-semibold text-slate-400 mb-2">
                        <span>Optimalisasi Database & API</span>
                        <span class="text-brand-400 font-mono" id="checkCountdown">Auto-check: 10s</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800/80 overflow-hidden relative">
                        <div class="h-full bg-gradient-to-r from-accent-sky via-brand-400 to-accent-cyan rounded-full w-3/4 relative">
                            <div class="absolute inset-0 bg-white/30 animate-shimmer"></div>
                        </div>
                    </div>
                </div>

                <!-- Feature Highlights Under Maintenance -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto mb-8 text-left">
                    <div class="glass-pill rounded-xl p-3 border border-white/5 hover:border-sky-500/30 transition-colors">
                        <div class="text-base mb-1">🤖</div>
                        <div class="text-xs font-bold text-white">Gemini AI</div>
                        <div class="text-[10.5px] text-slate-400">Asisten Belanja Pintar</div>
                    </div>
                    <div class="glass-pill rounded-xl p-3 border border-white/5 hover:border-brand-500/30 transition-colors">
                        <div class="text-base mb-1">💳</div>
                        <div class="text-xs font-bold text-white">QRIS Instan</div>
                        <div class="text-[10.5px] text-slate-400">Bebas Biaya Admin</div>
                    </div>
                    <div class="glass-pill rounded-xl p-3 border border-white/5 hover:border-accent-amber/30 transition-colors">
                        <div class="text-base mb-1">🚚</div>
                        <div class="text-xs font-bold text-white">Lacak Resi</div>
                        <div class="text-[10.5px] text-slate-400">Update Status Realtime</div>
                    </div>
                    <div class="glass-pill rounded-xl p-3 border border-white/5 hover:border-accent-violet/30 transition-colors">
                        <div class="text-base mb-1">🎟️</div>
                        <div class="text-xs font-bold text-white">Kupon Promo</div>
                        <div class="text-[10.5px] text-slate-400">Diskon & Flash Sale</div>
                    </div>
                </div>

                <!-- Action Button Group -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                    <!-- Check Server Button -->
                    <button id="btnCheckStatus" onclick="checkServerStatus()" class="w-full sm:w-auto flex-1 py-3.5 px-6 rounded-xl bg-gradient-to-r from-sky-500 to-brand-500 hover:from-sky-400 hover:to-brand-400 text-white font-bold text-sm transition-all duration-200 shadow-lg shadow-sky-500/25 flex items-center justify-center gap-2 group active:scale-95">
                        <svg id="refreshIcon" class="w-4 h-4 transition-transform group-hover:rotate-180 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span id="btnCheckText">Periksa Status Server</span>
                    </button>

                    <!-- CS Hotline WhatsApp Button -->
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20NitipDong,%20saya%20ingin%20bertanya%20terkait%20layanan%20website" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto py-3.5 px-5 rounded-xl glass-pill hover:bg-white/10 text-slate-200 font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 border border-white/10">
                        <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/>
                        </svg>
                        <span>Hubungi CS</span>
                    </a>
                </div>

                <!-- Toast Notification Container -->
                <div id="statusToast" class="hidden mt-4 p-3 rounded-xl text-xs font-semibold max-w-md mx-auto transition-all"></div>
            </div>
        </div>
    </main>

    <!-- 3. Footer -->
    <footer class="relative z-10 w-full px-6 py-4 text-center border-t border-white/5 bg-navy-950/60 backdrop-blur">
        <p class="text-xs text-slate-500 font-medium">
            &copy; {{ date('Y') }} <span class="text-slate-400 font-bold">NitipDong</span>. Hak Cipta Dilindungi Undang-Undang.
        </p>
    </footer>

    <!-- Interactive Auto-Poll & Real-time Clock Script -->
    <script>
        // Live Clock (WIB)
        function updateClock() {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const wib = new Date(utc + (3600000 * 7)); // UTC+7
            const timeStr = wib.toTimeString().split(' ')[0] + ' WIB';
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Server Status Polling
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
                        showToast('🎉 Server telah aktif kembali! Mengalihkan ke halaman utama...', 'success');
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1200);
                        return;
                    }
                }
                
                if (manual) {
                    showToast('Sistem masih dalam tahap pemeliharaan. Mohon tunggu beberapa saat lagi.', 'warning');
                }
            } catch (err) {
                if (manual) {
                    showToast('Koneksi sedang disiapkan. Silakan coba kembali sesaat lagi.', 'warning');
                }
            } finally {
                if (manual) {
                    setTimeout(() => {
                        btnText.textContent = 'Periksa Status Server';
                        refreshIcon.classList.remove('animate-spin');
                    }, 600);
                }
                countdown = 10;
            }
        }

        function showToast(msg, type) {
            if (!toast) return;
            toast.classList.remove('hidden', 'bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/30', 'bg-amber-500/20', 'text-amber-300', 'border-amber-500/30');
            if (type === 'success') {
                toast.classList.add('bg-emerald-500/20', 'text-emerald-300', 'border', 'border-emerald-500/30');
            } else {
                toast.classList.add('bg-amber-500/20', 'text-amber-300', 'border', 'border-amber-500/30');
            }
            toast.textContent = msg;
            toast.classList.remove('hidden');
        }

        // Periodic 1-second interval for countdown & trigger check
        setInterval(() => {
            countdown--;
            if (countdownEl) {
                countdownEl.textContent = `Auto-check: ${countdown}s`;
            }
            if (countdown <= 0) {
                countdown = 10;
                checkServerStatus(false);
            }
        }, 1000);
    </script>
</body>
</html>
