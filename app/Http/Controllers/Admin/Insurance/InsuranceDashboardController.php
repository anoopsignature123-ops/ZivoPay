<?php

namespace App\Http\Controllers\Admin\Insurance;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use Illuminate\View\View;

class InsuranceDashboardController extends Controller
{
    public function __invoke(): View
    {
        $statusCounts = Application::query()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $applicationStats = collect(Application::STATUSES)->mapWithKeys(fn (string $status): array => [$status => (int) ($statusCounts[$status] ?? 0)]);
        $recentApplications = Application::query()
            ->with(['user:id,name,mobile', 'category:id,name', 'product:id,name'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('admin.insurance.dashboard', [
            'applicationStats' => $applicationStats,
            'totalApplications' => $applicationStats->sum(),
            'todayApplications' => Application::query()->whereDate('submitted_at', today())->count(),
            'activeCategories' => InsuranceCategory::query()->where('is_active', true)->count(),
            'activeCompanies' => InsuranceCompany::query()->where('is_active', 'yes')->count(),
            'activeProducts' => InsuranceProduct::query()->where('is_active', 'yes')->count(),
            'recentApplications' => $recentApplications,
        ]);
    }
}
