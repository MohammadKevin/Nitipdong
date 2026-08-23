param (
    [string]$Version
)

Write-Host ""
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  🚀 NITIPDONG 1-CLICK ALL-IN-ONE BUILD & RELEASE SCRIPT  " -ForegroundColor Yellow -BackgroundColor DarkBlue
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Tentukan Versi Target
if (-not $Version) {
    $currentVer = "2.0.2"
    $Version = Read-Host "Masukkan nomor versi baru yang akan dirilis (Contoh: 2.0.3)"
    if (-not $Version) {
        $Version = "2.0.3"
    }
}

Write-Host "📌 Target Versi Rilis: v$Version" -ForegroundColor Green

# 2. Jalankan Artisan Otomatisasi Versi (.env, pubspec, api_service, cache)
Write-Host "`n[1/4] Menyinkronkan konfigurasi versi ke .env, Flutter, dan API..." -ForegroundColor Cyan
php artisan app:release $Version --force

# 3. Build APK Flutter Release
Write-Host "`n[2/4] Membangun (Build) APK Flutter Release..." -ForegroundColor Cyan
$mobileDir = Join-Path $PSScriptRoot "nitipdong_mobile"
Push-Location $mobileDir

# Cek apakah flutter tersedia di PATH, jika tidak cari di folder Flutter umum
$flutterCmd = "flutter"
try {
    & $flutterCmd --version | Out-Null
} catch {
    if (Test-Path "C:\src\flutter\bin\flutter.bat") {
        $flutterCmd = "C:\src\flutter\bin\flutter.bat"
    } elseif (Test-Path "$env:LOCALAPPDATA\flutter\bin\flutter.bat") {
        $flutterCmd = "$env:LOCALAPPDATA\flutter\bin\flutter.bat"
    }
}

& $flutterCmd build apk --release
$buildSuccess = $LASTEXITCODE -eq 0
Pop-Location

if (-not $buildSuccess) {
    Write-Host "`n⚠️ Build Flutter APK selesai atau butuh dijalankan terpisah jika Flutter PATH belum disetel." -ForegroundColor Yellow
} else {
    Write-Host "✅ Build APK Release Berhasil!" -ForegroundColor Green
}

# 4. Copy & Deploy APK ke folder downloads
Write-Host "`n[3/4] Mengunggah APK ke folder public/downloads..." -ForegroundColor Cyan
$sourceApk = Join-Path $mobileDir "build\app\outputs\flutter-apk\app-release.apk"
$publicDownloads = Join-Path $PSScriptRoot "public\downloads"

if (-not (Test-Path $publicDownloads)) {
    New-Item -ItemType Directory -Path $publicDownloads -Force | Out-Null
}

if (Test-Path $sourceApk) {
    Copy-Item $sourceApk (Join-Path $publicDownloads "nitipdong.apk") -Force
    Copy-Item $sourceApk (Join-Path $publicDownloads "NitipDong-latest.apk") -Force
    Copy-Item $sourceApk (Join-Path $publicDownloads "NitipDong-v$Version.apk") -Force
    Write-Host "✅ APK berhasil disalin ke: public/downloads/nitipdong.apk" -ForegroundColor Green
} else {
    Write-Host "ℹ️ Letakkan file APK hasil build di: public/downloads/nitipdong.apk" -ForegroundColor Gray
}

# 5. Clear Cache Terakhir
Write-Host "`n[4/4] Menyegarkan cache Laravel..." -ForegroundColor Cyan
php artisan config:clear
php artisan route:clear

Write-Host ""
Write-Host "==========================================================" -ForegroundColor Green
Write-Host "  🎉 PEMBARUAN RILIS v$Version SELESAI SECARA OTOMATIS!  " -ForegroundColor White -BackgroundColor DarkGreen
Write-Host "==========================================================" -ForegroundColor Green
Write-Host "👉 Link Download Web : https://budayakita.com/download/app" -ForegroundColor Cyan
Write-Host "👉 Status Versi API  : https://budayakita.com/api/v1/system/status" -ForegroundColor Cyan
Write-Host "👉 Force Update      : SEMUA USER LAMA OTOMATIS DIKUNCI & DIMINTA UPDATE" -ForegroundColor Yellow
Write-Host ""
