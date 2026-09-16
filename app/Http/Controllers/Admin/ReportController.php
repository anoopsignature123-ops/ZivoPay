<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserReward;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function depositHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->whereIn('type', ['deposit', 'investment', 'admin_credit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $deposits = $query->orderBy('id', 'desc')->paginate(20);
        $totalDepositAmount = (clone $query)->sum('amount');

        return view('admin.reports.deposits', compact('deposits', 'totalDepositAmount'));
    }

    public function transactionHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('wallet_type')) {
            $query->where('wallet_type', $request->wallet_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(25);
        $totalAmount = (clone $query)->sum('amount');

        return view('admin.reports.transactions', compact('transactions', 'totalAmount'));
    }

    public function subscriptionHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->whereIn('type', ['subscription', 'subscription_level']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $subscriptions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('admin.reports.subscriptions', compact('subscriptions', 'totalAmount'));
    }

    public function roiHistory(Request $request)
    {
        $query = Transaction::with(['user'])
            ->where('type', 'daily_roi');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $rois = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('admin.reports.roi', compact('rois', 'totalAmount'));
    }

    public function levelDirectHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->whereIn('type', ['direct_bonus', 'level_income']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $directs = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('admin.reports.level_direct', compact('directs', 'totalAmount'));
    }

    public function levelRoiHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->whereIn('type', ['level_roi', 'roi_level_income']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $levelRois = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');

        return view('admin.reports.level_roi', compact('levelRois', 'totalAmount'));
    }

    public function rewardHistory(Request $request)
    {
        $query = UserReward::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reward_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $rewards = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.reports.rewards', compact('rewards'));
    }

    public function directSubscriptionHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->where('type', 'subscription_level')
            ->where(function ($q) {
                $q->where('description', 'like', '%Level 1%')
                    ->orWhere('description', 'like', '%Direct%');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $subscriptions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Direct Bonus Referral Income Audit';

        return view('admin.reports.subscriptions', compact('subscriptions', 'totalAmount', 'reportTitle'));
    }

    public function teamSubscriptionHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->where('type', 'subscription_level')
            ->where(function ($q) {
                $q->where('description', 'not like', '%Level 1%')
                    ->where('description', 'like', '%Level%');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $subscriptions = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Team Bonus Referral Income Audit';

        return view('admin.reports.subscriptions', compact('subscriptions', 'totalAmount', 'reportTitle'));
    }

    public function directBusinessHistory(Request $request)
    {
        $query = Transaction::with(['user', 'fromUser'])
            ->where('type', 'direct_bonus');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $directs = $query->orderBy('id', 'desc')->paginate(20);
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Direct Business Income Audit';

        return view('admin.reports.level_direct', compact('directs', 'totalAmount', 'reportTitle'));
    }
}
