<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:down {msg? : Pesan kustom pemeliharaan} {--mobile : Hanya matikan aplikasi mobile} {--web : Hanya matikan website} {--title= : Judul modal pemeliharaan} {--secret= : Secret bypass}', function () {
    $message = $this->argument('msg');
    $title = $this->option('title') ?: 'Mode Pemeliharaan & Pengembangan 🛠️';
    $secret = $this->option('secret');
    $isMobileOnly = $this->option('mobile');
    $isWebOnly = $this->option('web');

    $data = [
        'title'      => $title,
        'message'    => $message ?: 'Sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.',
        'created_at' => date('Y-m-d H:i:s'),
    ];

    if ($isMobileOnly) {
        // Hanya Mobile App yang Down (Web tetap Live normal)
        \Illuminate\Support\Facades\File::put(
            storage_path('framework/maintenance_app.json'), 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $this->info("📱 [MOBILE ONLY] Mode maintenance aktif khusus Aplikasi Mobile.");
        $this->line("🌐 Website tetap AKTIF (Live) secara normal.");
    } elseif ($isWebOnly) {
        // Hanya Website yang Down (Mobile App tetap Live)
        \Illuminate\Support\Facades\File::put(
            storage_path('framework/maintenance_web.json'), 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $this->info("🌐 [WEB ONLY] Mode maintenance aktif khusus Website.");
        $this->line("📱 Aplikasi Mobile tetap AKTIF (Live) secara normal.");
    } else {
        // Keduanya (Web & Mobile) Down
        $params = [];
        if ($secret) {
            $params['--secret'] = $secret;
        }
        $this->call('down', $params);
        \Illuminate\Support\Facades\File::put(
            storage_path('framework/maintenance_message.json'), 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $this->info("🚀 [ALL] Mode maintenance aktif untuk WEBSITE & APLIKASI MOBILE.");
    }

    if ($message) {
        $this->line("📢 Pesan: <comment>{$message}</comment>");
    }
})->purpose('Mengaktifkan mode pemeliharaan (Bisa Semua, Khusus Mobile, atau Khusus Web)');

Artisan::command('app:up {--mobile : Hidupkan kembali khusus mobile} {--web : Hidupkan kembali khusus web}', function () {
    $isMobile = $this->option('mobile');
    $isWeb = $this->option('web');

    if ($isMobile) {
        @unlink(storage_path('framework/maintenance_app.json'));
        $this->info("📱 Aplikasi Mobile sudah kembali aktif normal (Live/Up)!");
    } elseif ($isWeb) {
        @unlink(storage_path('framework/maintenance_web.json'));
        $this->info("🌐 Website sudah kembali aktif normal (Live/Up)!");
    } else {
        $this->call('up');
        @unlink(storage_path('framework/maintenance_message.json'));
        @unlink(storage_path('framework/maintenance_app.json'));
        @unlink(storage_path('framework/maintenance_web.json'));
        $this->info("🚀 Seluruh sistem NitipDong (Web & Mobile Apps) sudah kembali aktif normal (Live/Up)!");
    }
})->purpose('Menonaktifkan mode pemeliharaan');
