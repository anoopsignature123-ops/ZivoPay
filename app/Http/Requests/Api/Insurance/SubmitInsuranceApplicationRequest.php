<?php

namespace App\Http\Requests\Api\Insurance;

use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class SubmitInsuranceApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:insurance_categories,id'],
            'company_id' => ['required', 'integer', 'exists:insurance_companies,id'],
            'product_id' => ['required', 'integer', 'exists:insurance_products,id'],
            'product_type_id' => ['nullable', 'integer', 'exists:product_types,id'],

            'full_name' => ['required', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'district' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pin_code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],

            'specifics' => ['nullable', 'array'],
            'specifics.product_type_id' => ['nullable', 'integer', 'exists:product_types,id'],
            'specifics.insured_person_name' => ['nullable', 'string', 'max:255'],
            'specifics.occupation' => ['nullable', 'string', 'max:255'],
            'specifics.annual_income_range' => ['nullable', 'string', 'max:100'],
            'specifics.annual_income' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'specifics.sum_assured' => ['nullable', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'specifics.policy_term' => ['nullable', 'integer', 'min:1', 'max:100'],
            'specifics.nominee_name' => ['nullable', 'string', 'max:255'],
            'specifics.nominee_relationship' => ['nullable', 'string', 'max:100'],
            'specifics.is_smoker' => ['nullable', 'boolean'],

            'specifics.proposer_name' => ['nullable', 'string', 'max:255'],
            'specifics.policy_type' => ['nullable', Rule::in(['individual', 'family'])],
            'specifics.members_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'specifics.has_pre_existing_conditions' => ['nullable', 'boolean'],
            'specifics.pre_existing_conditions' => ['nullable', 'string', 'max:2000'],
            'specifics.current_medications' => ['nullable', 'string', 'max:2000'],

            'specifics.vehicle_type' => ['nullable', 'string', 'max:100'],
            'specifics.registration_number' => ['nullable', 'string', 'max:100'],
            'specifics.property_number' => ['nullable', 'string', 'max:100'],
            'specifics.brand' => ['nullable', 'string', 'max:150'],
            'specifics.model' => ['nullable', 'string', 'max:150'],
            'specifics.variant' => ['nullable', 'string', 'max:150'],
            'specifics.manufacturing_year' => ['nullable', 'string', 'max:4'],
            'specifics.registration_year' => ['nullable', 'string', 'max:4'],
            'specifics.engine_number' => ['nullable', 'string', 'max:100'],
            'specifics.chassis_number' => ['nullable', 'string', 'max:100'],
            'specifics.fuel_type' => ['nullable', 'string', 'max:100'],
            'specifics.vehicle_usage' => ['nullable', 'string', 'max:100'],
            'specifics.seating_capacity' => ['nullable', 'string', 'max:30'],
            'specifics.load_capacity' => ['nullable', 'string', 'max:50'],
            'specifics.rto_location' => ['nullable', 'string', 'max:150'],
            'specifics.permit_number' => ['nullable', 'string', 'max:100'],
            'specifics.fitness_certificate_expiry' => ['nullable', 'date'],
            'specifics.previous_policy_number' => ['nullable', 'string', 'max:100'],
            'specifics.previous_insurer' => ['nullable', 'string', 'max:150'],
            'specifics.previous_policy_start_date' => ['nullable', 'date'],
            'specifics.renewal_date' => ['nullable', 'date'],
            'specifics.claim_history' => ['nullable', 'string', 'max:100'],
            'specifics.ncb_percentage' => ['nullable', 'string', 'max:30'],
            'specifics.existing_policy_type' => ['nullable', 'string', 'max:100'],
            'specifics.insurance_type' => ['nullable', 'string', 'max:100'],
            'specifics.property_type' => ['nullable', 'string', 'max:100'],
            'specifics.ownership_type' => ['nullable', 'string', 'max:100'],
            'specifics.property_address' => ['nullable', 'string', 'max:1000'],
            'specifics.construction_year' => ['nullable', 'string', 'max:4'],
            'specifics.built_up_area' => ['nullable', 'string', 'max:100'],
            'specifics.construction_type' => ['nullable', 'string', 'max:100'],
            'specifics.number_of_floors' => ['nullable', 'string', 'max:30'],
            'specifics.building_value' => ['nullable', 'string', 'max:100'],
            'specifics.contents_value' => ['nullable', 'string', 'max:100'],
            'specifics.occupancy_status' => ['nullable', 'string', 'max:100'],
            'specifics.travel_type' => ['nullable', 'string', 'max:100'],
            'specifics.destination_country' => ['nullable', 'string', 'max:100'],
            'specifics.trip_type' => ['nullable', 'string', 'max:100'],
            'specifics.travel_start_date' => ['nullable', 'date'],
            'specifics.travel_end_date' => ['nullable', 'date', 'after_or_equal:specifics.travel_start_date'],
            'specifics.travellers_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'specifics.passport_number' => ['nullable', 'string', 'max:100'],
            'specifics.visa_type' => ['nullable', 'string', 'max:100'],
            'specifics.travel_purpose' => ['nullable', 'string', 'max:150'],
            'specifics.employment_type' => ['nullable', 'string', 'max:100'],
            'specifics.risk_category' => ['nullable', 'string', 'max:100'],
            'specifics.disability_coverage' => ['nullable', 'string', 'max:100'],
            'specifics.accidental_death_coverage' => ['nullable', 'string', 'max:100'],
            'specifics.fire_safety_details' => ['nullable', 'string', 'max:2000'],
            'specifics.additional_remarks' => ['nullable', 'string', 'max:2000'],

            'requirements' => ['required', 'array'],
            'requirements.coverage_amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'requirements.monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'requirements.coverage_duration' => ['nullable', 'integer', 'min:1', 'max:100'],
            'requirements.special_requirements' => ['nullable', 'string', 'max:2000'],

            'preferences' => ['required', 'array'],
            'preferences.has_existing_policy' => ['required', 'boolean'],
            'preferences.contact_time' => ['nullable', Rule::in(['morning', 'afternoon', 'evening'])],
            'preferences.language' => ['nullable', Rule::in(['hindi', 'english', 'other'])],

            'documents' => ['required', 'array'],
            'documents.aadhaar' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'documents.pan' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'documents.photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'declaration_accepted' => ['required', 'accepted'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['category_id', 'company_id', 'product_id'])) {
                return;
            }

            $category = InsuranceCategory::query()->find($this->integer('category_id'));
            $company = InsuranceCompany::query()->find($this->integer('company_id'));
            $product = InsuranceProduct::query()->find($this->integer('product_id'));

            if ($category === null || ! $category->is_active) {
                $validator->errors()->add('category_id', 'Please select an active insurance category.');

                return;
            }

            if ($company === null || ! $company->is_active || (int) $company->insurance_category_id !== (int) $category->id) {
                $validator->errors()->add('company_id', 'Please select an active company from the selected category.');
            }

            if ($product === null || ! $product->is_active
                || (int) $product->insurance_category_id !== (int) $category->id
                || (int) $product->insurance_company_id !== (int) $company?->id) {
                $validator->errors()->add('product_id', 'Please select an active product from the selected company and category.');
            }

            $family = $this->categoryFamily($category);
            if ($family === null) {
                $validator->errors()->add('category_id', 'Only Life Insurance, Health Insurance and General Insurance applications are accepted.');

                return;
            }

            $requiredFields = match ($family) {
                'life' => [
                    'specifics.insured_person_name' => 'Enter the name of the person being insured.',
                    'specifics.occupation' => 'Enter the occupation or profession.',
                    'specifics.annual_income_range' => 'Enter the annual income range.',
                    'specifics.sum_assured' => 'Enter the sum assured.',
                    'specifics.policy_term' => 'Enter the policy term in years.',
                    'specifics.nominee_name' => 'Enter the nominee full name.',
                    'specifics.nominee_relationship' => 'Enter the nominee relationship.',
                ],
                'health' => [
                    'specifics.proposer_name' => 'Enter the primary insured person name.',
                    'specifics.policy_type' => 'Select individual or family floater.',
                    'specifics.members_count' => 'Enter the number of members to cover.',
                ],
                default => [],
            };

            foreach ($requiredFields as $field => $message) {
                if (blank($this->input($field))) {
                    $validator->errors()->add($field, $message);
                }
            }

            $productTypeId = data_get($this->input('specifics', []), 'product_type_id');

            if ($family === 'general' && filled($productTypeId)
                && ! $validator->errors()->has('specifics.product_type_id')) {
                $productType = ProductType::query()->find((int) $productTypeId);

                if ($productType === null || ! $productType->is_active
                    || (int) $productType->insurance_product_id !== (int) $product?->id
                    || ($productType->insurance_company_id !== null && (int) $productType->insurance_company_id !== (int) $company?->id)
                    || ($productType->insurance_category_id !== null && (int) $productType->insurance_category_id !== (int) $category->id)) {
                    $validator->errors()->add('specifics.product_type_id', 'Select an active product type belonging to the selected product.');
                } else {
                    foreach ($this->requiredGeneralFields($productType) as $field => $message) {
                        if (blank($this->input('specifics.'.$field))) {
                            $validator->errors()->add('specifics.'.$field, $message);
                        }
                    }
                }
            }

            if ($family === 'general' && blank($productTypeId)) {
                $validator->errors()->add('specifics.product_type_id', 'Select a product type for general insurance.');
            }

            if ($family !== 'general' && filled($productTypeId)) {
                $validator->errors()->add('specifics.product_type_id', 'Product type is only used for General Insurance.');
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        $specifics = (array) $this->input('specifics', []);

        if (blank($specifics['product_type_id'] ?? null) && $this->filled('product_type_id')) {
            $specifics['product_type_id'] = $this->input('product_type_id');
            $this->merge(['specifics' => $specifics]);
        }
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please correct the highlighted application details.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    private function categoryFamily(InsuranceCategory $category): ?string
    {
        $identity = mb_strtolower($category->slug.' '.$category->name);

        foreach (['life', 'health', 'general'] as $family) {
            if (str_contains($identity, $family)) {
                return $family;
            }
        }

        if (str_contains($identity, 'motor')) {
            return 'general';
        }

        return null;
    }

    /** @return array<string, string> */
    private function requiredGeneralFields(ProductType $productType): array
    {
        $identity = mb_strtolower($productType->slug.' '.$productType->name);

        if (str_contains($identity, 'commercial')) {
            return [
                'vehicle_type' => 'Enter the commercial vehicle type.',
                'registration_number' => 'Enter the vehicle registration number.',
                'brand' => 'Enter the vehicle brand or make.',
                'model' => 'Enter the vehicle model.',
                'registration_year' => 'Enter the vehicle registration year.',
                'load_capacity' => 'Enter the vehicle load capacity.',
            ];
        }

        if (str_contains($identity, 'car') || str_contains($identity, 'bike')
            || str_contains($identity, 'two-wheeler') || str_contains($identity, 'two wheeler')
            || str_contains($identity, 'motorcycle')) {
            return [
                'registration_number' => 'Enter the vehicle registration number.',
                'brand' => 'Enter the vehicle brand or make.',
                'model' => 'Enter the vehicle model or variant.',
                'registration_year' => 'Enter the vehicle registration year.',
            ];
        }

        if (str_contains($identity, 'travel')) {
            return [
                'travel_type' => 'Select the travel type.',
                'destination_country' => 'Enter the destination country.',
                'trip_type' => 'Select the trip type.',
                'travel_start_date' => 'Enter the travel start date.',
                'travel_end_date' => 'Enter the travel end date.',
                'travellers_count' => 'Enter the number of travellers.',
            ];
        }

        if (str_contains($identity, 'home') || str_contains($identity, 'property')) {
            return [
                'property_type' => 'Select the property type.',
                'ownership_type' => 'Select the property ownership type.',
                'property_address' => 'Enter the property address.',
                'construction_year' => 'Enter the construction year.',
                'built_up_area' => 'Enter the built-up area.',
            ];
        }

        if (str_contains($identity, 'personal-accident') || str_contains($identity, 'personal accident')) {
            return [
                'occupation' => 'Enter the occupation or profession.',
                'employment_type' => 'Select the employment type.',
                'risk_category' => 'Select the occupation risk category.',
            ];
        }

        return [];
    }
}
