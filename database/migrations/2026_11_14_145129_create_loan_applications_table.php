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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('loan_category_id')->constrained('loan_categories')->restrictOnDelete();
            $table->string('applicant_name');
            $table->date('date_of_birth')->nullable();
            $table->string('mobile', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->text('pan_number')->nullable();
            $table->text('aadhaar_number')->nullable();
            $table->string('employment_type')->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->string('employer_name')->nullable();
            $table->decimal('existing_monthly_emi', 15, 2)->nullable();
            $table->unsignedSmallInteger('credit_score')->nullable();
            $table->string('preferred_lender')->nullable();
            $table->decimal('loan_amount', 15, 2);
            $table->unsignedSmallInteger('tenure_years');
            $table->text('purpose')->nullable();
            $table->json('specifics')->nullable();
            $table->boolean('declaration_accepted')->default(false);
            $table->string('status', 30)->default('submitted');
            $table->text('admin_remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status'], 'loan_apps_user_status_idx');
            $table->index(['status', 'submitted_at'], 'loan_apps_status_date_idx');
            $table->index(['loan_category_id', 'status'], 'loan_apps_category_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
