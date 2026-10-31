<?php

namespace App\Console\Commands;

use App\Services\RoiIncomeService;
use Illuminate\Console\Command;

class RoiDistributeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roi:distribute';

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
        $this->info('Checking due 24-hour ROI cycles and 30-level ROI-on-ROI commissions...');

        $count = $roiService->processDailyRoiPayouts();

        $this->info("Processed {$count} due ROI payouts and matching commission cycles.");

        return Command::SUCCESS;
    }
}
