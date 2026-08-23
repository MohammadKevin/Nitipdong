<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SystemConfigController extends Controller
{
    /**
     * Get system status, maintenance mode, and app version config.
     */
    public function status(): JsonResponse
    {
        $downFilePath = storage_path('framework/down');
        $downData = [];
        if (file_exists($downFilePath)) {
            $downData = json_decode(@file_get_contents($downFilePath), true) ?: [];
        }

        $appMsgFilePath = storage_path('framework/maintenance_app.json');
        $globalMsgFilePath = storage_path('framework/maintenance_message.json');
        
        $msgData = [];
        if (file_exists($appMsgFilePath)) {
            $msgData = json_decode(@file_get_contents($appMsgFilePath), true) ?: [];
        } elseif (file_exists($globalMsgFilePath)) {
            $msgData = json_decode(@file_get_contents($globalMsgFilePath), true) ?: [];
        }

        $isGeneralDown = (method_exists(app(), 'isDownForMaintenance') && app()->isDownForMaintenance())
            || file_exists($downFilePath);

        // Mobile maintenance is active if:
        // 1. APP_MOBILE_MAINTENANCE=true in .env
        // 2. storage/framework/maintenance_app.json exists (php artisan app:down --mobile)
        // 3. General down is active AND not marked as web-only
        $isMobileDown = env('APP_MOBILE_MAINTENANCE', false) 
            || file_exists($appMsgFilePath)
            || ($isGeneralDown && !file_exists(storage_path('framework/maintenance_web.json')) && !env('APP_WEB_MAINTENANCE', false));

        $customMessage = $msgData['message'] ?? $downData['message'] ?? env('APP_MAINTENANCE_MESSAGE') ?? env('APP_MOBILE_MAINTENANCE_MESSAGE') ?? null;
        $maintenanceMessage = !empty($customMessage)
            ? (string) $customMessage
            : 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.';

        $customTitle = $msgData['title'] ?? env('APP_MAINTENANCE_TITLE') ?? env('APP_MOBILE_MAINTENANCE_TITLE') ?? null;
        $maintenanceTitle = !empty($customTitle)
            ? (string) $customTitle
            : 'Mode Pemeliharaan & Pengembangan 🛠️';

        $latestVersion = env('APP_MOBILE_LATEST_VERSION', '1.0.7');
        $minVersion = env('APP_MOBILE_MIN_VERSION', '1.0.0');

        return response()->json([
            'success'             => true,
            'is_maintenance'      => (bool) $isMobileDown,
            'maintenance_title'   => $maintenanceTitle,
            'maintenance_message' => $maintenanceMessage,
            'min_version'         => $minVersion,
            'latest_version'      => $latestVersion,
            'update_url'          => url('/download/app'),
            'support_whatsapp'    => '6281234567890',
        ]);
    }
}
