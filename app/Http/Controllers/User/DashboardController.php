<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Direct Network & Downline Stats
        $directMembersCount = User::where('sponsor_code', $user->referral_code)->count();
        $activeDirectMembersCount = User::where('sponsor_code', $user->referral_code)->where('status', 'active')->count();
        $inactiveDirectMembersCount = max(0, $directMembersCount - $activeDirectMembersCount);

        $recentDirects = User::where('sponsor_code', $user->referral_code)->latest()->take(5)->get();

        // Team Unilevel Network Statistics
        $totalTeamUserIds = $user->getBranchUserIds();
        $downlineIds = array_diff($totalTeamUserIds, [$user->id]);
        $totalTeamCount = count($downlineIds);

        if ($totalTeamCount > 0) {
            $totalActiveTeamCount = User::whereIn('id', $downlineIds)->where('status', 'active')->count();
            $totalInactiveTeamCount = User::whereIn('id', $downlineIds)->where('status', 'inactive')->count();
            $totalTeamBusiness = DB::table('user_investments')
                ->whereIn('user_id', $downlineIds)
                ->where('status', 'active')
                ->sum('amount');
        } else {
            $totalActiveTeamCount = 0;
            $totalInactiveTeamCount = 0;
            $totalTeamBusiness = 0;
        }

        $activeNetworkRatio = $totalTeamCount > 0 ? round(($totalActiveTeamCount / $totalTeamCount) * 100, 1) : 0;

        // Wallet Balances & Investment Metrics
        $totalInvested = $user->investments()->where('status', 'active')->sum('amount');
        $totalWithdrawals = $user->withdrawals()->where('status', 'approved')->sum('amount');
        $activeInvestments = $user->investments()->where('status', 'active')->latest()->take(5)->get();
        $recentTransactions = $user->transactions()->latest()->take(5)->get();

        // Comprehensive Income Breakdown (Today & Total)
        $todayRoi = $user->transactions()->where('type', 'daily_roi')->whereDate('created_at', now())->sum('amount');
        $totalRoiIncome = $user->transactions()->where('type', 'daily_roi')->sum('amount');

        $todayDirect = $user->transactions()->where('type', 'direct_bonus')->whereDate('created_at', now())->sum('amount');
        $totalDirectIncome = $user->transactions()->where('type', 'direct_bonus')->sum('amount');

        $todaySubLevel = $user->transactions()->whereIn('type', ['subscription_level', 'level_income'])->whereDate('created_at', now())->sum('amount');
        $totalSubLevelIncome = $user->transactions()->whereIn('type', ['subscription_level', 'level_income'])->sum('amount');

        $todayLevelRoi = $user->transactions()->whereIn('type', ['level_roi', 'roi_level_income'])->whereDate('created_at', now())->sum('amount');
        $totalLevelRoiIncome = $user->transactions()->whereIn('type', ['level_roi', 'roi_level_income'])->sum('amount');

        $todayDirectReward = $user->transactions()->where('type', 'direct_reward')->whereDate('created_at', now())->sum('amount');
        $totalDirectReward = $user->transactions()->where('type', 'direct_reward')->sum('amount');

        $todayTeamReward = $user->transactions()->where('type', 'team_reward')->whereDate('created_at', now())->sum('amount');
        $totalTeamReward = $user->transactions()->where('type', 'team_reward')->sum('amount');

        $totalIncomeEarned = $totalRoiIncome + $totalDirectIncome + $totalSubLevelIncome + $totalLevelRoiIncome + $totalDirectReward + $totalTeamReward;

        return view('user.dashboard', compact(
            'user',
            'directMembersCount',
            'activeDirectMembersCount',
            'inactiveDirectMembersCount',
            'recentDirects',
            'totalTeamCount',
            'totalActiveTeamCount',
            'totalInactiveTeamCount',
            'totalTeamBusiness',
            'activeNetworkRatio',
            'totalInvested',
            'totalWithdrawals',
            'activeInvestments',
            'recentTransactions',
            'todayRoi',
            'totalRoiIncome',
            'todayDirect',
            'totalDirectIncome',
            'todaySubLevel',
            'totalSubLevelIncome',
            'todayLevelRoi',
            'totalLevelRoiIncome',
            'todayDirectReward',
            'totalDirectReward',
            'todayTeamReward',
            'totalTeamReward',
            'totalIncomeEarned'
        ));
    }
}
