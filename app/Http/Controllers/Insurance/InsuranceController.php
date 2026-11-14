<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Insurance\SubmitInsuranceApplicationRequest;
use App\Http\Resources\Api\Insurance\InsuranceApplicationResource;
use App\Http\Resources\Api\Insurance\InsuranceCategoryResource;
use App\Http\Resources\Api\Insurance\InsuranceCompanyResource;
use App\Http\Resources\Api\Insurance\InsuranceProductResource;
use App\Http\Resources\Api\Insurance\InsuranceProductTypeResource;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductType;
use App\Models\User;
use App\Services\Insurance\SubmitInsuranceApplicationService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class InsuranceController extends Controller
{
    use ApiResponse;

    public function insuranceCategories(): JsonResponse
    {
        return $this->handleInsuranceRequest(function (): JsonResponse {
            $categories = InsuranceCategory::query()->where('is_active', true)->orderBy('display_order')->orderBy('name')->get();

            return $this->successResponse(InsuranceCategoryResource::collection($categories)->resolve(),
                $categories->isEmpty()
                    ? 'No active insurance categories are available right now.'
                    : 'Insurance categories retrieved successfully.',
            );
        }, 'Unable to retrieve insurance categories.');
    }

    public function insuranceCategoryCompanies(Request $request): JsonResponse
    {
        return $this->handleInsuranceRequest(function () use ($request): JsonResponse {
            $this->mapRequestAliases($request, ['insurance_category_id' => 'category_id', 'cat_id' => 'category_id']);
            $data = $request->validate([
                'category_id' => ['required', 'integer', 'exists:insurance_categories,id'],
                'search' => ['nullable', 'string', 'max:120'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
                'page' => ['nullable', 'integer', 'min:1'],
            ]);

            $category = InsuranceCategory::query()->whereKey((int) $data['category_id'])->where('is_active', true)->first();
            if ($category === null) {
                return $this->errorResponse('This category is unavailable or inactive. Please select an active category.', 404);
            }

            $search = trim((string) ($data['search'] ?? ''));
            $companies = InsuranceCompany::query()
                ->where('insurance_category_id', $category->id)
                ->where('is_active', 'yes')
                ->whereHas('products', fn (Builder $query) => $query
                    ->where('is_active', 'yes')
                    ->where('insurance_category_id', $category->id))
                ->when($search !== '', function (Builder $query) use ($search, $category): void {
                    $query->where(function (Builder $companyQuery) use ($search, $category): void {
                        $companyQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhereHas('products', function (Builder $productQuery) use ($search, $category): void {
                                $productQuery->where('is_active', 'yes')
                                    ->where('insurance_category_id', $category->id)
                                    ->where('name', 'like', '%'.$search.'%');
                            });
                    });
                })
                ->with(['products' => function (HasMany $query) use ($category): void {
                    $query->select([
                        'id', 'insurance_category_id', 'insurance_company_id', 'name', 'slug', 'description', 'image', 'display_order',
                    ])->where('is_active', 'yes')
                        ->where('insurance_category_id', $category->id)
                        ->orderBy('display_order')
                        ->orderBy('name');
                }])
                ->withCount(['products' => fn (Builder $query) => $query
                    ->where('is_active', 'yes')
                    ->where('insurance_category_id', $category->id)])
                ->orderBy('display_order')
                ->orderBy('name')
                ->paginate(
                    (int) ($data['per_page'] ?? 10),
                    ['*'],
                    'page',
                    (int) ($data['page'] ?? 1),
                )
                ->withQueryString();

            return $this->successResponse([
                'category' => InsuranceCategoryResource::make($category)->resolve(),
                'items' => InsuranceCompanyResource::collection($companies->getCollection())->resolve(),
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                    'from' => $companies->firstItem(),
                    'to' => $companies->lastItem(),
                    'has_more_pages' => $companies->hasMorePages(),
                ],
                'filters' => [
                    'category_id' => $category->id,
                    'search' => $search,
                    'per_page' => $companies->perPage(),
                ],
            ],
                $companies->total() === 0
                    ? ($search !== ''
                        ? 'No companies or products match your search in this category.'
                        : 'No active companies with products are available in this category.')
                    : 'Insurance companies retrieved successfully.',
            );
        }, 'Unable to retrieve insurance companies.');
    }

    public function insuranceCategoryProducts(Request $request): JsonResponse
    {
        return $this->handleInsuranceRequest(function () use ($request): JsonResponse {
            $this->mapRequestAliases($request, [
                'insurance_category_id' => 'category_id',
                'cat_id' => 'category_id',
                'insurance_company_id' => 'company_id',
            ]);
            $data = $request->validate([
                'category_id' => ['required', 'integer', 'exists:insurance_categories,id'],
                'company_id' => ['required', 'integer', 'exists:insurance_companies,id'],
            ]);

            $category = InsuranceCategory::query()
                ->whereKey((int) $data['category_id'])
                ->where('is_active', true)
                ->first();

            if ($category === null) {
                return $this->errorResponse('This category is unavailable or inactive. Please select an active category.', 404);
            }

            $company = InsuranceCompany::query()
                ->whereKey((int) $data['company_id'])
                ->where('insurance_category_id', $category->id)
                ->where('is_active', 'yes')
                ->first();

            if ($company === null) {
                return $this->validationErrorResponse([
                    'company_id' => ['The selected active company does not belong to the selected category.'],
                ]);
            }

            $products = InsuranceProduct::query()
                ->where('insurance_category_id', $category->id)
                ->where('insurance_company_id', $company->id)
                ->where('is_active', 'yes')
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();

            return $this->successResponse(
                InsuranceProductResource::collection($products)->resolve(),
                $products->isEmpty()
                    ? 'No active products are available for this company in the selected category.'
                    : 'Insurance products retrieved successfully.',
            );
        }, 'Unable to retrieve insurance products.');
    }

    public function insuranceProductTypes(Request $request): JsonResponse
    {
        return $this->handleInsuranceRequest(function () use ($request): JsonResponse {
            $this->mapRequestAliases($request, ['insurance_product_id' => 'product_id']);
            $data = $request->validate([
                'product_id' => ['required', 'integer', 'exists:insurance_products,id'],
            ]);

            $product = InsuranceProduct::query()
                ->whereKey((int) $data['product_id'])
                ->where('is_active', 'yes')
                ->first();

            if ($product === null) {
                return $this->errorResponse('This product is unavailable or inactive. Please select an active product.', 404);
            }

            $productTypes = ProductType::query()
                ->where('insurance_product_id', $product->id)
                ->where('is_active', 'yes')
                ->where(fn (Builder $query) => $query->whereNull('insurance_category_id')
                    ->orWhere('insurance_category_id', $product->insurance_category_id))
                ->where(fn (Builder $query) => $query->whereNull('insurance_company_id')
                    ->orWhere('insurance_company_id', $product->insurance_company_id))
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();

            return $this->successResponse(
                InsuranceProductTypeResource::collection($productTypes)->resolve(),
                $productTypes->isEmpty()
                    ? 'No active product types are available for this product yet.'
                    : 'Insurance product types retrieved successfully.',
            );
        }, 'Unable to retrieve insurance product types.');
    }

    public function insuranceApplicationSubmission(
        SubmitInsuranceApplicationRequest $request,
        SubmitInsuranceApplicationService $submissionService,
    ): JsonResponse {
        return $this->handleInsuranceRequest(function () use ($request, $submissionService): JsonResponse {
            /** @var User $user */
            $user = $request->user();
            $application = $submissionService->submit($request->validated(), $user);

            return $this->successResponse(
                InsuranceApplicationResource::make($application)->resolve(),
                'Insurance application submitted successfully.',
                201,
            );
        }, 'Unable to submit insurance application.');
    }

    /** @param callable(): JsonResponse $operation */
    private function handleInsuranceRequest(callable $operation, string $failureMessage): JsonResponse
    {
        try {
            return $operation();
        } catch (ValidationException $exception) {
            return $this->validationErrorResponse($exception->errors());
        } catch (Throwable $exception) {
            Log::error($failureMessage, ['exception' => $exception]);

            return $this->errorResponse($failureMessage, 500);
        }
    }

    /** @param array<string, string> $aliases */
    private function mapRequestAliases(Request $request, array $aliases): void
    {
        foreach ($aliases as $alias => $canonical) {
            if (! $request->filled($canonical) && $request->filled($alias)) {
                $request->merge([$canonical => $request->input($alias)]);
            }
        }
    }
}
