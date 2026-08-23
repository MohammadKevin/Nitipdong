# NitipDong

Platform jual beli online berbasis Laravel. Dibangun buat belajar sekaligus dipakai sendiri — mendukung banyak toko (multi-seller), ada sistem approval dari admin, dan tampilannya sudah responsif dari HP sampai desktop.

![NitipDong Logo](public/img/nitipdong-logo.png)

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![Tailwind](https://img.shields.io/badge/Tailwind-3.x-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## Kenapa bikin ini

Awalnya cuma iseng bikin toko online sederhana, tapi lama-lama nambah fitur terus sampai jadi cukup lengkap: ada role customer, seller, admin, sampai super admin. Cocok buat yang mau belajar Laravel dari sistem yang beneran dipakai, bukan sekadar tutorial CRUD.

## Role & fitur

**Customer**
- Cari produk pakai filter & pencarian
- Keranjang belanja dan wishlist
- Beberapa metode pembayaran
- Lacak status pesanan
- Kasih rating & review
- Chat langsung sama seller
- Notifikasi pesanan

**Seller**
- Dashboard toko sendiri
- Upload beberapa foto produk sekaligus
- Lihat statistik penjualan
- Atur stok/inventory
- Laporan keuangan
- Kelola pesanan masuk
- Chat sama pembeli

**Admin**
- Approve toko baru
- Moderasi produk yang di-upload
- Kelola user
- Analytics & reporting
- Atur kategori produk

## Soal tampilan

Desainnya pakai Tailwind, dengan gradient dan efek glass (backdrop blur) di beberapa bagian, plus animasi kecil-kecil biar nggak kaku. Sudah responsif di semua ukuran layar. Untuk galeri produk, ada thumbnail navigation, zoom saat hover, dan lazy loading gambar biar loading-nya nggak berat.

## Instalasi

Butuh PHP 8.2+, Composer, Node.js, dan MySQL/PostgreSQL.

```bash
git clone https://github.com/MohammadKevin/nitipdong.git
cd nitipdong

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Isi konfigurasi database di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nitipdong
DB_USERNAME=root
DB_PASSWORD=
```

Lanjut jalankan migration, seed data (opsional), build assets, terus start servernya:

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder   # opsional, buat data contoh
npm run build
php artisan serve
```

Kalau sudah jalan, buka `http://localhost:8000`.

## Upload foto produk

Format yang didukung: JPG, JPEG, PNG, WebP — maksimal 2MB per file. Resolusi minimal 800x800px, tapi disarankan 1200x1200px dengan rasio 1:1 biar hasilnya rapi di galeri.

Caranya: login sebagai seller → **Dashboard Toko** → **Kelola Produk** → **Tambah/Edit Produk** → upload foto utama plus maksimal 5 foto tambahan → simpan.

Beberapa hal yang bikin foto produk lebih enak dilihat:
- Pencahayaan cukup, jangan gelap atau blur
- Background bersih, jangan terlalu ramai
- Ambil dari beberapa sudut biar detailnya kelihatan
- Hindari watermark yang kebesaran

## Struktur data produk

Field tambahan di tabel produk:

```php
images                // JSON, array foto tambahan
is_featured            // boolean, produk unggulan
badge                  // string: new / sale / hot / bestseller
discount_percentage    // int
rating                 // decimal
sold_count              // int
```

Beberapa helper method di model `Product`:

```php
$product->getAllImages()        // gabungan foto utama + tambahan
$product->getDiscountedPrice()  // harga setelah diskon
$product->getOriginalPrice()    // harga asli
```

## Kustomisasi

**Ganti warna brand** — edit `resources/css/app.css`, misalnya ganti gradient `from-cyan-500 to-blue-600` sesuai warna yang kamu mau.

**Ganti logo** — replace `public/img/icon.jpg`, lalu update referensinya di `welcome.blade.php` dan `app.blade.php`.

**Tambah payment gateway** — tambahkan konfigurasi di `config/services.php`, buat implementasinya di `app/Services/PaymentService.php`, lalu sambungkan ke view checkout.

## Keamanan

Sudah ada proteksi standar: CSRF, pencegahan SQL injection & XSS, autentikasi/otorisasi berbasis role, password di-hash pakai Bcrypt, rate limiting, dan validasi upload file.

## Stack yang dipakai

- **Backend**: Laravel 11, MySQL/PostgreSQL, Laravel Breeze buat auth
- **Frontend**: Tailwind CSS, Alpine.js, Heroicons, font Plus Jakarta Sans & Inter
- **Tools**: Composer, NPM, Vite

## Masih dikerjain

- [✔️] Payment gateway (Midtrans/Xendit)
- [ ] Export laporan ke PDF
- [ ] Email notification
- [ ] Push notification
- [ ] Filter pencarian yang lebih detail
- [ ] Voucher & sistem diskon
- [ ] Loyalty points
- [ ] Integrasi media sosial

## Kontribusi

Kalau mau bantu develop, silakan fork repo ini, bikin branch baru, dan ajukan pull request. Bug report atau saran fitur juga boleh banget lewat Issues.

## Lisensi

MIT — bebas dipakai, silakan cek file [LICENSE](LICENSE) untuk detailnya.

## Kontak

**Kevin**
Website: [corecraft.my.id](https://corecraft.my.id)
Email: kvn4.200581@gmail.com
GitHub: [@MohammadKevin](https://github.com/MohammadKevin)
