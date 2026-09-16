<?php

namespace App\Console\Commands;

use App\Models\UserInvestment;
use App\Services\MLMIncomeService;
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
    protected $description = 'Process daily ROI distributions and 15-level ROI matching income for active investments';

    /**
     * Execute the console command.
     */
    public function handle(MLMIncomeService $incomeService): int
    {
        $this->info('Starting ZIVO PAY Daily ROI processing...');

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

        $this->info("Successfully processed Daily ROI & 15-Level ROI Income for {$count} active investments.");

        return Command::SUCCESS;
    }
}
