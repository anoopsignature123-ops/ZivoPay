<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use App\Services\A1TopupService;
use App\Services\RechargeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RechargeAdminController extends Controller
{
    /**
     * Display Admin Recharge Ledger & Live API Provider Balance.
     */
    public function index(Request $request, A1TopupService $a1topupService): View
    {
        $query = Recharge::with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('operator_code', 'like', "%{$search}%")
                    ->orWhere('operator_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'success', 'failed', 'refunded'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $recharges = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Fetch Live Gateway Balance
        $providerBalance = $a1topupService->checkBalance();

        $stats = [
            'total_volume' => (float) Recharge::where('status', 'success')->sum('amount'),
            'total_success_count' => Recharge::where('status', 'success')->count(),
            'total_failed_count' => Recharge::where('status', 'failed')->count(),
            'total_pending_count' => Recharge::where('status', 'pending')->count(),
            'wallet_topup' => (float) Transaction::where('type', 'deposit')->sum('amount'),
            'opening_balance' => (float) User::sum('deposit_wallet'),
            'recharge_debit' => (float) Recharge::whereIn('status', ['success', 'pending'])->sum('amount'),
            'refund_credit' => (float) Recharge::where('status', 'refunded')->sum('amount'),
            'profit' => (float) (Recharge::where('status', 'success')->sum('amount') * 0.025), // 2.5% Commission Margin
        ];

        return view('admin.recharge.index', compact('recharges', 'providerBalance', 'stats'));
    }

    /**
     * Admin Sync Order Status with A1Topup Gateway.
     */
    public function sync(Recharge $recharge, RechargeService $rechargeService): RedirectResponse
    {
        $updatedRecharge = $rechargeService->syncOrderStatus($recharge);

        return redirect()->back()->with('success', "Order #{$updatedRecharge->order_id} status synced: ".ucfirst($updatedRecharge->status));
    }

    /**
     * Admin Force Refund stuck pending order to user's Fund Wallet.
     */
    public function refund(Recharge $recharge, RechargeService $rechargeService): RedirectResponse
    {
        $refunded = $rechargeService->refundRecharge($recharge, 'Manual Force Refund by Admin');

        if ($refunded) {
            return redirect()->back()->with('success', "Success! ₹{$recharge->amount} has been refunded to User's Fund Wallet for Order #{$recharge->order_id}.");
        }

        return redirect()->back()->with('error', "Order #{$recharge->order_id} could not be refunded or is already refunded.");
    }
}
