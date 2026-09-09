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
        Schema::create('user_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rank_level')->comment('1 to 17 rank level');
            $table->string('rank_name');
            $table->decimal('matching_requirement', 15, 2)->default(0.00);
            $table->decimal('business_requirement', 15, 2)->default(0.00);
            $table->decimal('monthly_reward', 15, 2)->default(0.00);
            $table->integer('total_months')->default(5);
            $table->integer('months_paid')->default(0);
            $table->decimal('total_paid', 15, 2)->default(0.00);
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamp('last_paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_salaries');
    }
};
