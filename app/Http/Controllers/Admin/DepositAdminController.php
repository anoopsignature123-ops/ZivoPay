<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositAdminController extends Controller
{
    /**
     * Display all User Add Fund / Deposit requests in Admin Panel.
     */
    public function index(Request $request): View
    {
        $query = Deposit::with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('deposit_ref', 'like', "%{$search}%")
                    ->orWhere('trx_hash', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $deposits = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total_approved' => (float) Deposit::where('status', 'approved')->sum('final_amount'),
            'total_pending' => (float) Deposit::where('status', 'pending')->sum('amount'),
            'total_rejected' => (float) Deposit::where('status', 'rejected')->sum('amount'),
            'total_count' => Deposit::count(),
        ];

        return view('admin.deposits.index', compact('deposits', 'stats'));
    }

    /**
     * Approve pending deposit and credit user's Fund Wallet.
     */
    public function approve(Request $request, Deposit $deposit, DepositService $depositService): RedirectResponse
    {
        $remark = $request->input('admin_remark', 'Approved by Admin & credited to Fund Wallet.');
        $success = $depositService->approveDeposit($deposit, $remark);

        if ($success) {
            return redirect()->back()->with('success', "Deposit #{$deposit->deposit_ref} approved! ₹".number_format($deposit->final_amount, 2)." credited to {$deposit->user->name}'s Fund Wallet.");
        }

        return redirect()->back()->with('error', "Deposit #{$deposit->deposit_ref} could not be approved or is already processed.");
    }

    /**
     * Reject pending deposit.
     */
    public function reject(Request $request, Deposit $deposit, DepositService $depositService): RedirectResponse
    {
        $remark = $request->input('admin_remark', 'Deposit request rejected by Admin.');
        $success = $depositService->rejectDeposit($deposit, $remark);

        if ($success) {
            return redirect()->back()->with('success', "Deposit #{$deposit->deposit_ref} has been rejected.");
        }

        return redirect()->back()->with('error', "Deposit #{$deposit->deposit_ref} could not be rejected.");
    }
}
