<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserInvestment;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvestmentAdminController extends Controller
{
    public function investments()
    {
        $investments = UserInvestment::with('user')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $totalActiveInvestments = UserInvestment::where('status', 'active')->sum('amount');
        $totalReturned = UserInvestment::sum('total_returned');

        return view('admin.investment.index', compact('investments', 'totalActiveInvestments', 'totalReturned'));
    }

    public function triggerDailyRoi()
    {
        Artisan::call('zivo:process-daily-roi');
        $output = Artisan::output();

        return back()->with('success', 'Daily ROI & 15-Level ROI Level Income processing triggered successfully! '.$output);
    }

    public function withdrawals(Request $request)
    {
        $query = Withdrawal::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('account_details', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->orderBy('id', 'desc')->paginate(20);

        $stats = [
            'total_requests' => Withdrawal::count(),
            'pending_count' => Withdrawal::where('status', 'pending')->count(),
            'pending_amount' => (float) Withdrawal::where('status', 'pending')->sum('amount'),
            'approved_amount' => (float) Withdrawal::where('status', 'approved')->sum('final_amount'),
            'rejected_count' => Withdrawal::where('status', 'rejected')->count(),
        ];

        return view('admin.withdrawal.index', compact('withdrawals', 'stats'));
    }

    public function updateWithdrawalStatus(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['status' => 'This withdrawal request has already been processed and cannot be modified.']);
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->update([
                'status' => $request->status,
                'admin_remarks' => $request->admin_remarks,
                'processed_at' => now(),
            ]);

            // If rejected, refund full amount back to user's earning wallet
            if ($request->status === 'rejected') {
                $withdrawal->user->increment('earning_wallet', $withdrawal->amount);

                $reasonNote = $request->admin_remarks ? " Reason: {$request->admin_remarks}" : '';

                Transaction::create([
                    'user_id' => $withdrawal->user_id,
                    'amount' => $withdrawal->amount,
                    'wallet_type' => 'earning_wallet',
                    'type' => 'admin_credit',
                    'description' => "Withdrawal Request #{$withdrawal->trx_id} Rejected - Balance Refunded.{$reasonNote}",
                    'trx_id' => 'REF-'.strtoupper(Str::random(8)),
                ]);
            }
        });

        $statusLabel = strtoupper($request->status);

        return back()->with('success', "Withdrawal request #{$withdrawal->trx_id} marked as {$statusLabel} successfully.");
    }
}
