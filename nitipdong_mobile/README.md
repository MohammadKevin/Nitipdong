# 📱 NitipDong Mobile App (Flutter Native)

Aplikasi mobile resmi **NitipDong** dibangun dengan **Flutter (Dart)** dan terhubung secara langsung (*real-time*) ke backend **Laravel 11 REST API**.

---

## 🚀 Fitur Utama Mobile App:
- **🎨 Brand Identity NitipDong:** Tema elegan Cyan (`#0891b2`), Deep Navy (`#0b1528`), dan typography Google Fonts Plus Jakarta Sans.
- **🔐 Autentikasi Token Sanctum:** Login & Register akun pembeli/seller, profile, logout.
- **🛍️ Katalog & Promo Feed:**
  - Banner Carousel interaktif dengan auto-indicator.
  - Quick Category filter horizontal chips.
  - Flash Sale countdown timer dengan bar progress sisa stok.
  - Infinite / paginated 2-column product grid with badges (Diskon, Rating, Terjual).
- **🔎 Pencarian & Filter Produk:** Pencarian live berdasarkan kata kunci, sorting (Termurah, Termahal, Terlaris, Terpopuler).
- **📦 Detail Produk (PDP):**
  - Galeri gambar multi-slide dengan indikator zoom.
  - Informasi toko & badge Official.
  - Tombol aksi sticky: Chat Toko, Tambah Keranjang (+ Keranjang), dan Beli Sekarang.
- **🛒 Keranjang Belanja:** Stepper jumlah (+/-), subtotal otomatis, dan hapus item.
- **💳 Checkout:** Pemilihan kurir pengiriman (Gratis Ongkir Rp0), metode pembayaran (QRIS, VA Bank), dan konfirmasi pesanan instan.
- **📋 Status Pesanan:** Filter pesanan (Menunggu, Diproses, Dikirim, Selesai) dan detail rincian transaksi.

---

## ⚙️ Konfigurasi URL Server API (`lib/services/api_service.dart`)

Buka file [`lib/services/api_service.dart`](lib/services/api_service.dart) dan sesuaikan `baseUrl`:
- **Android Emulator (Default):** `http://10.0.2.2:8000/api/v1`
- **HP Android Fisik (Satu Jaringan Wi-Fi):** `http://192.168.x.x:8000/api/v1`
- **Server Production / Hosting DomaiNesia:** `https://domain-anda.com/api/v1`

---

## 🛠️ Cara Menjalankan & Build APK

### 1. Jalankan Aplikasi di Emulator / HP:
```bash
cd nitipdong_mobile
flutter pub get
flutter run
```

### 2. Build File Installer APK Android:
```bash
flutter build apk --release
```
File APK siap install akan berada di:
`build/app/outputs/flutter-apk/app-release.apk`
