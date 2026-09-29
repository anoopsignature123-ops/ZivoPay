<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\P2pTransfer;
use App\Models\Transaction;
use App\Models\UserInvestment;
use App\Models\UserReward;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

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

    /**
     * Combined Deposit and P2P Transaction Audit Report for User.
     */
    public function depositP2pHistory(Request $request)
    {
        $user = Auth::user();

        // 1. Fetch Deposits
        $depositQuery = Deposit::where('user_id', $user->id);

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
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        $deposits = $depositQuery->get()->map(function ($dep) {
            $isAdmin = $dep->payment_method === 'ADMIN';

            return [
                'id' => 'DEP-'.$dep->id,
                'created_at' => $dep->created_at,
                'record_type' => 'deposit',
                'category' => $isAdmin ? 'admin_credit' : 'deposit',
                'category_label' => $isAdmin ? 'Admin Add Fund' : 'Add Fund ('.$dep->payment_method.')',
                'badge_class' => $isAdmin ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40',
                'ref_code' => $dep->deposit_ref,
                'amount' => (float) $dep->final_amount,
                'party_detail' => $isAdmin ? 'Processed by System Admin' : 'Payment Gateway: '.$dep->payment_method,
                'trx_hash' => $dep->trx_hash ?? 'N/A',
                'status' => $dep->status,
                'status_badge' => $dep->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($dep->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'),
                'remark' => $dep->admin_remark ?? 'Direct Wallet Recharge',
                'proof_file' => $dep->proof_file,
            ];
        });

        // 2. Fetch P2P Transfers
        $p2pQuery = P2pTransfer::with(['sender', 'receiver'])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });

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
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('receiver', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $p2pTransfers = $p2pQuery->get()->map(function ($p2p) use ($user) {
            $isSender = ($p2p->sender_id === $user->id);

            return [
                'id' => 'P2P-'.$p2p->id,
                'created_at' => $p2p->created_at,
                'record_type' => 'p2p',
                'category' => $isSender ? 'p2p_sent' : 'p2p_received',
                'category_label' => $isSender ? 'P2P Transfer Sent' : 'P2P Transfer Received',
                'badge_class' => $isSender ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-teal-500/20 text-teal-300 border border-teal-500/40',
                'ref_code' => $p2p->trx_id,
                'amount' => (float) $p2p->amount,
                'party_detail' => $isSender
                    ? 'To: '.($p2p->receiver?->name ?? 'Member').' ('.($p2p->receiver?->referral_code ?? 'N/A').')'
                    : 'From: '.($p2p->sender?->name ?? 'Member').' ('.($p2p->sender?->referral_code ?? 'N/A').')',
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
            if ($type === 'p2p') {
                $allRecords = $allRecords->whereIn('category', ['p2p_sent', 'p2p_received']);
            } else {
                $allRecords = $allRecords->where('category', $type);
            }
        }

        // Filter status if requested
        if ($request->filled('status')) {
            $reqStatus = strtolower($request->status);
            $allRecords = $allRecords->filter(function ($item) use ($reqStatus) {
                return strtolower($item['status']) === $reqStatus;
            });
        }

        // Sort BY created_at DESC
        $sortedRecords = $allRecords->sortByDesc('created_at')->values();

        // Calculate Summary Totals
        $totalDepositAmount = $sortedRecords->whereIn('category', ['deposit', 'admin_credit'])->where('status', 'approved')->sum('amount');
        $totalP2pSentAmount = $sortedRecords->where('category', 'p2p_sent')->sum('amount');
        $totalP2pReceivedAmount = $sortedRecords->where('category', 'p2p_received')->sum('amount');
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

        return view('user.reports.deposit_p2p', compact(
            'user',
            'paginatedRecords',
            'totalDepositAmount',
            'totalP2pSentAmount',
            'totalP2pReceivedAmount',
            'totalCount'
        ));
    }

    public function packageHistory(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->where('type', 'subscription');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('user.reports.package_history', compact('user', 'transactions'));
    }

    public function investmentHistory(Request $request)
    {
        $user = Auth::user();
        $query = UserInvestment::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('plan_name', 'like', "%{$search}%");
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $investments = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (float) UserInvestment::where('user_id', $user->id)->sum('amount');

        return view('user.reports.investment_history', compact('user', 'investments', 'totalAmount'));
    }

    public function subscriptionIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->whereIn('type', ['subscription_level', 'subscription']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.subscription', compact('user', 'transactions', 'totalAmount'));
    }

    public function dailyRoiIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->where('type', 'daily_roi');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.roi', compact('user', 'transactions', 'totalAmount'));
    }

    public function fundWalletRoiIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'FWPROFIT%')
                    ->orWhere('description', 'like', '%Fund Wallet%');
            });

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Fund Wallet Daily Profit (ROI Income)';

        return view('user.income.roi', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }

    public function capitalInvestmentRoiIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'DROI%')
                    ->orWhere('trx_id', 'like', 'ROI%')
                    ->orWhere('description', 'like', '%Daily ROI Income%')
                    ->orWhere('description', 'like', '%Capital%');
            });

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Capital Investment Daily ROI Income';

        return view('user.income.roi', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }

    public function levelDirectIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->whereIn('type', ['direct_bonus', 'level_income']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.level_direct', compact('user', 'transactions', 'totalAmount'));
    }

    public function levelRoiIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->whereIn('type', ['level_roi', 'roi_level_income']);

        $reportTitle = 'ROI ON LEVEL INCOME (26% MATCHING)';
        if ($request->query('type') === 'fund') {
            $reportTitle = 'ROI ON LEVEL INCOME (SECONDARY)';
        } elseif ($request->query('type') === 'investment') {
            $reportTitle = 'ROI ON ROI LEVEL INCOME';
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');

        return view('user.income.level_roi', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
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

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
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

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Team Bonus Referral Income';

        return view('user.income.subscription', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }

    public function directBusinessIncome(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->where('type', 'direct_bonus');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');
        $reportTitle = 'Direct Business Income';

        return view('user.income.level_direct', compact('user', 'transactions', 'totalAmount', 'reportTitle'));
    }
}
