<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Halaman Berakhir — NitipDong</title>
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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-white">
    <div class="max-w-md w-full text-center space-y-6">
        <!-- Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl mb-2">
            <svg class="w-10 h-10 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <!-- Badge -->
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                Sesi Berakhir (419)
            </span>
        </div>

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            Sesi Formulir Kedaluwarsa
        </h1>

        <!-- Message Box -->
        <div class="bg-slate-900/60 backdrop-blur border border-slate-800 rounded-2xl p-5 text-slate-300 text-sm leading-relaxed">
            <p>Sesi keamanan halaman telah berakhir karena tidak ada aktivitas beberapa saat. Silakan muat ulang formulir untuk melanjutkan pendaftaran atau masuk ke akun Anda.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button onclick="window.location.reload()" class="flex-1 py-3 px-5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-sm transition-all duration-200 shadow-lg shadow-cyan-600/25 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Muat Ulang Halaman</span>
            </button>
            <a href="/" class="py-3 px-5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-sm transition-all text-center">
                Kembali ke Beranda
            </a>
        </div>

        <!-- Footer Info -->
        <p class="text-xs text-slate-500 font-medium">
            NitipDong Platform • Official Marketplace
        </p>
    </div>
</body>
</html>
