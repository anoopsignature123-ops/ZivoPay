<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductType;
use App\Models\User;
use Database\Seeders\InsuranceDemoSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class InsuranceApplicationSubmissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
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

        foreach ([
            '2026_11_12_164211_create_insurance_categories_table.php',
            '2026_11_12_164226_create_insurance_companies_table.php',
            '2026_11_12_164235_create_insurance_products_table.php',
            '2026_11_13_110641_create_product_types_table.php',
            '2026_11_13_110644_create_applications_table.php',
        ] as $migrationFile) {
            $migration = require database_path('migrations/'.$migrationFile);
            $migration->up();
        }
    }

    public function test_authenticated_user_can_submit_general_insurance_application_with_documents(): void
    {
        Storage::fake('local');
        $user = $this->createUser();
        $catalog = $this->createGeneralCatalog();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/insurance/application-submission', $this->applicationPayload($catalog, [
            'product_type_id' => $catalog['type']->id,
            'property_type' => 'Apartment',
            'ownership_type' => 'Owned',
            'property_address' => '10 Test Street, Jaipur',
            'construction_year' => '2018',
            'built_up_area' => '1200 sq ft',
        ]));

        $response->assertCreated()->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.status', 'submitted')
            ->where('data.category.id', $catalog['category']->id)
            ->where('data.product.id', $catalog['product']->id)
            ->has('data.application_number')
            ->has('data.documents', 3)
            ->etc());

        $this->assertDatabaseHas('application_details', [
            'user_id' => $user->id,
            'insurance_category_id' => $catalog['category']->id,
            'insurance_company_id' => $catalog['company']->id,
            'insurance_product_id' => $catalog['product']->id,
            'district' => 'Jaipur',
            'status' => 'submitted',
        ]);
        $this->assertDatabaseHas('application_general_specifications', [
            'product_type_id' => $catalog['type']->id,
            'property_type' => 'Apartment',
            'construction_year' => '2018',
        ]);
        $this->assertDatabaseHas('application_requirements', ['coverage_amount' => '500000.00']);
        $this->assertDatabaseHas('application_additions', ['is_existing_policy' => 'no', 'contact_time' => 'morning']);
        $this->assertDatabaseCount('application_documents', 3);
        $this->assertDatabaseCount('application_details', 1);

        foreach (['aadhaar', 'pan', 'photo'] as $documentType) {
            $path = DB::table('application_documents')->where('document_type', $documentType)->value('document_path');
            $this->assertNotEmpty($path);
            Storage::disk('local')->assertExists($path);
        }

        $this->assertCount(3, Storage::disk('local')->allFiles('insurance/applications'));
    }

    public function test_submission_rejects_a_product_type_from_another_product(): void
    {
        $user = $this->createUser();
        $catalog = $this->createGeneralCatalog();
        $otherProduct = InsuranceProduct::query()->create([
            'insurance_category_id' => $catalog['category']->id,
            'insurance_company_id' => $catalog['company']->id,
            'name' => 'Other General Product',
            'slug' => 'other-general-product',
            'is_active' => 'yes',
        ]);
        $otherType = ProductType::query()->create([
            'insurance_category_id' => $catalog['category']->id,
            'insurance_company_id' => $catalog['company']->id,
            'insurance_product_id' => $otherProduct->id,
            'name' => 'Home Insurance',
            'slug' => 'other-home-insurance',
            'is_active' => 'yes',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/insurance/application-submission', $this->applicationPayload($catalog, [
            'product_type_id' => $otherType->id,
            'property_type' => 'Apartment',
            'ownership_type' => 'Owned',
            'property_address' => '10 Test Street, Jaipur',
            'construction_year' => '2018',
            'built_up_area' => '1200 sq ft',
        ]));

        $response->assertUnprocessable()->assertJsonValidationErrors('specifics.product_type_id');
        $this->assertDatabaseCount('application_details', 0);
    }

    public function test_submission_requires_existing_api_authentication(): void
    {
        $catalog = $this->createGeneralCatalog();

        $this->postJson('/api/insurance/application-submission', $this->applicationPayload($catalog, [
            'product_type_id' => $catalog['type']->id,
            'property_type' => 'Apartment',
            'ownership_type' => 'Owned',
            'property_address' => '10 Test Street, Jaipur',
            'construction_year' => '2018',
            'built_up_area' => '1200 sq ft',
        ]))
            ->assertUnauthorized();
    }

    public function test_life_insurance_submission_saves_life_specific_fields(): void
    {
        $user = $this->createUser();
        $catalog = $this->createFamilyCatalog('Life Insurance', 'life-insurance', 'Term Life Protection', 'term-life-protection');
        $payload = $this->applicationPayload($catalog, [
            'insured_person_name' => 'Insured Customer',
            'occupation' => 'Salaried',
            'annual_income_range' => '₹5,00,000 - ₹10,00,000',
            'annual_income' => 750000,
            'sum_assured' => 5000000,
            'policy_term' => 20,
            'nominee_name' => 'Nominee Customer',
            'nominee_relationship' => 'Spouse',
            'is_smoker' => false,
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/insurance/application-submission', $payload)->assertCreated();

        $this->assertDatabaseHas('application_specifications', [
            'personal_name' => 'Insured Customer',
            'annual_income_range' => '₹5,00,000 - ₹10,00,000',
            'annual_income' => '750000.00',
            'is_smoker' => 'no',
        ]);
    }

    public function test_health_insurance_submission_saves_health_fields_and_long_condition_notes(): void
    {
        $user = $this->createUser();
        $catalog = $this->createFamilyCatalog('Health Insurance', 'health-insurance', 'Family Health Cover', 'family-health-cover');
        $longConditionNotes = rtrim(str_repeat('Existing condition details. ', 20));
        $payload = $this->applicationPayload($catalog, [
            'proposer_name' => 'Primary Insured',
            'policy_type' => 'family',
            'members_count' => 4,
            'has_pre_existing_conditions' => true,
            'pre_existing_conditions' => $longConditionNotes,
            'current_medications' => 'As prescribed',
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/insurance/application-submission', $payload)->assertCreated();

        $this->assertDatabaseHas('application_health_specifications', [
            'personal_name' => 'Primary Insured',
            'plan_type' => 'family',
            'number_of_dependents' => 4,
            'pre_existing_conditions' => $longConditionNotes,
        ]);
        $this->assertDatabaseHas('application_requirements', ['coverage_amount' => '500000.00']);
    }

    public function test_demo_seeder_creates_three_catalog_categories_and_six_general_types(): void
    {
        (new InsuranceDemoSeeder)->run();

        $this->assertDatabaseCount('insurance_categories', 3);
        $this->assertDatabaseCount('insurance_companies', 3);
        $this->assertDatabaseCount('insurance_products', 5);
        $this->assertDatabaseCount('product_types', 6);
        $this->assertEqualsCanonicalizing([
            'Bike Insurance', 'Car Insurance', 'Commercial Vehicle', 'Home Insurance', 'Personal Accident', 'Travel Insurance',
        ], ProductType::query()->pluck('name')->all());
    }

    public function test_admin_can_open_application_details_and_approve_the_application(): void
    {
        Storage::fake('local');
        $catalog = $this->createGeneralCatalog();
        $customer = $this->createUser();
        $admin = User::query()->create([
            'role_id' => 1,
            'name' => 'Insurance Admin',
            'email' => 'admin@example.test',
            'referral_code' => 'ZIVO-ADMIN01',
        ]);
        $application = Application::query()->create([
            'application_number' => 'ZIVO-INS-TEST-0001',
            'user_id' => $customer->id,
            'insurance_category_id' => $catalog['category']->id,
            'insurance_company_id' => $catalog['company']->id,
            'insurance_product_id' => $catalog['product']->id,
            'name' => 'Test Customer',
            'guardian_name' => 'Test Guardian',
            'email' => 'customer@example.test',
            'phone' => '9876543210',
            'date_of_birth' => '1990-04-18',
            'gender' => 'female',
            'address' => '10 Test Street',
            'district' => 'Jaipur',
            'state' => 'Rajasthan',
            'pin_code' => '302001',
            'is_declaration_accepted' => true,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
        $application->generalSpecifications()->create([
            'product_type_id' => $catalog['type']->id,
            'property_type' => 'Apartment',
            'ownership_type' => 'Owned',
            'property_address' => '10 Test Street, Jaipur',
            'construction_year' => '2018',
            'built_up_area' => '1200 sq ft',
        ]);
        $application->requirements()->create(['coverage_amount' => 500000]);
        $uploadedDocument = UploadedFile::fake()->image('identity.png');
        $documentPath = $uploadedDocument->store('insurance/applications/'.$application->application_number, 'local');
        $document = ApplicationDocument::query()->create([
            'application_detail_id' => $application->id,
            'document_type' => 'aadhaar',
            'document_path' => $documentPath,
            'original_name' => 'identity.png',
            'mime_type' => 'image/png',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.insurance.applications.index'))
            ->assertOk()
            ->assertSee('Submitted by: Insurance Customer')
            ->assertSee('ID '.$customer->id);

        $this->withoutVite()
            ->actingAs($admin)
            ->get(route('admin.insurance.applications.show', $application))
            ->assertOk()
            ->assertSee('Test Customer')
            ->assertSee('Submitted by account')
            ->assertSee('Insurance Customer')
            ->assertSee('User ID: '.$customer->id)
            ->assertSee('Home Insurance')
            ->assertSee('Jaipur')
            ->assertSee(route('admin.insurance.applications.documents.preview', [$application, $document->id]));

        $this->get(route('admin.insurance.applications.documents.preview', [$application, $document->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $downloadResponse = $this->get(route('admin.insurance.applications.documents.show', [$application, $document->id]));
        $downloadResponse->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->assertStringStartsWith('attachment', (string) $downloadResponse->headers->get('Content-Disposition'));

        $this->post(route('admin.insurance.applications.status', $application), [
            'status' => 'approved',
            'admin_remarks' => 'Documents verified.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('application_details', [
            'id' => $application->id,
            'status' => 'approved',
            'admin_remarks' => 'Documents verified.',
        ]);
        $this->assertNotNull($application->fresh()->approved_at);
    }

    /** @return array{category: InsuranceCategory, company: InsuranceCompany, product: InsuranceProduct, type: ProductType} */
    private function createGeneralCatalog(): array
    {
        $catalog = $this->createFamilyCatalog('General Insurance', 'general-insurance', 'General Insurance Protection', 'general-insurance-protection');
        $type = ProductType::query()->create([
            'insurance_category_id' => $catalog['category']->id,
            'insurance_company_id' => $catalog['company']->id,
            'insurance_product_id' => $catalog['product']->id,
            'name' => 'Home Insurance',
            'slug' => 'home-insurance',
            'is_active' => 'yes',
        ]);

        return [...$catalog, 'type' => $type];
    }

    /** @return array{category: InsuranceCategory, company: InsuranceCompany, product: InsuranceProduct} */
    private function createFamilyCatalog(string $categoryName, string $categorySlug, string $productName, string $productSlug): array
    {
        $category = InsuranceCategory::query()->create([
            'name' => $categoryName,
            'slug' => $categorySlug,
            'is_active' => true,
        ]);
        $company = InsuranceCompany::query()->create([
            'insurance_category_id' => $category->id,
            'name' => 'Zivo '.$categoryName.' Provider',
            'slug' => 'zivo-'.$categorySlug.'-provider',
            'is_active' => 'yes',
        ]);
        $product = InsuranceProduct::query()->create([
            'insurance_category_id' => $category->id,
            'insurance_company_id' => $company->id,
            'name' => $productName,
            'slug' => $productSlug,
            'is_active' => 'yes',
        ]);

        return compact('category', 'company', 'product');
    }

    private function createUser(): User
    {
        return User::query()->create([
            'role_id' => 2,
            'name' => 'Insurance Customer',
            'email' => 'customer@example.test',
            'mobile' => '9876543210',
            'referral_code' => 'ZIVO-TEST01',
        ]);
    }

    /** @param array{category: InsuranceCategory, company: InsuranceCompany, product: InsuranceProduct} $catalog
     * @param  array<string, mixed>  $specifics
     * @return array<string, mixed>
     */
    private function applicationPayload(array $catalog, array $specifics): array
    {
        return [
            'category_id' => $catalog['category']->id,
            'company_id' => $catalog['company']->id,
            'product_id' => $catalog['product']->id,
            'full_name' => 'Test Customer',
            'guardian_name' => 'Test Guardian',
            'date_of_birth' => '1990-04-18',
            'gender' => 'female',
            'mobile' => '9876543210',
            'email' => 'customer@example.test',
            'address' => '10 Test Street',
            'district' => 'Jaipur',
            'state' => 'Rajasthan',
            'pin_code' => '302001',
            'specifics' => $specifics,
            'requirements' => ['coverage_amount' => 500000, 'monthly_budget' => 2000, 'coverage_duration' => 10],
            'preferences' => ['has_existing_policy' => false, 'contact_time' => 'morning', 'language' => 'hindi'],
            'documents' => [
                'aadhaar' => UploadedFile::fake()->image('aadhaar.png'),
                'pan' => UploadedFile::fake()->image('pan.png'),
                'photo' => UploadedFile::fake()->image('photo.png'),
            ],
            'declaration_accepted' => true,
        ];
    }
}
