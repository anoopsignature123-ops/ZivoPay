<?php

namespace App\Console\Commands;

use App\Models\UserInvestment;
use App\Services\MLMIncomeService;
use App\Services\RoiIncomeService;
use Illuminate\Console\Command;

class SyncBackRoiCommand extends Command
{
    protected $signature = 'zivo:sync-back-roi
                            {--days=1 : Deprecated; ROI follows activation-relative 24-hour cycles}
                            {--user= : Optional user identifier; retained for command compatibility}
                            {--force : Deprecated; cannot bypass the 24-hour payout interval}';

    protected $description = 'Repair investment metadata and process only ROI cycles that are due';

    public function handle(RoiIncomeService $roiService): int
    {
        $repairedCount = 0;

        UserInvestment::query()->orderBy('id')->chunkById(100, function ($investments) use (&$repairedCount): void {
            foreach ($investments as $investment) {
                $amount = (float) $investment->amount;
                if ($amount <= 0) {
                    continue;
                }

                $tierInfo = MLMIncomeService::getTierInfo($amount);
                $updates = [];

                if (empty($investment->plan_name)) {
                    $updates['plan_name'] = $tierInfo['tier_name'];
                }

                if ((float) $investment->daily_percentage <= 0) {
                    $updates['daily_percentage'] = $tierInfo['daily_percentage'];
                }

                if ((float) $investment->daily_amount <= 0) {
                    $updates['daily_amount'] = $tierInfo['daily_amount'];
                }

                if ((float) $investment->monthly_amount <= 0) {
                    $updates['monthly_amount'] = $tierInfo['monthly_amount'];
                }

                if ((int) $investment->total_days <= 0) {
                    $updates['total_days'] = 730;
                }

                if ($updates !== []) {
                    $investment->update($updates);
                    $repairedCount++;
                }
            }
        });

        $processedCount = $roiService->processDailyRoiPayouts();

        $this->info("Repaired {$repairedCount} investment record(s).");
        $this->info("Processed {$processedCount} payout cycle(s) currently due. --days and --force do not bypass 24-hour timing.");

        return self::SUCCESS;
    }
}
