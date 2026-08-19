<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FlashSale;
use App\Models\Product;

$flashSale = FlashSale::first();
if (!$flashSale) {
    $flashSale = FlashSale::create([
        'title' => 'Flash Sale Kilat BelanjaIn Super Promo',
        'start_time' => now()->subHours(2),
        'end_time' => now()->addHours(22),
        'is_active' => true,
    ]);
} else {
    $flashSale->update([
        'start_time' => now()->subHours(2),
        'end_time' => now()->addHours(22),
        'is_active' => true,
    ]);
}

$flashSale->items()->delete();

// Select 6 exciting items for flash sale
$fsProducts = Product::whereIn('slug', [
    'yiqii-50cm-60cm-lemari-pakaian-plastik-transparan',
    'philips-air-fryer-hd9200-4-1l',
    'pulsar-x2-wireless-gaming-mouse-white',
    'erigo-t-shirt-oversize-graphic-vintage',
    'skintific-5x-ceramide-barrier-moisture-gel',
    'sony-wh-1000xm5-wireless-headphones-silver'
])->get();

if ($fsProducts->isEmpty()) {
    $fsProducts = Product::take(6)->get();
}

foreach ($fsProducts as $p) {
    $discount = $p->discount_percentage > 0 ? $p->discount_percentage : 40;
    $fsPrice = round($p->price * (1 - ($discount / 100)));
    $allocated = rand(50, 100);
    $sold = rand(20, $allocated - 5);

    $flashSale->items()->create([
        'flash_sale_id' => $flashSale->id,
        'product_id' => $p->id,
        'flash_sale_price' => $fsPrice,
        'discount_percentage' => $discount,
        'stock_allocated' => $allocated,
        'stock_sold' => $sold,
        'is_active' => true,
    ]);
}

echo "✅ Flash sale aktif dengan 6 item pilihan!\n";
