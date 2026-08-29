<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate file sitemap.xml untuk optimasi SEO mesin pencari.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Membuat file sitemap.xml...');

        $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 1. Static Pages
        $staticPages = [
            [
                'url'        => $baseUrl . '/',
                'lastmod'    => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority'   => '1.0',
            ],
            [
                'url'        => $baseUrl . '/products',
                'lastmod'    => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority'   => '0.9',
            ],
            [
                'url'        => $baseUrl . '/app-download',
                'lastmod'    => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority'   => '0.5',
            ],
        ];

        foreach ($staticPages as $page) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($page['url']) . "</loc>\n";
            $xml .= "        <lastmod>{$page['lastmod']}</lastmod>\n";
            $xml .= "        <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "        <priority>{$page['priority']}</priority>\n";
            $xml .= "    </url>\n";
        }

        // 2. Dynamic Active Products
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->select(['id', 'slug', 'updated_at'])
            ->get();

        foreach ($products as $product) {
            $prodUrl = $baseUrl . '/products/' . ($product->slug ?: $product->id);
            $lastmod = ($product->updated_at ?? now())->toAtomString();

            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($prodUrl) . "</loc>\n";
            $xml .= "        <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "        <changefreq>daily</changefreq>\n";
            $xml .= "        <priority>0.8</priority>\n";
            $xml .= "    </url>\n";
        }

        // 3. Dynamic Approved Stores
        $stores = Store::where('status', 'approved')
            ->select(['id', 'slug', 'updated_at'])
            ->get();

        foreach ($stores as $store) {
            $storeUrl = $baseUrl . '/toko/' . ($store->slug ?: $store->id);
            $lastmod = ($store->updated_at ?? now())->toAtomString();

            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($storeUrl) . "</loc>\n";
            $xml .= "        <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "        <changefreq>weekly</changefreq>\n";
            $xml .= "        <priority>0.7</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        $sitemapPath = public_path('sitemap.xml');
        File::put($sitemapPath, $xml);

        $totalUrls = count($staticPages) + $products->count() + $stores->count();
        $this->info("Sitemap berhasil dibuat di {$sitemapPath} dengan total {$totalUrls} URL.");

        return 0;
    }
}
