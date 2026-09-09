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
        Schema::table('deposits', function (Blueprint $table) {
            if (! Schema::hasColumn('deposits', 'wallet_address')) {
                $table->string('wallet_address')->nullable()->after('payment_gateway');
            }
            if (! Schema::hasColumn('deposits', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('wallet_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'wallet_address')) {
                $table->dropColumn('wallet_address');
            }
            if (Schema::hasColumn('deposits', 'gateway_reference')) {
                $table->dropColumn('gateway_reference');
            }
        });
    }
};
