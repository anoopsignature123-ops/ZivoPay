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
        if (Schema::hasTable('payment_gateway_settings')) {
            return;
        }
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('mode')->default('test');
            $table->string('test_key_id')->nullable();
            $table->text('test_key_secret')->nullable();
            $table->text('test_webhook_secret')->nullable();
            $table->string('live_key_id')->nullable();
            $table->text('live_key_secret')->nullable();
            $table->text('live_webhook_secret')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_settings');
    }
};
