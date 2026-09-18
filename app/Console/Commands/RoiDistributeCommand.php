<?php

namespace App\Console\Commands;

use App\Models\UserInvestment;
use App\Services\MLMIncomeService;
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
    public function handle(MLMIncomeService $incomeService): int
    {
        $this->info('Starting ZIVO PAY Daily ROI Yield & 15-Level Distribution...');

        $activeInvestments = UserInvestment::where('status', 'active')
            ->where('days_completed', '<', 730)
            ->where(function ($q) {
                $q->whereNull('last_payout_at')
                    ->orWhere('last_payout_at', '<=', now()->subHours(20));
            })
            ->get();

        $count = 0;
        foreach ($activeInvestments as $investment) {
            $incomeService->distributeDailyROI($investment);
            $count++;
        }

        $this->info("Successfully processed Daily ROI & 15-Level Income for {$count} active contracts.");

        return Command::SUCCESS;
    }
}
