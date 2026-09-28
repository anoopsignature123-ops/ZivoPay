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
    protected $description = 'Process daily ROI distributions and 15-level ROI matching income for active investments & deposit balances';

    /**
     * Execute the console command.
     */
    public function handle(RoiIncomeService $roiService): int
    {
        $this->info('Starting ZIVO PAY Daily ROI & Fund Deposit Yield processing...');

        $count = $roiService->processDailyRoiPayouts();

        $this->info("Successfully processed Daily ROI & 15-Level ROI Level Income for {$count} active contracts / deposit accounts.");

        return Command::SUCCESS;
    }
}
