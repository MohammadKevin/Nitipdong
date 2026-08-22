<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:down {msg? : Pesan kustom pemeliharaan aplikasi} {--title= : Judul modal pemeliharaan} {--secret= : Secret bypass}', function () {
    $message = $this->argument('msg');
    $title = $this->option('title') ?: 'Mode Pemeliharaan & Pengembangan 🛠️';
    $secret = $this->option('secret');

    $params = [];
    if ($secret) {
        $params['--secret'] = $secret;
    }

    $this->call('down', $params);

    $data = [
        'title'      => $title,
        'message'    => $message ?: 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    \Illuminate\Support\Facades\File::put(
        storage_path('framework/maintenance_message.json'), 
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    $this->info("✅ Sistem NitipDong sekarang dalam mode pemeliharaan (Down).");
    if ($message) {
        $this->line("📢 Pesan Mobile/Web: <comment>{$message}</comment>");
    }
})->purpose('Mengaktifkan mode pemeliharaan dengan pesan kustom untuk Mobile & Web');

Artisan::command('app:up', function () {
    $this->call('up');
    @unlink(storage_path('framework/maintenance_message.json'));
    $this->info("🚀 Sistem NitipDong sudah kembali aktif normal (Live/Up)!");
})->purpose('Menonaktifkan mode pemeliharaan');
