<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
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
            'exchangeRate' => (float) config('gateway.usd_to_inr_rate', 100),
            'payoutEnabled' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return back()->withErrors([
            'payout' => 'Automated bank payouts are paused until the official iPaymentWallet IMPS API contract is confirmed and configured.',
        ])->withInput();
    }
}
