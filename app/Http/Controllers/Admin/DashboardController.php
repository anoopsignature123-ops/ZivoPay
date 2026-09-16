<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Member Management Statistics (Exclude Admin, role_id != 1)
        $totalMembers = User::where('role_id', '!=', 1)->count();
        $activeMembers = User::where('role_id', '!=', 1)->where('status', 'active')->count();
        $inactiveMembers = User::where('role_id', '!=', 1)->where('status', 'inactive')->count();
        $subscribedMembers = User::where('role_id', '!=', 1)->where('is_subscription_active', 1)->count();
        $subscriptionRevenue = $subscribedMembers * 3000.00;

        // Daily Activity Metrics
        $todayNewMembers = User::where('role_id', '!=', 1)->whereDate('created_at', now())->count();
        $todayActivations = User::where('role_id', '!=', 1)->whereDate('activated_at', now())->count();

        // Wallet & Balance Summaries (Member Wallets)
        $totalDepositWalletSum = User::where('role_id', '!=', 1)->sum('deposit_wallet');
        $totalEarningWalletSum = User::where('role_id', '!=', 1)->sum('earning_wallet');

        // Capital Investments & Earnings Metrics
        $totalInvestmentsSum = UserInvestment::where('status', 'active')->sum('amount');
        $totalRoiDistributed = Transaction::where('type', 'daily_roi')->sum('amount');
        $totalCommissionsPaid = Transaction::whereIn('type', ['direct_bonus', 'level_income', 'subscription_level', 'level_roi', 'roi_level_income'])->sum('amount');

        // Withdrawals Summary
        $pendingWithdrawalsSum = Withdrawal::where('status', 'pending')->sum('amount');
        $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();
        $approvedWithdrawalsSum = Withdrawal::where('status', 'approved')->sum('amount');

        // Recent Collections for Live Dashboard Feed (No withdrawal cards on dashboard)
        $recentUsers = User::where('role_id', '!=', 1)->latest()->take(6)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(6)->get();
        $recentSubscriptions = User::where('role_id', '!=', 1)
            ->where('is_subscription_active', 1)
            ->latest('activated_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers',
            'activeMembers',
            'inactiveMembers',
            'subscribedMembers',
            'subscriptionRevenue',
            'todayNewMembers',
            'todayActivations',
            'totalDepositWalletSum',
            'totalEarningWalletSum',
            'totalInvestmentsSum',
            'totalRoiDistributed',
            'totalCommissionsPaid',
            'pendingWithdrawalsSum',
            'pendingWithdrawalsCount',
            'approvedWithdrawalsSum',
            'recentUsers',
            'recentTransactions',
            'recentSubscriptions'
        ));
    }
}
