<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RoiIncomeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(RoiIncomeService $roiIncomeService): View
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
        $reportThrough = Carbon::now()->endOfDay();
        $duplicateFundRoiIds = $roiIncomeService->duplicateFundRoiTransactionIds([(int) $user->id], $reportThrough);
        $duplicateCommissionIds = $roiIncomeService->duplicateRoiLevelCommissionTransactionIds($reportThrough, (int) $user->id);
        $recentTransactionsQuery = $user->transactions();
        if ($duplicateFundRoiIds !== []) {
            $recentTransactionsQuery->whereNotIn('id', $duplicateFundRoiIds);
        }
        if ($duplicateCommissionIds !== []) {
            $recentTransactionsQuery->whereNotIn('id', $duplicateCommissionIds);
        }
        $recentTransactions = $recentTransactionsQuery->latest()->take(5)->get();

        // Comprehensive Income Breakdown (Today & Total)
        $todayFundRoi = $user->transactions()
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'FWPROFIT%')
                    ->orWhere('description', 'like', '%Fund Wallet%');
            })
            ->when($duplicateFundRoiIds !== [], fn ($query) => $query->whereNotIn('id', $duplicateFundRoiIds))
            ->whereDate('created_at', now())
            ->sum('amount');

        $totalFundRoi = $user->transactions()
            ->where('type', 'daily_roi')
            ->where(function ($q) {
                $q->where('trx_id', 'like', 'FWPROFIT%')
                    ->orWhere('description', 'like', '%Fund Wallet%');
            })
            ->when($duplicateFundRoiIds !== [], fn ($query) => $query->whereNotIn('id', $duplicateFundRoiIds))
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

        $todayLevelRoi = $user->transactions()
            ->whereIn('type', ['level_roi', 'roi_level_income'])
            ->when($duplicateCommissionIds !== [], fn ($query) => $query->whereNotIn('id', $duplicateCommissionIds))
            ->whereDate('created_at', now())
            ->sum('amount');
        $totalLevelRoiQuery = $user->transactions()->whereIn('type', ['level_roi', 'roi_level_income']);
        if ($duplicateCommissionIds !== []) {
            $totalLevelRoiQuery->whereNotIn('id', $duplicateCommissionIds);
        }
        $totalLevelRoiIncome = $totalLevelRoiQuery->sum('amount');

        $todayTransfer = $user->transactions()->where('type', 'wallet_transfer')->whereDate('created_at', now())->sum('amount');
        $totalTransfer = $user->transactions()->where('type', 'wallet_transfer')->sum('amount');

        $totalIncomeEarned = $totalRoiIncome + $totalLevelRoiIncome;

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
            'todayLevelRoi',
            'totalLevelRoiIncome',
            'todayTransfer',
            'totalTransfer',
            'totalIncomeEarned'
        ));
    }
}
