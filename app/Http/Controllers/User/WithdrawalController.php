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

        if ($request->filled('status') && in_array($request->input('status'), ['pending', 'approved', 'completed', 'rejected'], true)) {
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
            'total_approved' => (float) Withdrawal::query()->where('user_id', $user->id)->where('status', 'completed')->sum('final_amount'),
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
        $validated = $request->validate([
            'from_wallet' => ['required', 'string', 'in:earning_wallet,deposit_wallet'],
            'payout_method' => ['required', 'string', 'in:usdt_bep20,upi,bank'],
            'amount' => ['required', 'numeric', 'min:5', 'decimal:0,2'],
            'usdt_address' => ['required_if:payout_method,usdt_bep20', 'nullable', 'string', 'regex:/\A0x[a-fA-F0-9]{40}\z/'],
            'upi_id' => ['required_if:payout_method,upi', 'nullable', 'string', 'max:100', 'regex:/\A[a-zA-Z0-9._-]{2,256}@[a-zA-Z0-9.-]{2,64}\z/'],
            'account_holder' => ['required_if:payout_method,bank', 'nullable', 'string', 'max:120'],
            'account_number' => ['required_if:payout_method,bank', 'nullable', 'string', 'regex:/\A[0-9]{8,20}\z/'],
            'ifsc' => ['required_if:payout_method,bank', 'nullable', 'string', 'regex:/\A[A-Za-z]{4}0[A-Za-z0-9]{6}\z/'],
        ], [
            'amount.min' => 'Minimum withdrawal request is ₹5.00.',
            'usdt_address.regex' => 'Enter a valid BEP20 address: 0x followed by exactly 40 hexadecimal characters.',
            'upi_id.regex' => 'Enter a valid UPI ID, for example name@bank.',
            'account_number.regex' => 'Enter a valid bank account number (8 to 20 digits).',
            'ifsc.regex' => 'Enter a valid IFSC code.',
        ]);

        if (! $request->user()->isKycApproved()) {
            throw ValidationException::withMessages(['payout' => 'Complete KYC verification before requesting a withdrawal.']);
        }

        $rate = (float) config('gateway.usd_to_inr_rate', 100);
        if ($validated['payout_method'] === 'usdt_bep20' && $rate <= 0) {
            throw ValidationException::withMessages(['amount' => 'USDT conversion rate is not configured correctly.']);
        }

        $grossInr = round((float) $validated['amount'], 2);
        $feeInr = round($grossInr * 0.05, 2);
        $netInr = round($grossInr - $feeInr, 2);
        $usdtAmount = $validated['payout_method'] === 'usdt_bep20' ? round($netInr / $rate, 6) : null;
        $sourceWallet = $validated['from_wallet'];

        if ($usdtAmount !== null && $usdtAmount <= 0) {
            throw ValidationException::withMessages(['amount' => 'The requested amount is too small to convert to USDT.']);
        }

        $paymentMethod = match ($validated['payout_method']) {
            'upi' => 'INR UPI',
            'bank' => 'INR Bank Transfer',
            default => 'USDT BEP20',
        };
        $accountDetails = match ($validated['payout_method']) {
            'upi' => json_encode(['upi_id' => $validated['upi_id']], JSON_THROW_ON_ERROR),
            'bank' => json_encode([
                'account_holder' => $validated['account_holder'],
                'account_number' => $validated['account_number'],
                'ifsc' => strtoupper($validated['ifsc']),
            ], JSON_THROW_ON_ERROR),
            default => trim((string) $validated['usdt_address']),
        };

        DB::transaction(function () use ($request, $grossInr, $feeInr, $netInr, $usdtAmount, $rate, $sourceWallet, $paymentMethod, $accountDetails): void {
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
                'payment_method' => $paymentMethod,
                'account_details' => $accountDetails,
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
                'description' => $paymentMethod.' withdrawal request reserved. Net payout: '.($usdtAmount !== null ? number_format($usdtAmount, 6).' USDT' : '₹'.number_format($netInr, 2)).'.',
            ]);
        });

        return back()->with('success', 'Withdrawal request submitted. Your wallet amount is reserved and the payout is awaiting admin review.');
    }
}
