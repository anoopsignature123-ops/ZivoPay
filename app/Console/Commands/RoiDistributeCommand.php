<?php

namespace App\Console\Commands;

use App\Services\IncomeEngineService;
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
    protected $description = 'Distribute daily ROI yield (0.5%), Referral ROI (0.5%), and Matching ROI (0.5%) under Dex Trade capping limits';

    /**
     * Execute the console command.
     */
    public function handle(IncomeEngineService $engine): int
    {
        $this->info('Starting Daily Dex Trade ROI Yield & Passive Distributions...');

        $results = $engine->runAllDailyIncomes();

        $this->info('Daily ROI Distribution Complete!');
        $this->line("- Daily ROI Contracts: {$results['daily_roi']['processed_contracts']} | Total: \$".number_format($results['daily_roi']['total_roi_amount'], 2));
        $this->line("- Referral ROI Sponsors: {$results['referral_roi']['sponsors_processed']} | Total: \$".number_format($results['referral_roi']['total_referral_roi_amount'], 2));
        $this->line("- Matching ROI Contracts: {$results['matching_roi']['processed_contracts']} | Total: \$".number_format($results['matching_roi']['total_matching_roi_amount'], 2));

        return Command::SUCCESS;
    }
}
