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
        Schema::create('a1_plan_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_type', 20);
            $table->string('operator_code', 20)->nullable();
            $table->string('circle_code', 20)->nullable();
            $table->string('name', 150)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->unique(['catalog_type', 'operator_code', 'circle_code'], 'a1plan_catalog_lookup_unique');
            $table->index(['catalog_type', 'operator_code', 'circle_code'], 'a1plan_catalog_lookup_index');
        });

        Schema::create('a1_plan_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_type', 20);
            $table->string('status', 20)->default('running');
            $table->unsignedInteger('requested_count')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failure_count')->default(0);
            $table->json('summary')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->index(['catalog_type', 'status', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a1_plan_sync_runs');
        Schema::dropIfExists('a1_plan_catalogs');
    }
};
