<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NetworkController extends Controller
{
    /**
     * Display listing of direct members for current logged-in user.
     */
    public function directMembers(Request $request): View
    {
        $user = Auth::user();
        $query = User::where('sponsor_code', $user->referral_code);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $directs = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => User::where('sponsor_code', $user->referral_code)->count(),
            'active' => User::where('sponsor_code', $user->referral_code)->where('status', 'active')->count(),
            'left' => User::where('sponsor_code', $user->referral_code)->where('position', 'left')->count(),
            'right' => User::where('sponsor_code', $user->referral_code)->where('position', 'right')->count(),
        ];

        return view('user.network.direct', compact('directs', 'stats'));
    }

    /**
     * Display Visual Binary Team Tree for member.
     */
    public function treeView(Request $request): View
    {
        $currentUser = Auth::user();
        $searchCode = $request->query('code');

        if ($searchCode) {
            $targetUser = User::where('referral_code', $searchCode)->first();
            // Ensure member can view root or downline tree
            if ($targetUser) {
                $rootUser = $targetUser;
            } else {
                $rootUser = $currentUser;
            }
        } else {
            $rootUser = $currentUser;
        }

        $treeData = $this->buildBinaryTreeData($rootUser);
        $directMembers = User::where('sponsor_code', $rootUser->referral_code)->latest()->get();

        return view('user.network.tree', compact('rootUser', 'treeData', 'directMembers'));
    }

    /**
     * Build dynamic binary genealogy team tree structure for visual display.
     */
    private function buildBinaryTreeData(User $root): array
    {
        $root->load(['sponsor', 'userPackages', 'transactions']);

        // Helper closure to fetch all downline members for a direct leg in creation order
        $getLegMembers = function (User $rootUser, string $position) {
            $directs = User::where('sponsor_code', $rootUser->referral_code)
                ->where('position', $position)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($directs->isEmpty() && $position === 'left') {
                $directs = User::where('sponsor_code', $rootUser->referral_code)
                    ->where(function ($q) {
                        $q->whereNull('position')->orWhere('position', 'left');
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            $allLegUserIds = [];
            foreach ($directs as $direct) {
                $allLegUserIds = array_merge($allLegUserIds, $direct->getBranchUserIds());
            }

            $allLegUserIds = array_values(array_unique($allLegUserIds));

            if (empty($allLegUserIds)) {
                return collect();
            }

            return User::whereIn('id', $allLegUserIds)
                ->with(['sponsor', 'userPackages', 'transactions'])
                ->orderBy('created_at', 'asc')
                ->get();
        };

        $leftMembers = $getLegMembers($root, 'left');
        $rightMembers = $getLegMembers($root, 'right');

        // Level 1 Nodes
        $leftNode = $leftMembers->first();
        $rightNode = $rightMembers->first();

        // Track used member IDs to avoid duplicate rendering across slots
        $usedLeftIds = $leftNode ? [$leftNode->id] : [];
        $usedRightIds = $rightNode ? [$rightNode->id] : [];

        // Populate Level 2 (Grandchildren) under Left Node
        if ($leftNode) {
            // L-LEFT candidate: Check direct left referral of leftNode first, or next available left member in pool
            $llCandidate = User::where('sponsor_code', $leftNode->referral_code)
                ->where('position', 'left')
                ->first()
                ?? $leftMembers->whereNotIn('id', $usedLeftIds)->first();

            if ($llCandidate) {
                $llCandidate->load(['sponsor', 'userPackages', 'transactions']);
                $leftNode->left_child = $llCandidate;
                $usedLeftIds[] = $llCandidate->id;
            } else {
                $leftNode->left_child = null;
            }

            // L-RIGHT candidate: Check direct right referral of leftNode first, or next available left member in pool
            $lrCandidate = User::where('sponsor_code', $leftNode->referral_code)
                ->where('position', 'right')
                ->first()
                ?? $leftMembers->whereNotIn('id', $usedLeftIds)->first();

            if ($lrCandidate) {
                $lrCandidate->load(['sponsor', 'userPackages', 'transactions']);
                $leftNode->right_child = $lrCandidate;
                $usedLeftIds[] = $lrCandidate->id;
            } else {
                $leftNode->right_child = null;
            }
        }

        // Populate Level 2 (Grandchildren) under Right Node
        if ($rightNode) {
            // R-LEFT candidate: Check direct left referral of rightNode first, or next available right member in pool
            $rlCandidate = User::where('sponsor_code', $rightNode->referral_code)
                ->where('position', 'left')
                ->first()
                ?? $rightMembers->whereNotIn('id', $usedRightIds)->first();

            if ($rlCandidate) {
                $rlCandidate->load(['sponsor', 'userPackages', 'transactions']);
                $rightNode->left_child = $rlCandidate;
                $usedRightIds[] = $rlCandidate->id;
            } else {
                $rightNode->left_child = null;
            }

            // R-RIGHT candidate: Check direct right referral of rightNode first, or next available right member in pool
            $rrCandidate = User::where('sponsor_code', $rightNode->referral_code)
                ->where('position', 'right')
                ->first()
                ?? $rightMembers->whereNotIn('id', $usedRightIds)->first();

            if ($rrCandidate) {
                $rrCandidate->load(['sponsor', 'userPackages', 'transactions']);
                $rightNode->right_child = $rrCandidate;
                $usedRightIds[] = $rrCandidate->id;
            } else {
                $rightNode->right_child = null;
            }
        }

        // Calculate Business Volumes and Total Downline Counts
        $leftUserIds = $leftMembers->pluck('id')->toArray();
        $rightUserIds = $rightMembers->pluck('id')->toArray();

        $leftBusiness = ! empty($leftUserIds)
            ? (float) UserPackage::whereIn('user_id', $leftUserIds)->where('status', 'active')->sum('invested_amount')
            : 0.00;

        $rightBusiness = ! empty($rightUserIds)
            ? (float) UserPackage::whereIn('user_id', $rightUserIds)->where('status', 'active')->sum('invested_amount')
            : 0.00;

        $leftCount = count($leftUserIds);
        $rightCount = count($rightUserIds);

        return [
            'root' => $root,
            'left_child' => $leftNode,
            'right_child' => $rightNode,
            'left_business' => $leftBusiness,
            'right_business' => $rightBusiness,
            'left_count' => $leftCount,
            'right_count' => $rightCount,
            'total_team' => $leftCount + $rightCount,
            'total_business' => $leftBusiness + $rightBusiness,
        ];
    }
}
