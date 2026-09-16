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
        Schema::create('user_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('plan_name')->default('ZIVO Financial Plan');
            $table->decimal('amount', 15, 2);
            $table->decimal('daily_percentage', 5, 2)->default(0.15); // 0.15%, 0.20%, 0.25%, 0.30%
            $table->decimal('daily_amount', 15, 2);
            $table->decimal('monthly_amount', 15, 2)->default(0.00);
            $table->decimal('total_returned', 15, 2)->default(0.00);
            $table->integer('days_completed')->default(0);
            $table->integer('total_days')->default(730); // 24 Months
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('activated_at')->useCurrent();
            $table->timestamp('last_payout_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_investments');
    }
};
