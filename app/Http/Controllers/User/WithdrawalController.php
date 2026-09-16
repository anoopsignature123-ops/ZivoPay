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

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->orderBy('id', 'desc')->paginate(15);

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
            'amount' => 'required|numeric|min:500',
            'payment_method' => 'required|string',
            'account_details' => 'required|string|min:5',
        ], [
            'amount.min' => 'Minimum 24x7 withdrawal amount is ₹500.',
            'account_details.min' => 'Please provide complete account/wallet details (at least 5 characters).',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;

        if ($user->earning_wallet < $amount) {
            return back()->withErrors(['amount' => 'Insufficient Earning Wallet balance. Available: ₹'.number_format($user->earning_wallet, 2)])->withInput();
        }

        // 5% Admin Processing Charge Calculation
        $chargePercent = 5.0;
        $charge = round(($amount * $chargePercent) / 100, 2);
        $finalAmount = round($amount - $charge, 2);

        DB::transaction(function () use ($user, $amount, $charge, $finalAmount, $request) {
            // Deduct earning wallet
            $user->decrement('earning_wallet', $amount);

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
                'wallet_type' => 'earning_wallet',
                'type' => 'withdrawal',
                'description' => "24x7 Withdrawal Request via {$request->payment_method} (Gross: ₹".number_format($amount, 2).', Fee: ₹'.number_format($charge, 2).', Payable: ₹'.number_format($finalAmount, 2).')',
                'trx_id' => $trxId,
            ]);
        });

        return redirect()->route('user.withdrawal.index')->with('success', '24x7 Withdrawal request # submitted! Gross: ₹'.number_format($amount, 2).' | Net Payable: ₹'.number_format($finalAmount, 2));
    }
}
