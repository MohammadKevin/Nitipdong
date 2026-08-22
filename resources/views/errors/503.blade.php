<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Pemeliharaan — NitipDong</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b1528;
            color: #ffffff;
        }
        .glow {
            box-shadow: 0 0 50px -10px rgba(14, 165, 233, 0.35);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-sky-500 selection:text-white">
    @php
        $msgFilePath = storage_path('framework/maintenance_message.json');
        $msgData = [];
        if (file_exists($msgFilePath)) {
            $msgData = json_decode(@file_get_contents($msgFilePath), true) ?: [];
        }
        $customMessage = $msgData['message'] ?? env('APP_MAINTENANCE_MESSAGE') ?? (isset($exception) ? $exception->getMessage() : null);
        $title = $msgData['title'] ?? env('APP_MAINTENANCE_TITLE') ?? 'Mode Pemeliharaan & Pengembangan 🛠️';
        $message = !empty($customMessage) ? $customMessage : 'Website NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.';
    @endphp

    <div class="max-w-md w-full text-center space-y-6">
        <!-- Logo / Construction Icon Card -->
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-slate-900/90 border border-slate-750 glow mb-2">
            <svg class="w-12 h-12 text-sky-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>

        <!-- Badge -->
        <div>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-extrabold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/30">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                PENGEMBANGAN SISTEM
            </span>
        </div>

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
            {{ $title }}
        </h1>

        <!-- Message Box -->
        <div class="bg-slate-900/60 backdrop-blur border border-slate-800 rounded-2xl p-5 text-slate-300 text-sm sm:text-base leading-relaxed shadow-xl">
            <p>{{ $message }}</p>
        </div>

        <!-- Refresh Action Button -->
        <div class="pt-2">
            <button onclick="window.location.reload()" class="w-full py-3.5 px-6 rounded-xl bg-sky-500 hover:bg-sky-400 active:scale-[0.99] text-white font-bold text-sm transition-all duration-200 shadow-lg shadow-sky-500/25 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Coba Muat Ulang Halaman</span>
            </button>
        </div>

        <!-- Footer Info -->
        <p class="text-xs text-slate-500 font-medium">
            NitipDong Platform • Official Marketplace
        </p>
    </div>
</body>
</html>
