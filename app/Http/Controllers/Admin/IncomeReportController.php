<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeReportController extends Controller
{
    /**
     * Common helper to filter transactions by type and request filters.
     */
    private function getIncomeReport(Request $request, string $type)
    {
        $query = Transaction::with(['user', 'user.sponsor', 'userPackage.user'])->where('type', $type);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('txn_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%")
                            ->orWhereHas('sponsor', function ($sq) use ($search) {
                                $sq->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('referral_code', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = (clone $query)->latest('id')->paginate(15)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');
        $totalCount = (clone $query)->count();

        return compact('logs', 'totalAmount', 'totalCount');
    }

    /**
     * Master Income Summary Report across all 7 Dex Trade income types.
     */
    public function summary(Request $request): View
    {
        $roiTotal = Transaction::where('type', 'daily_roi')->sum('amount');
        $directTotal = Transaction::where('type', 'direct_commission')->sum('amount');
        $matchingTotal = Transaction::where('type', 'matching_income')->sum('amount');
        $referralRoiTotal = Transaction::where('type', 'referral_roi')->sum('amount');
        $matchingRoiTotal = Transaction::where('type', 'matching_roi')->sum('amount');
        $uplineMatchingTotal = Transaction::where('type', 'upline_matching')->sum('amount');
        $salaryTotal = Transaction::where('type', 'salary_income')->sum('amount');

        $grandTotal = $roiTotal + $directTotal + $matchingTotal + $referralRoiTotal + $matchingRoiTotal + $uplineMatchingTotal + $salaryTotal;

        $query = Transaction::with(['user', 'user.sponsor', 'userPackage.user'])
            ->whereIn('type', [
                'daily_roi',
                'direct_commission',
                'matching_income',
                'referral_roi',
                'matching_roi',
                'upline_matching',
                'salary_income',
            ]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('txn_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $recentIncomes = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.reports.summary', compact(
            'roiTotal',
            'directTotal',
            'matchingTotal',
            'referralRoiTotal',
            'matchingRoiTotal',
            'uplineMatchingTotal',
            'salaryTotal',
            'grandTotal',
            'recentIncomes'
        ));
    }

    public function roi(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'daily_roi');

        return view('admin.reports.roi', $data);
    }

    public function direct(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'direct_commission');

        return view('admin.reports.direct', $data);
    }

    public function matching(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'matching_income');

        return view('admin.reports.matching', $data);
    }

    public function referralRoi(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'referral_roi');

        return view('admin.reports.referral_roi', $data);
    }

    public function matchingRoi(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'matching_roi');

        return view('admin.reports.matching_roi', $data);
    }

    public function uplineMatching(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'upline_matching');

        return view('admin.reports.upline_matching', $data);
    }

    public function salary(Request $request): View
    {
        $data = $this->getIncomeReport($request, 'salary_income');

        return view('admin.reports.salary', $data);
    }
}
