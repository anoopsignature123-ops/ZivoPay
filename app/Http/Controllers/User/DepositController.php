<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\User\DepositService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DepositController extends Controller
{
    /**
     * Display Add Fund / Deposit page for User.
     */
    public function index(): View
    {
        $user = Auth::user();
        $deposits = Deposit::where('user_id', $user->id)->latest()->paginate(10);
        $usdtWalletAddress = config('services.usdt.wallet_address', env('USDT_WALLET_ADDRESS', '0x71C7656EC7ab88b098defB751B7401B5f6d8976F'));

        return view('user.deposits.index', compact('user', 'deposits', 'usdtWalletAddress'));
    }

    /**
     * Store new Deposit Request & initialize gateway payment session.
     */
    public function store(Request $request, DepositService $depositService): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'max:50000'],
        ], [
            'amount.required' => 'Please enter a valid deposit amount.',
            'amount.numeric' => 'Deposit amount must be a valid number.',
            'amount.min' => 'Minimum deposit amount is $10.00 USDT.',
            'amount.max' => 'Maximum single deposit amount is $50,000.00 USDT.',
        ]);

        $result = $depositService->createCustomFund(Auth::user(), (float) $request->amount, 'USDT (BEP20)');

        if (! $result['success']) {
            return back()
                ->withErrors(['amount' => $result['message']])
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->route('user.deposits.payment', $result['deposit']->id);
    }

    /**
     * Display payment checkout page & trigger instant verify check.
     */
    public function show(Deposit $deposit, DepositService $depositService): View|RedirectResponse
    {
        abort_if($deposit->user_id !== Auth::id(), 403);

        if ($deposit->status === 'approved') {
            return redirect()->route('user.deposits.history')->with('success', 'Payment already verified & credited.');
        }

        $depositService->verifyAndProcessDeposit($deposit);
        $deposit->refresh();

        return view('user.deposits.payment', compact('deposit'));
    }

    /**
     * Dedicated payment checkout view.
     */
    public function paymentView(Deposit $deposit): View
    {
        abort_unless($deposit->user_id === Auth::id(), 403);

        return view('user.deposits.payment', compact('deposit'));
    }

    /**
     * AJAX Live Polling Check Status Endpoint.
     */
    public function checkStatus(Deposit $deposit, DepositService $depositService): JsonResponse
    {
        abort_unless($deposit->user_id === Auth::id(), 403);

        if ($deposit->status === 'approved') {
            return response()->json(['status' => 'success', 'message' => 'Payment confirmed!']);
        }

        $isPaid = $depositService->verifyAndProcessDeposit($deposit);
        $deposit->refresh();

        if ($deposit->status === 'approved' || $isPaid) {
            return response()->json(['status' => 'success', 'message' => 'Payment confirmed successfully!']);
        }

        return response()->json(['status' => 'pending', 'message' => 'Waiting for payment confirmation...']);
    }

    /**
     * Display dedicated My Deposit History page with date range & status filters.
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $query = Deposit::with(['user', 'transaction'])->where('user_id', $user->id);

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('txn_hash', 'like', "%{$search}%")
                    ->orWhere('deposit_ref', 'like', "%{$search}%");
            });
        }

        $deposits = $query->latest()->paginate(15)->withQueryString();

        return view('user.deposits.history', compact('user', 'deposits'));
    }
}
