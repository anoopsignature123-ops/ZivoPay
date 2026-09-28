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
    protected $description = 'Distribute ZIVO PAY daily ROI yield (0.15% - 0.30%) and 15-level ROI matching income';

    /**
     * Execute the console command.
     */
    public function handle(RoiIncomeService $roiService): int
    {
        $this->info('Starting ZIVO PAY Daily ROI Yield & 15-Level Distribution...');

        $count = $roiService->processDailyRoiPayouts();

        $this->info("Successfully processed Daily ROI & 15-Level Income for {$count} active contracts / deposit accounts.");

        return Command::SUCCESS;
    }
}
