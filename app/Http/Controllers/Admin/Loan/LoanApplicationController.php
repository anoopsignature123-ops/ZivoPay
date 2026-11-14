<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Loan\UpdateLoanApplicationStatusRequest;
use App\Models\LoanApplication;
use App\Models\LoanApplicationDocument;
use App\Models\LoanCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:191'], 'status' => ['nullable', Rule::in(LoanApplication::STATUSES)],
            'category_id' => ['nullable', 'integer', 'exists:loan_categories,id'], 'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $applications = $this->query($filters)->with(['user:id,name,email,mobile', 'category:id,name'])->latest('created_at')->paginate(15)->withQueryString();
        $counts = LoanApplication::query()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $stats = collect(LoanApplication::STATUSES)->mapWithKeys(fn (string $status) => [$status => (int) ($counts[$status] ?? 0)]);

        return view('admin.loan.applications.index', [
            'applications' => $applications, 'filters' => $filters, 'stats' => $stats,
            'categories' => LoanCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(LoanApplication $application): View
    {
        $application->load(['user:id,name,email,mobile', 'category', 'documents', 'statusHistories.changedBy:id,name']);

        return view('admin.loan.applications.show', compact('application'));
    }

    public function updateStatus(UpdateLoanApplicationStatusRequest $request, LoanApplication $application): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($application, $data, $request): void {
            if ($application->status !== $data['status'] || ($data['admin_remarks'] ?? null) !== $application->admin_remarks) {
                $application->statusHistories()->create([
                    'old_status' => $application->status, 'new_status' => $data['status'],
                    'changed_by' => $request->user()?->getAuthIdentifier(), 'remarks' => $data['admin_remarks'] ?? null,
                ]);
            }
            $application->update([
                'status' => $data['status'], 'admin_remarks' => $data['admin_remarks'] ?? null,
                'approved_at' => $data['status'] === 'approved' ? ($application->approved_at ?? now()) : null,
            ]);
        });

        return back()->with('success', 'Loan application review was saved.');
    }

    public function document(LoanApplication $application, int $document): StreamedResponse
    {
        [$record, $disk, $path] = $this->resolveDocument($application, $document);

        return Storage::disk($disk)->download($path, $record->original_name ?: basename($path));
    }

    public function previewDocument(LoanApplication $application, int $document): StreamedResponse
    {
        [$record, $disk, $path] = $this->resolveDocument($application, $document);
        $mimeType = Storage::disk($disk)->mimeType($path);
        abort_unless(in_array($mimeType, ['image/jpeg', 'image/png', 'application/pdf'], true), 415);

        return Storage::disk($disk)->response($path, $record->original_name ?: basename($path), ['Content-Type' => $mimeType, 'X-Content-Type-Options' => 'nosniff'], 'inline');
    }

    /** @return array{0: LoanApplicationDocument, 1: string, 2: string} */
    private function resolveDocument(LoanApplication $application, int $document): array
    {
        $record = $application->documents()->findOrFail($document);
        $path = str_replace('\\', '/', $record->file_path);
        abort_if($path === '' || Str::contains('/'.$path.'/', '/../'), 404);
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return [$record, $disk, $path];
            }
        }
        abort(404);
    }

    /** @param array<string, mixed> $filters */
    private function query(array $filters): Builder
    {
        return LoanApplication::query()
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $term = trim($filters['search']);
                $query->where(fn (Builder $nested) => $nested->where('application_number', 'like', "%{$term}%")
                    ->orWhere('applicant_name', 'like', "%{$term}%")->orWhere('mobile', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int|string $id) => $query->where('loan_category_id', $id))
            ->when($filters['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('submitted_at', '>=', $date))
            ->when($filters['to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('submitted_at', '<=', $date));
    }
}
