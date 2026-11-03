<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserInvestment;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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

        return back()->with('success', 'Due Fund Wallet daily ROI, Fixed Package monthly ROI, and eligible 30-level ROI-on-ROI cycles processed. '.$output);
    }

    public function withdrawals(Request $request): View
    {
        return $this->renderWithdrawals($request, 'all');
    }

    public function inrWithdrawals(Request $request): View
    {
        return $this->renderWithdrawals($request, 'inr');
    }

    public function usdWithdrawals(Request $request): View
    {
        return $this->renderWithdrawals($request, 'usd');
    }

    public function pendingWithdrawalCounts(): JsonResponse
    {
        $counts = Withdrawal::query()
            ->where('status', 'pending')
            ->selectRaw('COALESCE(SUM(CASE WHEN payment_method IN (?, ?) THEN 1 ELSE 0 END), 0) as inr', ['INR UPI', 'INR Bank Transfer'])
            ->selectRaw('COALESCE(SUM(CASE WHEN payment_method = ? THEN 1 ELSE 0 END), 0) as usd', ['USDT BEP20'])
            ->first();

        return response()->json([
            'inr' => (int) ($counts?->inr ?? 0),
            'usd' => (int) ($counts?->usd ?? 0),
        ]);
    }

    private function renderWithdrawals(Request $request, string $withdrawalType): View
    {
        $query = $this->applyWithdrawalType(Withdrawal::query(), $withdrawalType)->with('user');

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

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'completed', 'rejected'], true)) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $statsRow = $this->applyWithdrawalType(Withdrawal::query(), $withdrawalType)
            ->selectRaw('COUNT(*) as total_requests')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as pending_amount")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'approved' THEN final_amount ELSE 0 END), 0) as approved_amount")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'completed' THEN final_amount ELSE 0 END), 0) as completed_amount")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_count")
            ->first();

        $stats = [
            'total_requests' => (int) $statsRow->total_requests,
            'pending_count' => (int) $statsRow->pending_count,
            'pending_amount' => (float) $statsRow->pending_amount,
            'approved_amount' => (float) $statsRow->approved_amount,
            'completed_amount' => (float) $statsRow->completed_amount,
            'rejected_count' => (int) $statsRow->rejected_count,
        ];

        $contractAddress = (string) config('services.usdt.payout_contract_address', '');
        $withdrawalRoute = match ($withdrawalType) {
            'inr' => 'admin.withdrawals.inr',
            'usd' => 'admin.withdrawals.usd',
            default => 'admin.withdrawals',
        };

        return view('admin.withdrawal.index', compact('withdrawals', 'stats', 'contractAddress', 'withdrawalType', 'withdrawalRoute'));
    }

    private function applyWithdrawalType(Builder $query, string $withdrawalType): Builder
    {
        return match ($withdrawalType) {
            'inr' => $query->whereIn('payment_method', ['INR UPI', 'INR Bank Transfer']),
            'usd' => $query->where('payment_method', 'USDT BEP20'),
            default => $query,
        };
    }

    public function updateWithdrawalStatus(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:rejected'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($withdrawal, $validated): void {
            $lockedWithdrawal = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if (! in_array($lockedWithdrawal->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages(['status' => 'Only pending or approved withdrawals can be rejected.']);
            }

            $lockedWithdrawal->update([
                'status' => 'rejected',
                'admin_remarks' => $validated['admin_remarks'] ?? null,
                'provider_reference' => null,
                'processed_at' => now(),
            ]);

            $user = $lockedWithdrawal->user()->lockForUpdate()->firstOrFail();
            $sourceWallet = in_array($lockedWithdrawal->source_wallet, ['deposit_wallet', 'earning_wallet'], true)
                ? $lockedWithdrawal->source_wallet
                : 'earning_wallet';
            $user->increment($sourceWallet, $lockedWithdrawal->amount);
            $user->refresh();

            Transaction::query()->create([
                'user_id' => $lockedWithdrawal->user_id,
                'amount' => $lockedWithdrawal->amount,
                'wallet_type' => $sourceWallet === 'deposit_wallet' ? 'fund' : 'earning',
                'type' => 'withdrawal_refund',
                'trx_type' => '+',
                'post_balance' => $user->{$sourceWallet},
                'description' => 'Rejected withdrawal #'.$lockedWithdrawal->trx_id.' refunded to original wallet.',
                'trx_id' => 'REF-'.strtoupper(Str::random(10)),
            ]);
        });

        return back()->with('success', "Withdrawal request #{$withdrawal->trx_id} rejected and refunded to its original wallet.");
    }

    public function completeManualWithdrawal(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $validated = $request->validate([
            'provider_reference' => ['required', 'string', 'max:160'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($withdrawal, $validated): void {
            $lockedWithdrawal = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);
            if (! in_array($lockedWithdrawal->payment_method, ['INR UPI', 'INR Bank Transfer'], true)
                || ! in_array($lockedWithdrawal->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages(['status' => 'Only pending INR UPI or bank withdrawals can be marked paid here.']);
            }

            if (Withdrawal::query()->where('provider_reference', $validated['provider_reference'])->where('id', '<>', $lockedWithdrawal->id)->exists()) {
                throw ValidationException::withMessages(['provider_reference' => 'This UTR/reference is already recorded for another withdrawal.']);
            }

            $lockedWithdrawal->update([
                'status' => 'completed',
                'provider_reference' => $validated['provider_reference'],
                'admin_remarks' => $validated['admin_remarks'] ?? null,
                'processed_at' => now(),
            ]);
        });

        return back()->with('success', "INR withdrawal #{$withdrawal->trx_id} marked paid.");
    }

    public function bulkApproveWithdrawals(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'withdrawal_ids' => ['required', 'array', 'min:1', 'max:100'],
            'withdrawal_ids.*' => ['required', 'integer', 'distinct', 'exists:withdrawals,id'],
        ]);

        $updated = DB::transaction(function () use ($validated): int {
            $withdrawals = Withdrawal::query()
                ->whereIn('id', $validated['withdrawal_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($withdrawals->count() !== count($validated['withdrawal_ids']) || $withdrawals->contains(fn (Withdrawal $item): bool => $item->status !== 'pending' || $item->usdt_amount === null || ! preg_match('/^0x[a-fA-F0-9]{40}$/', (string) $item->account_details))) {
                throw ValidationException::withMessages(['withdrawal_ids' => 'Every selected request must be pending, have a saved USDT conversion, and contain a valid BEP20 address.']);
            }

            return Withdrawal::query()->whereIn('id', $validated['withdrawal_ids'])->where('status', 'pending')->update([
                'status' => 'approved',
                'processed_at' => null,
            ]);
        });

        return response()->json(['success' => true, 'message' => "{$updated} withdrawal request(s) approved.", 'updated' => $updated]);
    }

    public function bulkCompleteWithdrawals(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'withdrawal_ids' => ['required', 'array', 'min:1', 'max:100'],
            'withdrawal_ids.*' => ['required', 'integer', 'distinct', 'exists:withdrawals,id'],
            'transaction_hash' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{64}$/'],
        ]);

        if (Withdrawal::query()->where('provider_reference', $validated['transaction_hash'])->whereNotIn('id', $validated['withdrawal_ids'])->exists()) {
            throw ValidationException::withMessages(['transaction_hash' => 'This BSC transaction is already attached to another withdrawal batch.']);
        }

        $contractAddress = strtolower((string) config('services.usdt.payout_contract_address', ''));
        $rpcUrl = (string) config('services.usdt.rpc_url', '');
        if (! preg_match('/^0x[a-f0-9]{40}$/', $contractAddress) || ! filter_var($rpcUrl, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['transaction_hash' => 'Configure a valid USDT_PAYOUT_CONTRACT_ADDRESS and BSC_RPC_URL before completing payouts.']);
        }

        $selectedWithdrawals = Withdrawal::query()->whereIn('id', $validated['withdrawal_ids'])->get();
        if ($selectedWithdrawals->count() !== count($validated['withdrawal_ids']) || $selectedWithdrawals->contains(fn (Withdrawal $item): bool => $item->status !== 'approved' || $item->usdt_amount === null || ! preg_match('/^0x[a-fA-F0-9]{40}$/', (string) $item->account_details))) {
            throw ValidationException::withMessages(['withdrawal_ids' => 'Every selected request must still be approved with a saved USDT amount and valid BEP20 address.']);
        }

        $this->verifyBscTransaction($rpcUrl, $validated['transaction_hash'], $contractAddress, $selectedWithdrawals->all());

        $updated = DB::transaction(function () use ($validated): int {
            $withdrawals = Withdrawal::query()
                ->whereIn('id', $validated['withdrawal_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($withdrawals->count() !== count($validated['withdrawal_ids']) || $withdrawals->contains(fn (Withdrawal $item): bool => $item->status !== 'approved' || $item->usdt_amount === null)) {
                throw ValidationException::withMessages(['withdrawal_ids' => 'Every selected request must still be approved with a saved USDT amount.']);
            }

            return Withdrawal::query()->whereIn('id', $validated['withdrawal_ids'])->where('status', 'approved')->update([
                'status' => 'completed',
                'provider_reference' => $validated['transaction_hash'],
                'processed_at' => now(),
            ]);
        });

        return response()->json(['success' => true, 'message' => "{$updated} withdrawal request(s) marked completed.", 'updated' => $updated]);
    }

    /**
     * @param  list<Withdrawal>  $withdrawals
     */
    private function verifyBscTransaction(string $rpcUrl, string $transactionHash, string $contractAddress, array $withdrawals): void
    {
        try {
            $rpc = fn (string $method, array $params) => Http::timeout(12)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => $method,
                'params' => $params,
            ]);
            $chainResponse = $rpc('eth_chainId', []);
            $transactionResponse = $rpc('eth_getTransactionByHash', [$transactionHash]);
            $receiptResponse = $rpc('eth_getTransactionReceipt', [$transactionHash]);
            $ownerResponse = $rpc('eth_call', [['to' => $contractAddress, 'data' => '0x8da5cb5b'], 'latest']);
            $transaction = $transactionResponse->json('result');
            $receipt = $receiptResponse->json('result');
            $owner = strtolower('0x'.substr((string) $ownerResponse->json('result'), -40));
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages(['transaction_hash' => 'Could not verify this transaction on BNB Smart Chain. Try again after it is confirmed.']);
        }

        if (! $chainResponse->successful() || strtolower((string) $chainResponse->json('result')) !== '0x38'
            || ! $transactionResponse->successful() || ! is_array($transaction)
            || ! $receiptResponse->successful() || ! is_array($receipt)
            || strtolower((string) ($transaction['to'] ?? '')) !== $contractAddress
            || strtolower((string) ($transaction['from'] ?? '')) !== $owner
            || strtolower((string) ($transaction['input'] ?? '')) === ''
            || strtolower((string) ($receipt['transactionHash'] ?? '')) !== strtolower($transactionHash)
            || strtolower((string) ($receipt['to'] ?? '')) !== $contractAddress
            || strtolower((string) ($receipt['status'] ?? '')) !== '0x1'
        ) {
            throw ValidationException::withMessages(['transaction_hash' => 'Transaction is not confirmed successfully from the payout contract owner on BNB Smart Chain.']);
        }

        $input = strtolower(ltrim((string) $transaction['input'], '0x'));
        if (substr($input, 0, 8) !== 'bd13a803' || strlen($input) < 136) {
            throw ValidationException::withMessages(['transaction_hash' => 'The transaction does not call the payout contract bulk withdrawal function.']);
        }

        $arguments = substr($input, 8);
        $recipientsOffset = hexdec(substr($arguments, 0, 64)) * 2;
        $amountsOffset = hexdec(substr($arguments, 64, 64)) * 2;
        if ($recipientsOffset > strlen($arguments) - 64 || $amountsOffset > strlen($arguments) - 64) {
            throw ValidationException::withMessages(['transaction_hash' => 'The transaction data is malformed.']);
        }

        $recipientCount = hexdec(substr($arguments, $recipientsOffset, 64));
        $amountCount = hexdec(substr($arguments, $amountsOffset, 64));
        if ($recipientCount !== count($withdrawals) || $amountCount !== count($withdrawals)) {
            throw ValidationException::withMessages(['transaction_hash' => 'The on-chain recipient list does not match the selected withdrawal requests.']);
        }

        $actualRecipients = [];
        $actualAmounts = [];
        for ($index = 0; $index < $recipientCount; $index++) {
            $recipientWord = substr($arguments, $recipientsOffset + 64 + ($index * 64), 64);
            $amountWord = substr($arguments, $amountsOffset + 64 + ($index * 64), 64);
            if (strlen($recipientWord) !== 64 || strlen($amountWord) !== 64) {
                throw ValidationException::withMessages(['transaction_hash' => 'The transaction data is incomplete.']);
            }

            $actualRecipients[] = '0x'.substr($recipientWord, -40);
            $actualAmounts[] = $amountWord;
        }

        $matchingTokenPrecision = false;
        for ($decimals = 0; $decimals <= 18; $decimals++) {
            $expected = [];
            foreach ($withdrawals as $withdrawal) {
                $expectedAmount = $this->decimalTokenUnitsHex((string) $withdrawal->usdt_amount, $decimals);
                if ($expectedAmount === null) {
                    $expected = [];
                    break;
                }
                $expected[] = [strtolower((string) $withdrawal->account_details), $expectedAmount];
            }

            $actual = [];
            foreach ($actualRecipients as $index => $recipient) {
                $actual[] = [$recipient, $actualAmounts[$index]];
            }
            sort($expected);
            sort($actual);

            if ($expected === $actual) {
                $matchingTokenPrecision = true;
                break;
            }
        }

        if (! $matchingTokenPrecision) {
            throw ValidationException::withMessages(['transaction_hash' => 'The on-chain recipients or USDT amounts do not match these selected requests.']);
        }
    }

    private function decimalTokenUnitsHex(string $amount, int $decimals): ?string
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');
        if (strlen($fraction) > $decimals) {
            return null;
        }

        $decimal = ltrim(($whole ?: '0').str_pad($fraction, $decimals, '0'), '0') ?: '0';
        $hex = '';
        while ($decimal !== '0') {
            $quotient = '';
            $remainder = 0;
            foreach (str_split($decimal) as $digit) {
                $current = ($remainder * 10) + (int) $digit;
                $quotient .= (string) intdiv($current, 16);
                $remainder = $current % 16;
            }
            $hex = dechex($remainder).$hex;
            $decimal = ltrim($quotient, '0') ?: '0';
        }

        return str_pad($hex, 64, '0', STR_PAD_LEFT);
    }
}
