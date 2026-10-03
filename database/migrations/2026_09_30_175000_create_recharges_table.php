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
        Schema::create('recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_id', 100)->unique();
            $table->string('service_type', 50)->default('mobile');
            $table->string('operator_code', 50);
            $table->string('operator_name', 100)->nullable();
            $table->string('circle_code', 20)->nullable();
            $table->string('number', 100);
            $table->decimal('amount', 12, 2);
            $table->string('status', 30)->default('pending'); // pending, success, failed, refunded
            $table->string('txid', 100)->nullable();
            $table->string('opid', 100)->nullable();
            $table->json('api_response')->nullable();
            $table->string('admin_remark', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharges');
    }
};
