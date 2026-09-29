<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\P2pTransfer;
use App\Models\Transaction;
use App\Models\UserReward;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Combined Deposit and P2P Audit Report for System Admin.
     */
    public function depositP2pHistory(Request $request)
    {
        // 1. Fetch Deposits with User
        $depositQuery = Deposit::with('user');

        if ($request->filled('from_date')) {
            $depositQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $depositQuery->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $depositQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $depositQuery->where(function ($q) use ($search) {
                $q->where('deposit_ref', 'like', "%{$search}%")
                    ->orWhere('trx_hash', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $deposits = $depositQuery->get()->map(function ($dep) {
            $isAdmin = $dep->payment_method === 'ADMIN';

            return [
                'id' => 'DEP-'.$dep->id,
                'created_at' => $dep->created_at,
                'record_type' => 'deposit',
                'category' => $isAdmin ? 'admin_credit' : 'deposit',
                'category_label' => $isAdmin ? 'Admin Add Fund' : 'Deposit ('.$dep->payment_method.')',
                'badge_class' => $isAdmin ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40',
                'ref_code' => $dep->deposit_ref,
                'user' => $dep->user,
                'user_name' => $dep->user?->name ?? 'N/A',
                'user_code' => $dep->user?->referral_code ?? 'N/A',
                'amount' => (float) $dep->final_amount,
                'party_detail' => $isAdmin ? 'Direct Admin Credit' : 'Payment Method: '.$dep->payment_method,
                'trx_hash' => $dep->trx_hash ?? 'N/A',
                'status' => $dep->status,
                'status_badge' => $dep->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($dep->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'),
                'remark' => $dep->admin_remark ?? 'Direct Wallet Recharge',
                'proof_file' => $dep->proof_file,
            ];
        });

        // 2. Fetch P2P Transfers with Sender & Receiver
        $p2pQuery = P2pTransfer::with(['sender', 'receiver']);

        if ($request->filled('from_date')) {
            $p2pQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $p2pQuery->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $p2pQuery->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('sender', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('receiver', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $p2pTransfers = $p2pQuery->get()->map(function ($p2p) {
            return [
                'id' => 'P2P-'.$p2p->id,
                'created_at' => $p2p->created_at,
                'record_type' => 'p2p',
                'category' => 'p2p',
                'category_label' => 'P2P Member Transfer',
                'badge_class' => 'bg-purple-500/20 text-purple-300 border border-purple-500/40',
                'ref_code' => $p2p->trx_id,
                'user' => $p2p->sender,
                'user_name' => $p2p->sender?->name ?? 'N/A',
                'user_code' => $p2p->sender?->referral_code ?? 'N/A',
                'amount' => (float) $p2p->amount,
                'party_detail' => 'Sender: '.($p2p->sender?->referral_code ?? 'N/A').' ➔ Receiver: '.($p2p->receiver?->referral_code ?? 'N/A'),
                'trx_hash' => $p2p->trx_id,
                'status' => 'completed',
                'status_badge' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40',
                'remark' => $p2p->remarks ?? 'P2P Member Transfer',
                'proof_file' => null,
            ];
        });

        // 3. Combine & Filter by Category Type if requested
        $allRecords = $deposits->concat($p2pTransfers);

        if ($request->filled('type')) {
            $type = $request->type;
            $allRecords = $allRecords->where('category', $type);
        }

        if ($request->filled('status')) {
            $reqStatus = strtolower($request->status);
            $allRecords = $allRecords->filter(function ($item) use ($reqStatus) {
                return strtolower($item['status']) === $reqStatus;
            });
        }

        // Sort BY created_at DESC
        $sortedRecords = $allRecords->sortByDesc('created_at')->values();

        // Calculate Totals
        $totalDepositAmount = $sortedRecords->whereIn('category', ['deposit', 'admin_credit'])->where('status', 'approved')->sum('amount');
        $totalP2pAmount = $sortedRecords->where('category', 'p2p')->sum('amount');
        $totalCount = $sortedRecords->count();

        // Manual Pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentPageItems = $sortedRecords->slice(($page - 1) * $perPage, $perPage)->values();

        $paginatedRecords = new LengthAwarePaginator(
            $currentPageItems,
            $sortedRecords->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.reports.deposit_p2p', compact(
            'paginatedRecords',
            'totalDepositAmount',
            'totalP2pAmount',
            'totalCount'
        ));
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
