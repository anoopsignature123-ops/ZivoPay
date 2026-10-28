<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $tables = [
        'shop_categories',
        'shop_products',
        'shop_addresses',
        'shop_carts',
        'shop_cart_items',
        'shop_promo_codes',
        'shop_orders',
        'shop_order_items',
        'shop_banners',
        'shop_payments',
        'shop_ratings',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->setEngine('InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep transactional storage enabled even when rolling back application code.
    }

    private function setEngine(string $engine): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` ENGINE={$engine}");
            }
        }
    }
};
