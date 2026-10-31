<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE a1_plan_catalogs ENGINE = InnoDB');
        DB::statement('ALTER TABLE a1_plan_sync_runs ENGINE = InnoDB');
        DB::statement('ALTER TABLE a1_plan_catalogs DROP INDEX a1plan_catalog_lookup_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('a1_plan_catalogs', function (Blueprint $table): void {
            $table->index(['catalog_type', 'operator_code', 'circle_code'], 'a1plan_catalog_lookup_index');
        });
    }
};
