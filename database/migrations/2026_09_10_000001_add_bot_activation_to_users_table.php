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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_bot_active')) {
                $table->boolean('is_bot_active')->default(false)->after('status');
            }
            if (! Schema::hasColumn('users', 'bot_activated_at')) {
                $table->timestamp('bot_activated_at')->nullable()->after('is_bot_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_bot_active', 'bot_activated_at']);
        });
    }
};
