<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InsuranceReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $query = $this->applicationQuery($filters);
        $statusCounts = (clone $query)->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $total = (clone $query)->count();
        $applications = $query->with(['category:id,name', 'company:id,name', 'product:id,name'])
            ->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.insurance.reports.index', [
            'applications' => $applications,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'total' => $total,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->orderBy('name')->get(['id', 'name']),
            'products' => InsuranceProduct::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->applicationQuery($this->validatedFilters($request))
            ->with(['category:id,name', 'company:id,name', 'product:id,name', 'user:id,name,email,mobile']);

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Application number', 'Customer', 'Mobile', 'Email', 'Category', 'Company', 'Product', 'Status', 'Submitted']);
            $safeCell = static function (mixed $value): mixed {
                if (! is_string($value) || ! preg_match('/^[\s]*[=+\-@]/u', $value)) {
                    return $value;
                }

                return "'".$value;
            };

            $query->chunkById(500, function ($applications) use ($output, $safeCell): void {
                foreach ($applications as $application) {
                    fputcsv($output, array_map($safeCell, [
                        $application->application_number,
                        $application->name ?: $application->user?->name,
                        $application->phone ?: $application->user?->mobile,
                        $application->email ?: $application->user?->email,
                        $application->category?->name,
                        $application->company?->name,
                        $application->product?->name,
                        $application->status,
                        $application->submitted_at?->toDateTimeString() ?: $application->created_at?->toDateTimeString(),
                    ]));
                }
            });

            fclose($output);
        }, 'insurance-applications-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array<string, mixed> */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', Rule::in(Application::STATUSES)],
            'category_id' => ['nullable', 'integer', 'exists:insurance_categories,id'],
            'company_id' => ['nullable', 'integer', 'exists:insurance_companies,id'],
            'product_id' => ['nullable', 'integer', 'exists:insurance_products,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
    }

    /** @param array<string, mixed> $filters */
    private function applicationQuery(array $filters): Builder
    {
        return Application::query()
            ->when(($filters['search'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $search = trim((string) $filters['search']);
                $query->where(fn (Builder $nested) => $nested
                    ->where('application_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int|string $id) => $query->where('insurance_category_id', $id))
            ->when($filters['company_id'] ?? null, fn (Builder $query, int|string $id) => $query->where('insurance_company_id', $id))
            ->when($filters['product_id'] ?? null, fn (Builder $query, int|string $id) => $query->where('insurance_product_id', $id))
            ->when($filters['from'] ?? null, fn (Builder $query, string $date) => $query->whereRaw('DATE(COALESCE(submitted_at, created_at)) >= ?', [$date]))
            ->when($filters['to'] ?? null, fn (Builder $query, string $date) => $query->whereRaw('DATE(COALESCE(submitted_at, created_at)) <= ?', [$date]));
    }
}
