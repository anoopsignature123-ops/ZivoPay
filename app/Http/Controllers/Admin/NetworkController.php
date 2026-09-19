<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NetworkController extends Controller
{
    /**
     * Display listing of direct members across network in Admin Panel.
     */
    public function directMembers(Request $request): View
    {
        $query = User::with(['sponsor'])->whereNotNull('sponsor_code');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%")
                    ->orWhere('sponsor_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $directs = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::whereNotNull('sponsor_code')->count(),
            'active' => User::whereNotNull('sponsor_code')->where('status', 'active')->count(),
            'inactive' => User::whereNotNull('sponsor_code')->where('status', 'inactive')->count(),
        ];

        return view('admin.network.direct', compact('directs', 'stats'));
    }

    /**
     * Display Visual Team Tree in Admin Panel.
     */
    public function treeView(Request $request): View
    {
        $searchCode = trim((string) ($request->query('code') ?? $request->query('search') ?? $request->query('query') ?? $request->query('user_id') ?? ''));

        if (! empty($searchCode)) {
            $rootUser = User::where('referral_code', $searchCode)
                ->orWhere('email', $searchCode)
                ->orWhere('mobile', $searchCode)
                ->orWhere('name', 'like', "%{$searchCode}%")
                ->first();
        }

        if (! isset($rootUser) || ! $rootUser) {
            $rootUser = User::where('role_id', 2)->whereNull('sponsor_code')->first() ?? User::where('role_id', 2)->first();
        }

        if (! $rootUser) {
            $rootUser = new User(['name' => 'No Users Yet', 'referral_code' => 'ZIVO-0000000']);
        }

        $rootUser->load(['sponsor', 'directs']);
        $directMembers = $rootUser->exists ? User::where('sponsor_code', $rootUser->referral_code)->with('directs')->get() : collect();

        $treeData = [
            'root' => $rootUser,
            'direct_members' => $directMembers,
            'total_directs' => $directMembers->count(),
            'active_directs' => $directMembers->where('status', 'active')->count(),
            'total_team_count' => $rootUser->exists ? max(0, count($rootUser->getBranchUserIds()) - 1) : 0,
        ];

        return view('admin.network.tree', compact('rootUser', 'treeData', 'directMembers'));
    }
}
