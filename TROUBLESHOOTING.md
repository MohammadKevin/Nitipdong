# Troubleshooting Guide - BelanjaIn

## Masalah: Gambar Produk Tidak Muncul

### Penyebab:
1. Cache browser yang lama
2. Cache aplikasi Laravel
3. File gambar tidak ada

### Solusi:

#### 1. Clear Cache Laravel
```bash
php artisan optimize:clear
```

#### 2. Clear Cache Browser
- **Chrome/Edge**: Tekan `Ctrl + Shift + Delete` → Clear cached images and files
- **Firefox**: Tekan `Ctrl + Shift + Delete` → Clear cache
- Atau **Hard Refresh**: `Ctrl + F5`

#### 3. Cek File Gambar
```bash
php check_images.php
```

### Lokasi Gambar:
- Path database: `img/nama-file.jpg`
- Path fisik: `public/img/nama-file.jpg`
- URL: `http://localhost:8000/img/nama-file.jpg`

---

## Masalah: Auto-Scroll Berhenti Setelah 2x Putaran

### Penyebab:
- Logic Alpine.js tidak restart interval dengan benar

### Solusi:
✅ **Sudah diperbaiki** di `welcome.blade.php`
- Resume scroll sekarang re-init interval
- Infinite loop terus berjalan

---

## Masalah: Redirect After Login

### Redirect by Role:
- **Super Admin** → `/super-admin/dashboard`
- **Admin** → `/admin/dashboard`
- **Seller** → `/seller/dashboard`
- **Customer** → `/customer/dashboard`

### Jika masih redirect ke orders:
Check file: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

```php
return match ($user->role) {
    'super_admin' => redirect()->route('super_admin.dashboard'),
    'admin'       => redirect()->route('admin.dashboard'),
    'seller'      => redirect()->route('seller.dashboard'),
    default       => redirect()->route('customer.dashboard'),
};
```

---

## Commands Berguna:

### Clear All Cache
```bash
php artisan optimize:clear
```

### Clear Specific Cache
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Check Products
```bash
php check_products.php
```

### Check Images
```bash
php check_images.php
```

---

## Masalah Umum Lainnya:

### 1. MySQL Not Running
**Error**: `No connection could be made...`

**Solusi**: Start MySQL di XAMPP Control Panel

### 2. 403 Forbidden saat Checkout
**Sudah diperbaiki**: Cart dan checkout routes sudah dipindah keluar dari `role:customer` middleware

### 3. Mass Assignment Error
**Sudah diperbaiki**: Semua models sudah ada `$fillable` property

---

## Tips:

1. **Selalu clear cache** setelah update code
2. **Hard refresh browser** (Ctrl + F5) setelah perubahan
3. **Cek console browser** untuk error JavaScript
4. **Cek Laravel log** di `storage/logs/laravel.log`

---

Dibuat: <?= date('Y-m-d H:i:s') ?>
