<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\LoanCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $query = $this->query($filters);
        $statusCounts = (clone $query)->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $byCategory = (clone $query)->selectRaw('loan_category_id, COUNT(*) as applications, COALESCE(SUM(loan_amount), 0) as requested_amount')
            ->with('category:id,name')->groupBy('loan_category_id')->orderByDesc('applications')->get();
        $byStatus = (clone $query)->selectRaw('status, COUNT(*) as applications, COALESCE(SUM(loan_amount), 0) as requested_amount')->groupBy('status')->orderBy('status')->get();
        $applications = (clone $query)->with(['user:id,name,email,mobile', 'category:id,name'])
            ->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.loan.reports.index', [
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'total' => (clone $query)->count(),
            'applications' => $applications,
            'byCategory' => $byCategory,
            'byStatus' => $byStatus,
            'categories' => LoanCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $query = $this->query($filters)->with(['category:id,name', 'user:id,name,email,mobile'])->latest('created_at');

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Application number', 'Applicant', 'User', 'User ID', 'Mobile', 'Email', 'Category', 'Loan amount', 'Tenure years', 'Lender', 'Status', 'Submitted at']);
            $query->chunkById(500, function ($applications) use ($output): void {
                foreach ($applications as $application) {
                    fputcsv($output, [$application->application_number, $application->applicant_name, $application->user?->name, $application->user_id, $application->mobile, $application->email, $application->category?->name, $application->loan_amount, $application->tenure_years, $application->preferred_lender, $application->status, $application->submitted_at?->toDateTimeString() ?: $application->created_at?->toDateTimeString()]);
                }
            });
            fclose($output);
        }, 'loan-applications-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array<string, mixed> */
    private function filters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:191'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'category_id' => ['nullable', 'integer', 'exists:loan_categories,id'],
            'status' => ['nullable', Rule::in(LoanApplication::STATUSES)],
        ]);
    }

    /** @param array<string, mixed> $filters */
    private function query(array $filters): Builder
    {
        return LoanApplication::query()
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $search = trim((string) $filters['search']);
                $query->where(fn (Builder $nested) => $nested
                    ->where('application_number', 'like', "%{$search}%")
                    ->orWhere('applicant_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($filters['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('submitted_at', '>=', $date))
            ->when($filters['to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('submitted_at', '<=', $date))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int|string $id) => $query->where('loan_category_id', $id))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
    }
}
