<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Withdrawal::query()->where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($withdrawalQuery) use ($search): void {
                $withdrawalQuery->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('account_details', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $withdrawals = $query->latest('id')->paginate(15)->withQueryString();
        $stats = [
            'total_approved' => (float) Withdrawal::query()->where('user_id', $user->id)->where('status', 'approved')->sum('final_amount'),
            'total_pending' => (float) Withdrawal::query()->where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'total_rejected' => (float) Withdrawal::query()->where('user_id', $user->id)->where('status', 'rejected')->sum('amount'),
            'total_count' => Withdrawal::query()->where('user_id', $user->id)->count(),
        ];

        return view('user.withdrawal.index', [
            'user' => $user,
            'withdrawals' => $withdrawals,
            'stats' => $stats,
            'payoutEnabled' => true,
            'conversionRate' => (float) config('gateway.usd_to_inr_rate', 100),
            'contractAddress' => (string) config('services.usdt.contract_address', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'usdt_address' => trim((string) $request->input('usdt_address')),
        ]);

        $validated = $request->validate([
            'from_wallet' => ['required', 'string', 'in:earning_wallet,deposit_wallet'],
            'amount' => ['required', 'numeric', 'min:5', 'decimal:0,2'],
            'usdt_address' => ['bail', 'required', 'string', 'regex:/\A0x[a-fA-F0-9]{40}\z/'],
        ], [
            'amount.min' => 'Minimum withdrawal request is ₹5.00.',
            'usdt_address.regex' => 'Enter a valid BEP20 address: 0x followed by exactly 40 hexadecimal characters.',
        ]);

        if (! $request->user()->isKycApproved()) {
            throw ValidationException::withMessages(['payout' => 'Complete KYC verification before requesting a withdrawal.']);
        }

        $rate = (float) config('gateway.usd_to_inr_rate', 100);
        if ($rate <= 0) {
            throw ValidationException::withMessages(['amount' => 'USDT conversion rate is not configured correctly.']);
        }

        $grossInr = round((float) $validated['amount'], 2);
        $feeInr = round($grossInr * 0.05, 2);
        $netInr = round($grossInr - $feeInr, 2);
        $usdtAmount = round($netInr / $rate, 6);
        $sourceWallet = $validated['from_wallet'];

        if ($usdtAmount <= 0) {
            throw ValidationException::withMessages(['amount' => 'The requested amount is too small to convert to USDT.']);
        }

        DB::transaction(function () use ($request, $grossInr, $feeInr, $netInr, $usdtAmount, $rate, $sourceWallet, $validated): void {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);
            $balance = (float) $user->{$sourceWallet};

            if ($balance < $grossInr) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient wallet balance. Available: ₹'.number_format($balance, 2).'.',
                ]);
            }

            $user->decrement($sourceWallet, $grossInr);
            $user->refresh();

            $trxId = 'WTH-'.strtoupper(Str::random(10));
            Withdrawal::query()->create([
                'user_id' => $user->id,
                'source_wallet' => $sourceWallet,
                'amount' => $grossInr,
                'charge' => $feeInr,
                'final_amount' => $netInr,
                'amount_inr' => $netInr,
                'conversion_rate' => $rate,
                'usdt_amount' => $usdtAmount,
                'payment_method' => 'USDT BEP20',
                'account_details' => $validated['usdt_address'],
                'trx_id' => $trxId,
                'status' => 'pending',
            ]);

            Transaction::query()->create([
                'user_id' => $user->id,
                'trx_id' => $trxId,
                'type' => 'withdrawal_request',
                'wallet_type' => $sourceWallet === 'deposit_wallet' ? 'fund' : 'earning',
                'trx_type' => '-',
                'amount' => $grossInr,
                'charge' => $feeInr,
                'post_balance' => $user->{$sourceWallet},
                'description' => 'USDT BEP20 withdrawal request reserved. Net payout: '.number_format($usdtAmount, 6).' USDT.',
            ]);
        });

        return back()->with('success', 'Withdrawal request submitted. INR has been reserved and the USDT payout is awaiting admin review.');
    }
}
