<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserInvestment;
use App\Services\MLMIncomeService;
use Illuminate\Console\Command;

class IncomeProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'income:process {user? : Optional User ID, Email, or Referral Code to target}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and calculate ZIVO PAY incomes, level matching, and milestone rewards';

    /**
     * Execute the console command.
     */
    public function handle(MLMIncomeService $incomeService): int
    {
        $userInput = $this->argument('user');

        if ($userInput) {
            $users = User::where('id', $userInput)
                ->orWhere('email', $userInput)
                ->orWhere('referral_code', $userInput)
                ->get();
        } else {
            $users = User::where('status', 'active')->get();
        }

        if ($users->isEmpty()) {
            $this->error('No active users found matching selection criteria.');

            return Command::FAILURE;
        }

        $this->info('Starting ZIVO PAY Income & Reward Calculation for '.$users->count().' user(s)...');

        $rows = [];

        foreach ($users as $user) {
            // Evaluate milestone rewards
            $incomeService->checkUserRewardAchievements($user);

            // Process daily ROI for user's active investments
            $userInvestments = UserInvestment::where('user_id', $user->id)
                ->where('status', 'active')
                ->get();

            $roiProcessed = 0;
            foreach ($userInvestments as $investment) {
                $incomeService->distributeDailyROI($investment);
                $roiProcessed++;
            }

            $rows[] = [
                'user' => $user->name.' ('.$user->referral_code.')',
                'active_investments' => $userInvestments->count(),
                'roi_processed' => $roiProcessed,
                'earning_wallet' => '₹'.number_format((float) $user->fresh()->earning_wallet, 2),
            ];
        }

        $this->table(
            ['User / Referral', 'Active Contracts', 'ROI Cycles Processed', 'Earning Wallet'],
            $rows
        );

        $this->info('ZIVO PAY Income & Reward Distribution Completed Successfully!');

        return Command::SUCCESS;
    }
}
