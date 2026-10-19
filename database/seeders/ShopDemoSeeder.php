<?php

namespace Database\Seeders;

use App\Models\Shop\Banner;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Models\Shop\PromoCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Electronics', 'electronics', 'Everyday tech and smart accessories.', '#dbeafe', '#2563eb', 'TECH'],
            ['Fashion', 'fashion', 'Comfortable styles for every day.', '#fce7f3', '#db2777', 'STYLE'],
            ['Home & Kitchen', 'home-kitchen', 'Useful finds for a happier home.', '#fef3c7', '#d97706', 'HOME'],
            ['Health & Beauty', 'health-beauty', 'Self-care and wellness essentials.', '#dcfce7', '#16a34a', 'CARE'],
            ['Travel & Outdoors', 'travel-outdoors', 'Practical gear for trips and weekends.', '#cffafe', '#0891b2', 'TRAVEL'],
            ['Accessories', 'accessories', 'Small details that make a big difference.', '#ede9fe', '#7c3aed', 'MORE'],
        ];
        $categoryIds = [];

        foreach ($categories as $sort => [$name, $slug, $description, $background, $accent, $label]) {
            $imagePath = $this->artwork("categories/{$slug}", $name, $label, $background, $accent);
            $category = Category::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $description,
                'image_path' => $imagePath,
                'sort_order' => $sort + 1,
                'is_active' => true,
            ]);
            $categoryIds[$slug] = $category->id;
        }

        $products = [
            ['electronics', 'Wireless ANC Earbuds', 'DEMO-ELEC-001', '🎧', 1999, 3999, 48, true, true],
            ['electronics', 'Smart Fitness Tracker', 'DEMO-ELEC-002', '⌚', 2499, 4999, 35, true, true],
            ['electronics', 'Portable Bluetooth Speaker', 'DEMO-ELEC-003', '🔊', 1499, 2299, 27, false, false],
            ['fashion', 'Everyday Cotton T-Shirt', 'DEMO-FASH-001', '👕', 599, 999, 80, true, false],
            ['fashion', 'Classic Canvas Sneakers', 'DEMO-FASH-002', '👟', 1299, 1999, 24, false, true],
            ['fashion', 'Urban Crossbody Bag', 'DEMO-FASH-003', '👜', 899, 1499, 31, false, false],
            ['home-kitchen', 'Stainless Steel Bottle', 'DEMO-HOME-001', '🧴', 649, 999, 60, true, false],
            ['home-kitchen', 'Minimal Desk Lamp', 'DEMO-HOME-002', '💡', 1099, 1799, 19, false, true],
            ['home-kitchen', 'Ceramic Coffee Mug Set', 'DEMO-HOME-003', '☕', 799, 1199, 42, false, false],
            ['health-beauty', 'Daily Skincare Essentials Kit', 'DEMO-CARE-001', '🧴', 999, 1499, 33, true, true],
            ['health-beauty', 'Relaxing Aroma Diffuser', 'DEMO-CARE-002', '🌿', 1199, 1899, 21, false, false],
            ['health-beauty', 'Cushioned Yoga Mat', 'DEMO-CARE-003', '🧘', 899, 1399, 28, false, false],
            ['travel-outdoors', 'Weekend Travel Backpack', 'DEMO-TRVL-001', '🎒', 1799, 2699, 17, true, false],
            ['travel-outdoors', 'Compact Travel Organizer', 'DEMO-TRVL-002', '🧳', 499, 799, 55, false, true],
            ['travel-outdoors', 'Insulated Camping Tumbler', 'DEMO-TRVL-003', '🏕️', 749, 1099, 26, false, false],
            ['accessories', 'Fast Charge Power Bank', 'DEMO-ACC-001', '🔋', 1399, 2199, 39, true, true],
            ['accessories', 'Braided USB-C Cable', 'DEMO-ACC-002', '🔌', 299, 499, 100, false, false],
            ['accessories', 'Classic Polarized Sunglasses', 'DEMO-ACC-003', '🕶️', 699, 1199, 32, false, false],
        ];

        foreach ($products as [$categorySlug, $name, $sku, $emoji, $price, $mrp, $stock, $featured, $flashDeal]) {
            [$background, $accent] = $this->categoryColors($categorySlug);
            $imagePath = $this->artwork('products/' . Str::slug($sku), $name, $emoji, $background, $accent);
            Product::firstOrCreate(['slug' => Str::slug($name)], [
                'category_id' => $categoryIds[$categorySlug],
                'name' => $name,
                'sku' => $sku,
                'short_description' => "Demo {$name} listing with sample pricing and stock.",
                'description' => "{$name}. Demo catalogue content; replace with verified product specifications before launch.",
                'price' => $price,
                'mrp' => $mrp,
                'stock' => $stock,
                'image_paths' => [$imagePath],
                'is_active' => true,
                'is_featured' => $featured,
                'is_flash_deal' => $flashDeal,
                'flash_deal_ends_at' => $flashDeal ? now()->addDays(7) : null,
            ]);
        }

        foreach ([
            ['Festive Mega Sale', 'Up to 60% off · Extra wallet cashback', 'festive-mega-sale', '#dcfce7', '#059669'],
            ['Flash Deals', 'Fresh picks at special demo prices', 'flash-deals', '#fee2e2', '#ef4444'],
            ['Everyday Essentials', 'Useful finds for home, travel and more', 'everyday-essentials', '#dbeafe', '#2563eb'],
        ] as $sort => [$title, $subtitle, $slug, $background, $accent]) {
            $imagePath = $this->artwork("banners/{$slug}", $title, 'SALE', $background, $accent);
            Banner::firstOrCreate(['title' => $title], [
                'subtitle' => $subtitle,
                'image_path' => $imagePath,
                'target_url' => '/api/shop/products',
                'sort_order' => $sort + 1,
                'is_active' => true,
            ]);
        }

        foreach ([
            ['WELCOME10', '10% off eligible demo shop orders.', 'percent', 10, 500, 500],
            ['ZIVO200', '₹200 off eligible demo shop orders.', 'fixed', 200, 2000, null],
            ['FREESHIP', 'Free delivery offer for demo checkout.', 'fixed', 99, 999, 99],
        ] as [$code, $description, $type, $value, $minimum, $maximum]) {
            PromoCode::firstOrCreate(['code' => $code], [
                'description' => $description,
                'discount_type' => $type,
                'discount_value' => $value,
                'minimum_subtotal' => $minimum,
                'maximum_discount' => $maximum,
                'usage_limit' => 500,
                'usage_count' => 0,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(90),
                'is_active' => true,
            ]);
        }
    }

    /** @return array{0: string, 1: string} */
    private function categoryColors(string $slug): array
    {
        foreach ([
            'electronics' => ['#dbeafe', '#2563eb'],
            'fashion' => ['#fce7f3', '#db2777'],
            'home-kitchen' => ['#fef3c7', '#d97706'],
            'health-beauty' => ['#dcfce7', '#16a34a'],
            'travel-outdoors' => ['#cffafe', '#0891b2'],
            'accessories' => ['#ede9fe', '#7c3aed'],
        ] as $category => $colors) {
            if ($slug === $category) {
                return $colors;
            }
        }

        return ['#f3f4f6', '#059669'];
    }

    private function artwork(string $slug, string $title, string $graphic, string $background, string $accent): string
    {
        $path = 'shop/demo/' . str_replace('/', '-', $slug) . '.svg';
        $title = htmlspecialchars($title, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $graphic = htmlspecialchars($graphic, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" role="img" aria-label="{$title}">
                <defs>
                    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1"><stop stop-color="{$background}"/><stop offset="1" stop-color="#fff"/></linearGradient>
                    <filter id="shadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="18" stdDeviation="18" flood-color="{$accent}" flood-opacity=".18"/></filter>
                </defs>
                <rect width="800" height="600" rx="42" fill="url(#bg)"/>
                <circle cx="680" cy="110" r="150" fill="{$accent}" opacity=".08"/><circle cx="105" cy="505" r="115" fill="{$accent}" opacity=".07"/>
                <rect x="245" y="100" width="310" height="310" rx="58" fill="#fff" filter="url(#shadow)"/>
                <text x="400" y="315" text-anchor="middle" font-size="142" font-family="Arial,sans-serif">{$graphic}</text>
                <rect x="250" y="440" width="300" height="8" rx="4" fill="{$accent}" opacity=".35"/>
                <text x="400" y="500" text-anchor="middle" fill="{$accent}" font-size="30" font-weight="700" font-family="Arial,sans-serif">{$title}</text>
            </svg>
            SVG;
        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}