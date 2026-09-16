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
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('reward_title');
            $table->decimal('business_required', 15, 2);
            $table->string('reward_item'); // Mobile, Laptop, EV Scooty, Car DP 3L, Tata Punch, Tata Sierra
            $table->enum('type', ['direct_business', 'team_business'])->default('direct_business');
            $table->enum('status', ['pending', 'achieved', 'claimed'])->default('pending');
            $table->timestamp('achieved_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
    }
};
