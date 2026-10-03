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
        Schema::table('recharges', function (Blueprint $table) {
            if (! Schema::hasColumn('recharges', 'value1')) {
                $table->string('value1', 100)->nullable()->after('number');
            }
            if (! Schema::hasColumn('recharges', 'value2')) {
                $table->string('value2', 100)->nullable()->after('value1');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recharges', function (Blueprint $table) {
            $table->dropColumn(['value1', 'value2']);
        });
    }
};
