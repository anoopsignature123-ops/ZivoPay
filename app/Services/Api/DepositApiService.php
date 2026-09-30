<?php

namespace App\Services\Api;

use App\Models\Deposit;
use App\Models\User;
use App\Services\DepositService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class DepositApiService
{
    public function __construct(
        protected DepositService $depositService
    ) {}

    /**
     * Submit and process a new deposit / add fund request.
     */
    public function storeDeposit(User $user, array $validatedData, ?UploadedFile $proofFile = null): Deposit
    {
        $proofPath = null;

        if ($proofFile) {
            $proofPath = $proofFile->store('deposit_proofs', 'public');
        }

        return $this->depositService->createDeposit(
            user: $user,
            amount: (float) $validatedData['amount'],
            paymentMethod: $validatedData['payment_method'],
            trxHash: trim((string) $validatedData['trx_hash']),
            proofPath: $proofPath
        );
    }

    /**
     * Fetch user's deposit history with filters, statistics, and pagination.
     */
    public function getDepositHistory(User $user, array $filters = [], int $perPage = 15): array
    {
        $query = Deposit::where('user_id', $user->id);

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('deposit_ref', 'like', "%{$search}%")
                    ->orWhere('trx_hash', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'rejected'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        /** @var LengthAwarePaginator $deposits */
        $deposits = $query->orderBy('id', 'desc')->paginate($perPage);

        $stats = [
            // 'total_approved' => (float) Deposit::where('user_id', $user->id)->where('status', 'approved')->sum('final_amount'),
            // 'total_pending' => (float) Deposit::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            // 'total_rejected' => (float) Deposit::where('user_id', $user->id)->where('status', 'rejected')->sum('amount'),
            'total_count' => Deposit::where('user_id', $user->id)->count(),
            'current_deposit_wallet_balance' => (float) $user->deposit_wallet,
        ];

        return [
            'deposits' => $deposits,
            'stats' => $stats,
        ];
    }
}