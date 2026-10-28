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
        DB::table('payment_gateway_settings')->where('gateway', 'razorpay')->delete();

        Schema::table('payment_gateway_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'mode',
                'test_key_id',
                'test_key_secret',
                'test_webhook_secret',
                'live_key_id',
                'live_key_secret',
                'live_webhook_secret',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_settings', function (Blueprint $table): void {
            $table->string('mode')->default('test');
            $table->string('test_key_id')->nullable();
            $table->text('test_key_secret')->nullable();
            $table->text('test_webhook_secret')->nullable();
            $table->string('live_key_id')->nullable();
            $table->text('live_key_secret')->nullable();
            $table->text('live_webhook_secret')->nullable();
        });
    }
};
