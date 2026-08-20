# 🚀 Quick Start Guide - SakserShop

<div align="center">

![SakserShop](public/img/saksershop-logo.png)

**Get your e-commerce platform up and running in 5 minutes!**

</div>

---

## ⚡ Super Quick Setup (5 Minutes)

```bash
# 1. Install dependencies
composer install && npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Configure database (edit .env)
# DB_DATABASE=belanjain
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Run migrations
php artisan migrate

# 5. Build assets
npm run build

# 6. Start server
php artisan serve
```

✅ **Done! Open http://localhost:8000**

---

## 📋 Step-by-Step Installation

### **Step 1: Install Dependencies** (2 minutes)

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### **Step 2: Environment Setup** (1 minute)

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### **Step 3: Database Configuration** (1 minute)

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=belanjain
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create database:
```bash
# Via MySQL CLI
mysql -u root -p
CREATE DATABASE belanjain;
exit;
```

### **Step 4: Run Migrations** (30 seconds)

```bash
php artisan migrate
```

### **Step 5: (Optional) Seed Demo Data** (30 seconds)

```bash
# Seed categories
php artisan db:seed

# Seed demo products
php artisan db:seed --class=ProductSeeder
```

### **Step 6: Build Assets** (1 minute)

```bash
npm run build
```

### **Step 7: Start Server** (10 seconds)

```bash
php artisan serve
```

Open browser: **http://localhost:8000**

---

## 👤 Create Your First User

### **Option 1: Via Registration Page**

1. Go to http://localhost:8000/register
2. Fill in the form:
   - Name: Your Name
   - Email: your@email.com
   - Password: password123
   - Role: Customer/Seller
3. Click "Daftar"
4. Login with your credentials

### **Option 2: Via Tinker (Super Admin)**

```bash
php artisan tinker
```

```php
// Create Super Admin
User::create([
    'name' => 'Super Admin',
    'email' => 'admin@belanjain.com',
    'password' => bcrypt('password'),
    'role' => 'super_admin',
    'email_verified_at' => now(),
]);

// Create Seller
$seller = User::create([
    'name' => 'Toko Saya',
    'email' => 'seller@belanjain.com',
    'password' => bcrypt('password'),
    'role' => 'seller',
    'email_verified_at' => now(),
]);

// Create Store for Seller
App\Models\Store::create([
    'user_id' => $seller->id,
    'name' => 'Toko Saya',
    'slug' => 'toko-saya',
    'description' => 'Toko online terpercaya',
    'status' => 'approved',
]);

// Create Category
App\Models\Category::create([
    'name' => 'Elektronik',
    'slug' => 'elektronik',
]);

exit;
```

---

## 🏪 Create Your First Product

### **Via Web Interface:**

1. **Login as Seller**
   - Email: seller@belanjain.com
   - Password: password

2. **Go to Seller Centre**
   - Click "Seller Centre" di top menu
   - Or go to `/seller/dashboard`

3. **Add Product**
   - Click "Kelola Produk"
   - Click "Tambah Produk Baru"

4. **Upload Photos**
   - Upload 1 main photo (required)
   - Upload up to 5 additional photos (optional)

5. **Fill Product Info**
   - Name: "Smartphone Android 2026"
   - Category: "Elektronik"
   - Price: 5000000
   - Discount: 10 (%)
   - Stock: 50
   - Badge: "new"
   - Featured: ✓ (checked)
   - Description: Detail produk...

6. **Save Product**
   - Click "Simpan Produk"
   - Product is now live!

### **Via Seeder (Demo Data):**

```bash
php artisan db:seed --class=ProductSeeder
```

This creates 10 demo products. **Remember to replace with your own photos!**

---

## 🎨 Customize Your Store

### **1. Change Logo**

Replace these files:
```
public/img/saksershop-logo.png    → Your square logo (80x80px+)
```

### **2. Change Brand Colors**

Edit `resources/css/app.css`:
```css
/* Find and replace */
cyan-500  → your-primary-color
blue-600  → your-secondary-color
```

Then rebuild:
```bash
npm run build
```

### **3. Change Site Name**

Edit `.env`:
```env
APP_NAME="Your Store Name"
```

Edit views:
```php
// resources/views/welcome.blade.php
// Search for "BelanjaIn" and replace
```

---

## 📸 Upload Product Photos

### **Recommended Specs:**
- **Size**: 1200x1200px (minimum 800x800px)
- **Format**: JPG, PNG, WebP
- **Max File Size**: 2MB
- **Ratio**: Square (1:1)

