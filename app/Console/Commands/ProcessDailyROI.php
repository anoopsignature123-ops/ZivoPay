<?php

namespace App\Console\Commands;

use App\Services\RoiIncomeService;
use Illuminate\Console\Command;

class ProcessDailyROI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zivo:process-daily-roi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due 24-hour ROI cycles and 30-level ROI-on-ROI commissions (50% total)';

    /**
     * Execute the console command.
     */
    public function handle(RoiIncomeService $roiService): int
    {
        $this->info('Starting ZIVO PAY Daily ROI & Fund Deposit Yield processing...');

        $count = $roiService->processDailyRoiPayouts();

        $this->info("Processed {$count} due ROI payouts and matching commission cycles.");

        return Command::SUCCESS;
    }
}
