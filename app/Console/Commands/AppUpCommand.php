<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:up 
                            {--mobile : Hidupkan kembali khusus aplikasi mobile} 
                            {--web : Hidupkan kembali khusus website}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menonaktifkan mode pemeliharaan (Bisa Semua, Khusus Mobile, atau Khusus Web)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isMobile = $this->option('mobile');
        $isWeb = $this->option('web');

        if ($isMobile) {
            @unlink(storage_path('framework/maintenance_app.json'));
            $this->info("📱 Aplikasi Mobile sudah kembali aktif normal (Live/Up)!");
        } elseif ($isWeb) {
            @unlink(storage_path('framework/maintenance_web.json'));
            if (file_exists(storage_path('framework/down'))) {
                $this->call('up');
            }
            $this->info("🌐 Website sudah kembali aktif normal (Live/Up)!");
        } else {
            if (file_exists(storage_path('framework/down'))) {
                $this->call('up');
            }
            @unlink(storage_path('framework/maintenance_message.json'));
            @unlink(storage_path('framework/maintenance_app.json'));
            @unlink(storage_path('framework/maintenance_web.json'));
            $this->info("🚀 Seluruh sistem NitipDong (Web & Mobile Apps) sudah kembali aktif normal (Live/Up)!");
        }

        return Command::SUCCESS;
    }
}
