<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AppReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release 
                            {ver? : Nomor versi baru (contoh: 2.0.3)} 
                            {--min= : Batas versi minimal untuk Force Update (default: sama dengan versi baru)} 
                            {--force : Langsung kunci aplikasi lama (Force Update wajib)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatisasi rilis versi baru: update .env, pubspec.yaml, api_service.dart, dan bersihkan cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $version = $this->argument('ver');

        if (!$version) {
            $currentEnv = env('APP_MOBILE_LATEST_VERSION', '2.0.2');
            $version = $this->ask("Masukkan nomor versi baru (Versi saat ini: {$currentEnv})", $this->suggestNextVersion($currentEnv));
        }

        $version = trim($version);
        $minVersion = $this->option('min') ?: ($this->option('force') ? $version : $version);

        $this->info("🚀 Memulai otomatisasi rilis ke versi: v{$version} (Min: v{$minVersion})...");

        // 1. Update .env
        $this->updateEnvFile($version, $minVersion);
        $this->line("✅ File .env berhasil diperbarui (LATEST={$version}, MIN={$minVersion})");

        // 2. Update pubspec.yaml
        $this->updatePubspec($version);
        $this->line("✅ File nitipdong_mobile/pubspec.yaml berhasil diperbarui");

        // 3. Update api_service.dart
        $this->updateApiService($version);
        $this->line("✅ File nitipdong_mobile/lib/services/api_service.dart berhasil diperbarui");

        // 4. Bersihkan Cache Laravel
        $this->call('config:clear');
        $this->call('route:clear');

        $this->newLine();
        $this->info("🎉 SUKSES! Seluruh sistem sekarang sudah sinkron di versi v{$version}!");
        $this->line("👉 Link Download: <comment>" . url("/download/app") . "</comment>");
        $this->line("👉 API Status: <comment>" . url("/api/v1/system/status") . "</comment>");
        $this->newLine();
        $this->line("💡 <info>Langkah selanjutnya</info>: Jalankan <comment>flutter build apk --release</comment> dan letakkan APK di <comment>public/downloads/nitipdong.apk</comment> (atau jalankan script <comment>.\\release_app.ps1 {$version}</comment>)");

        return Command::SUCCESS;
    }

    private function suggestNextVersion(string $current): string
    {
        $parts = explode('.', explode('+', $current)[0]);
        if (count($parts) === 3) {
            $parts[2] = (int) $parts[2] + 1;
            return implode('.', $parts);
        }
        return '2.0.3';
    }

    private function updateEnvFile(string $version, string $minVersion): void
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) return;

        $content = File::get($envPath);

        // Update APP_MOBILE_LATEST_VERSION
        if (preg_match('/^APP_MOBILE_LATEST_VERSION=.*/m', $content)) {
            $content = preg_replace('/^APP_MOBILE_LATEST_VERSION=.*/m', "APP_MOBILE_LATEST_VERSION={$version}", $content);
        } else {
            $content .= "\nAPP_MOBILE_LATEST_VERSION={$version}";
        }

        // Update APP_MOBILE_MIN_VERSION
        if (preg_match('/^APP_MOBILE_MIN_VERSION=.*/m', $content)) {
            $content = preg_replace('/^APP_MOBILE_MIN_VERSION=.*/m', "APP_MOBILE_MIN_VERSION={$minVersion}", $content);
        } else {
            $content .= "\nAPP_MOBILE_MIN_VERSION={$minVersion}";
        }

        File::put($envPath, $content);
    }

    private function updatePubspec(string $version): void
    {
        $pubspecPath = base_path('nitipdong_mobile/pubspec.yaml');
        if (!File::exists($pubspecPath)) return;

        $content = File::get($pubspecPath);
        $buildNumber = 20;

        if (preg_match('/version:\s*[0-9\.]+\+([0-9]+)/', $content, $matches)) {
            $buildNumber = (int) $matches[1] + 1;
        }

        $newVersionLine = "version: {$version}+{$buildNumber}";
        $content = preg_replace('/version:\s*.*/', $newVersionLine, $content);

        File::put($pubspecPath, $content);
    }

    private function updateApiService(string $version): void
    {
        $apiPath = base_path('nitipdong_mobile/lib/services/api_service.dart');
        if (!File::exists($apiPath)) return;

        $content = File::get($apiPath);
        $content = preg_replace(
            '/static const String currentAppVersion = \'[^\']+\';/',
            "static const String currentAppVersion = '{$version}';",
            $content
        );

        File::put($apiPath, $content);
    }
}
