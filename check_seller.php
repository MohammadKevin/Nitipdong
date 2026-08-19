<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK DATA SELLER ===\n\n";

$seller = \App\Models\User::where('email', 'seller@belanjain.test')->first();
echo "Seller User ID: {$seller->id}\n";
echo "Name: {$seller->name}\n";
echo "Email: {$seller->email}\n";
echo "Role: {$seller->role}\n";

if ($seller->store) {
    echo "Store ID: {$seller->store->id}\n";
    echo "Store Name: {$seller->store->name}\n\n";

    echo "=== PRODUK DI STORE INI ===\n";
    $products = \App\Models\Product::where('store_id', $seller->store->id)->get(['id', 'name', 'is_active']);

    if ($products->count() > 0) {
        foreach ($products as $p) {
            echo "  - ID {$p->id}: {$p->name} (Active: " . ($p->is_active ? 'Yes' : 'No') . ")\n";
        }
        echo "\nTotal: {$products->count()} produk\n";
    } else {
        echo "  TIDAK ADA PRODUK!\n";
    }
} else {
    echo "SELLER TIDAK PUNYA STORE!\n";
}
