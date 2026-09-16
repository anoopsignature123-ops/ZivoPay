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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('wallet_type')->default('earning');
            $table->string('type');
            $table->string('trx_type', 10)->nullable();
            $table->decimal('charge', 15, 2)->default(0.00);
            $table->decimal('post_balance', 15, 2)->default(0.00);
            $table->integer('level')->nullable(); // Level 1 to 15
            $table->text('description');
            $table->string('trx_id')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
