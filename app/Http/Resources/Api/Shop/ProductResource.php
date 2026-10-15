<?php

namespace App\Http\Resources\Api\Shop;

use App\Services\Shop\ShopImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = app(ShopImageService::class);

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'mrp' => (float) $this->mrp,
            'discount_percent' => $this->mrp > 0 ? (int) round((($this->mrp - $this->price) / $this->mrp) * 100) : 0,
            'stock' => $this->stock,
            'in_stock' => $this->stock > 0,
            'images' => collect($this->image_paths ?? [])->map(fn (string $path): ?string => $images->url($path))->all(),
            'is_featured' => (bool) $this->is_featured,
            'is_flash_deal' => (bool) $this->is_flash_deal,
            'flash_deal_ends_at' => $this->flash_deal_ends_at?->toIso8601String(),
        ];
    }
}
