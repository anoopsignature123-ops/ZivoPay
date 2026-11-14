<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Insurance\InsuranceCompanyRequest;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Services\Insurance\InsuranceCatalogAssetService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class InsuranceCompanyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        $companies = InsuranceCompany::query()->with('category')->withCount(['products', 'productTypes'])
            ->when($search !== '', fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")))
            ->when($categoryId !== null && ctype_digit((string) $categoryId), fn ($query) => $query->where('insurance_category_id', $categoryId))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $status === 'active' ? 'yes' : 'no'))
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.insurance.catalog-index', [
            'resource' => 'companies',
            'title' => 'Insurance Companies',
            'singular' => 'Company',
            'description' => 'Manage insurers and connect each company to its category.',
            'icon' => 'building-2',
            'records' => $companies,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'stats' => [
                'total' => InsuranceCompany::count(),
                'active' => InsuranceCompany::where('is_active', 'yes')->count(),
                'inactive' => InsuranceCompany::where('is_active', 'no')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.insurance.catalog-form', [
            'resource' => 'companies',
            'record' => new InsuranceCompany,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(InsuranceCompanyRequest $request, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'companies') : null;
        unset($data['image']);

        try {
            $company = DB::transaction(fn (): InsuranceCompany => InsuranceCompany::query()->create([
                ...$data,
                'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], InsuranceCompany::class),
                'display_order' => $data['display_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
                'image' => $uploadedImage,
            ]));
        } catch (Throwable $exception) {
            $assets->deleteImage($uploadedImage);
            throw $exception;
        }

        return redirect()->route('admin.insurance.companies.index')
            ->with('success', "Insurance company '{$company->name}' was created.");
    }

    public function edit(InsuranceCompany $company): View
    {
        return view('admin.insurance.catalog-form', [
            'resource' => 'companies',
            'record' => $company,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(InsuranceCompanyRequest $request, InsuranceCompany $company, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        $data = $request->validated();
        if ((int) $data['insurance_category_id'] !== (int) $company->insurance_category_id
            && ($company->products()->exists() || $company->productTypes()->exists())) {
            return back()->withInput()->with('error', 'This company cannot change categories while products or product types are linked to it.');
        }

        $uploadedImage = isset($data['image']) ? $assets->storeImage($data['image'], 'companies') : null;
        unset($data['image']);
        $oldImage = $company->image;

        try {
            DB::transaction(function () use ($company, $data, $request, $assets, $uploadedImage): void {
                $company->fill([
                    ...$data,
                    'slug' => $assets->uniqueSlug(trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'], InsuranceCompany::class, $company->id),
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

        return redirect()->route('admin.insurance.companies.index')
            ->with('success', "Insurance company '{$company->name}' was updated.");
    }

    public function destroy(InsuranceCompany $company, InsuranceCatalogAssetService $assets): RedirectResponse
    {
        try {
            DB::transaction(function () use ($company): void {
                if ($company->products()->exists() || $company->productTypes()->exists()) {
                    throw new \DomainException('This company has related products or product types and cannot be deleted.');
                }

                $company->delete();
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (QueryException) {
            return back()->with('error', 'This company has related records and cannot be deleted.');
        }

        $assets->deleteImage($company->image);

        return redirect()->route('admin.insurance.companies.index')->with('success', 'Insurance company deleted.');
    }
}
