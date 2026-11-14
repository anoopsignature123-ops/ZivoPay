<?php

namespace App\Http\Requests\Api\Loan;

use App\Models\LoanCategory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class SubmitLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:loan_categories,id'],
            'applicant_name' => ['required', 'string', 'max:255'], 'date_of_birth' => ['required', 'date', 'before:today'],
            'mobile' => ['required', 'regex:/^[0-9]{10}$/'], 'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'], 'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'], 'pin_code' => ['required', 'regex:/^[0-9]{6}$/'],
            'pan_number' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/i'], 'aadhaar_number' => ['required', 'regex:/^[0-9]{12}$/'],
            'employment_type' => ['required', Rule::in(['salaried', 'self_employed', 'business_owner', 'farmer_agriculture'])],
            'monthly_income' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'employer_name' => ['required', 'string', 'max:255'], 'existing_monthly_emi' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'credit_score' => ['nullable', 'integer', 'between:300,900'],
            'property_location' => ['nullable', 'string', 'max:255'], 'property_value' => ['nullable', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'loan_amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'], 'tenure_years' => ['required', 'integer', 'min:1', 'max:50'],
            'preferred_lender' => ['required', 'string', 'max:255'], 'purpose' => ['nullable', 'string', 'max:2000'],
            'specifics' => ['nullable', 'array'], 'specifics.*' => ['nullable', 'string', 'max:2000'],
            'documents' => ['required', 'array'],
            'documents.pan' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'documents.aadhaar' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'documents.income_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'documents.photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'declaration_accepted' => ['required', 'accepted'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('category_id')) {
                return;
            }
            $category = LoanCategory::query()->find($this->integer('category_id'));
            if ($category === null || ! $category->is_active) {
                $validator->errors()->add('category_id', 'Please select an active loan category.');

                return;
            }
            if ($category->minimum_amount !== null && $this->filled('loan_amount') && (float) $this->input('loan_amount') < (float) $category->minimum_amount) {
                $validator->errors()->add('loan_amount', 'The requested amount is below the minimum for this loan category.');
            }
            if ($category->maximum_amount !== null && $this->filled('loan_amount') && (float) $this->input('loan_amount') > (float) $category->maximum_amount) {
                $validator->errors()->add('loan_amount', 'The requested amount exceeds the maximum for this loan category.');
            }
            if ($category->maximum_tenure_years !== null && $this->filled('tenure_years') && $this->integer('tenure_years') > $category->maximum_tenure_years) {
                $validator->errors()->add('tenure_years', 'The requested tenure exceeds the maximum for this loan category.');
            }
        }];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false, 'message' => 'Please correct the highlighted loan application details.', 'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
