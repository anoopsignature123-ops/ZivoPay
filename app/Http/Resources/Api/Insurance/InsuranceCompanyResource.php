<?php

namespace App\Http\Resources\Api\Insurance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InsuranceCompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->insurance_category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'display_order' => $this->display_order,
            'rating' => 4.8,
            'products_count' => $this->whenCounted('products'),
            'products' => $this->whenLoaded('products', fn ($products) => InsuranceProductResource::collection($products)->resolve()),
        ];
    }
}
