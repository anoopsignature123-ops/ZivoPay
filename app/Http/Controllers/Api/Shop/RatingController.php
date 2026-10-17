<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Models\Shop\Rating;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use ApiResponse;

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:shop_products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $product = Product::query()->whereKey($data['product_id'])->where('is_active', true)->firstOrFail();

        $rating = Rating::query()->updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $request->user()->id],
            ['rating' => $data['rating'], 'review' => $data['review'] ?? null],
        );

        return $this->successResponse([
            'id' => $rating->id,
            'product_id' => $rating->product_id,
            'rating' => $rating->rating,
            'review' => $rating->review,
            'created_at' => $rating->created_at?->toIso8601String(),
            'updated_at' => $rating->updated_at?->toIso8601String(),
        ], 'Product rating saved.');
    }
}
