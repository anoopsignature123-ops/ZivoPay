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
        Schema::table('kycs', function (Blueprint $table) {
            if (! Schema::hasColumn('kycs', 'pan_number')) {
                $table->string('pan_number')->nullable()->after('document_number');
            }
            if (! Schema::hasColumn('kycs', 'pan_image')) {
                $table->string('pan_image')->nullable()->after('back_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kycs', function (Blueprint $table) {
            if (Schema::hasColumn('kycs', 'pan_number')) {
                $table->dropColumn('pan_number');
            }
            if (Schema::hasColumn('kycs', 'pan_image')) {
                $table->dropColumn('pan_image');
            }
        });
    }
};
