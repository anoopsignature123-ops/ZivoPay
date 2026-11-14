<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Insurance\InsuranceProductRequest;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Services\Insurance\InsuranceCatalogAssetService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class InsuranceProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $companyId = $request->query('company_id');

        $products = InsuranceProduct::query()->with(['category', 'company'])->withCount('productTypes')
            ->when($search !== '', fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")))
            ->when($categoryId !== null && ctype_digit((string) $categoryId), fn ($query) => $query->where('insurance_category_id', $categoryId))
            ->when($companyId !== null && ctype_digit((string) $companyId), fn ($query) => $query->where('insurance_company_id', $companyId))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $status === 'active' ? 'yes' : 'no'))
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.insurance.catalog-index', [
            'resource' => 'products',
            'title' => 'Insurance Products',
            'singular' => 'Product',
            'description' => 'Maintain the products customers can choose from in the insurance catalog.',
            'icon' => 'shield-check',
            'records' => $products,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->with('category:id,name')->orderBy('name')->get(['id', 'insurance_category_id', 'name']),
            'stats' => [
                'total' => InsuranceProduct::count(),
                'active' => InsuranceProduct::where('is_active', 'yes')->count(),
                'inactive' => InsuranceProduct::where('is_active', 'no')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.insurance.catalog-form', [
            'resource' => 'products',
            'record' => new InsuranceProduct,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->with('category:id,name')->orderBy('name')->get(['id', 'insurance_category_id', 'name']),
        ]);
    }

    public function store(InsuranceProductRequest $request, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'products') : null;
        unset($data['image']);

        try {
            $product = DB::transaction(fn (): InsuranceProduct => InsuranceProduct::query()->create([
                ...$data,
                'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], InsuranceProduct::class),
                'display_order' => $data['display_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
                'image' => $uploadedImage,
            ]));
        } catch (Throwable $exception) {
            $assets->deleteImage($uploadedImage);
            throw $exception;
        }

        return redirect()->route('admin.insurance.products.index')
            ->with('success', "Insurance product '{$product->name}' was created.");
    }

    public function edit(InsuranceProduct $product): View
    {
        return view('admin.insurance.catalog-form', [
            'resource' => 'products',
            'record' => $product,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->with('category:id,name')->orderBy('name')->get(['id', 'insurance_category_id', 'name']),
        ]);
    }

    public function update(InsuranceProductRequest $request, InsuranceProduct $product, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'products') : null;
        unset($data['image']);
        $oldImage = $product->image;

        try {
            DB::transaction(function () use ($product, $data, $request, $assets, $uploadedImage): void {
                $product->fill([
                    ...$data,
                    'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], InsuranceProduct::class, $product->id),
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

        return redirect()->route('admin.insurance.products.index')
            ->with('success', "Insurance product '{$product->name}' was updated.");
    }

    public function destroy(InsuranceProduct $product, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        try {
            DB::transaction(function () use ($product): void {
                if ($product->productTypes()->exists()) {
                    throw new \DomainException('This product has related product types and cannot be deleted.');
                }

                $product->delete();
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (QueryException) {
            return back()->with('error', 'This product has related records and cannot be deleted.');
        }

        $assets->deleteImage($product->image);

        return redirect()->route('admin.insurance.products.index')->with('success', 'Insurance product deleted.');
    }
}
