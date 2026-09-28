<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycAdminController extends Controller
{
    /**
     * List all User KYC submissions with filters and search.
     */
    public function index(Request $request)
    {
        $query = Kyc::with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
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

        $kycs = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_pending' => Kyc::where('status', 'pending')->count(),
            'total_approved' => Kyc::where('status', 'approved')->count(),
            'total_rejected' => Kyc::where('status', 'rejected')->count(),
            'total_count' => Kyc::count(),
        ];

        return view('admin.kyc.index', compact('kycs', 'stats'));
    }

    /**
     * View single KYC detail with document previews.
     */
    public function show(Kyc $kyc)
    {
        $kyc->load('user');

        return view('admin.kyc.show', compact('kyc'));
    }

    /**
     * Approve User KYC.
     */
    public function approve(Kyc $kyc)
    {
        $kyc->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_at' => now(),
        ]);

        $kyc->user->update([
            'kyc_status' => 'approved',
        ]);

        return redirect()->back()->with('success', "KYC approved for member {$kyc->user->name} ({$kyc->user->referral_code}). Withdrawal features are now unlocked.");
    }

    /**
     * Reject User KYC with reason.
     */
    public function reject(Request $request, Kyc $kyc)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:3|max:500',
        ], [
            'rejection_reason.required' => 'Please provide a clear reason for rejecting the KYC submission.',
        ]);

        $reason = trim((string) $request->rejection_reason);

        $kyc->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
        ]);

        $kyc->user->update([
            'kyc_status' => 'rejected',
        ]);

        return redirect()->back()->with('success', "KYC submission rejected for member {$kyc->user->name} ({$kyc->user->referral_code}).");
    }
}
