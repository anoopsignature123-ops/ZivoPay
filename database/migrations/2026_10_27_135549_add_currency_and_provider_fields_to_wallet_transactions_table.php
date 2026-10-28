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
        Schema::table('deposits', function (Blueprint $table): void {
            if (! Schema::hasColumn('deposits', 'amount_inr')) {
                $table->decimal('amount_inr', 15, 2)->nullable();
            }
            if (! Schema::hasColumn('deposits', 'conversion_rate')) {
                $table->decimal('conversion_rate', 10, 4)->nullable();
            }
        });

        Schema::table('withdrawals', function (Blueprint $table): void {
            if (! Schema::hasColumn('withdrawals', 'amount_inr')) {
                $table->decimal('amount_inr', 15, 2)->nullable();
            }
            if (! Schema::hasColumn('withdrawals', 'conversion_rate')) {
                $table->decimal('conversion_rate', 10, 4)->nullable();
            }
            if (! Schema::hasColumn('withdrawals', 'provider_reference')) {
                $table->string('provider_reference')->nullable();
            }
        });

        if (! Schema::hasColumn('payment_gateway_settings', 'exchange_rate')) {
            Schema::table('payment_gateway_settings', function (Blueprint $table): void {
                $table->decimal('exchange_rate', 10, 4)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table): void {
            $table->dropColumn(['amount_inr', 'conversion_rate']);
        });

        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->dropColumn(['amount_inr', 'conversion_rate', 'provider_reference']);
        });

        if (Schema::hasColumn('payment_gateway_settings', 'exchange_rate')) {
            Schema::table('payment_gateway_settings', function (Blueprint $table): void {
                $table->dropColumn('exchange_rate');
            });
        }
    }
};
