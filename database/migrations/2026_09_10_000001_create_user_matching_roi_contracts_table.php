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
        Schema::create('user_matching_roi_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('matching_amount', 15, 2)->default(0.00);
            $table->decimal('daily_amount', 15, 2)->default(0.00);
            $table->integer('duration_days')->default(150);
            $table->integer('days_paid')->default(0);
            $table->decimal('total_paid', 15, 2)->default(0.00);
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_matching_roi_contracts');
    }
};
