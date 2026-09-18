<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Withdrawal::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('withdraw_ref', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('account_details', 'like', "%{$search}%");
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

        $withdrawals = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total_approved' => (float) Withdrawal::where('user_id', $user->id)->where('status', 'approved')->sum('final_amount'),
            'total_pending' => (float) Withdrawal::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'total_rejected' => (float) Withdrawal::where('user_id', $user->id)->where('status', 'rejected')->sum('amount'),
            'total_count' => Withdrawal::where('user_id', $user->id)->count(),
        ];

        return view('user.withdrawal.index', compact('user', 'withdrawals', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_wallet' => 'nullable|string|in:earning_wallet,deposit_wallet',
            'amount' => 'required|numeric|min:500',
            'payment_method' => 'required|string',
            'account_details' => 'required|string|min:5',
        ], [
            'amount.min' => 'Minimum 24x7 withdrawal amount is ₹500.',
            'account_details.min' => 'Please provide complete account/wallet details (at least 5 characters).',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;
        $fromWallet = $request->input('from_wallet', 'earning_wallet');
        if (! in_array($fromWallet, ['earning_wallet', 'deposit_wallet'])) {
            $fromWallet = 'earning_wallet';
        }

        $walletName = ($fromWallet === 'deposit_wallet') ? 'Fund Wallet' : 'Earning Wallet';
        $availableBalance = ($fromWallet === 'deposit_wallet') ? (float) $user->deposit_wallet : (float) $user->earning_wallet;

        if ($availableBalance < $amount) {
            return back()->withErrors(['amount' => "Insufficient {$walletName} balance. Available: ₹".number_format($availableBalance, 2)])->withInput();
        }

        // 5% Admin Processing Charge Calculation
        $chargePercent = 5.0;
        $charge = round(($amount * $chargePercent) / 100, 2);
        $finalAmount = round($amount - $charge, 2);

        DB::transaction(function () use ($user, $amount, $charge, $finalAmount, $request, $fromWallet, $walletName) {
            // Deduct selected wallet
            if ($fromWallet === 'deposit_wallet') {
                $user->decrement('deposit_wallet', $amount);
            } else {
                $user->decrement('earning_wallet', $amount);
            }

            $trxId = 'WTH-'.strtoupper(Str::random(10));

            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'charge' => $charge,
                'final_amount' => $finalAmount,
                'payment_method' => $request->payment_method,
                'account_details' => $request->account_details,
                'trx_id' => $trxId,
                'status' => 'pending',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => $fromWallet === 'deposit_wallet' ? 'fund' : 'earning_wallet',
                'type' => 'withdrawal',
                'description' => "24x7 Withdrawal Request from {$walletName} via {$request->payment_method} (Gross: ₹".number_format($amount, 2).', Fee: ₹'.number_format($charge, 2).', Payable: ₹'.number_format($finalAmount, 2).')',
                'trx_id' => $trxId,
            ]);
        });

        return redirect()->route('user.withdrawal.index')->with('success', "24x7 Withdrawal request submitted from {$walletName}! Gross: ₹".number_format($amount, 2).' | Net Payable: ₹'.number_format($finalAmount, 2));
    }
}
