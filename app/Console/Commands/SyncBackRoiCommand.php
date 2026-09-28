<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use App\Services\MLMIncomeService;
use App\Services\RoiIncomeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncBackRoiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zivo:sync-back-roi 
                            {--days=1 : Number of daily ROI cycles to process manually} 
                            {--user= : Target user ID, referral code, or email} 
                            {--force : Force payout ignoring same-day limit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair back-dated investments schema data and manually process pending/back daily ROI closing payouts';

    /**
     * Execute the console command.
     */
    public function handle(RoiIncomeService $roiService): int
    {
        $days = (int) $this->option('days');
        $userInput = $this->option('user');
        $force = (bool) $this->option('force');

        if ($days < 1) {
            $days = 1;
        }

        $this->info('====================================================');
        $this->info('  ZIVO PAY - BACK DATA SYNC & MANUAL ROI CLOSING    ');
        $this->info('====================================================');

        // STEP 1: REPAIR & SYNC EXISTING BACK INVESTMENTS SCHEMA DATA
        $this->info("\nStep 1: Inspecting and Repairing existing investment records...");
        $investments = UserInvestment::all();
        $repairedCount = 0;

        foreach ($investments as $inv) {
            $amount = (float) $inv->amount;
            if ($amount <= 0) {
                continue;
            }

            $tierInfo = MLMIncomeService::getTierInfo($amount);

            $needsUpdate = false;
            $updateData = [];

            if (empty($inv->plan_name)) {
                $updateData['plan_name'] = $tierInfo['tier_name'];
                $needsUpdate = true;
            }

            if ((float) $inv->daily_percentage <= 0) {
                $updateData['daily_percentage'] = $tierInfo['daily_percentage'];
                $needsUpdate = true;
            }

            if ((float) $inv->daily_amount <= 0) {
                $updateData['daily_amount'] = $tierInfo['daily_amount'];
                $needsUpdate = true;
            }

            if ((float) $inv->monthly_amount <= 0) {
                $updateData['monthly_amount'] = $tierInfo['monthly_amount'];
                $needsUpdate = true;
            }

            if ((int) $inv->total_days <= 0) {
                $updateData['total_days'] = 730;
                $needsUpdate = true;
            }

            if ($inv->status === 'completed' && (int) $inv->days_completed < 730) {
                $updateData['status'] = 'active';
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $inv->update($updateData);
                $repairedCount++;
            }
        }

        $this->info("✓ Repaired/Synced {$repairedCount} investment record(s) schema fields.");

        // STEP 2: PROCESS MANUAL / BACK DAILY ROI CLOSING PAYOUTS
        $this->info("\nStep 2: Processing {$days} day(s) of Manual ROI Closing payouts...");

        $totalCyclesProcessed = 0;

        for ($cycle = 1; $cycle <= $days; $cycle++) {
            $this->line("  --> Running Closing Cycle {$cycle}/{$days}...");

            if ($userInput) {
                $targetUsers = User::where('id', $userInput)
                    ->orWhere('email', $userInput)
                    ->orWhere('referral_code', $userInput)
                    ->get();

                if ($targetUsers->isEmpty()) {
                    $this->error("No user found matching search: {$userInput}");

                    return Command::FAILURE;
                }

                $cycleProcessed = 0;
                foreach ($targetUsers as $user) {
                    // Process Deposit Wallet ROI
                    if ($user->deposit_wallet >= 1000) {
                        $fundBalance = (float) $user->deposit_wallet;
                        $rate = $roiService->calculateDailyRoiPercentage($fundBalance);
                        $dailyProfit = ($fundBalance * $rate) / 100;

                        if ($dailyProfit > 0) {
                            DB::transaction(function () use ($user, $dailyProfit, $rate, &$cycleProcessed) {
                                $user->increment('earning_wallet', $dailyProfit);

                                Transaction::create([
                                    'user_id' => $user->id,
                                    'trx_id' => 'MANUAL-FW-'.strtoupper(Str::random(8)),
                                    'type' => 'daily_roi',
                                    'wallet_type' => 'earning',
                                    'trx_type' => '+',
                                    'amount' => $dailyProfit,
                                    'charge' => 0.00,
                                    'post_balance' => $user->earning_wallet,
                                    'description' => "Manual Closing: Fund Wallet Daily Profit ({$rate}%) on Balance ₹".number_format($user->deposit_wallet, 2),
                                ]);

                                $cycleProcessed++;
                            });
                        }
                    }

                    // Process Active Investments ROI
                    $userInvestments = UserInvestment::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->get();

                    foreach ($userInvestments as $inv) {
                        DB::transaction(function () use ($inv, $user, &$cycleProcessed) {
                            $dailyAmount = (float) $inv->daily_amount;
                            $user->increment('earning_wallet', $dailyAmount);
                            $inv->increment('days_completed');
                            $inv->increment('total_returned', $dailyAmount);
                            $inv->update(['last_payout_at' => now()]);

                            if ($inv->days_completed >= $inv->total_days) {
                                $inv->update(['status' => 'completed']);
                            }

                            Transaction::create([
                                'user_id' => $user->id,
                                'trx_id' => 'MANUAL-INV-'.strtoupper(Str::random(8)),
                                'type' => 'daily_roi',
                                'wallet_type' => 'earning',
                                'trx_type' => '+',
                                'amount' => $dailyAmount,
                                'charge' => 0.00,
                                'post_balance' => $user->earning_wallet,
                                'description' => "Manual Closing: Daily ROI (Day {$inv->days_completed}/730) on Capital ₹".number_format($inv->amount, 2),
                            ]);

                            $cycleProcessed++;
                        });
                    }
                }

                $totalCyclesProcessed += $cycleProcessed;
            } else {
                // Process all users/investments across system
                $cycleProcessed = $roiService->processDailyRoiPayouts();
                $totalCyclesProcessed += $cycleProcessed;
            }
        }

        $this->info("\n====================================================");
        $this->info("✓ SUCCESS: Processed {$totalCyclesProcessed} ROI closing payouts across {$days} cycle(s).");
        $this->info("====================================================\n");

        return Command::SUCCESS;
    }
}
