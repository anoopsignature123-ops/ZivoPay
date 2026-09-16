<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\UserInvestment;
use App\Models\UserReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Transaction::where('user_id', $user->id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);

        // Income Totals Summary
        $totalRoi = Transaction::where('user_id', $user->id)->where('type', 'daily_roi')->sum('amount');
        $totalLevelIncome = Transaction::where('user_id', $user->id)->whereIn('type', ['direct_bonus', 'level_income'])->sum('amount');
        $totalRoiLevelIncome = Transaction::where('user_id', $user->id)->whereIn('type', ['level_roi', 'roi_level_income'])->sum('amount');
        $totalSubscriptionIncome = Transaction::where('user_id', $user->id)->whereIn('type', ['subscription_level', 'subscription'])->sum('amount');

        $achievedRewards = UserReward::where('user_id', $user->id)->get();

        return view('user.income.index', compact(
            'user',
            'transactions',
            'totalRoi',
            'totalLevelIncome',
            'totalRoiLevelIncome',
            'totalSubscriptionIncome',
            'achievedRewards'
        ));
    }

    public function depositHistory(Request $request)
    {
        $user = Auth::user();
        $query = Deposit::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('deposit_ref', 'like', "%{$search}%")
                    ->orWhere('trx_hash', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $deposits = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalApproved = (float) Deposit::where('user_id', $user->id)->where('status', 'approved')->sum('final_amount');
        $totalPending = (float) Deposit::where('user_id', $user->id)->where('status', 'pending')->sum('amount');
        $totalCount = Deposit::where('user_id', $user->id)->count();

        return view('user.reports.deposits', compact('user', 'deposits', 'totalApproved', 'totalPending', 'totalCount'));
    }

    public function packageHistory(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->where('type', 'subscription');

        $transactions = $query->orderBy('id', 'desc')->paginate(20);

        return view('user.reports.package_history', compact('user', 'transactions'));
    }

    public function investmentHistory(Request $request)
    {
        $user = Auth::user();
        $investments = UserInvestment::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (float) UserInvestment::where('user_id', $user->id)->sum('amount');

        return view('user.reports.investment_history', compact('user', 'investments', 'totalAmount'));
    }

    public function subscriptionIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->whereIn('type', ['subscription_level', 'subscription']);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.subscription', compact('user', 'transactions', 'totalAmount'));
    }

    public function dailyRoiIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->where('type', 'daily_roi');

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.roi', compact('user', 'transactions', 'totalAmount'));
    }

    public function levelDirectIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->whereIn('type', ['direct_bonus', 'level_income']);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.level_direct', compact('user', 'transactions', 'totalAmount'));
    }

    public function levelRoiIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->whereIn('type', ['level_roi', 'roi_level_income']);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.level_roi', compact('user', 'transactions', 'totalAmount'));
    }

    public function subscriptionDirectIncome(Request $request)
    {
        return $this->directSubscriptionIncome($request);
    }

    public function subscriptionTeamIncome(Request $request)
    {
        return $this->teamSubscriptionIncome($request);
    }

    public function roiIncome(Request $request)
    {
        return $this->dailyRoiIncome($request);
    }

    public function directSubscriptionIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)
            ->where('type', 'subscription_level')
            ->where(function ($q) {
                $q->where('description', 'like', '%Level 1%')
                    ->orWhere('description', 'like', '%Direct%');
            });

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Direct Bonus Referral Income';

        return view('user.income.subscription', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }

    public function teamSubscriptionIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)
            ->where('type', 'subscription_level')
            ->where(function ($q) {
                $q->where('description', 'not like', '%Level 1%')
                    ->where('description', 'like', '%Level%');
            });

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Team Bonus Referral Income';

        return view('user.income.subscription', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }

    public function directBusinessIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->where('type', 'direct_bonus');

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Direct Business Income';

        return view('user.income.level_direct', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }
}
