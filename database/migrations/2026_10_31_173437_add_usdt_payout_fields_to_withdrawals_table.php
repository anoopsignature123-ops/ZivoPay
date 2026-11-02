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
        Schema::table('withdrawals', function (Blueprint $table): void {
            if (! Schema::hasColumn('withdrawals', 'source_wallet')) {
                $table->string('source_wallet', 32)->default('earning_wallet')->after('user_id');
            }
            if (! Schema::hasColumn('withdrawals', 'usdt_amount')) {
                $table->decimal('usdt_amount', 20, 6)->nullable()->after('amount_inr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $columns = array_values(array_filter(
                ['source_wallet', 'usdt_amount'],
                fn (string $column): bool => Schema::hasColumn('withdrawals', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
