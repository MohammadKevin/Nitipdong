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

        $isDown = (method_exists(app(), 'isDownForMaintenance') && app()->isDownForMaintenance())
            || file_exists($downFilePath);

        $isMaintenance = (bool) (env('APP_MAINTENANCE', false) || $isDown);

        $customMessage = $downData['message'] ?? env('APP_MAINTENANCE_MESSAGE') ?? null;
        $maintenanceMessage = !empty($customMessage)
            ? (string) $customMessage
            : 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.';

        $customTitle = env('APP_MAINTENANCE_TITLE') ?? null;
        $maintenanceTitle = !empty($customTitle)
            ? (string) $customTitle
            : 'Mode Pemeliharaan & Pengembangan 🛠️';

        $latestVersion = env('APP_MOBILE_LATEST_VERSION', '1.0.3');
        $minVersion = env('APP_MOBILE_MIN_VERSION', '1.0.0');

        return response()->json([
            'success'             => true,
            'is_maintenance'      => $isMaintenance,
            'maintenance_title'   => $maintenanceTitle,
            'maintenance_message' => $maintenanceMessage,
            'min_version'         => $minVersion,
            'latest_version'      => $latestVersion,
            'update_url'          => url('/download/app'),
            'support_whatsapp'    => '6281234567890',
        ]);
    }
}
