<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NetworkController extends Controller
{
    /**
     * Direct Members Directory with Filtering & Stats.
     */
    public function directMembers(Request $request): View
    {
        $user = Auth::user();

        $query = User::where('sponsor_code', $user->referral_code);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = strtolower((string) $request->input('status'));
            if (in_array($status, ['active', 'inactive'])) {
                $query->where('status', $status);
            }
        }

        $directs = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $allDirects = User::where('sponsor_code', $user->referral_code)->get();

        $stats = [
            'total' => $allDirects->count(),
            'active' => $allDirects->where('status', 'active')->count(),
            'inactive' => $allDirects->where('status', 'inactive')->count(),
        ];

        return view('user.network.direct', compact('directs', 'stats'));
    }

    /**
     * Direct Network Unilevel Downline Tree View.
     */
    public function treeView(Request $request): View
    {
        $currentUser = Auth::user();
        $code = trim((string) ($request->input('code') ?? $request->input('search') ?? $request->input('query') ?? ''));

        if (! empty($code)) {
            $targetUser = User::where(function ($q) use ($code) {
                $q->where('referral_code', $code)
                    ->orWhere('email', $code)
                    ->orWhere('mobile', $code)
                    ->orWhere('name', 'like', "%{$code}%");
            })->first();

            if ($targetUser && ($targetUser->id === $currentUser->id || in_array($targetUser->id, $currentUser->getBranchUserIds(), true))) {
                $root = $targetUser;
            } else {
                return redirect()->route('user.network.tree')->with('error', 'Member not found in your downline tree.');
            }
        } else {
            $root = $currentUser;
        }

        $root->load(['sponsor', 'directs']);

        $directMembers = User::where('sponsor_code', $root->referral_code)->with('directs')->get();

        $treeData = [
            'root' => $root,
            'direct_members' => $directMembers,
            'total_directs' => $directMembers->count(),
            'active_directs' => $directMembers->where('status', 'active')->count(),
            'total_team_count' => max(0, count($root->getBranchUserIds()) - 1),
        ];

        return view('user.network.tree', compact('treeData'));
    }
}
