<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Insurance\InsuranceCategoryRequest;
use App\Models\InsuranceCategory;
use App\Services\Insurance\InsuranceCatalogAssetService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class InsuranceCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $categories = InsuranceCategory::query()
            ->withCount(['companies', 'products', 'productTypes'])
            ->when($search !== '', fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $status === 'active'))
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.insurance.catalog-index', [
            'resource' => 'categories',
            'title' => 'Insurance Categories',
            'singular' => 'Category',
            'description' => 'Organize the insurance catalog into clear, searchable categories.',
            'icon' => 'tags',
            'records' => $categories,
            'stats' => [
                'total' => InsuranceCategory::count(),
                'active' => InsuranceCategory::where('is_active', true)->count(),
                'inactive' => InsuranceCategory::where('is_active', false)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.insurance.catalog-form', ['resource' => 'categories', 'record' => new InsuranceCategory]);
    }

    public function store(InsuranceCategoryRequest $request, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'categories') : null;
        unset($data['image']);

        try {
            $category = DB::transaction(fn (): InsuranceCategory => InsuranceCategory::query()->create([
                ...$data,
                'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], InsuranceCategory::class),
                'display_order' => $data['display_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
                'image' => $uploadedImage,
            ]));
        } catch (Throwable $exception) {
            $assets->deleteImage($uploadedImage);
            throw $exception;
        }

        return redirect()->route('admin.insurance.categories.index')
            ->with('success', "Insurance category '{$category->name}' was created.");
    }

    public function edit(InsuranceCategory $category): View
    {
        return view('admin.insurance.catalog-form', ['resource' => 'categories', 'record' => $category]);
    }

    public function update(InsuranceCategoryRequest $request, InsuranceCategory $category, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'categories') : null;
        unset($data['image']);
        $oldImage = $category->image;

        try {
            DB::transaction(function () use ($category, $data, $request, $assets, $uploadedImage): void {
                $category->fill([
                    ...$data,
                    'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], InsuranceCategory::class, $category->id),
                    'display_order' => $data['display_order'] ?? 0,
                    'is_active' => $request->boolean('is_active'),
                    ...($uploadedImage !== null ? ['image' => $uploadedImage] : []),
                ])->save();
            });
        } catch (Throwable $exception) {
            $assets->deleteImage($uploadedImage);
            throw $exception;
        }

        if ($uploadedImage !== null) {
            $assets->deleteImage($oldImage);
        }

        return redirect()->route('admin.insurance.categories.index')
            ->with('success', "Insurance category '{$category->name}' was updated.");
    }

    public function destroy(InsuranceCategory $category, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        try {
            DB::transaction(function () use ($category): void {
                if ($category->companies()->exists() || $category->products()->exists() || $category->productTypes()->exists()) {
                    throw new \DomainException('This category has related companies, products or product types and cannot be deleted.');
                }

                $category->delete();
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (QueryException) {
            return back()->with('error', 'This category has related records and cannot be deleted.');
        }

        $assets->deleteImage($category->image);

        return redirect()->route('admin.insurance.categories.index')->with('success', 'Insurance category deleted.');
    }
}
