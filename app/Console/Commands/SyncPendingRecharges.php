<?php

namespace App\Console\Commands;

use App\Models\Recharge;
use App\Services\RechargeService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('recharges:sync-pending {--limit=25 : Maximum pending orders to check in one run (1-100)}')]
#[Description('Poll recent pending recharge orders and apply confirmed provider results.')]
class SyncPendingRecharges extends Command
{
    public function handle(RechargeService $rechargeService): int
    {
        $limit = max(1, min(100, (int) $this->option('limit')));
        $now = now();
        $reviewCount = Recharge::query()
            ->where('status', 'pending')
            ->where('created_at', '<', $now->copy()->subHour())
            ->count();
        $checkedCount = 0;
        $resolvedCount = 0;
        $errorCount = 0;

        $pendingRecharges = Recharge::query()
            ->where('status', 'pending')
            ->where('created_at', '>=', $now->copy()->subHour())
            ->where('updated_at', '<=', $now->copy()->subMinutes(2))
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($pendingRecharges as $recharge) {
            try {
                $updatedRecharge = $rechargeService->syncOrderStatus($recharge);
                $checkedCount++;

                if (! $updatedRecharge->isPending()) {
                    $resolvedCount++;
                }
            } catch (Throwable $exception) {
                $errorCount++;

                Log::warning('Scheduled recharge status check failed.', [
                    'recharge_id' => $recharge->id,
                    'order_id' => $recharge->order_id,
                    'exception' => $exception::class,
                ]);
            }
        }

        $this->info("Checked {$checkedCount} pending recharge(s); resolved {$resolvedCount}; transient errors {$errorCount}; pending older than one hour for admin review {$reviewCount}.");

        return self::SUCCESS;
    }
}
