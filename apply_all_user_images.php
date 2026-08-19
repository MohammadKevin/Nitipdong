<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

echo "=== MENGHUBUNGKAN SEMUA FOTO USER DI PUBLIC/IMG KE PRODUK ===\n\n";

$explicitMap = [
    // 1. Rumah Tangga
    'yiqii-50cm-60cm-lemari-pakaian-plastik-transparan' => 'img/Lemari-yiqi.jpeg',
    'philips-air-fryer-hd9200-4-1l' => 'img/philips-air-fryer.jpg',
    'set-wajan-panci-keramik-marble-5pcs' => 'img/set-wajan.jpg',

    // 2. Handphone & Tablet
    'iphone-15-pro-max-256gb-natural-titanium' => 'img/iphone-15-pro-max.jpg',
    'samsung-galaxy-s24-ultra-512gb' => 'img/samsung-s24-ultra.jpg',
    'ipad-pro-11-inch-m4-oled-256gb' => 'img/apple-ipad.webp',

    // 3. Gaming
    'pulsar-x2-wireless-gaming-mouse-white' => 'img/pulsar-x-susanto.jpg',
    'logitech-g-pro-x-superlight-2-black' => 'img/logitech.webp',
    'playstation-5-slim-digital-edition-1tb' => 'img/ps5resmi.webp',
    'razer-blackwidow-v4-pro-mechanical-keyboard' => 'img/razer.jpg',

    // 4. Komputer & Laptop
    'apple-macbook-pro-14-m3-pro-space-black' => 'img/macbookm3pro.jpg',
    'asus-rog-zephyrus-g14-oled-rtx4060' => 'img/rogzephyrus.jpg',
    'monitor-gaming-27-inch-2k-ips-180hz' => 'img/monitorxiaomi.webp',

    // 5. Fashion Pria
    'erigo-t-shirt-oversize-graphic-vintage' => 'img/erigo.jpg',
    'kemeja-oxford-pria-lengan-panjang-navy' => 'img/kameja.jpg',
    'ventela-public-low-white-sneakers' => 'img/vantela.jpg',

    // 6. Fashion Wanita
    'dress-wanita-casual-a-line-korean-floral' => 'img/dress.jpg',
    'tas-selempang-wanita-kulit-vintage-brown' => 'img/tasselempang.jpg',

    // 7. Kecantikan
    'skintific-5x-ceramide-barrier-moisture-gel' => 'img/skintific.jpg',
    'serum-wajah-niacinamide-10-zinc-1-brightening' => 'img/ordinary.jpg',
    'lipstick-matte-velvet-long-lasting-berry' => 'img/lipstik.jpg',

    // 8. Kesehatan
    'vitamin-c-1000mg-zinc-60-tablet' => 'img/vitamin.webp',
    'masker-medis-3-ply-bfe-99-surgical-50pcs' => 'img/masker.jpg',

    // 9. Olahraga
    'sepatu-lari-nike-air-zoom-pegasus-40-black' => 'img/nike.jpg',
];

$count = 0;
foreach (Product::all() as $p) {
    $slug = $p->slug;
    $nameLower = strtolower($p->name);

    if (isset($explicitMap[$slug])) {
        $p->image = $explicitMap[$slug];
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'lemari') || str_contains($nameLower, 'yiqii')) {
        $p->image = 'img/Lemari-yiqi.jpeg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'philips') || str_contains($nameLower, 'air fryer')) {
        $p->image = 'img/philips-air-fryer.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'wajan') || str_contains($nameLower, 'panci')) {
        $p->image = 'img/set-wajan.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'iphone')) {
        $p->image = 'img/iphone-15-pro-max.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'samsung') || str_contains($nameLower, 's24')) {
        $p->image = 'img/samsung-s24-ultra.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'ipad')) {
        $p->image = 'img/apple-ipad.webp';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'pulsar')) {
        $p->image = 'img/pulsar-x-susanto.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'logitech')) {
        $p->image = 'img/logitech.webp';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'playstation') || str_contains($nameLower, 'ps5')) {
        $p->image = 'img/ps5resmi.webp';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'razer') || str_contains($nameLower, 'keyboard')) {
        $p->image = 'img/razer.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'macbook')) {
        $p->image = 'img/macbookm3pro.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'zephyrus') || str_contains($nameLower, 'asus rog')) {
        $p->image = 'img/rogzephyrus.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'monitor')) {
        $p->image = 'img/monitorxiaomi.webp';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'erigo') || str_contains($nameLower, 't-shirt') || str_contains($nameLower, 'kaos')) {
        $p->image = 'img/erigo.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'kemeja')) {
        $p->image = 'img/kameja.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'ventela') || (str_contains($nameLower, 'sneakers') && !str_contains($nameLower, 'nike'))) {
        $p->image = 'img/vantela.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'dress')) {
        $p->image = 'img/dress.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'tas selempang') || str_contains($nameLower, 'shoulder bag')) {
        $p->image = 'img/tasselempang.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'skintific') || str_contains($nameLower, 'ceramide')) {
        $p->image = 'img/skintific.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'serum') || str_contains($nameLower, 'niacinamide')) {
        $p->image = 'img/ordinary.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'lipstick') || str_contains($nameLower, 'lipstik')) {
        $p->image = 'img/lipstik.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'vitamin')) {
        $p->image = 'img/vitamin.webp';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'masker')) {
        $p->image = 'img/masker.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    } elseif (str_contains($nameLower, 'nike') || str_contains($nameLower, 'pegasus')) {
        $p->image = 'img/nike.jpg';
        $p->save();
        echo "✅ [{$p->id}] {$p->name} => {$p->image}\n";
        $count++;
    }
}

echo "\n🎉 SELESAI! Sebanyak {$count} produk telah diperbarui dengan foto dari public/img!\n";
