<?php

namespace App\Console\Commands;

use App\Services\RoiIncomeService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class BackfillRoiCommand extends Command
{
    protected $signature = 'roi:backfill {--from=2026-11-01 : First due date to backfill} {--through= : Last due date to backfill} {--apply : Credit the missing ROI payouts}';

    protected $description = 'Preview or credit missing daily Fund Wallet ROI based on each day\'s recorded balance';

    public function handle(RoiIncomeService $roiService): int
    {
        $from = Carbon::parse($this->option('from'))->startOfDay();
        $through = Carbon::parse($this->option('through') ?: now())->endOfDay();

        if ($from->gt($through)) {
            $this->error('The --from date must be before --through.');

            return self::FAILURE;
        }

        if (! $this->option('apply')) {
            $preview = $roiService->previewMissedRoi($from, $through);
            $this->warn('Dry run only: no balances or transactions were changed.');
            $this->line("Window: {$from->toDateTimeString()} through {$through->toDateTimeString()}");
            $this->info("Eligible missed cycles: {$preview['count']}");
            $this->info('Estimated ROI credit: ₹'.number_format($preview['amount'], 2));
            $this->warn('Run again with --apply to credit the missing Fund Wallet ROI cycles.');

            return self::SUCCESS;
        }

        $count = $roiService->backfillMissedRoi($from, $through);
        $this->info("Backfilled {$count} missed ROI payouts with their due timestamps.");

        return self::SUCCESS;
    }
}
