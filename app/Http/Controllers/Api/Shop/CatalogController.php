<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Shop\CategoryResource;
use App\Http\Resources\Api\Shop\ProductResource;
use App\Models\Shop\Product;
use App\Services\Shop\ShopCatalogService;
use App\Services\Shop\ShopImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    use ApiResponse;

    public function home(Request $request, ShopCatalogService $catalog, ShopImageService $images): JsonResponse
    {
        $filters = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:shop_categories,id'],
            'search' => ['nullable', 'string', 'max:120'],
            'featured' => ['nullable', 'boolean'],
            'flash_deals' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
        $filters['featured'] = $request->boolean('featured');
        $filters['flash_deals'] = $request->boolean('flash_deals');

        $home = $catalog->home();
        $products = $catalog->products($filters);

        return $this->successResponse([
            'banners' => $home['banners']->map(fn ($banner) => [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'image' => $images->url($banner->image_path),
            ])->values(),
            'categories' => CategoryResource::collection($home['categories'])->resolve(),
            'flash_deals' => ProductResource::collection($home['flash_deals'])->resolve(),
            'products' => [
                'items' => ProductResource::collection($products->getCollection())->resolve(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ],
            'popular_products' => ProductResource::collection($home['featured'])->resolve(),
        ], 'Shop home retrieved.');
    }

    public function show(string $product): JsonResponse
    {
        $query = Product::query()->where('is_active', true)->with('category')
            ->withCount('ratings')->withAvg('ratings', 'rating');
        $item = ctype_digit($product)
            ? $query->whereKey((int) $product)->firstOrFail()
            : $query->where('slug', $product)->firstOrFail();

        return $this->successResponse(ProductResource::make($item)->resolve(), 'Shop product retrieved.');
    }
}
