<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\P2pTransfer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class P2pController extends Controller
{
    /**
     * Display P2P Member Transfer page & transfer history.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = P2pTransfer::with(['sender', 'receiver'])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });

        if ($request->filled('type')) {
            if ($request->type === 'sent') {
                $query->where('sender_id', $user->id);
            } elseif ($request->type === 'received') {
                $query->where('receiver_id', $user->id);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('sender', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('receiver', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $transfers = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total_sent' => (float) P2pTransfer::where('sender_id', $user->id)->sum('amount'),
            'total_received' => (float) P2pTransfer::where('receiver_id', $user->id)->sum('amount'),
            'total_count' => P2pTransfer::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->count(),
        ];

        return view('user.p2p.index', compact('user', 'transfers', 'stats'));
    }

    /**
     * AJAX Live Member Lookup by Referral Code.
     */
    public function checkMember(Request $request)
    {
        $code = trim((string) $request->query('member_code'));

        if (empty($code)) {
            return response()->json(['success' => false, 'message' => 'Member code is required.'], 400);
        }

        $sender = Auth::user();
        $receiver = User::where('referral_code', $code)->first();

        if (! $receiver) {
            return response()->json(['success' => false, 'message' => 'Invalid Referral Code. No member found.'], 444);
        }

        if ($sender && $sender->id === $receiver->id) {
            return response()->json(['success' => false, 'message' => 'You cannot transfer funds to your own account via P2P. Use Internal Transfer instead.'], 422);
        }

        return response()->json([
            'success' => true,
            'name' => $receiver->name,
            'referral_code' => $receiver->referral_code,
            'email' => $receiver->email,
            'is_active' => (bool) $receiver->is_subscription_active,
        ]);
    }

    /**
     * Execute P2P Member Transfer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_code' => 'required|string|exists:users,referral_code',
            'from_wallet' => 'required|string|in:deposit_wallet,earning_wallet',
            'amount' => 'required|numeric|min:10',
            'remarks' => 'nullable|string|max:255',
        ], [
            'receiver_code.required' => 'Please enter recipient member Referral Code / User ID.',
            'receiver_code.exists' => 'Target member code does not exist.',
            'amount.min' => 'Minimum P2P Transfer amount is ₹10.',
        ]);

        $sender = Auth::user();
        $receiverCode = trim((string) $request->receiver_code);
        $receiver = User::where('referral_code', $receiverCode)->firstOrFail();

        if ($sender->id === $receiver->id) {
            return back()->withErrors(['receiver_code' => 'You cannot transfer funds to your own account via P2P. Please use Internal Wallet Transfer instead.'])->withInput();
        }

        $fromWallet = $request->from_wallet;
        $amount = (float) $request->amount;
        $sourceWalletName = ($fromWallet === 'deposit_wallet') ? 'Fund Wallet' : 'Earning Wallet';
        $availableBalance = ($fromWallet === 'deposit_wallet') ? (float) $sender->deposit_wallet : (float) $sender->earning_wallet;

        if ($availableBalance < $amount) {
            return back()->withErrors(['amount' => "Insufficient {$sourceWalletName} balance. Available: ₹".number_format($availableBalance, 2)])->withInput();
        }

        $trxId = 'P2P-'.strtoupper(Str::random(10));
        $remarks = $request->remarks ? trim((string) $request->remarks) : null;

        DB::transaction(function () use ($sender, $receiver, $amount, $fromWallet, $sourceWalletName, $trxId, $remarks) {
            // Deduct from Sender
            if ($fromWallet === 'deposit_wallet') {
                $sender->decrement('deposit_wallet', $amount);
            } else {
                $sender->decrement('earning_wallet', $amount);
            }

            // Credit to Receiver's Fund Wallet
            $receiver->increment('deposit_wallet', $amount);

            // Record P2P Log
            P2pTransfer::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $amount,
                'from_wallet' => $fromWallet,
                'trx_id' => $trxId,
                'remarks' => $remarks,
            ]);

            // Sender Debit Transaction
            Transaction::create([
                'user_id' => $sender->id,
                'amount' => $amount,
                'wallet_type' => $fromWallet === 'deposit_wallet' ? 'fund' : 'earning',
                'type' => 'p2p_transfer',
                'trx_type' => '-',
                'description' => "P2P Fund Transfer Sent to {$receiver->name} ({$receiver->referral_code}) from {$sourceWalletName}",
                'trx_id' => $trxId,
            ]);

            // Receiver Credit Transaction
            Transaction::create([
                'user_id' => $receiver->id,
                'amount' => $amount,
                'wallet_type' => 'fund',
                'type' => 'p2p_transfer',
                'trx_type' => '+',
                'description' => "P2P Fund Transfer Received from {$sender->name} ({$sender->referral_code}) into Fund Wallet",
                'trx_id' => $trxId.'-RCV',
            ]);
        });

        return redirect()->route('user.p2p.index')->with('success', 'Successfully transferred ₹'.number_format($amount, 2)." to member {$receiver->name} ({$receiver->referral_code})!");
    }
}
