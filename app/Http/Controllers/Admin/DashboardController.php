<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPackage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // 1. Member Management Statistics (Exclude Admin, role_id != 1)
        $totalMembers = User::where('role_id', '!=', 1)->count();
        $activeMembers = User::where('role_id', '!=', 1)->where('status', 'active')->count();
        $pendingMembers = User::where('role_id', '!=', 1)->where('status', 'pending')->count();

        // 2. Wallet & Balance Summaries (Member Wallets)
        $totalDepositWalletSum = User::where('role_id', '!=', 1)->sum('deposit_wallet');
        $totalEarningWalletSum = User::where('role_id', '!=', 1)->sum('earning_wallet');

        // 3. Deposit Request Statistics
        $totalApprovedDepositsSum = Deposit::where('status', 'approved')->sum('amount');
        $pendingDepositsCount = Deposit::where('status', 'pending')->count();

        // 4. Investment & Capital Statistics
        $totalPackagesPurchasedCount = UserPackage::count();
        $activePackagesCount = UserPackage::where('status', 'active')->count();
        $totalCapitalInvestedSum = UserPackage::sum('invested_amount');

        // 5. Income Payout Summaries (Across all 7 Dex Trade Income Streams)
        $totalRoiPaidSum = Transaction::where('type', 'daily_roi')->sum('amount');
        $totalDirectCommissionPaidSum = Transaction::where('type', 'direct_commission')->sum('amount');
        $totalMatchingPaidSum = Transaction::where('type', 'matching_income')->sum('amount');
        $totalReferralRoiPaidSum = Transaction::where('type', 'referral_roi')->sum('amount');
        $totalMatchingRoiPaidSum = Transaction::where('type', 'matching_roi')->sum('amount');
        $totalUplineMatchingPaidSum = Transaction::where('type', 'upline_matching')->sum('amount');
        $totalSalaryPaidSum = Transaction::where('type', 'salary_income')->sum('amount');

        $totalIncomeDistributedSum = $totalRoiPaidSum + $totalDirectCommissionPaidSum + $totalMatchingPaidSum
            + $totalReferralRoiPaidSum + $totalMatchingRoiPaidSum + $totalUplineMatchingPaidSum + $totalSalaryPaidSum;

        // 6. Recent Live Activity Collections
        $recentUsers = User::where('role_id', '!=', 1)->latest()->take(5)->get();
        $recentDeposits = Deposit::with('user')->latest()->take(5)->get();
        $recentInvestments = UserPackage::with(['user', 'package'])->latest()->take(5)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalMembers', 'activeMembers', 'pendingMembers',
            'totalDepositWalletSum', 'totalEarningWalletSum',
            'totalApprovedDepositsSum', 'pendingDepositsCount',
            'totalPackagesPurchasedCount', 'activePackagesCount', 'totalCapitalInvestedSum',
            'totalRoiPaidSum', 'totalDirectCommissionPaidSum', 'totalMatchingPaidSum',
            'totalReferralRoiPaidSum', 'totalMatchingRoiPaidSum', 'totalUplineMatchingPaidSum', 'totalSalaryPaidSum',
            'totalIncomeDistributedSum',
            'recentUsers', 'recentDeposits', 'recentInvestments', 'recentTransactions'
        ));
    }
}
