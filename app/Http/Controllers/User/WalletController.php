<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WalletTransfer;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function showBuyPackageForm()
    {
        $user = Auth::user();

        return view('user.package.buy', compact('user'));
    }

    public function showTransferForm()
    {
        $user = Auth::user();
        $transfers = WalletTransfer::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('user.wallet.transfer', compact('user', 'transfers'));
    }

    public function activateSubscription(SubscriptionService $subscriptionService)
    {
        $user = Auth::user();

        if ($user->is_subscription_active) {
            return back()->with('info', 'Your Zivo Family Kit (₹3,000) User Account Subscription is already active!');
        }

        if ($user->deposit_wallet < 3000.00) {
            return back()->withErrors(['subscription' => 'Insufficient Fund Wallet balance. Please add at least ₹3,000 to your Fund Wallet to activate your account. Available: ₹'.number_format($user->deposit_wallet, 2)]);
        }

        $success = $subscriptionService->activateSubscription($user);

        if ($success) {
            return back()->with('success', 'Congratulations! Your Zivo Family Kit (₹3,000) Account Subscription has been activated successfully!');
        }

        return back()->withErrors(['subscription' => 'Failed to activate subscription. Please try again.']);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
        ], [
            'amount.min' => 'Minimum transfer amount is ₹10.',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;

        if ($user->earning_wallet < $amount) {
            return back()->withErrors(['amount' => 'Insufficient Earning Wallet balance. Available: ₹'.number_format($user->earning_wallet, 2)]);
        }

        // Perform instant transfer: Earning Wallet -> Fund Wallet
        $user->earning_wallet -= $amount;
        $user->deposit_wallet += $amount;
        $user->save();

        $trxId = 'TRF-'.strtoupper(Str::random(10));

        WalletTransfer::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'from_wallet' => 'earning_wallet',
            'to_wallet' => 'deposit_wallet',
            'trx_id' => $trxId,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'wallet_type' => 'earning',
            'type' => 'wallet_transfer',
            'trx_type' => '-',
            'description' => 'Internal Wallet Transfer: Earning Wallet -> Fund Wallet',
            'trx_id' => $trxId,
        ]);

        return back()->with('success', 'Successfully transferred ₹'.number_format($amount, 2).' from Earning Wallet to Fund Wallet!');
    }
}
