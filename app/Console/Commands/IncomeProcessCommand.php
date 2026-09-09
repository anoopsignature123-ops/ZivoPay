<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Incomes\MatchingIncomeService;
use App\Services\Incomes\SalaryIncomeService;
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
    protected $description = 'Process and distribute Dex Trade incomes (10% Binary Matching and 17-Level Salary Plan)';

    /**
     * Execute the console command.
     */
    public function handle(
        MatchingIncomeService $matchingService,
        SalaryIncomeService $salaryService
    ): int {
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
            $this->error('No active users found matching your selection criteria.');

            return Command::FAILURE;
        }

        $this->info('Starting Dex Trade Income Processing Cycle for '.$users->count().' user(s)...');

        $rows = [];

        foreach ($users as $user) {
            $legStats = $user->leg_volume_stats;

            $powerLeg = (float) ($legStats['power_leg'] ?? 0);
            $weakerLeg = (float) ($legStats['remaining_leg'] ?? 0);

            // Process Incomes
            $matchingPaid = $matchingService->processUserMatching($user, $powerLeg, $weakerLeg);
            $salaryPaid = $salaryService->processUserSalaryPayout($user);

            $rows[] = [
                'user' => $user->name.' ('.$user->referral_code.')',
                'matching' => '$'.number_format($matchingPaid, 2),
                'salary' => '$'.number_format($salaryPaid, 2),
                'earning_wallet' => '$'.number_format((float) $user->fresh()->earning_wallet, 2),
            ];
        }

        $this->table(
            ['User / Referral', '10% Matching Income', '17-Level Salary Income', 'Earning Wallet'],
            $rows
        );

        $this->info('Dex Trade Income Distribution Cycle Completed Successfully!');

        return Command::SUCCESS;
    }
}
