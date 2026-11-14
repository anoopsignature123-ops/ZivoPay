<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Insurance\ProductTypeRequest;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductType;
use App\Services\Insurance\InsuranceCatalogAssetService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ProductTypeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'category_id' => ['nullable', 'integer', 'exists:insurance_categories,id'],
            'company_id' => ['nullable', 'integer', 'exists:insurance_companies,id'],
            'product_id' => ['nullable', 'integer', 'exists:insurance_products,id'],
        ]);
        $search = trim((string) ($filters['search'] ?? ''));

        $productTypes = ProductType::query()->with(['category', 'company', 'product'])
            ->when($search !== '', fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('insurance_category_id', $categoryId))
            ->when($filters['company_id'] ?? null, fn ($query, $companyId) => $query->where('insurance_company_id', $companyId))
            ->when($filters['product_id'] ?? null, fn ($query, $productId) => $query->where('insurance_product_id', $productId))
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->where('is_active', 'yes'))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($query) => $query->where('is_active', 'no'))
            ->orderBy('display_order')->orderBy('name')->paginate(12)->withQueryString();

        return view('admin.insurance.catalog-index', [
            'resource' => 'product-types',
            'title' => 'Insurance Product Types',
            'singular' => 'Product Type',
            'description' => 'Manage product types and optionally scope them to a category, company or product.',
            'icon' => 'shapes',
            'records' => $productTypes,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->with('category:id,name')->orderBy('name')->get(['id', 'insurance_category_id', 'name']),
            'products' => InsuranceProduct::query()->with(['category:id,name', 'company:id,name'])->orderBy('name')->get(['id', 'insurance_category_id', 'insurance_company_id', 'name']),
            'stats' => [
                'total' => ProductType::count(),
                'active' => ProductType::where('is_active', 'yes')->count(),
                'inactive' => ProductType::where('is_active', 'no')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.insurance.catalog-form', [
            'resource' => 'product-types',
            'record' => new ProductType,
            ...$this->formOptions(),
        ]);
    }

    public function store(ProductTypeRequest $request, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'product-types') : null;
        unset($data['image']);

        try {
            $productType = DB::transaction(fn (): ProductType => ProductType::query()->create([
                ...$data,
                'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], ProductType::class),
                'display_order' => $data['display_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
                'image' => $uploadedImage,
            ]));
        } catch (Throwable $exception) {
            $assets->deleteImage($uploadedImage);
            throw $exception;
        }

        return redirect()->route('admin.insurance.product-types.index')
            ->with('success', "Product type '{$productType->name}' was created.");
    }

    public function edit(ProductType $product_type): View
    {
        return view('admin.insurance.catalog-form', [
            'resource' => 'product-types',
            'record' => $product_type,
            ...$this->formOptions(),
        ]);
    }

    public function update(ProductTypeRequest $request, ProductType $product_type, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'product-types') : null;
        unset($data['image']);
        $oldImage = $product_type->image;

        try {
            DB::transaction(function () use ($product_type, $data, $request, $assets, $uploadedImage): void {
                $product_type->fill([
                    ...$data,
                    'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], ProductType::class, $product_type->id),
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

        return redirect()->route('admin.insurance.product-types.index')
            ->with('success', "Product type '{$product_type->name}' was updated.");
    }

    public function destroy(ProductType $product_type, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        try {
            DB::transaction(fn () => $product_type->delete());
        } catch (QueryException) {
            return back()->with('error', 'This product type could not be deleted because it is in use.');
        }

        $assets->deleteImage($product_type->image);

        return redirect()->route('admin.insurance.product-types.index')->with('success', 'Product type deleted.');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->with('category:id,name')->orderBy('name')->get(['id', 'insurance_category_id', 'name']),
            'products' => InsuranceProduct::query()->with(['category:id,name', 'company:id,name'])->orderBy('name')->get(['id', 'insurance_category_id', 'insurance_company_id', 'name']),
        ];
    }
}
