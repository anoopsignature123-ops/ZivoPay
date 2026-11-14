<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_details', function (Blueprint $table): void {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('insurance_company_id')->constrained('insurance_companies')->restrictOnDelete();
            $table->foreignId('insurance_product_id')->constrained('insurance_products')->restrictOnDelete();
            $table->foreignId('insurance_category_id')->constrained('insurance_categories')->restrictOnDelete();
            $table->string('name');
            $table->string('guardian_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('address');
            $table->string('district', 100);
            $table->string('state', 100);
            $table->string('pin_code', 6);
            $table->boolean('is_declaration_accepted')->default(false);
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->text('admin_remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('application_specifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_detail_id')->unique()->constrained('application_details')->cascadeOnDelete();
            $table->string('personal_name');
            $table->string('occupation');
            $table->decimal('annual_income', 15, 2)->nullable();
            $table->string('annual_income_range', 100);
            $table->string('nominee_name');
            $table->string('nominee_relation', 100);
            $table->unsignedSmallInteger('policy_term');
            $table->decimal('sum_assured', 15, 2);
            $table->enum('is_smoker', ['yes', 'no'])->default('no');
            $table->timestamps();
        });

        Schema::create('application_health_specifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_detail_id')->unique()->constrained('application_details')->cascadeOnDelete();
            $table->string('personal_name');
            $table->enum('plan_type', ['individual', 'family']);
            $table->unsignedTinyInteger('number_of_dependents');
            $table->text('pre_existing_conditions');
            $table->text('current_medications')->nullable();
            $table->timestamps();
        });

        Schema::create('application_general_specifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_detail_id')->unique()->constrained('application_details')->cascadeOnDelete();
            $table->foreignId('product_type_id')->constrained('product_types')->restrictOnDelete();

            $table->string('vehicle_type')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('property_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('variant')->nullable();
            $table->string('manufacturing_year', 4)->nullable();
            $table->string('registration_year', 4)->nullable();
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('vehicle_usage')->nullable();
            $table->string('seating_capacity', 30)->nullable();
            $table->string('load_capacity', 50)->nullable();
            $table->string('rto_location')->nullable();
            $table->string('permit_number')->nullable();
            $table->date('fitness_certificate_expiry')->nullable();
            $table->string('previous_policy_number')->nullable();
            $table->string('previous_insurer')->nullable();
            $table->date('previous_policy_start_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->string('claim_history')->nullable();
            $table->string('ncb_percentage', 30)->nullable();
            $table->string('existing_policy_type')->nullable();
            $table->string('insurance_type')->nullable();

            $table->string('property_type')->nullable();
            $table->string('ownership_type')->nullable();
            $table->text('property_address')->nullable();
            $table->string('construction_year', 4)->nullable();
            $table->string('built_up_area')->nullable();
            $table->string('construction_type')->nullable();
            $table->string('number_of_floors', 30)->nullable();
            $table->string('building_value')->nullable();
            $table->string('contents_value')->nullable();
            $table->string('occupancy_status')->nullable();
            $table->text('fire_safety_details')->nullable();

            $table->string('travel_type')->nullable();
            $table->string('destination_country')->nullable();
            $table->string('trip_type')->nullable();
            $table->date('travel_start_date')->nullable();
            $table->date('travel_end_date')->nullable();
            $table->unsignedSmallInteger('travellers_count')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('visa_type')->nullable();
            $table->string('travel_purpose')->nullable();

            $table->string('occupation')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('risk_category')->nullable();
            $table->string('disability_coverage')->nullable();
            $table->string('accidental_death_coverage')->nullable();
            $table->text('additional_remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('application_requirements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_detail_id')->unique()->constrained('application_details')->cascadeOnDelete();
            $table->decimal('coverage_amount', 15, 2);
            $table->decimal('monthly_budget', 15, 2)->nullable();
            $table->unsignedSmallInteger('coverage_duration')->nullable();
            $table->text('special_requirements')->nullable();
            $table->timestamps();
        });

        Schema::create('application_additions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_detail_id')->unique()->constrained('application_details')->cascadeOnDelete();
            $table->enum('is_existing_policy', ['yes', 'no'])->default('no');
            $table->string('contact_time')->nullable();
            $table->string('language_preference')->nullable();
            $table->timestamps();
        });

        Schema::create('application_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_detail_id')->constrained('application_details')->cascadeOnDelete();
            $table->enum('document_type', ['aadhaar', 'pan', 'photo']);
            $table->string('document_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();
            $table->index(['application_detail_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('application_additions');
        Schema::dropIfExists('application_requirements');
        Schema::dropIfExists('application_specifications');
        Schema::dropIfExists('application_health_specifications');
        Schema::dropIfExists('application_general_specifications');
        Schema::dropIfExists('application_details');
    }
};