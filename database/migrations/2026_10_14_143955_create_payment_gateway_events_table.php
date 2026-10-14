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
        if (Schema::hasTable('payment_gateway_events')) {
            return;
        }
        Schema::create('payment_gateway_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->string('payload_hash', 64);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_events');
    }
};
