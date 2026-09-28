<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\P2pTransfer;
use Illuminate\Http\Request;

class P2pAdminController extends Controller
{
    /**
     * Audit log of all P2P Member Transfers across the platform.
     */
    public function index(Request $request)
    {
        $query = P2pTransfer::with(['sender', 'receiver']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('sender', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('receiver', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transfers = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalVolume = (float) P2pTransfer::sum('amount');
        $totalCount = P2pTransfer::count();

        return view('admin.reports.p2p', compact('transfers', 'totalVolume', 'totalCount'));
    }
}
