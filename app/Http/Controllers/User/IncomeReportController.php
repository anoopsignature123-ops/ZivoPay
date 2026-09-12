<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncomeReportController extends Controller
{
    /**
     * Common helper for authenticated user income reports.
     */
    private function getUserIncomeReport(Request $request, string $type)
    {
        $userId = Auth::id();
        $query = Transaction::with(['user', 'user.sponsor', 'userPackage.user'])
            ->where('user_id', $userId)
            ->where('type', $type);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('txn_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
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
        $userId = Auth::id();
        $user = Auth::user();

        $roiTotal = Transaction::where('user_id', $userId)->where('type', 'daily_roi')->sum('amount');
        $directTotal = Transaction::where('user_id', $userId)->where('type', 'direct_commission')->sum('amount');
        $matchingTotal = Transaction::where('user_id', $userId)->where('type', 'matching_income')->sum('amount');
        $referralRoiTotal = Transaction::where('user_id', $userId)->where('type', 'referral_roi')->sum('amount');
        $matchingRoiTotal = Transaction::where('user_id', $userId)->where('type', 'matching_roi')->sum('amount');
        $uplineMatchingTotal = Transaction::where('user_id', $userId)->where('type', 'upline_matching')->sum('amount');
        $salaryTotal = Transaction::where('user_id', $userId)->where('type', 'salary_income')->sum('amount');

        $grandTotal = $roiTotal + $directTotal + $matchingTotal + $referralRoiTotal + $matchingRoiTotal + $uplineMatchingTotal + $salaryTotal;

        $query = Transaction::with(['user', 'user.sponsor', 'userPackage.user'])
            ->where('user_id', $userId)
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
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $recentIncomes = $query->latest('id')->paginate(15)->withQueryString();

        return view('user.reports.summary', compact(
            'user',
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
        $data = $this->getUserIncomeReport($request, 'daily_roi');

        return view('user.reports.roi', $data);
    }

    public function direct(Request $request): View
    {
        $data = $this->getUserIncomeReport($request, 'direct_commission');

        return view('user.reports.direct', $data);
    }

    public function matching(Request $request): View
    {
        $data = $this->getUserIncomeReport($request, 'matching_income');

        return view('user.reports.matching', $data);
    }

    public function referralRoi(Request $request): View
    {
        $data = $this->getUserIncomeReport($request, 'referral_roi');

        return view('user.reports.referral_roi', $data);
    }

    public function matchingRoi(Request $request): View
    {
        $data = $this->getUserIncomeReport($request, 'matching_roi');

        return view('user.reports.matching_roi', $data);
    }

    public function uplineMatching(Request $request): View
    {
        $data = $this->getUserIncomeReport($request, 'upline_matching');

        return view('user.reports.upline_matching', $data);
    }

    public function salary(Request $request): View
    {
        $data = $this->getUserIncomeReport($request, 'salary_income');

        return view('user.reports.salary', $data);
    }
}
