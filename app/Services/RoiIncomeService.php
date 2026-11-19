<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoiIncomeService
{
    public function calculateDailyRoiPercentage(float $amount): float
    {
        if ($amount >= 1000000) {
            return 0.30;
        }

        if ($amount >= 500000) {
            return 0.25;
        }

        if ($amount >= 100000) {
            return 0.20;
        }

        return 0.15;
    }

    public function processDailyRoiPayouts(): int
    {
        $processedCount = 0;

        UserInvestment::query()
            ->where('status', 'active')
            ->whereColumn('days_completed', '<', 'total_days')
            ->orderBy('id')
            ->chunkById(100, function ($investments) use (&$processedCount): void {
                foreach ($investments as $investment) {
                    if ($this->processInvestmentRoi((int) $investment->id)) {
                        $processedCount++;
                    }
                }
            });

        User::query()
            ->where('deposit_wallet', '>', 0)
            ->orderBy('id')
            ->chunkById(100, function ($users) use (&$processedCount): void {
                foreach ($users as $user) {
                    if ($this->processFundWalletRoi((int) $user->id)) {
                        $processedCount++;
                    }
                }
            });

        return $processedCount;
    }

    public function backfillMissedRoi(Carbon $from, Carbon $through): int
    {
        $processedCount = 0;
        $payouts = $this->calculateMissedFundWalletRoi($from, $through);

        foreach ($payouts as $payout) {
            DB::transaction(function () use ($payout, &$processedCount): void {
                $user = User::query()->lockForUpdate()->find($payout['user_id']);
                if (! $user) {
                    return;
                }

                $alreadyPaid = Transaction::query()
                    ->where('user_id', $user->id)
                    ->where('type', 'daily_roi')
                    ->where('trx_id', 'like', 'FWPROFIT-%')
                    ->whereDate('created_at', $payout['due_at']->toDateString())
                    ->exists();
                if ($alreadyPaid) {
                    return;
                }

                $this->creditRoi(
                    $user,
                    $payout['amount'],
                    $payout['due_at'],
                    'FWPROFIT-'.strtoupper(Str::random(10)),
                    'daily_roi',
                    "Fund Wallet Daily ROI ({$payout['rate']}%) on Balance ₹".number_format($payout['balance'], 2),
                );
                $processedCount++;
            });
        }

        return $processedCount;
    }

    /**
     * @return array{count: int, amount: float}
     */
    public function previewMissedRoi(Carbon $from, Carbon $through): array
    {
        $payouts = $this->calculateMissedFundWalletRoi($from, $through);

        return [
            'count' => count($payouts),
            'amount' => round(array_sum(array_column($payouts, 'amount')), 2),
        ];
    }

    /**
     * @return list<array{user_id: int, excess_count: int, excess_amount: float, recovery_amount: float, unrecoverable_amount: float, trx_id: string}>
     */
    public function previewDuplicateRoiRecovery(Carbon $from, Carbon $through, ?string $userIdentifier = null): array
    {
        $recoveries = [];

        $users = User::query()->when($userIdentifier, function ($query) use ($userIdentifier): void {
            $query->where(function ($query) use ($userIdentifier): void {
                $query->where('referral_code', $userIdentifier);
                if (ctype_digit($userIdentifier)) {
                    $query->orWhereKey((int) $userIdentifier);
                }
            });
        });

        $users->orderBy('id')->chunkById(100, function ($users) use (&$recoveries, $from, $through): void {
            foreach ($users as $user) {
                $excess = $this->findDuplicateFundRoi((int) $user->id, $from, $through);
                if ($excess['count'] === 0) {
                    continue;
                }

                $trxId = $this->roiRecoveryTransactionId((int) $user->id, $from, $through);
                $alreadyRecovered = (float) Transaction::query()
                    ->where('user_id', $user->id)
                    ->where('trx_id', $trxId)
                    ->value('amount');
                $remaining = max(0, round($excess['amount'] - $alreadyRecovered, 2));
                $available = max(0, round((float) $user->earning_wallet, 2));
                $recoveryAmount = min($remaining, $available);

                $recoveries[] = [
                    'user_id' => (int) $user->id,
                    'excess_count' => $excess['count'],
                    'excess_amount' => $remaining,
                    'recovery_amount' => $recoveryAmount,
                    'unrecoverable_amount' => round($remaining - $recoveryAmount, 2),
                    'trx_id' => $trxId,
                ];
            }
        });

        return $recoveries;
    }

    public function recoverDuplicateRoi(Carbon $from, Carbon $through, ?string $userIdentifier = null): array
    {
        $recoveredCount = 0;
        $recoveredAmount = 0.0;
        $unrecoverableAmount = 0.0;
        $preview = $this->previewDuplicateRoiRecovery($from, $through, $userIdentifier);

        foreach ($preview as $recovery) {
            DB::transaction(function () use ($recovery, $from, $through, &$recoveredCount, &$recoveredAmount, &$unrecoverableAmount): void {
                $user = User::query()->lockForUpdate()->find($recovery['user_id']);
                if (! $user) {
                    return;
                }

                $existingRecovery = Transaction::query()->where('trx_id', $recovery['trx_id'])->exists();
                if ($existingRecovery) {
                    return;
                }

                $available = max(0, round((float) $user->earning_wallet, 2));
                $amount = min($recovery['excess_amount'], $available);
                if ($amount <= 0) {
                    $unrecoverableAmount += $recovery['excess_amount'];

                    return;
                }

                $user->earning_wallet = round($available - $amount, 2);
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'trx_id' => $recovery['trx_id'],
                    'type' => 'roi_recovery',
                    'wallet_type' => 'earning',
                    'trx_type' => '-',
                    'amount' => $amount,
                    'charge' => 0,
                    'post_balance' => $user->earning_wallet,
                    'description' => 'Recovery of duplicate Fund Wallet ROI credits from '.$from->toDateString().' through '.$through->toDateString(),
                ]);

                $recoveredCount += $recovery['excess_count'];
                $recoveredAmount += $amount;
                $unrecoverableAmount += round($recovery['excess_amount'] - $amount, 2);
            });
        }

        return [
            'recovered_count' => $recoveredCount,
            'recovered_amount' => round($recoveredAmount, 2),
            'unrecoverable_amount' => round($unrecoverableAmount, 2),
        ];
    }

    /**
     * @return list<array{user_id: int, excess_count: int, excess_amount: float, recovery_amount: float, unrecoverable_amount: float, trx_id: string}>
     */
    public function previewDuplicateRoiCommissionRecovery(Carbon $from, Carbon $through, ?string $userIdentifier = null): array
    {
        $duplicateEvents = [];
        $sources = User::query()->when($userIdentifier, function ($query) use ($userIdentifier): void {
            $query->where(function ($query) use ($userIdentifier): void {
                $query->where('referral_code', $userIdentifier);
                if (ctype_digit($userIdentifier)) {
                    $query->orWhereKey((int) $userIdentifier);
                }
            });
        });

        $sources->orderBy('id')->chunkById(100, function ($users) use (&$duplicateEvents, $from, $through): void {
            foreach ($users as $user) {
                $duplicate = $this->findDuplicateFundRoi((int) $user->id, $from, $through);
                foreach ($duplicate['events'] as $createdAt) {
                    $key = $user->id.'|'.$createdAt;
                    $duplicateEvents[$key] = ['source_user_id' => (int) $user->id, 'created_at' => $createdAt];
                }
            }
        });

        if ($duplicateEvents === []) {
            return [];
        }

        $sourceUserIds = array_values(array_unique(array_column($duplicateEvents, 'source_user_id')));
        $duplicateEventKeys = array_fill_keys(array_keys($duplicateEvents), true);
        $commissionTotals = [];
        DB::table('transactions')
            ->where('type', 'roi_level_income')
            ->whereIn('from_user_id', $sourceUserIds)
            ->whereBetween('created_at', [$from->toDateTimeString(), $through->toDateTimeString()])
            ->orderBy('id')
            ->chunkById(500, function ($transactions) use (&$commissionTotals, $duplicateEventKeys): void {
                foreach ($transactions as $transaction) {
                    $eventKey = $transaction->from_user_id.'|'.$transaction->created_at;
                    if (! isset($duplicateEventKeys[$eventKey])) {
                        continue;
                    }

                    $recipientId = (int) $transaction->user_id;
                    $commissionTotals[$recipientId] ??= ['count' => 0, 'amount' => 0.0];
                    $commissionTotals[$recipientId]['count']++;
                    $commissionTotals[$recipientId]['amount'] += (float) $transaction->amount;
                }
            });

        $recoveries = [];
        foreach ($commissionTotals as $recipientId => $commission) {
            $trxId = $this->roiLevelRecoveryTransactionId((int) $recipientId, $from, $through);
            $alreadyRecovered = (float) Transaction::query()->where('trx_id', $trxId)->value('amount');
            $remaining = max(0, round($commission['amount'] - $alreadyRecovered, 2));
            $available = max(0, round((float) User::query()->whereKey($recipientId)->value('earning_wallet'), 2));
            $recoveryAmount = min($remaining, $available);
            $recoveries[] = [
                'user_id' => (int) $recipientId,
                'excess_count' => $commission['count'],
                'excess_amount' => $remaining,
                'recovery_amount' => $recoveryAmount,
                'unrecoverable_amount' => round($remaining - $recoveryAmount, 2),
                'trx_id' => $trxId,
            ];
        }

        return $recoveries;
    }

    public function recoverDuplicateRoiCommissions(Carbon $from, Carbon $through, ?string $userIdentifier = null): array
    {
        $recoveredCount = 0;
        $recoveredAmount = 0.0;
        $unrecoverableAmount = 0.0;

        foreach ($this->previewDuplicateRoiCommissionRecovery($from, $through, $userIdentifier) as $recovery) {
            DB::transaction(function () use ($recovery, $from, $through, &$recoveredCount, &$recoveredAmount, &$unrecoverableAmount): void {
                $recipient = User::query()->lockForUpdate()->find($recovery['user_id']);
                if (! $recipient || Transaction::query()->where('trx_id', $recovery['trx_id'])->exists()) {
                    return;
                }

                $available = max(0, round((float) $recipient->earning_wallet, 2));
                $amount = min($recovery['excess_amount'], $available);
                if ($amount <= 0) {
                    $unrecoverableAmount += $recovery['excess_amount'];

                    return;
                }

                $recipient->earning_wallet = round($available - $amount, 2);
                $recipient->save();
                Transaction::create([
                    'user_id' => $recipient->id,
                    'trx_id' => $recovery['trx_id'],
                    'type' => 'roi_level_recovery',
                    'wallet_type' => 'earning',
                    'trx_type' => '-',
                    'amount' => $amount,
                    'charge' => 0,
                    'post_balance' => $recipient->earning_wallet,
                    'description' => 'Recovery of ROI-level commissions from duplicate Fund Wallet ROI for '.$from->toDateString().' through '.$through->toDateString(),
                ]);

                $recoveredCount += $recovery['excess_count'];
                $recoveredAmount += $amount;
                $unrecoverableAmount += round($recovery['excess_amount'] - $amount, 2);
            });
        }

        return [
            'recovered_count' => $recoveredCount,
            'recovered_amount' => round($recoveredAmount, 2),
            'unrecoverable_amount' => round($unrecoverableAmount, 2),
        ];
    }

    /** @param list<int> $userIds @return list<int> */
    public function duplicateFundRoiTransactionIds(array $userIds, Carbon $through): array
    {
        $duplicateIds = [];
        $from = Carbon::create(1970, 1, 1)->startOfDay();

        foreach (array_unique($userIds) as $userId) {
            $duplicateIds = array_merge(
                $duplicateIds,
                $this->findDuplicateFundRoi((int) $userId, $from, $through)['ids'],
            );
        }

        return $duplicateIds;
    }

    /** @return list<int> */
    public function duplicateRoiLevelCommissionTransactionIds(Carbon $through, ?int $recipientId = null): array
    {
        $duplicateEvents = [];
        $sourceUserIds = Transaction::query()
            ->where('type', 'daily_roi')
            ->where('trx_id', 'like', 'FWPROFIT-%')
            ->distinct()
            ->orderBy('user_id')
            ->pluck('user_id')
            ->all();
        $from = Carbon::create(1970, 1, 1)->startOfDay();

        foreach ($sourceUserIds as $sourceUserId) {
            $duplicate = $this->findDuplicateFundRoi((int) $sourceUserId, $from, $through);
            foreach ($duplicate['events'] as $createdAt) {
                $duplicateEvents[$sourceUserId.'|'.$createdAt] = true;
            }
        }

        if ($duplicateEvents === []) {
            return [];
        }

        $duplicateIds = [];
        DB::table('transactions')
            ->where('type', 'roi_level_income')
            ->whereIn('from_user_id', $sourceUserIds)
            ->when($recipientId, fn ($query) => $query->where('user_id', $recipientId))
            ->where('created_at', '<=', $through->toDateTimeString())
            ->orderBy('id')
            ->chunkById(500, function ($transactions) use (&$duplicateIds, $duplicateEvents): void {
                foreach ($transactions as $transaction) {
                    if (isset($duplicateEvents[$transaction->from_user_id.'|'.$transaction->created_at])) {
                        $duplicateIds[] = (int) $transaction->id;
                    }
                }
            });

        return $duplicateIds;
    }

    /** @return array{count: int, amount: float, events: list<string>, ids: list<int>} */
    private function findDuplicateFundRoi(int $userId, Carbon $from, Carbon $through): array
    {
        $transactions = Transaction::query()
            ->where('user_id', $userId)
            ->where(function ($query): void {
                $query->where('trx_id', 'like', 'FWPROFIT-%')
                    ->orWhere(function ($query): void {
                        $query->whereIn('wallet_type', ['fund', 'deposit', 'deposit_wallet'])
                            ->where(function ($query): void {
                                $query->where('trx_type', '+')
                                    ->orWhere(function ($query): void {
                                        $query->whereNull('trx_type')->where('description', 'like', 'Admin add:%');
                                    });
                            });
                    });
            })
            ->where('created_at', '<=', $through)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'trx_id', 'amount', 'wallet_type', 'trx_type', 'description', 'created_at']);

        $cycleAnchor = null;
        $duplicateCount = 0;
        $duplicateAmount = 0.0;
        $duplicateEvents = [];
        $duplicateIds = [];

        foreach ($transactions as $transaction) {
            $createdAt = Carbon::parse($transaction->created_at);
            $isFundRoi = str_starts_with((string) $transaction->trx_id, 'FWPROFIT-');

            if (! $isFundRoi) {
                $isPositiveCredit = $transaction->trx_type === '+'
                    || ($transaction->trx_type === null && str_starts_with((string) $transaction->description, 'Admin add:'));
                if ($isPositiveCredit) {
                    $cycleAnchor = $createdAt;
                }

                continue;
            }

            if ($createdAt->lt($from)) {
                if (! $cycleAnchor || $createdAt->gte($cycleAnchor->copy()->addDay())) {
                    $cycleAnchor = $createdAt;
                }

                continue;
            }
            if ($createdAt->gt($through)) {
                continue;
            }

            if ($cycleAnchor && $createdAt->lt($cycleAnchor->copy()->addDay())) {
                $duplicateCount++;
                $duplicateAmount += (float) $transaction->amount;
                $duplicateEvents[] = $createdAt->format('Y-m-d H:i:s');
                $duplicateIds[] = (int) $transaction->id;

                continue;
            }

            $cycleAnchor = $createdAt;
        }

        return ['count' => $duplicateCount, 'amount' => round($duplicateAmount, 2), 'events' => $duplicateEvents, 'ids' => $duplicateIds];
    }

    private function roiRecoveryTransactionId(int $userId, Carbon $from, Carbon $through): string
    {
        return 'ROIREC-'.strtoupper(substr(hash('sha256', $userId.'|'.$from->toDateString().'|'.$through->toDateString()), 0, 20));
    }

    private function roiLevelRecoveryTransactionId(int $userId, Carbon $from, Carbon $through): string
    {
        return 'ROILVLREC-'.strtoupper(substr(hash('sha256', $userId.'|'.$from->toDateString().'|'.$through->toDateString()), 0, 18));
    }

    /** @return list<array{user_id: int, amount: float, due_at: Carbon, balance: float, rate: float}> */
    private function calculateMissedFundWalletRoi(Carbon $from, Carbon $through): array
    {
        $payouts = [];
        $fundWalletTypes = ['fund', 'deposit', 'deposit_wallet'];

        User::query()->orderBy('id')->chunkById(100, function ($users) use (&$payouts, $from, $through, $fundWalletTypes): void {
            foreach ($users as $user) {
                $transactions = Transaction::query()
                    ->where('user_id', $user->id)
                    ->where(function ($query) use ($fundWalletTypes): void {
                        $query->whereIn('wallet_type', $fundWalletTypes)
                            ->orWhere(function ($query): void {
                                $query->where('type', 'daily_roi')->where('trx_id', 'like', 'FWPROFIT-%');
                            });
                    })
                    ->where('created_at', '<=', $through)
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get(['id', 'amount', 'wallet_type', 'type', 'trx_type', 'trx_id', 'description', 'post_balance', 'created_at']);

                $netSinceStart = 0.0;
                $movements = [];
                $paidDates = [];
                foreach ($transactions as $transaction) {
                    if ($transaction->type === 'daily_roi' && str_starts_with((string) $transaction->trx_id, 'FWPROFIT-')) {
                        $paidDates[Carbon::parse($transaction->created_at)->toDateString()] = true;

                        continue;
                    }

                    if (! in_array($transaction->wallet_type, $fundWalletTypes, true)) {
                        continue;
                    }

                    $direction = $transaction->trx_type;
                    if (! in_array($direction, ['+', '-'], true)) {
                        $direction = str_contains(mb_strtolower((string) $transaction->description), 'deduct') ? '-' : '+';
                    }
                    $delta = (float) $transaction->amount * ($direction === '+' ? 1 : -1);
                    $movements[] = ['at' => Carbon::parse($transaction->created_at), 'delta' => $delta];
                    if (Carbon::parse($transaction->created_at)->gt($from)) {
                        $netSinceStart += $delta;
                    }
                }

                $balance = round((float) $user->deposit_wallet - $netSinceStart, 2);
                $movementIndex = 0;
                while (isset($movements[$movementIndex]) && $movements[$movementIndex]['at']->lte($from)) {
                    $movementIndex++;
                }

                $day = $from->copy()->startOfDay();
                while ($day->lte($through)) {
                    $dayEnd = $day->copy()->endOfDay()->min($through);
                    while (isset($movements[$movementIndex]) && $movements[$movementIndex]['at']->lte($dayEnd)) {
                        $balance += $movements[$movementIndex]['delta'];
                        $movementIndex++;
                    }

                    $dateKey = $day->toDateString();
                    if ($balance > 0 && ! isset($paidDates[$dateKey])) {
                        $rate = $this->calculateDailyRoiPercentage($balance);
                        $payouts[] = [
                            'user_id' => (int) $user->id,
                            'amount' => round($balance * $rate / 100, 2),
                            'due_at' => $dayEnd,
                            'balance' => round($balance, 2),
                            'rate' => $rate,
                        ];
                    }

                    $day->addDay()->startOfDay();
                }
            }
        });

        return $payouts;
    }

    private function backfillInvestmentRoi(int $investmentId, Carbon $from, Carbon $through): int
    {
        return DB::transaction(function () use ($investmentId, $from, $through): int {
            $investment = UserInvestment::query()->lockForUpdate()->find($investmentId);
            if (! $investment || $investment->status !== 'active' || $investment->days_completed >= $investment->total_days) {
                return 0;
            }

            $user = User::query()->lockForUpdate()->find($investment->user_id);
            if (! $user) {
                return 0;
            }

            $anchor = $investment->last_payout_at ?? $investment->activated_at;
            if (! $anchor) {
                return 0;
            }

            $interval = $investment->roi_frequency === 'monthly' ? 'addMonthNoOverflow' : 'addDay';
            $processedCount = 0;
            $dailyAmount = round((float) $investment->daily_amount, 2);

            while ($investment->days_completed < $investment->total_days && $dailyAmount > 0) {
                $dueAt = Carbon::parse($anchor)->{$interval}();
                if ($dueAt->gt($through)) {
                    break;
                }

                if ($dueAt->gte($from)) {
                    $this->creditRoi($user, $dailyAmount, $dueAt, 'DROI-'.strtoupper(Str::random(10)), $investment->roi_frequency === 'monthly' ? 'fixed_package_roi' : 'daily_roi', $investment->roi_frequency === 'monthly'
                        ? 'Fixed Package ROI (Cycle '.($investment->days_completed + 1)."/{$investment->total_days}) on Capital ₹".number_format((float) $investment->amount, 2)
                        : 'Daily ROI (Day '.($investment->days_completed + 1)."/{$investment->total_days}) on Capital ₹".number_format((float) $investment->amount, 2));
                    $this->distributeRoiOnRoiMatchingBonus($user, $dailyAmount);
                    $processedCount++;
                }

                $investment->days_completed++;
                $investment->total_returned = round((float) $investment->total_returned + $dailyAmount, 2);
                $investment->last_payout_at = $dueAt;
                $investment->status = $investment->days_completed >= $investment->total_days ? 'completed' : 'active';
                $investment->save();
                $anchor = $dueAt;
            }

            return $processedCount;
        });
    }

    private function backfillFundWalletRoi(int $userId, Carbon $from, Carbon $through): int
    {
        return DB::transaction(function () use ($userId, $from, $through): int {
            $user = User::query()->lockForUpdate()->find($userId);
            if (! $user || (float) $user->deposit_wallet <= 0) {
                return 0;
            }

            $anchor = Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'daily_roi')
                ->where('trx_id', 'like', 'FWPROFIT-%')
                ->where('created_at', '<=', $through)
                ->latest('created_at')
                ->value('created_at');
            $anchor ??= Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('wallet_type', ['fund', 'deposit', 'deposit_wallet'])
                ->where('trx_type', '+')
                ->where('created_at', '<=', $through)
                ->latest('created_at')
                ->value('created_at');
            $anchor ??= $user->created_at;
            if (! $anchor) {
                return 0;
            }

            $processedCount = 0;
            $fundBalance = (float) $user->deposit_wallet;
            $balanceMovements = Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('wallet_type', ['fund', 'deposit', 'deposit_wallet'])
                ->where('created_at', '>', $anchor)
                ->where('created_at', '<=', $through)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get(['amount', 'trx_type', 'created_at']);
            $movementIndex = 0;
            $fundBalance += $balanceMovements->sum(fn (Transaction $movement): float => $movement->trx_type === '+' ? -(float) $movement->amount : (float) $movement->amount);
            while (true) {
                $dueAt = Carbon::parse($anchor)->addDay();
                if ($dueAt->gt($through)) {
                    break;
                }

                while (isset($balanceMovements[$movementIndex]) && Carbon::parse($balanceMovements[$movementIndex]->created_at)->lte($dueAt)) {
                    $movement = $balanceMovements[$movementIndex++];
                    $amount = (float) $movement->amount;
                    $fundBalance += $movement->trx_type === '+' ? $amount : -$amount;
                }

                if ($dueAt->gte($from)) {
                    $rate = $this->calculateDailyRoiPercentage($fundBalance);
                    $dailyProfit = round(($fundBalance * $rate) / 100, 2);
                    if ($dailyProfit <= 0) {
                        break;
                    }

                    $this->creditRoi($user, $dailyProfit, $dueAt, 'FWPROFIT-'.strtoupper(Str::random(10)), 'daily_roi', "Fund Wallet Daily ROI ({$rate}%) on Balance ₹".number_format($fundBalance, 2));
                    $this->distributeRoiOnRoiMatchingBonus($user, $dailyProfit);
                    $processedCount++;
                }

                $anchor = $dueAt;
            }

            return $processedCount;
        });
    }

    private function creditRoi(User $user, float $amount, Carbon $dueAt, string $transactionId, string $type, string $description): void
    {
        $user->earning_wallet = round((float) $user->earning_wallet + $amount, 2);
        $user->save();

        Transaction::query()->forceCreate([
            'user_id' => $user->id,
            'trx_id' => $transactionId,
            'type' => $type,
            'wallet_type' => 'earning',
            'trx_type' => '+',
            'amount' => $amount,
            'charge' => 0,
            'post_balance' => $user->earning_wallet,
            'description' => $description,
            'created_at' => $dueAt,
            'updated_at' => $dueAt,
        ]);
    }

    private function processInvestmentRoi(int $investmentId): bool
    {
        return DB::transaction(function () use ($investmentId): bool {
            $investment = UserInvestment::query()->lockForUpdate()->find($investmentId);
            if (! $investment || $investment->status !== 'active' || $investment->days_completed >= $investment->total_days) {
                return false;
            }

            $user = User::query()->lockForUpdate()->find($investment->user_id);
            if (! $user) {
                return false;
            }

            $lastPayoutAt = $investment->last_payout_at;
            if (! $lastPayoutAt && $investment->days_completed > 0) {
                $lastPayoutAt = Transaction::query()
                    ->where('user_id', $user->id)
                    ->where('type', 'daily_roi')
                    ->where('trx_id', 'like', 'DROI-%')
                    ->latest('id')
                    ->value('created_at');
            }

            $lastPayoutAt ??= $investment->activated_at;
            if (! $lastPayoutAt) {
                return false;
            }
            $dueAt = $investment->roi_frequency === 'monthly'
                ? Carbon::parse($lastPayoutAt)->addMonthNoOverflow()
                : Carbon::parse($lastPayoutAt)->addDay();
            if ($dueAt->isFuture()) {
                return false;
            }

            $dailyAmount = round((float) $investment->daily_amount, 2);
            if ($dailyAmount <= 0) {
                return false;
            }

            $user->earning_wallet = round((float) $user->earning_wallet + $dailyAmount, 2);
            $user->save();

            $investment->days_completed++;
            $investment->total_returned = round((float) $investment->total_returned + $dailyAmount, 2);
            $investment->last_payout_at = now();
            if ($investment->days_completed >= $investment->total_days) {
                $investment->status = 'completed';
            }
            $investment->save();

            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'DROI-'.strtoupper(Str::random(10)),
                'type' => $investment->roi_frequency === 'monthly' ? 'fixed_package_roi' : 'daily_roi',
                'wallet_type' => 'earning',
                'trx_type' => '+',
                'amount' => $dailyAmount,
                'charge' => 0,
                'post_balance' => $user->earning_wallet,
                'description' => $investment->roi_frequency === 'monthly'
                    ? "Fixed Package ROI (Cycle {$investment->days_completed}/{$investment->total_days}) on Capital ₹".number_format((float) $investment->amount, 2)
                    : "Daily ROI (Day {$investment->days_completed}/{$investment->total_days}) on Capital ₹".number_format((float) $investment->amount, 2),
            ]);

            $this->distributeRoiOnRoiMatchingBonus($user, $dailyAmount);

            return true;
        });
    }

    private function processFundWalletRoi(int $userId): bool
    {
        return DB::transaction(function () use ($userId): bool {
            $user = User::query()->lockForUpdate()->find($userId);
            if (! $user || (float) $user->deposit_wallet <= 0) {
                return false;
            }

            $lastFundRoi = Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'daily_roi')
                ->where('trx_id', 'like', 'FWPROFIT-%')
                ->latest('created_at')
                ->value('created_at');
            $lastFundCredit = Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('wallet_type', ['fund', 'deposit', 'deposit_wallet'])
                ->where('trx_type', '+')
                ->latest('created_at')
                ->value('created_at');
            $cycleAnchor = collect([$lastFundRoi, $lastFundCredit])
                ->filter()
                ->map(fn ($timestamp) => Carbon::parse($timestamp))
                ->sortByDesc(fn (Carbon $timestamp) => $timestamp->getTimestamp())
                ->first() ?? $user->created_at;

            if (! $cycleAnchor || Carbon::parse($cycleAnchor)->addDay()->isFuture()) {
                return false;
            }

            $fundBalance = (float) $user->deposit_wallet;
            $rate = $this->calculateDailyRoiPercentage($fundBalance);
            $dailyProfit = round(($fundBalance * $rate) / 100, 2);
            if ($dailyProfit <= 0) {
                return false;
            }

            $user->earning_wallet = round((float) $user->earning_wallet + $dailyProfit, 2);
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'FWPROFIT-'.strtoupper(Str::random(10)),
                'type' => 'daily_roi',
                'wallet_type' => 'earning',
                'trx_type' => '+',
                'amount' => $dailyProfit,
                'charge' => 0,
                'post_balance' => $user->earning_wallet,
                'description' => "Fund Wallet Daily ROI ({$rate}%) on Balance ₹".number_format($fundBalance, 2),
            ]);

            $this->distributeRoiOnRoiMatchingBonus($user, $dailyProfit);

            return true;
        });
    }

    private function distributeRoiOnRoiMatchingBonus(User $user, float $dailyRoiAmount): void
    {
        $currentSponsorCode = $user->sponsor_code;

        for ($level = 1; $level <= 30 && ! empty($currentSponsorCode); $level++) {
            $sponsor = User::query()->where('referral_code', $currentSponsorCode)->lockForUpdate()->first();
            if (! $sponsor) {
                break;
            }

            $activatedAt = $sponsor->subscription_activated_at ?? $sponsor->activated_at;
            if ($sponsor->isActiveForIncome() && $activatedAt && Carbon::parse($activatedAt)->lte(now()->subDay())) {
                $percentage = $this->roiMatchingPercentage($level);
                $commission = round($dailyRoiAmount * $percentage / 100, 2);

                if ($commission > 0) {
                    $sponsor->earning_wallet = round((float) $sponsor->earning_wallet + $commission, 2);
                    $sponsor->save();

                    Transaction::create([
                        'user_id' => $sponsor->id,
                        'from_user_id' => $user->id,
                        'trx_id' => 'ROILVL-L'.$level.'-'.strtoupper(Str::random(8)),
                        'type' => 'roi_level_income',
                        'wallet_type' => 'earning',
                        'trx_type' => '+',
                        'level' => $level,
                        'amount' => $commission,
                        'charge' => 0,
                        'post_balance' => $sponsor->earning_wallet,
                        'description' => "Level {$level} ROI-on-ROI commission ({$percentage}%) from {$user->name}'s ROI of ₹".number_format($dailyRoiAmount, 2),
                    ]);
                }
            }

            $currentSponsorCode = $sponsor->sponsor_code;
        }
    }

    private function roiMatchingPercentage(int $level): float
    {
        if ($level === 1) {
            return 15.0;
        }

        if ($level === 2) {
            return 8.0;
        }

        if ($level <= 8) {
            return 2.0;
        }

        if ($level <= 16) {
            return 1.0;
        }

        return 0.5;
    }
}
