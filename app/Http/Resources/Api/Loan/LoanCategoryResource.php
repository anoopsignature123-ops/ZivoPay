<?php

namespace App\Http\Resources\Api\Loan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'slug' => $this->slug, 'description' => $this->description,
            'icon' => $this->icon, 'indicative_interest_rate' => $this->indicative_interest_rate,
            'minimum_amount' => $this->minimum_amount, 'maximum_amount' => $this->maximum_amount,
            'maximum_tenure_years' => $this->maximum_tenure_years,
        ];
    }
}
