<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DepositController extends Controller
{
    /**
     * Display User Add Fund / Deposit Portal & Recent History.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Deposit::where('user_id', $user->id);

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $deposits = $query->orderBy('id', 'desc')->paginate(15);

        $stats = [
            'total_approved' => (float) Deposit::where('user_id', $user->id)->where('status', 'approved')->sum('final_amount'),
            'total_pending' => (float) Deposit::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'total_rejected' => (float) Deposit::where('user_id', $user->id)->where('status', 'rejected')->sum('amount'),
            'total_count' => Deposit::where('user_id', $user->id)->count(),
        ];

        return view('user.deposit.index', compact('user', 'deposits', 'stats'));
    }

    /**
     * Store new User Add Fund / Deposit request.
     */
    public function store(Request $request, DepositService $depositService): RedirectResponse
    {
        $minDeposit = config('gateway.min_deposit', 100.00);
        $maxDeposit = config('gateway.max_deposit', 500000.00);

        $request->validate([
            'amount' => "required|numeric|min:{$minDeposit}|max:{$maxDeposit}",
            'payment_method' => 'required|string|in:UPI,USDT,Online Gateway',
            'trx_hash' => 'required|string|min:6|max:100|unique:deposits,trx_hash',
            'proof_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remark' => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Minimum Add Fund amount is ₹'.number_format($minDeposit, 2).'.',
            'amount.max' => 'Maximum Add Fund amount is ₹'.number_format($maxDeposit, 2).'.',
            'trx_hash.required' => 'Please enter the UTR / Transaction Reference Number / Hash.',
            'trx_hash.unique' => 'This UTR / Transaction Reference Number has already been submitted for verification.',
            'proof_file.image' => 'Payment proof must be a valid image file (JPG, PNG, WEBP).',
            'proof_file.max' => 'Payment proof image size must not exceed 5MB.',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;
        $paymentMethod = $request->payment_method;
        $trxHash = trim((string) $request->trx_hash);
        $proofPath = null;

        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('deposit_proofs', 'public');
        }

        $deposit = $depositService->createDeposit(
            user: $user,
            amount: $amount,
            paymentMethod: $paymentMethod,
            trxHash: $trxHash,
            proofPath: $proofPath
        );

        return redirect()->route('user.deposit.index')->with(
            'success',
            'Success! ₹'.number_format($amount, 2)." has been instantly added & credited to your Fund Wallet! (Ref: {$deposit->deposit_ref})"
        );
    }
}
