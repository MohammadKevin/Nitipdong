<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AppDownCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:down 
                            {msg? : Pesan kustom pemeliharaan} 
                            {--mobile : Hanya aktifkan mode pemeliharaan untuk Aplikasi Mobile} 
                            {--web : Hanya aktifkan mode pemeliharaan untuk Website} 
                            {--title= : Judul modal pemeliharaan} 
                            {--secret= : Secret phrase bypass}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengaktifkan mode pemeliharaan (Bisa Semua, Khusus Mobile, atau Khusus Web)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
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
            File::put(
                storage_path('framework/maintenance_app.json'),
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            $this->info("📱 [MOBILE ONLY] Mode maintenance aktif khusus Aplikasi Mobile.");
            $this->line("🌐 Website tetap AKTIF (Live) secara normal.");
        } elseif ($isWebOnly) {
            // Hanya Website yang Down (Mobile App tetap Live)
            File::put(
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
            File::put(
                storage_path('framework/maintenance_message.json'),
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            $this->info("🚀 [ALL] Mode maintenance aktif untuk WEBSITE & APLIKASI MOBILE.");
        }

        if ($message) {
            $this->line("📢 Pesan: <comment>{$message}</comment>");
        }

        return Command::SUCCESS;
    }
}
