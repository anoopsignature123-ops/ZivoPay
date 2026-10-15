<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use App\Services\Shop\ShopImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()->withCount('products')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->search);
                $query->where(fn ($nested) => $nested->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%"));
            })
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $request->query('status') === 'active'))
            ->orderBy('sort_order')->paginate(10)->withQueryString();

        return view('admin.shop.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.shop.categories.form', ['category' => new Category]);
    }

    public function store(Request $request, ShopImageService $images): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $category = Category::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'image_path' => $request->file('image') ? $images->upload($request->file('image'), 'shop/categories') : null,
        ]);

        return redirect()->route('admin.shop.categories.index')->with('success', "Category {$category->name} created.");
    }

    public function edit(Category $category): View
    {
        return view('admin.shop.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category, ShopImageService $images): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $oldImage = $category->image_path;
        $category->fill([
            'name' => $data['name'],
            'slug' => $category->name === $data['name'] ? $category->slug : $this->uniqueSlug($data['name'], $category->id),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        if ($request->file('image')) {
            $category->image_path = $images->upload($request->file('image'), 'shop/categories');
        }
        $category->save();
        if ($oldImage !== $category->image_path) {
            $images->delete($oldImage);
        }

        return redirect()->route('admin.shop.categories.index')->with('success', "Category {$category->name} updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Move or remove this category’s products before deleting it.');
        }

        $image = $category->image_path;
        $category->delete();
        app(ShopImageService::class)->delete($image);

        return redirect()->route('admin.shop.categories.index')->with('success', 'Category deleted.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $counter = 2;
        while (Category::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
