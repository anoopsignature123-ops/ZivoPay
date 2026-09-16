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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('deposit_ref')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('charge', 15, 2)->default(0.00);
            $table->decimal('final_amount', 15, 2);
            $table->string('payment_method')->default('UPI');
            $table->string('trx_hash')->nullable()->unique();
            $table->string('proof_file')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
