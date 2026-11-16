<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\LoanCategory;
use Illuminate\View\View;

class LoanDashboardController extends Controller
{
    public function __invoke(): View
    {
        $counts = LoanApplication::query()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $statusCounts = collect(LoanApplication::STATUSES)->mapWithKeys(fn (string $status) => [$status => (int) ($counts[$status] ?? 0)]);
        $recentApplications = LoanApplication::query()->with(['user:id,name,referral_code', 'category:id,name'])->latest()->limit(8)->get();

        return view('admin.loan.dashboard', [
            'statusCounts' => $statusCounts,
            'totalApplications' => $statusCounts->sum(),
            'todayApplications' => LoanApplication::query()->whereDate('submitted_at', today())->count(),
            'activeCategories' => LoanCategory::query()->where('is_active', true)->count(),
            'totalCategories' => LoanCategory::query()->count(),
            'inactiveCategories' => LoanCategory::query()->where('is_active', false)->count(),
            'recentApplications' => $recentApplications,
        ]);
    }
}
