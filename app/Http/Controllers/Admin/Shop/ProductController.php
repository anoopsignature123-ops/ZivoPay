<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use App\Models\Shop\OrderItem;
use App\Models\Shop\Product;
use App\Services\Shop\ShopImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()->with('category')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->search);
                $query->where(fn ($nested) => $nested->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->status === 'active'))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.shop.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.shop.products.form', [
            'product' => new Product(['is_active' => true]),
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, ShopImageService $images): RedirectResponse
    {
        $data = $this->validatedData($request);
        if (count($request->file('images', [])) > 6) {
            return back()->withErrors(['images' => 'A product can have up to 6 images.'])->withInput();
        }
        $imagePaths = $request->file('images') ? $images->uploadMany($request->file('images')) : [];
        $product = Product::query()->create([
            ...$this->productAttributes($data, $request),
            'slug' => $this->uniqueSlug($data['name']),
            'image_paths' => $imagePaths,
        ]);

        return redirect()->route('admin.shop.products.index')->with('success', "Product {$product->name} created.");
    }

    public function edit(Product $product): View
    {
        return view('admin.shop.products.form', [
            'product' => $product,
            'categories' => Category::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Product $product, ShopImageService $images): RedirectResponse
    {
        $data = $this->validatedData($request, $product);
        $oldImages = $product->image_paths ?? [];
        $keepImages = array_values(array_intersect($request->input('keep_images', []), $oldImages));
        $newImages = $request->file('images') ? $images->uploadMany($request->file('images')) : [];
        if (count($keepImages) + count($newImages) > 6) {
            $images->deleteMany($newImages);

            return back()->withErrors(['images' => 'A product can have up to 6 images.'])->withInput();
        }

        $product->fill([
            ...$this->productAttributes($data, $request),
            'slug' => $product->name === $data['name'] ? $product->slug : $this->uniqueSlug($data['name'], $product->id),
            'image_paths' => [...$keepImages, ...$newImages],
        ])->save();
        $removedImages = array_values(array_diff($oldImages, $keepImages));
        $imagesUsedByOrderHistory = OrderItem::query()->whereIn('product_image', $removedImages)->pluck('product_image')->all();
        $images->deleteMany(array_values(array_diff($removedImages, $imagesUsedByOrderHistory)));

        return redirect()->route('admin.shop.products.index')->with('success', "Product {$product->name} updated.");
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function productAttributes(array $data, Request $request): array
    {
        return [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'sku' => $data['sku'],
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'mrp' => $data['mrp'],
            'stock' => $data['stock'],
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'is_flash_deal' => $request->boolean('is_flash_deal'),
            'flash_deal_ends_at' => $data['flash_deal_ends_at'] ?? null,
        ];
    }

    public function destroy(Product $product, ShopImageService $images): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);

            return redirect()->route('admin.shop.products.index')->with('success', 'Product archived because it is present in order history.');
        }

        $imagePaths = $product->image_paths ?? [];
        $product->delete();
        $images->deleteMany($imagePaths);

        return redirect()->route('admin.shop.products.index')->with('success', 'Product removed from the catalog.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:shop_categories,id'],
            'name' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:100', 'unique:shop_products,sku'.($product ? ','.$product->id : '')],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999'],
            'mrp' => ['required', 'numeric', 'decimal:0,2', 'gte:price', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:10000000'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_flash_deal' => ['nullable', 'boolean'],
            'flash_deal_ends_at' => ['required_if:is_flash_deal,1', 'nullable', 'date', 'after:now'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'keep_images' => ['nullable', 'array', 'max:6'],
            'keep_images.*' => ['string', 'max:255'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $counter = 2;
        while (Product::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '<>', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
