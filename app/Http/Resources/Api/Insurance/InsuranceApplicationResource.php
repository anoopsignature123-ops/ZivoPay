<?php

namespace App\Http\Resources\Api\Insurance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsuranceApplicationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_number' => $this->application_number,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at?->toISOString(),
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
            ],
            'product' => [
                'id' => $this->product?->id,
                'name' => $this->product?->name,
            ],
            'documents' => $this->documents->map(fn ($document): array => [
                'type' => $document->document_type,
                'original_name' => $document->original_name,
            ])->values(),
        ];
    }
}
