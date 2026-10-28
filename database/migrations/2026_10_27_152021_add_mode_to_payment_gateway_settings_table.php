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
        if (! Schema::hasColumn('payment_gateway_settings', 'mode')) {
            Schema::table('payment_gateway_settings', function (Blueprint $table): void {
                $table->string('mode', 10)->default('test');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payment_gateway_settings', 'mode')) {
            Schema::table('payment_gateway_settings', function (Blueprint $table): void {
                $table->dropColumn('mode');
            });
        }
    }
};
