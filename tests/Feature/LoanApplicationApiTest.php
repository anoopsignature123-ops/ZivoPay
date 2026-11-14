<?php

namespace Tests\Feature;

use App\Models\LoanApplication;
use App\Models\LoanCategory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LoanApplicationApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });
        Schema::create('withdrawals', function (Blueprint $table): void {
            $table->id();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        foreach (['2026_11_14_145127_create_loan_categories_table.php', '2026_11_14_145129_create_loan_applications_table.php', '2026_11_14_145133_create_loan_application_documents_table.php', '2026_11_14_145136_create_loan_application_status_histories_table.php'] as $file) {
            (require database_path('migrations/'.$file))->up();
        }
    }

    public function test_authenticated_customer_can_submit_loan_and_upload_documents(): void
    {
        Storage::fake('local');
        $user = $this->customer();
        $category = LoanCategory::query()->create(['name' => 'Home Loan', 'slug' => 'home-loan', 'is_active' => true, 'minimum_amount' => 100000, 'maximum_amount' => 5000000, 'maximum_tenure_years' => 30]);

        $response = $this->actingAs($user, 'sanctum')->post('/api/loans/application-submission', $this->payload($category), ['Accept' => 'application/json']);

        $response->assertCreated()->assertJsonPath('success', true)->assertJsonPath('data.status', 'submitted');
        $application = LoanApplication::query()->firstOrFail();
        $this->assertSame($user->id, $application->user_id);
        $this->assertSame('ABCDE1234F', $application->pan_number);
        $this->assertSame('123456789012', $application->aadhaar_number);
        $this->assertDatabaseCount('loan_application_documents', 4);
        $this->assertDatabaseCount('loan_application_status_histories', 1);
        $this->assertCount(4, Storage::disk('local')->allFiles('loans/applications/'.$application->application_number));
    }

    public function test_submission_requires_login_and_rejects_category_limits(): void
    {
        $category = LoanCategory::query()->create(['name' => 'Home Loan', 'slug' => 'home-loan', 'is_active' => true, 'maximum_amount' => 500000]);
        $this->postJson('/api/loans/application-submission', [])->assertUnauthorized();
        $response = $this->actingAs($this->customer(), 'sanctum')->postJson('/api/loans/application-submission', $this->payload($category, ['loan_amount' => 700000]));
        $response->assertUnprocessable()->assertJsonValidationErrors('loan_amount');
        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_customer_cannot_read_another_users_application(): void
    {
        $category = LoanCategory::query()->create(['name' => 'Car Loan', 'slug' => 'car-loan', 'is_active' => true]);
        $owner = $this->customer();
        $application = LoanApplication::query()->create(['application_number' => 'ZIVO-LN-OWNED1', 'user_id' => $owner->id, 'loan_category_id' => $category->id, 'applicant_name' => 'Owner', 'mobile' => '9876543210', 'loan_amount' => 100000, 'tenure_years' => 2]);
        $otherCustomer = $this->customer('other@example.test');

        $this->actingAs($otherCustomer, 'sanctum')->postJson('/api/loans/application-detail', ['application_id' => $application->id])->assertNotFound();
        $this->actingAs($owner, 'sanctum')->postJson('/api/loans/application-tracking', ['application_id' => $application->id])->assertOk()->assertJsonPath('data.application_number', $application->application_number);
    }

    public function test_admin_can_review_applications_and_open_loan_management_pages(): void
    {
        $customer = $this->customer();
        $admin = $this->customer('admin@example.test');
        $admin->forceFill(['role_id' => 1])->save();
        $category = LoanCategory::query()->create(['name' => 'Home Loan', 'slug' => 'home-loan', 'is_active' => true]);
        $application = LoanApplication::query()->create([
            'application_number' => 'ZIVO-LN-ADMIN01', 'user_id' => $customer->id, 'loan_category_id' => $category->id,
            'applicant_name' => 'Loan Applicant', 'mobile' => '9876543210', 'loan_amount' => 500000,
            'tenure_years' => 5, 'status' => 'submitted', 'submitted_at' => now(),
        ]);

        $this->withoutVite()->actingAs($admin)->get(route('admin.loan.dashboard'))->assertOk()->assertSee('Loan Dashboard');
        $this->get(route('admin.loan.categories.index'))->assertOk()->assertSee('Home Loan');
        $this->get(route('admin.loan.applications.index'))->assertOk()->assertSee($application->application_number)->assertSee('Submitted by: Loan Customer');
        $this->get(route('admin.loan.applications.show', $application))->assertOk()->assertSee('Supporting documents');
        $this->get(route('admin.loan.reports.index'))->assertOk()->assertSee('Category report');
        $this->post(route('admin.loan.applications.status', $application), ['status' => 'approved', 'admin_remarks' => 'Eligibility checked.'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('loan_applications', ['id' => $application->id, 'status' => 'approved', 'admin_remarks' => 'Eligibility checked.']);
        $this->assertDatabaseHas('loan_application_status_histories', ['loan_application_id' => $application->id, 'new_status' => 'approved', 'changed_by' => $admin->id]);
    }

    private function customer(string $email = 'customer@example.test'): User
    {
        $id = DB::table('users')->insertGetId(['role_id' => 2, 'name' => 'Loan Customer', 'email' => $email, 'mobile' => '9876543210', 'referral_code' => fake()->unique()->lexify('ZIVO-??????'), 'password' => 'hashed', 'created_at' => now(), 'updated_at' => now()]);

        return User::query()->findOrFail($id);
    }

    /** @param array<string, mixed> $overrides @return array<string, mixed> */
    private function payload(LoanCategory $category, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $category->id, 'applicant_name' => 'Loan Applicant', 'date_of_birth' => '1990-01-01',
            'mobile' => '9876543210', 'email' => 'loan@example.test', 'address' => '10 Test Street', 'city' => 'Jaipur',
            'state' => 'Rajasthan', 'pin_code' => '302001', 'pan_number' => 'ABCDE1234F', 'aadhaar_number' => '123456789012',
            'employment_type' => 'salaried', 'monthly_income' => 75000, 'employer_name' => 'Zivo Test', 'existing_monthly_emi' => 0,
            'credit_score' => 750, 'property_location' => 'Jaipur', 'property_value' => 4500000, 'loan_amount' => 500000,
            'tenure_years' => 5, 'preferred_lender' => 'State Bank of India', 'purpose' => 'Home purchase',
            'specifics' => ['property_type' => 'apartment'], 'declaration_accepted' => '1',
            'documents' => ['pan' => UploadedFile::fake()->image('pan.png'), 'aadhaar' => UploadedFile::fake()->image('aadhaar.png'), 'income_proof' => UploadedFile::fake()->create('salary.pdf', 100, 'application/pdf'), 'photo' => UploadedFile::fake()->image('photo.png')],
        ], $overrides);
    }
}
