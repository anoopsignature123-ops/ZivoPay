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
        if (! Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('category')->nullable()->default('general');
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->string('priority')->nullable()->default('medium');
                $table->string('status')->nullable()->default('pending');
                $table->text('admin_reply')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->string('attachment')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
