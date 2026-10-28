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
                ->with('category')->withCount('ratings')->withAvg('ratings', 'rating')->latest()->limit(12)->get(),
            'flash_deals' => Product::query()->where('is_active', true)->where('is_flash_deal', true)
                ->where(fn (Builder $query) => $query->whereNull('flash_deal_ends_at')->orWhere('flash_deal_ends_at', '>', $now))
                ->with('category')->withCount('ratings')->withAvg('ratings', 'rating')->latest()->limit(12)->get(),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function products(array $filters): LengthAwarePaginator
    {
        $query = Product::query()->where('is_active', true)->with('category')
            ->withCount('ratings')->withAvg('ratings', 'rating')
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $id) => $query->where('category_id', $id))
            ->when($filters['search'] ?? $filters['keyword'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $searchQuery) => $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%"));
            })
            ->when($filters['name'] ?? null, fn (Builder $query, string $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['sku'] ?? null, fn (Builder $query, string $sku) => $query->where('sku', 'like', "%{$sku}%"))
            ->when(array_key_exists('min_price', $filters), fn (Builder $query) => $query->where('price', '>=', $filters['min_price']))
            ->when(array_key_exists('max_price', $filters), fn (Builder $query) => $query->where('price', '<=', $filters['max_price']))
            ->when(($filters['in_stock'] ?? false) === true, fn (Builder $query) => $query->where('stock', '>', 0))
            ->when(($filters['featured'] ?? false) === true, fn (Builder $query) => $query->where('is_featured', true))
            ->when(($filters['flash_deals'] ?? false) === true, fn (Builder $query) => $query->where('is_flash_deal', true)
                ->where(fn (Builder $dealQuery) => $dealQuery->whereNull('flash_deal_ends_at')->orWhere('flash_deal_ends_at', '>', now())));

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            'name_asc' => $query->orderBy('name')->orderBy('id'),
            'rating_desc' => $query->orderByDesc('ratings_avg_rating')->orderByDesc('id'),
            'featured' => $query->orderByDesc('is_featured')->latest(),
            default => $query->latest(),
        };

        return $query->paginate((int) ($filters['per_page'] ?? 20))->withQueryString();
    }
}
