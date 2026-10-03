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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->string('category', 50)->default('mobile'); // mobile, dth, fastag, postpaid, electricity
            $table->string('icon', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });

        Schema::create('recharge_plans', function (Blueprint $table) {
            $table->id();
            $table->string('operator_code', 50);
            $table->decimal('amount', 10, 2);
            $table->string('validity', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('category', 100)->default('Recommended Plans');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['operator_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharge_plans');
        Schema::dropIfExists('operators');
    }
};
