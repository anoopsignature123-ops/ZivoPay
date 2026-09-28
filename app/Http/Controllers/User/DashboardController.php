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
        $todayFundRoi = $user->transactions()
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'FWPROFIT%')
                    ->orWhere('description', 'like', '%Fund Wallet%');
            })
            ->whereDate('created_at', now())
            ->sum('amount');

        $totalFundRoi = $user->transactions()
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'FWPROFIT%')
                    ->orWhere('description', 'like', '%Fund Wallet%');
            })
            ->sum('amount');

        $todayInvestmentRoi = $user->transactions()
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'DROI%')
                    ->orWhere('trx_id', 'like', 'ROI%')
                    ->orWhere('description', 'like', '%Capital%');
            })
            ->whereDate('created_at', now())
            ->sum('amount');

        $totalInvestmentRoi = $user->transactions()
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'DROI%')
                    ->orWhere('trx_id', 'like', 'ROI%')
                    ->orWhere('description', 'like', '%Capital%');
            })
            ->sum('amount');

        $todayRoi = $todayFundRoi + $todayInvestmentRoi;
        $totalRoiIncome = $totalFundRoi + $totalInvestmentRoi;

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

        $todayTransfer = $user->transactions()->where('type', 'wallet_transfer')->whereDate('created_at', now())->sum('amount');
        $totalTransfer = $user->transactions()->where('type', 'wallet_transfer')->sum('amount');

        $totalIncomeEarned = $totalRoiIncome + $totalDirectIncome + $totalSubLevelIncome + $totalLevelRoiIncome + $totalDirectReward + $totalTeamReward;

        // Sponsor Info
        $sponsor = User::where('referral_code', $user->sponsor_code)->first();

        // Pending Withdrawals Stats
        $pendingWithdrawalsAmount = $user->withdrawals()->where('status', 'pending')->sum('amount');
        $pendingWithdrawalsCount = $user->withdrawals()->where('status', 'pending')->count();

        return view('user.dashboard', compact(
            'user',
            'sponsor',
            'pendingWithdrawalsAmount',
            'pendingWithdrawalsCount',
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
            'todayFundRoi',
            'totalFundRoi',
            'todayInvestmentRoi',
            'totalInvestmentRoi',
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
            'todayTransfer',
            'totalTransfer',
            'totalIncomeEarned'
        ));
    }
}
