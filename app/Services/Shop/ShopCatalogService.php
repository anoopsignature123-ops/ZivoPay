<?php

namespace App\Services\Shop;

use App\Models\Shop\Banner;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ShopCatalogService
{
    /** @return array<string, mixed> */
    public function home(): array
    {
        $now = now();

        return [
            'banners' => Banner::query()->where('is_active', true)
                ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
                ->orderBy('sort_order')->get(),
            'categories' => Category::query()->where('is_active', true)
                ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
                ->orderBy('sort_order')->get(),
            'featured' => Product::query()->where('is_active', true)->where('is_featured', true)
                ->with('category')->latest()->limit(12)->get(),
            'flash_deals' => Product::query()->where('is_active', true)->where('is_flash_deal', true)
                ->where(fn (Builder $query) => $query->whereNull('flash_deal_ends_at')->orWhere('flash_deal_ends_at', '>', $now))
                ->with('category')->latest()->limit(12)->get(),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function products(array $filters): LengthAwarePaginator
    {
        return Product::query()->where('is_active', true)->with('category')
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $id) => $query->where('category_id', $id))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $searchQuery) => $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%"));
            })
            ->when(($filters['featured'] ?? false) === true, fn (Builder $query) => $query->where('is_featured', true))
            ->when(($filters['flash_deals'] ?? false) === true, fn (Builder $query) => $query->where('is_flash_deal', true)
                ->where(fn (Builder $dealQuery) => $dealQuery->whereNull('flash_deal_ends_at')->orWhere('flash_deal_ends_at', '>', now())))
            ->orderByDesc('is_featured')->latest()->paginate((int) ($filters['per_page'] ?? 20));
    }
}
