<?php

namespace App\Console\Commands;

use App\Models\User;
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
    protected $description = 'Evaluate ZIVO PAY milestone rewards for active users';

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

            $rows[] = [
                'user' => $user->name.' ('.$user->referral_code.')',
                'reward_check' => 'Completed',
                'earning_wallet' => '₹'.number_format((float) $user->fresh()->earning_wallet, 2),
            ];
        }

        $this->table(
            ['User / Referral', 'Reward Check', 'Earning Wallet'],
            $rows
        );

        $this->info('ZIVO PAY reward evaluation completed successfully. ROI payouts are processed by roi:distribute only.');

        return Command::SUCCESS;
    }
}
