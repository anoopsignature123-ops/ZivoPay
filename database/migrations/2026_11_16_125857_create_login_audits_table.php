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
        Schema::create('login_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 30);
            $table->string('login_method', 30);
            $table->string('ip_address', 45)->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_model')->nullable();
            $table->string('platform', 100)->nullable();
            $table->string('os_version', 100)->nullable();
            $table->string('app_version', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_in_at')->useCurrent();
            $table->timestamps();
            $table->index(['user_id', 'logged_in_at'], 'login_aud_user_time_idx');
            $table->index(['channel', 'logged_in_at'], 'login_aud_channel_time_idx');
            $table->index('ip_address', 'login_aud_ip_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_audits');
    }
};
