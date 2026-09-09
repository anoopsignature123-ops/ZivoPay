<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $today = now()->startOfDay();

        // 1. Personal Investment & Capping Stats
        $totalInvested = UserPackage::where('user_id', $user->id)->sum('invested_amount');
        $activeInvestmentsCount = UserPackage::where('user_id', $user->id)->where('status', 'active')->count();

        $activeInvestmentAmount = $user->total_active_investment;
        $workingCap = $user->working_income_cap;
        $nonWorkingCap = $user->non_working_income_cap;

        $workingEarned = $user->total_working_earned;
        $nonWorkingEarned = $user->total_non_working_earned;

        $remainingWorkingCap = $user->remaining_working_cap;
        $remainingNonWorkingCap = $user->remaining_non_working_cap;

        // 2. Personal Income Summaries Across All 7 Dex Trade Incomes
        $totalRoiEarned = Transaction::where('user_id', $user->id)->where('type', 'daily_roi')->sum('amount');
        $todayRoiEarned = Transaction::where('user_id', $user->id)->where('type', 'daily_roi')->where('created_at', '>=', $today)->sum('amount');

        $totalDirectEarned = Transaction::where('user_id', $user->id)->where('type', 'direct_commission')->sum('amount');
        $todayDirectEarned = Transaction::where('user_id', $user->id)->where('type', 'direct_commission')->where('created_at', '>=', $today)->sum('amount');

        $totalMatchingEarned = Transaction::where('user_id', $user->id)->where('type', 'matching_income')->sum('amount');
        $todayMatchingEarned = Transaction::where('user_id', $user->id)->where('type', 'matching_income')->where('created_at', '>=', $today)->sum('amount');

        $totalReferralRoiEarned = Transaction::where('user_id', $user->id)->where('type', 'referral_roi')->sum('amount');
        $todayReferralRoiEarned = Transaction::where('user_id', $user->id)->where('type', 'referral_roi')->where('created_at', '>=', $today)->sum('amount');

        $totalMatchingRoiEarned = Transaction::where('user_id', $user->id)->where('type', 'matching_roi')->sum('amount');
        $todayMatchingRoiEarned = Transaction::where('user_id', $user->id)->where('type', 'matching_roi')->where('created_at', '>=', $today)->sum('amount');

        $totalUplineMatchingEarned = Transaction::where('user_id', $user->id)->where('type', 'upline_matching')->sum('amount');
        $todayUplineMatchingEarned = Transaction::where('user_id', $user->id)->where('type', 'upline_matching')->where('created_at', '>=', $today)->sum('amount');

        $totalSalaryEarned = Transaction::where('user_id', $user->id)->where('type', 'salary_income')->sum('amount');
        $todaySalaryEarned = Transaction::where('user_id', $user->id)->where('type', 'salary_income')->where('created_at', '>=', $today)->sum('amount');

        $totalIncomeEarned = $totalRoiEarned + $totalDirectEarned + $totalMatchingEarned + $totalReferralRoiEarned + $totalMatchingRoiEarned + $totalUplineMatchingEarned + $totalSalaryEarned;

        // 3. Withdrawal & Wallet Stats
        $totalWithdrawn = Withdrawal::where('user_id', $user->id)->whereIn('status', ['approved', 'completed'])->sum('net_amount');

        // 4. Direct Network & Leg Volume Stats
        $directMembersCount = User::where('sponsor_code', $user->referral_code)->count();
        $activeDirectMembersCount = User::where('sponsor_code', $user->referral_code)->where('status', 'active')->count();

        $legStats = $user->leg_volume_stats;

        // 5. Recent Collections
        $activePackages = UserPackage::with('package')->where('user_id', $user->id)->latest()->take(5)->get();
        $recentTransactions = Transaction::where('user_id', $user->id)->latest()->take(5)->get();
        $recentDeposits = Deposit::where('user_id', $user->id)->latest()->take(5)->get();

        return view('user.dashboard', compact(
            'user',
            'totalInvested', 'activeInvestmentsCount', 'activeInvestmentAmount',
            'workingCap', 'nonWorkingCap', 'workingEarned', 'nonWorkingEarned',
            'remainingWorkingCap', 'remainingNonWorkingCap',
            'totalRoiEarned', 'todayRoiEarned',
            'totalDirectEarned', 'todayDirectEarned',
            'totalMatchingEarned', 'todayMatchingEarned',
            'totalReferralRoiEarned', 'todayReferralRoiEarned',
            'totalMatchingRoiEarned', 'todayMatchingRoiEarned',
            'totalUplineMatchingEarned', 'todayUplineMatchingEarned',
            'totalSalaryEarned', 'todaySalaryEarned',
            'totalIncomeEarned', 'totalWithdrawn',
            'directMembersCount', 'activeDirectMembersCount', 'legStats',
            'activePackages', 'recentTransactions', 'recentDeposits'
        ));
    }
}
