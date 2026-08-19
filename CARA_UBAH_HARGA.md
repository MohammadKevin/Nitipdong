# Panduan Mengubah Harga Produk

## Sistem Harga BelanjaIn

BelanjaIn menggunakan sistem harga 3 layer:

```
1. HARGA DASAR SELLER (di database `products.price`)
   ↓ + 5% markup platform
2. HARGA TAYANG KE CUSTOMER (`customer_base_price`)
   ↓ - discount_percentage (jika ada)
3. HARGA FINAL CUSTOMER (`final_price`)
```

### Contoh Perhitungan:
- Harga dasar seller: **Rp 650.000**
- Markup platform 5%: **Rp 682.500** (ini yang tampil ke customer)
- Diskon 15%: **Rp 580.125** (harga final yang dibayar customer)

---

## Cara 1: Melalui UI Seller (Recommended)

1. Login sebagai **seller**:
   - Email: `seller@belanjain.test`
   - Password: `password`

2. Akses: http://localhost:8000/seller/products

3. Klik tombol **Edit** (ikon pensil) pada produk

4. Ubah nilai di field:
   - **Harga Dasar Toko (Rp)** — ini harga yang diterima seller
   - **Diskon Promosi (%)** — diskon opsional

5. Klik **Simpan Perubahan Produk**

---

## Cara 2: Melalui Script PHP (Batch Update)

Edit file `update_prices.php`:

```php
$updates = [
    27 => ['price' => 18999000, 'discount' => 5],    // iPhone
    28 => ['price' => 16499000, 'discount' => 8],    // Samsung
    29 => ['price' => 650000, 'discount' => 15],     // Mouse
];
```

Jalankan:
```bash
php update_prices.php
```

---

## Cara 3: Melalui Tinker (Update Cepat)

```bash
php artisan tinker
```

Lalu ketik:
```php
// Update harga iPhone
\App\Models\Product::find(27)->update(['price' => 18999000]);

// Update harga + diskon sekaligus
\App\Models\Product::find(28)->update([
    'price' => 16499000,
    'discount_percentage' => 10
]);
```

---

## Cara 4: Langsung ke Database (MySQL)

```sql
-- Update harga dasar
UPDATE products SET price = 650000 WHERE id = 29;

-- Update harga + diskon
UPDATE products 
SET price = 16499000, discount_percentage = 8 
WHERE id = 28;
```

---

## Field Penting di Tabel `products`

| Field | Deskripsi | Contoh |
|-------|-----------|--------|
| `price` | Harga dasar seller (sebelum markup 5%) | 650000 |
| `discount_percentage` | Persentase diskon (0-99) | 15 |
| `is_active` | Status aktif produk | true/false |
| `stock` | Jumlah stok | 20 |

---

## Tips

1. **Harga yang dimasukkan adalah harga DASAR SELLER**, bukan harga customer
2. Platform otomatis markup 5% untuk komisi
3. Customer akan melihat: `(price * 1.05) * (1 - discount/100)`
4. Setelah ubah harga, clear cache: `php artisan cache:clear`

---

## Troubleshooting

**Harga tidak berubah di frontend?**
```bash
php artisan cache:clear
php artisan view:clear
```

**Mau tahu harga final customer?**
```bash
php check_products.php
```
