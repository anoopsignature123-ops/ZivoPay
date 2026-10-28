<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('deposits', 'provider_mode')) {
            Schema::table('deposits', function (Blueprint $table): void {
                $table->string('provider_mode', 10)->nullable()->after('payment_provider');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('deposits', 'provider_mode')) {
            Schema::table('deposits', function (Blueprint $table): void {
                $table->dropColumn('provider_mode');
            });
        }
    }
};
