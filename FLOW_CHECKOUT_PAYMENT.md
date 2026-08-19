# 🛒 Flow Checkout & Payment BelanjaIn

## ✅ **SUDAH DIPERBAIKI!**

Sekarang **semua user yang login** (admin, seller, atau siapa saja) bisa:
- ✅ Add to cart
- ✅ View cart
- ✅ Checkout
- ✅ Upload bukti pembayaran
- ✅ Tracking order

---

## 📋 **Flow Lengkap dari Browse hingga Pengiriman**

### **1. Browse & Add to Cart**
1. User login dengan akun apa saja
2. Browse produk di homepage atau katalog
3. Klik **"+ Keranjang Belanja"** atau **"Beli Sekarang"**
4. Produk masuk ke cart

**URL Cart**: http://localhost:8000/customer/cart

---

### **2. Review Cart & Apply Voucher (Opsional)**
1. Di halaman cart, review produk yang dipilih
2. Ubah quantity jika perlu
3. Apply voucher code jika ada (opsional)
4. Lihat ringkasan:
   - Subtotal item
   - Diskon produk
   - Diskon voucher
   - **Total Bayar**

---

### **3. Checkout & Input Alamat**
1. Klik tombol **"Lanjut ke Checkout"**
2. **URL**: http://localhost:8000/customer/checkout
3. Input alamat pengiriman (minimal 15 karakter)
4. Review sekali lagi total pembayaran
5. Klik **"Buat Pesanan"**

---

### **4. Upload Bukti Pembayaran**
1. Setelah checkout berhasil, akan redirect ke cart dengan pesan sukses
2. Order dibuat dengan status **"Pending"**
3. **URL Payment**: http://localhost:8000/customer/orders/{order_id}/payment
4. Upload foto bukti transfer
5. Klik **"Konfirmasi Pembayaran"**
6. Status order berubah jadi **"Processing"**

---

### **5. Seller Memproses Order**
1. Seller login: http://localhost:8000/login
   - Email: `seller@belanjain.test`
   - Password: `password`

2. Masuk ke **"Pesanan Masuk"** atau http://localhost:8000/seller/orders

3. Seller melihat order baru dengan status **"Processing"**

4. Seller update status order:
   - **Processing** → Packing barang
   - **Shipped** → Barang dikirim (isi resi)
   - **Completed** → Barang diterima customer
   - **Cancelled** → Dibatalkan

---

### **6. Tracking & Monitoring**

**Customer dapat melihat order di:**
- http://localhost:8000/customer/cart (lihat pesan sukses)
- Atau login sebagai customer dan lihat dashboard

**Seller dapat melihat order di:**
- http://localhost:8000/seller/orders
- http://localhost:8000/seller/dashboard

**Admin dapat melihat semua order di:**
- http://localhost:8000/admin/dashboard
- http://localhost:8000/super-admin/dashboard

---

## 🔧 **Yang Sudah Diperbaiki**

### **Sebelumnya (ERROR 403):**
```php
// Cart & Checkout hanya untuk role:customer
Route::middleware(['role:customer'])->group(function () {
    Route::post('/cart/add/{product}', ...);
    Route::get('/checkout', ...);
});
```

### **Sekarang (SUDAH FIX):**
```php
// Cart & Checkout untuk semua authenticated user
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/cart/add/{product}', ...);
    Route::get('/checkout', ...);
});
```

---

## 📱 **Testing Flow Lengkap**

```bash
# 1. Refresh browser
Ctrl + F5

# 2. Login dengan user apa saja
http://localhost:8000/login

# 3. Add produk keyboard ke cart
http://localhost:8000/product/{slug}
Klik "+ Keranjang Belanja"

# 4. View cart
http://localhost:8000/customer/cart

# 5. Checkout
Klik "Lanjut ke Checkout"
Input alamat: "Jln Riyanto no 68, Jakarta Selatan"
Klik "Buat Pesanan"

# 6. Upload bukti pembayaran
Akan redirect ke cart dengan link payment
Upload foto transfer
Klik "Konfirmasi Pembayaran"

# 7. Seller memproses
Login sebagai seller
http://localhost:8000/seller/orders
Update status order
```

---

## 🎉 **Status Order**

1. **Pending** - Menunggu pembayaran
2. **Processing** - Sedang dikemas seller
3. **Shipped** - Barang dalam pengiriman
4. **Completed** - Barang sudah diterima
5. **Cancelled** - Dibatalkan

---

## 📝 **Produk yang Tersedia**

1. iPhone 15 Pro Max - Rp 18.951.503
2. Samsung S24 Ultra - Rp 15.938.034
3. Mouse Pulsar X2 Susanto - Rp 2.520.420
4. Keyboard Rexus Daxa M84X - Rp 566.055

**Total: 4 produk** siap untuk diorder!
