<?php

namespace App\Http\Resources\Api\Loan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'application_number' => $this->application_number,
            'category' => LoanCategoryResource::make($this->whenLoaded('category')),
            'applicant' => [
                'name' => $this->applicant_name, 'date_of_birth' => $this->date_of_birth?->toDateString(),
                'mobile' => $this->mobile, 'email' => $this->email, 'address' => $this->address,
                'city' => $this->city, 'state' => $this->state, 'pin_code' => $this->pin_code,
            ],
            'employment' => [
                'type' => $this->employment_type, 'monthly_income' => $this->monthly_income,
                'employer_name' => $this->employer_name, 'existing_monthly_emi' => $this->existing_monthly_emi,
                'credit_score' => $this->credit_score,
            ],
            'loan' => [
                'amount' => $this->loan_amount, 'tenure_years' => $this->tenure_years,
                'preferred_lender' => $this->preferred_lender, 'purpose' => $this->purpose,
                'specifics' => $this->specifics,
            ],
            'status' => $this->status, 'submitted_at' => $this->submitted_at,
            'documents' => $this->whenLoaded('documents', fn () => $this->documents->map(fn ($document) => [
                'id' => $document->id, 'type' => $document->document_type,
                'verification_status' => $document->verification_status,
            ])),
            'status_history' => $this->whenLoaded('statusHistories', fn () => $this->statusHistories->map(fn ($history) => [
                'status' => $history->new_status, 'remarks' => $history->remarks, 'changed_at' => $history->created_at,
            ])),
        ];
    }
}