### **Photo Checklist:**
- [ ] Good lighting
- [ ] Clean background (white/solid color)
- [ ] Sharp focus
- [ ] Multiple angles
- [ ] Show details
- [ ] No watermarks

**📚 Full Guide:** See `PANDUAN_UPLOAD_FOTO.md`

---

## 🧪 Testing Your Setup

### **Test as Customer:**

1. Open http://localhost:8000
2. Browse products
3. Click a product
4. Add to cart
5. View cart
6. Checkout (if implemented)

### **Test as Seller:**

1. Login as seller
2. Go to Seller Centre
3. Add new product with photos
4. Edit existing product
5. View store page
6. Check orders (if any)

### **Test as Admin:**

1. Login as admin
2. Go to Admin Panel
3. Approve/reject stores
4. Moderate products
5. Manage categories

---

## 🔧 Common Issues & Solutions

### **Issue: npm install fails**

```bash
# Clear cache
npm cache clean --force

# Remove node_modules
rm -rf node_modules

# Reinstall
npm install
```

### **Issue: Migration fails**

```bash
# Fresh migration
php artisan migrate:fresh

# With seeder
php artisan migrate:fresh --seed
```

### **Issue: Assets not loading**

```bash
# Clear cache
php artisan cache:clear
php artisan view:clear

# Rebuild assets
npm run build

# Create storage link
php artisan storage:link
```

### **Issue: Images not showing**

```bash
# Create symbolic link
php artisan storage:link

# Check permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### **Issue: "Module openssl already loaded"**

This is just a warning, not an error. Safe to ignore or:

Edit `php.ini`:
```ini
# Comment out duplicate extensions
;extension=openssl
```

---

## 📚 Next Steps

### **For Development:**

1. ✅ Install Laravel Debugbar:
```bash
composer require barryvdh/laravel-debugbar --dev
```

2. ✅ Setup IDE Helper:
```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
```

3. ✅ Setup Testing:
```bash
php artisan test
```

### **For Production:**

1. ✅ Optimize application:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. ✅ Setup queue worker:
```bash
php artisan queue:work
```

3. ✅ Setup cron jobs:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### **Implement Additional Features:**

- [ ] Payment Gateway (Midtrans/Xendit)
- [ ] Email Notifications
- [ ] SMS OTP
- [ ] Shipping Integration (JNE, TIKI, POS)
- [ ] Review & Rating System
- [ ] Wishlist
- [ ] Voucher System
- [ ] Admin Dashboard Analytics

---

## 🎯 Checklist Before Going Live

### **Security:**
- [ ] Change `APP_KEY`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Use HTTPS
- [ ] Setup firewall
- [ ] Regular backups

### **Performance:**
- [ ] Enable caching
- [ ] Optimize images
- [ ] Use CDN
- [ ] Enable gzip
- [ ] Minify assets

### **SEO:**
- [ ] Add meta descriptions
- [ ] Setup Google Analytics
- [ ] Create sitemap
- [ ] Add robots.txt
- [ ] Schema markup

### **Legal:**
- [ ] Terms of Service
- [ ] Privacy Policy
- [ ] Return Policy
- [ ] Cookie Policy

---

## 💡 Pro Tips

### **Development:**
```bash
# Watch mode (auto rebuild on change)
npm run dev

# Run in background
php artisan serve &
```

### **Debugging:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear everything
php artisan optimize:clear
```

### **Database:**
```bash
# Export database
mysqldump -u root -p belanjain > backup.sql

# Import database
mysql -u root -p belanjain < backup.sql
```

---

## 🆘 Get Help

### **Documentation:**
- 📖 Laravel: https://laravel.com/docs
- 🎨 Tailwind: https://tailwindcss.com/docs
- 🔧 Alpine.js: https://alpinejs.dev

### **Community:**
- 💬 Laravel Discord: https://discord.gg/laravel
- 📱 Telegram: (Your group)
- 🐦 Twitter: @belanjain

### **Support:**
- 📧 Email: support@belanjain.com
- 🌐 Website: https://belanjain.com/help

---

## 🎉 Success!

Congratulations! Your BelanjaIn e-commerce platform is now ready to use!

### **What's Next?**

1. 📸 Upload your product photos
2. 🏪 Customize your store branding
3. 📝 Add product descriptions
4. 🎨 Customize colors & design
5. 🚀 Go live and start selling!

---

<div align="center">

**Happy Selling! 🛍️**

Made with ❤️ in Indonesia 🇮🇩

[📖 Full Documentation](README.md) | [📸 Photo Guide](PANDUAN_UPLOAD_FOTO.md) | [📝 Changelog](CHANGELOG.md)

</div>
