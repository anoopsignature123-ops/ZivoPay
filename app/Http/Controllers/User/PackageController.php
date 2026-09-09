<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPackage;
use App\Services\Incomes\DirectIncomeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Display Available Dex Trade Packages for Purchase.
     */
    public function index(): View
    {
        $user = Auth::user();
        $packages = Package::where('status', 'active')->orderBy('id', 'asc')->get();

        $userActivePackages = UserPackage::where('user_id', $user->id)
            ->where('status', 'active')
            ->selectRaw('package_id, SUM(invested_amount) as total_invested, COUNT(id) as active_count, MAX(expires_at) as max_expires_at')
            ->groupBy('package_id')
            ->get()
            ->keyBy('package_id');

        return view('user.packages.index', compact('user', 'packages', 'userActivePackages'));
    }

    /**
     * Buy / Invest in a Package using Deposit Wallet balance.
     */
    public function buy(Request $request): RedirectResponse
    {
        $request->validate([
            'invested_amount' => 'required|numeric|min:10',
        ]);

        $investedAmount = (float) $request->invested_amount;

        // Enforce Multiple of $10 Rule (Dex Trade PDF Slide 8)
        if (fmod($investedAmount, 10.0) != 0) {
            return redirect()->back()->with('error', 'Investment amount must be an exact multiple of $10 (e.g., $10, $20, $30, $100, $500).');
        }

        $user = Auth::user();

        $package = Package::where('status', 'active')->first();
        if (! $package) {
            return redirect()->back()->with('error', 'No active investment package is currently available.');
        }

        // Check Deposit Wallet Balance
        if ((float) $user->deposit_wallet < $investedAmount) {
            return redirect()->route('user.deposits.index')->with('error', "Insufficient Deposit Wallet Balance (\${$user->deposit_wallet}). Please add funds first to invest \${$investedAmount}!");
        }

        // Perform Transaction: Deduct Deposit Wallet, Create UserPackage, Activate User Account
        DB::transaction(function () use ($user, $package, $investedAmount) {
            // Deduct Deposit Wallet
            $user->decrement('deposit_wallet', $investedAmount);

            // Activate User
            $user->update([
                'status' => 'active',
                'activated_at' => $user->activated_at ?? now(),
            ]);

            // Calculate ROI amounts (0.5% Daily, 2X Total Return)
            $dailyRoiAmount = ($investedAmount * 0.50) / 100;
            $totalReturnAmount = $investedAmount * 2.00;

            $userPackage = UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'invested_amount' => $investedAmount,
                'daily_roi' => 0.50,
                'daily_roi_amount' => $dailyRoiAmount,
                'duration_days' => 400,
                'total_return_amount' => $totalReturnAmount,
                'paid_roi_amount' => 0.00,
                'status' => 'active',
                'purchased_at' => now(),
                'expires_at' => now()->addDays(400),
            ]);

            // Log detailed financial transaction
            Transaction::create([
                'user_id' => $user->id,
                'txn_number' => 'TXN-'.rand(10000000, 99999999),
                'wallet_type' => 'deposit_wallet',
                'amount' => $investedAmount,
                'charge' => 0.00,
                'post_balance' => $user->fresh()->deposit_wallet,
                'trx_type' => '-',
                'type' => 'package_purchase',
                'description' => 'Invested $'.number_format($investedAmount, 2).' in Dex Trade Package via Deposit Wallet',
                'reference_id' => $userPackage->id,
                'status' => 'completed',
            ]);

            // Distribute 10% Direct Referral Commission to Sponsor
            app(DirectIncomeService::class)->distributeDirectCommission($user, $userPackage, $investedAmount);
        });

        return redirect()->route('user.packages.history')->with('success', 'Congratulations! You have successfully invested $'.number_format($investedAmount, 2).' in Dex Trade! 0.5% Daily ROI activated.');
    }

    /**
     * View User's Purchased Packages History.
     */
    public function history(): View
    {
        $user = Auth::user();
        $userPackages = UserPackage::with('package')->where('user_id', $user->id)->latest()->paginate(10);

        return view('user.packages.history', compact('user', 'userPackages'));
    }
}
