<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users with search & filters.
     */
    public function index(Request $request): View
    {
        $query = User::with(['role', 'sponsor', 'directMembers'])->where('role_id', 2);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Live AJAX Autocomplete Suggestions for Global Header Search.
     */
    public function globalSearchSuggestions(Request $request): JsonResponse
    {
        $query = trim($request->query('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('role_id', 2)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('referral_code', 'like', "%{$query}%")
                    ->orWhere('mobile', 'like', "%{$query}%");
            })
            ->take(6)
            ->get();

        $results = [];

        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'title' => $user->name,
                'subtitle' => $user->referral_code.' • '.$user->email,
                'status' => strtoupper($user->status),
                'url' => route('admin.users.show', $user->id),
                'initial' => strtoupper(substr($user->name, 0, 1)),
            ];
        }

        return response()->json($results);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $generatedCode = User::generateReferralCode();

        return view('admin.users.create', compact('generatedCode'));
    }

    /**
     * Store a newly created user in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'sponsor_code' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.unique' => 'This email address is already registered in the system.',
            'mobile.unique' => 'This mobile number is already registered in the system.',
        ]);

        $referralCode = User::generateReferralCode();

        User::create([
            'role_id' => 2,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'referral_code' => $referralCode,
            'sponsor_code' => $request->sponsor_code,
            'position' => 'direct',
            'status' => 'active',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users')->with('success', "Member {$request->name} ({$referralCode}) created successfully.");
    }

    /**
     * Display the specified user details.
     */
    public function show(User $user): View
    {
        $user->load([
            'role',
            'sponsor',
            'directMembers',
        ]);

        $directCount = User::where('sponsor_code', $user->referral_code)->count();

        return view('admin.users.show', compact('user', 'directCount'));
    }

    /**
     * Direct Add or Deduct Fund from User's Deposit Wallet or Earning Wallet by Admin.
     */
    public function addFund(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'wallet_type' => 'required|in:deposit_wallet,earning_wallet',
            'action' => 'nullable|in:add,deduct',
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'nullable|string|max:255',
        ]);

        $walletType = $request->wallet_type;
        $action = $request->input('action', 'add');
        $amount = (float) $request->amount;
        $userRemark = $request->input('remark');
        $remark = $userRemark ? "{$userRemark} (Processed by Admin)" : 'Processed by Admin';

        DB::transaction(function () use ($user, $walletType, $action, $amount, $remark) {
            if ($action === 'deduct') {
                $user->decrement($walletType, $amount);
                $trxType = 'admin_debit';
            } else {
                $user->increment($walletType, $amount);
                $trxType = 'admin_credit';
            }

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => $walletType,
                'type' => $trxType,
                'description' => "Admin {$action}: {$remark}",
                'trx_id' => 'ADM-'.strtoupper(Str::random(10)),
            ]);
        });

        $walletLabel = $walletType === 'deposit_wallet' ? 'Fund Wallet' : 'Earning Wallet';

        return redirect()->back()->with('success', "₹{$amount} successfully {$action}ed in {$user->name}'s {$walletLabel}. Remark: {$remark}");
    }

    /**
     * Show the form for creating or editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in database.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'mobile' => 'required|string|max:20|unique:users,mobile,'.$user->id,
            'sponsor_code' => 'required|string',
            'position' => 'required|in:left,right,direct',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'email.unique' => 'This email address is already in use by another user account.',
            'mobile.unique' => 'This mobile number is already in use by another user account.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'sponsor_code' => $request->sponsor_code,
            'position' => strtolower($request->position),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', "Member {$user->referral_code} updated successfully.");
    }

    /**
     * Login as User / Impersonate User from Admin Panel.
     */
    public function impersonate(User $user): RedirectResponse
    {
        $adminId = Auth::id() ?? session('admin_user_id', 1);
        session()->put('admin_user_id', $adminId);
        session()->put('impersonated_user_id', $user->id);

        return redirect()->route('user.dashboard')->with('info', "Logged in as member {$user->name} ({$user->referral_code}).");
    }

    /**
     * Toggle user status (active vs inactive) or deactivate account.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $statusLabel = strtoupper($newStatus);

        return redirect()->back()->with('success', "Member {$user->name} ({$user->referral_code}) status changed to {$statusLabel}.");
    }

    /**
     * Stop impersonating and return to Admin Panel.
     */
    public function stopImpersonating(): RedirectResponse
    {
        session()->forget('impersonated_user_id');

        if (session()->has('admin_user_id')) {
            $admin = User::find(session('admin_user_id'));
            if ($admin) {
                Auth::setUser($admin);

                return redirect()->route('admin.users')->with('success', 'Exited member view and returned to Admin Control Panel.');
            }
        }

        return redirect()->route('user.dashboard');
    }
}
