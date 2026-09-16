<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserInvestment;
use App\Services\MLMIncomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $investments = UserInvestment::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $packages = Package::where('status', 'active')->orderBy('min_amount', 'asc')->get();
        $rewardTiers = MLMIncomeService::getRewardTiers();

        return view('user.investment.plan', compact('user', 'investments', 'packages', 'rewardTiers'));
    }

    public function store(Request $request, MLMIncomeService $incomeService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ], [
            'amount.min' => 'Minimum investment amount is ₹1,000.',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;

        // Check if user has sufficient deposit wallet
        if ($user->deposit_wallet < $amount) {
            return back()->withErrors(['amount' => 'Insufficient Deposit Wallet balance. Please add funds to your wallet first. Required: ₹'.number_format($amount, 2).', Available: ₹'.number_format($user->deposit_wallet, 2)]);
        }

        $investment = $incomeService->createInvestment($user, $amount, 'deposit_wallet');

        return redirect()->route('user.investment.index')->with('success', "Congratulations! Your {$investment->plan_name} for ₹".number_format($amount, 2).' has been activated successfully!');
    }
}
