<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Insurance\UpdateInsuranceApplicationStatusRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InsuranceApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', Rule::in(Application::STATUSES)],
            'category_id' => ['nullable', 'integer', 'exists:insurance_categories,id'],
            'company_id' => ['nullable', 'integer', 'exists:insurance_companies,id'],
            'product_id' => ['nullable', 'integer', 'exists:insurance_products,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $applications = $this->applicationQuery($filters)
            ->with(['user:id,name,email,mobile', 'category:id,name', 'company:id,name', 'product:id,name'])
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.insurance.applications.index', [
            'applications' => $applications,
            'filters' => $filters,
            'categories' => InsuranceCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => InsuranceCompany::query()->orderBy('name')->get(['id', 'name']),
            'products' => InsuranceProduct::query()->orderBy('name')->get(['id', 'name']),
            'stats' => $this->statusCounts(),
        ]);
    }

    public function show(Application $application): View
    {
        $application->load([
            'user:id,name,email,mobile', 'category', 'company', 'product', 'specifications', 'healthSpecifications',
            'generalSpecifications.productType', 'requirements', 'additions', 'documents',
        ]);

        return view('admin.insurance.applications.show', compact('application'));
    }

    public function updateStatus(UpdateInsuranceApplicationStatusRequest $request, Application $application): RedirectResponse
    {
        $data = $request->validated();
        $isApproved = $data['status'] === 'approved';
        $hasBeenSubmitted = in_array($data['status'], ['submitted', 'under_review', 'approved', 'rejected'], true);

        $application->forceFill([
            'status' => $data['status'],
            'admin_remarks' => $data['admin_remarks'] ?? null,
            'submitted_at' => $hasBeenSubmitted ? ($application->submitted_at ?? now()) : $application->submitted_at,
            'approved_at' => $isApproved ? ($application->approved_at ?? now()) : null,
        ])->save();

        return back()->with('success', 'Application status and admin remarks were saved.');
    }

    public function document(Application $application, int $document): StreamedResponse
    {
        ['applicationDocument' => $applicationDocument, 'disk' => $disk, 'path' => $path] = $this->resolveDocument($application, $document);

        return Storage::disk($disk)->download($path, $applicationDocument->original_name ?: basename($path));
    }

    public function previewDocument(Application $application, int $document): StreamedResponse
    {
        ['applicationDocument' => $applicationDocument, 'disk' => $disk, 'path' => $path] = $this->resolveDocument($application, $document);
        $storage = Storage::disk($disk);
        $mimeType = $storage->mimeType($path);

        abort_unless(in_array($mimeType, ['image/jpeg', 'image/png'], true), 415);

        return $storage->response(
            $path,
            $applicationDocument->original_name ?: basename($path),
            ['Content-Type' => $mimeType, 'X-Content-Type-Options' => 'nosniff'],
            'inline',
        );
    }

    /** @return array{applicationDocument: ApplicationDocument, disk: string, path: string} */
    private function resolveDocument(Application $application, int $document): array
    {
        $applicationDocument = $application->documents()->findOrFail($document);
        $path = str_replace('\\', '/', $applicationDocument->document_path);

        abort_if($path === '' || Str::contains('/'.$path.'/', '/../'), 404);

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return compact('applicationDocument', 'disk', 'path');
            }
        }

        abort(404);
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

    /** @return array<string, int> */
    private function statusCounts(): array
    {
        $counts = Application::query()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');

        return collect(Application::STATUSES)->mapWithKeys(fn (string $status): array => [$status => (int) ($counts[$status] ?? 0)])->all();
    }
}
