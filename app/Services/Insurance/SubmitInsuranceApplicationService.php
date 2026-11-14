<?php

namespace App\Services\Insurance;

use App\Models\Application;
use App\Models\InsuranceCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SubmitInsuranceApplicationService
{
    /** @param array<string, mixed> $data */
    public function submit(array $data, User $user): Application
    {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($data, $user, &$storedPaths): Application {
                $application = Application::query()->create([
                    'application_number' => $this->applicationNumber(),
                    'user_id' => $user->getKey(),
                    'insurance_category_id' => $data['category_id'],
                    'insurance_company_id' => $data['company_id'],
                    'insurance_product_id' => $data['product_id'],
                    'name' => $data['full_name'],
                    'guardian_name' => $data['guardian_name'],
                    'email' => $data['email'],
                    'phone' => $data['mobile'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'address' => $data['address'],
                    'district' => $data['district'],
                    'state' => $data['state'],
                    'pin_code' => $data['pin_code'],
                    'is_declaration_accepted' => true,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                $specifics = $data['specifics'] ?? [];
                $categoryFamily = $this->categoryFamily((int) $data['category_id']);

                if ($categoryFamily === 'life') {
                    $application->specifications()->create([
                        'personal_name' => $specifics['insured_person_name'],
                        'occupation' => $specifics['occupation'],
                        'annual_income' => $specifics['annual_income'] ?? null,
                        'annual_income_range' => $specifics['annual_income_range'],
                        'sum_assured' => $specifics['sum_assured'],
                        'policy_term' => $specifics['policy_term'],
                        'nominee_name' => $specifics['nominee_name'],
                        'nominee_relation' => $specifics['nominee_relationship'],
                        'is_smoker' => filter_var($specifics['is_smoker'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'yes' : 'no',
                    ]);
                } elseif ($categoryFamily === 'health') {
                    $hasPreExistingConditions = filter_var($specifics['has_pre_existing_conditions'] ?? false, FILTER_VALIDATE_BOOLEAN);

                    $application->healthSpecifications()->create([
                        'personal_name' => $specifics['proposer_name'],
                        'plan_type' => $specifics['policy_type'],
                        'number_of_dependents' => $specifics['members_count'],
                        'pre_existing_conditions' => $hasPreExistingConditions
                            ? ($specifics['pre_existing_conditions'] ?? 'yes')
                            : 'no',
                        'current_medications' => $specifics['current_medications'] ?? null,
                    ]);
                } else {
                    $application->generalSpecifications()->create(array_merge(
                        ['product_type_id' => $specifics['product_type_id']],
                        Arr::only($specifics, [
                            'vehicle_type', 'registration_number', 'property_number', 'brand', 'model', 'variant',
                            'manufacturing_year', 'registration_year', 'engine_number', 'chassis_number', 'fuel_type',
                            'vehicle_usage', 'seating_capacity', 'load_capacity', 'rto_location', 'permit_number',
                            'fitness_certificate_expiry', 'previous_policy_number', 'previous_insurer',
                            'previous_policy_start_date', 'renewal_date', 'claim_history', 'ncb_percentage',
                            'existing_policy_type', 'insurance_type',
                            'property_type', 'ownership_type', 'property_address', 'construction_year', 'built_up_area',
                            'construction_type', 'number_of_floors', 'building_value', 'contents_value', 'occupancy_status',
                            'travel_type', 'destination_country', 'trip_type', 'travel_start_date', 'travel_end_date',
                            'travellers_count', 'passport_number', 'visa_type', 'travel_purpose', 'occupation',
                            'employment_type', 'risk_category', 'disability_coverage', 'accidental_death_coverage',
                            'fire_safety_details', 'additional_remarks',
                        ]),
                    ));
                }

                $application->requirements()->create($data['requirements']);
                $application->additions()->create([
                    'is_existing_policy' => filter_var($data['preferences']['has_existing_policy'], FILTER_VALIDATE_BOOLEAN) ? 'yes' : 'no',
                    'contact_time' => $data['preferences']['contact_time'] ?? null,
                    'language_preference' => $data['preferences']['language'] ?? null,
                ]);

                foreach (['aadhaar', 'pan', 'photo'] as $documentType) {
                    /** @var UploadedFile $file */
                    $file = $data['documents'][$documentType];
                    $path = $file->store('insurance/applications/'.$application->application_number, 'local');

                    if ($path === false) {
                        throw new \RuntimeException('The '.$documentType.' document could not be stored.');
                    }

                    $storedPaths[] = $path;
                    $application->documents()->create([
                        'document_type' => $documentType,
                        'document_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }

                return $application->load(['category', 'company', 'product', 'documents']);
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }
    }

    private function applicationNumber(): string
    {
        do {
            $applicationNumber = 'ZIVO-INS-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (Application::query()->where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }

    private function categoryFamily(int $categoryId): ?string
    {
        $category = InsuranceCategory::query()->findOrFail($categoryId);
        $identity = mb_strtolower($category->slug.' '.$category->name);

        foreach (['life', 'health', 'general'] as $family) {
            if (str_contains($identity, $family)) {
                return $family;
            }
        }

        return null;
    }
}
