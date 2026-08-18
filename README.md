# 🛍️ BelanjaIn - Platform E-Commerce Modern

<div align="center">

![BelanjaIn Logo](public/img/icon.jpg)

**Platform jual beli online yang mudah, aman, dan terpercaya**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-blue.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

</div>

---

## ✨ Fitur Utama

### 🎯 **Multi-Role System**
- **Customer** - Belanja produk, kelola keranjang, tracking pesanan
- **Seller** - Kelola toko, produk, dan pesanan
- **Admin** - Moderasi produk dan persetujuan toko
- **Super Admin** - Kontrol penuh sistem

### 🛒 **Fitur Customer**
- 📱 Katalog produk dengan filter & pencarian
- 🛍️ Keranjang belanja & wishlist
- 💳 Multiple payment methods
- 📦 Order tracking real-time
- ⭐ Rating & review produk
- 💬 Live chat dengan seller
- 🔔 Notifikasi pesanan

### 🏪 **Fitur Seller**
- 🎨 Dashboard toko yang intuitif
- 📸 Upload multi-foto produk
- 📊 Statistik penjualan
- 📦 Manajemen stok & inventory
- 💰 Laporan keuangan
- 📮 Kelola pesanan masuk
- 💬 Chat dengan pembeli

### 🔧 **Fitur Admin**
- ✅ Persetujuan toko baru
- 🛡️ Moderasi produk
- 👥 Manajemen user
- 📈 Analytics & reporting
- 🏷️ Kelola kategori

---

## 🎨 Design Highlights

### 🌟 **Modern UI/UX**
- **Gradient Design** - Colorful gradients untuk visual menarik
- **Glass Morphism** - Efek kaca modern dengan backdrop blur
- **Micro Interactions** - Animasi smooth untuk pengalaman yang engaging
- **Responsive Layout** - Sempurna di semua device (mobile, tablet, desktop)
- **Dark Mode Ready** - Siap untuk implementasi dark mode

### 📸 **Professional Product Gallery**
- Multi-image support dengan thumbnail navigation
- Zoom on hover effect
- Image lazy loading untuk performa optimal
- Placeholder state yang elegan

### 🎯 **Performance Optimized**
- Tailwind CSS dengan purging untuk file size minimal
- Image optimization
- Lazy loading components
- Efficient database queries

---

## 🚀 Instalasi

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

### Step-by-Step Installation

```bash
# 1. Clone repository
git clone https://github.com/yourusername/belanjain.git
cd belanjain

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=belanjain
DB_USERNAME=root
DB_PASSWORD=

# 5. Jalankan migration
php artisan migrate

# 6. (Optional) Seed data demo
php artisan db:seed --class=ProductSeeder

# 7. Build assets
npm run build

# 8. Jalankan server
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 📸 Upload Foto Produk

### Format yang Disupport
- **Format**: JPG, JPEG, PNG, WebP
- **Ukuran**: Max 2MB per file
- **Resolusi**: Minimum 800x800px (Recommended: 1200x1200px)
- **Ratio**: Square (1:1) untuk hasil terbaik

### Cara Upload Foto

1. **Login sebagai Seller**
2. Buka **Dashboard Toko**
3. Pilih **Kelola Produk**
4. Klik **Tambah/Edit Produk**
5. Upload **Foto Utama** dan **Foto Tambahan** (maks 5 foto)
6. Simpan

### 💡 Tips Foto Produk Berkualitas

✅ **DO's:**
- Gunakan pencahayaan yang baik
- Background bersih dan minimalis
- Foto dari berbagai sudut
- Tampilkan detail produk
- Gunakan resolusi tinggi

❌ **DON'TS:**
- Foto blur atau gelap
- Watermark berlebihan
- Background yang terlalu ramai
- Foto yang distorsi

---

## 🎨 Struktur Database Produk

### Field Produk Baru

```php
// Migration telah menambahkan field berikut:
- images (JSON)              // Array foto tambahan
- is_featured (boolean)      // Produk unggulan
- badge (string)             // Badge: new, sale, hot, bestseller
- discount_percentage (int)  // Persentase diskon
- rating (decimal)           // Rating produk
- sold_count (int)          // Jumlah terjual
```

### Helper Methods di Model Product

```php
$product->getAllImages()        // Dapatkan semua foto (utama + tambahan)
$product->getDiscountedPrice()  // Harga setelah diskon
$product->getOriginalPrice()    // Harga asli
```

---

## 🎯 Customization Guide

### 🎨 **Mengubah Warna Brand**

Edit file `resources/css/app.css`:

```css
/* Ganti dari cyan/blue ke warna brand Anda */
.from-cyan-500  → .from-purple-500
.to-blue-600    → .to-pink-600
```

### 🖼️ **Mengubah Logo**

1. Replace file `public/img/icon.jpg` dengan logo Anda
2. Update di `welcome.blade.php` dan `app.blade.php`

### 📱 **Menambah Payment Gateway**

1. Tambah konfigurasi di `config/services.php`
2. Implementasi di `app/Services/PaymentService.php`
3. Update view checkout

---

## 🔒 Security Features

- ✅ CSRF Protection
- ✅ SQL Injection Prevention
- ✅ XSS Protection
- ✅ Authentication & Authorization
- ✅ Password Hashing (Bcrypt)
- ✅ Rate Limiting
- ✅ Secure File Upload

---

## 📊 Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze
- **Storage**: Local/S3

### Frontend
- **CSS Framework**: Tailwind CSS 3.x
- **Icons**: Heroicons
- **Fonts**: Plus Jakarta Sans, Inter
- **JS**: Alpine.js

### Tools
- **Version Control**: Git
- **Package Manager**: Composer, NPM
- **Build Tool**: Vite

---

## 📝 Todo List

- [ ] Implementasi payment gateway (Midtrans/Xendit)
- [ ] Export laporan PDF
- [ ] Email notifications
- [ ] Push notifications
- [ ] Advanced search filters
- [ ] Wishlist functionality
- [ ] Product comparison
- [ ] Voucher & discount system
- [ ] Loyalty points
- [ ] Social media integration

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Developer

**Your Name**
- Website: [yourwebsite.com](https://yourwebsite.com)
- Email: your.email@example.com
- GitHub: [@yourusername](https://github.com/yourusername)

---

## 🙏 Acknowledgments

- Laravel Team for the amazing framework
- Tailwind Labs for Tailwind CSS
- All open-source contributors

---

<div align="center">

**⭐ Star this repo if you like it! ⭐**

Made with ❤️ in Indonesia 🇮🇩

</div>
