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
        if (! Schema::hasColumn('deposits', 'payment_provider')) {
            Schema::table('deposits', fn (Blueprint $table) => $table->string('payment_provider')->nullable()->after('gateway_reference'));
        }
        if (! Schema::hasColumn('deposits', 'payment_order_id')) {
            Schema::table('deposits', fn (Blueprint $table) => $table->string('payment_order_id')->nullable()->unique()->after('payment_provider'));
        }
        if (! Schema::hasColumn('deposits', 'payment_id')) {
            Schema::table('deposits', fn (Blueprint $table) => $table->string('payment_id')->nullable()->unique()->after('payment_order_id'));
        }
        if (! Schema::hasColumn('deposits', 'payment_metadata')) {
            Schema::table('deposits', fn (Blueprint $table) => $table->json('payment_metadata')->nullable()->after('payment_id'));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table): void {
            $table->dropUnique(['payment_id']);
            $table->dropUnique(['payment_order_id']);
            $table->dropColumn(['payment_provider', 'payment_order_id', 'payment_id', 'payment_metadata']);
        });
    }
};
