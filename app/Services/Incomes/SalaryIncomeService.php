<?php

namespace App\Services\Incomes;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSalary;
use App\Services\IncomeCapService;
use Illuminate\Support\Facades\DB;

/**
 * Class SalaryIncomeService
 *
 * INCOME RULE 7: 17-LEVEL SALARY INCOME (Dex Trade PDF Slide 16 & 17)
 * -------------------------------------------------------------------------
 * Description:
 * Milestone salary rewards based on Binary Matching Volume and Direct Business Volume.
 * 17 Rank Levels ranging from $50/mo up to $1,200,000/mo.
 * Subject to 8X Working Income Cap limit.
 */
class SalaryIncomeService
{
    public const SALARY_RANKS = [
        1 => ['name' => 'Rank 1',  'matching' => 5000.00,       'business' => 250.00,       'reward' => 50.00,      'months' => 5],
        2 => ['name' => 'Rank 2',  'matching' => 10000.00,      'business' => 500.00,       'reward' => 100.00,     'months' => 5],
        3 => ['name' => 'Rank 3',  'matching' => 25000.00,      'business' => 1000.00,      'reward' => 100.00,     'months' => 10],
        4 => ['name' => 'Rank 4',  'matching' => 50000.00,      'business' => 2500.00,      'reward' => 250.00,     'months' => 10],
        5 => ['name' => 'Rank 5',  'matching' => 100000.00,     'business' => 5000.00,      'reward' => 500.00,     'months' => 10],
        6 => ['name' => 'Rank 6',  'matching' => 250000.00,     'business' => 10000.00,     'reward' => 1000.00,    'months' => 10],
        7 => ['name' => 'Rank 7',  'matching' => 500000.00,     'business' => 20000.00,     'reward' => 2000.00,    'months' => 10],
        8 => ['name' => 'Rank 8',  'matching' => 1000000.00,    'business' => 50000.00,     'reward' => 5000.00,    'months' => 10],
        9 => ['name' => 'Rank 9',  'matching' => 2500000.00,    'business' => 100000.00,    'reward' => 5000.00,    'months' => 20],
        10 => ['name' => 'Rank 10', 'matching' => 5000000.00,    'business' => 250000.00,    'reward' => 10000.00,   'months' => 25],
        11 => ['name' => 'Rank 11', 'matching' => 10000000.00,   'business' => 500000.00,    'reward' => 20000.00,   'months' => 25],
        12 => ['name' => 'Rank 12', 'matching' => 25000000.00,   'business' => 1000000.00,   'reward' => 40000.00,   'months' => 25],
        13 => ['name' => 'Rank 13', 'matching' => 50000000.00,   'business' => 2000000.00,   'reward' => 80000.00,   'months' => 25],
        14 => ['name' => 'Rank 14', 'matching' => 100000000.00,  'business' => 4000000.00,   'reward' => 160000.00,  'months' => 25],
        15 => ['name' => 'Rank 15', 'matching' => 250000000.00,  'business' => 8000000.00,   'reward' => 320000.00,  'months' => 25],
        16 => ['name' => 'Rank 16', 'matching' => 500000000.00,  'business' => 15000000.00,  'reward' => 600000.00,  'months' => 25],
        17 => ['name' => 'Rank 17', 'matching' => 1000000000.00, 'business' => 30000000.00,  'reward' => 1200000.00, 'months' => 25],
    ];

    public function __construct(
        protected IncomeCapService $capService
    ) {}

    /**
     * Check and qualify user for new salary ranks based on leg volume stats.
     */
    public function checkSalaryQualifications(User $user): void
    {
        if ($user->status !== 'active') {
            return;
        }

        $stats = $user->leg_volume_stats;
        $matchedVolume = min((float) $stats['power_leg'], (float) $stats['remaining_leg']);
        $totalBusiness = (float) $stats['total_team'];

        foreach (self::SALARY_RANKS as $rankLevel => $spec) {
            if ($matchedVolume >= $spec['matching'] && $totalBusiness >= $spec['business']) {
                UserSalary::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'rank_level' => $rankLevel,
                    ],
                    [
                        'rank_name' => $spec['name'],
                        'matching_requirement' => $spec['matching'],
                        'business_requirement' => $spec['business'],
                        'monthly_reward' => $spec['reward'],
                        'total_months' => $spec['months'],
                        'months_paid' => 0,
                        'total_paid' => 0.00,
                        'status' => 'active',
                    ]
                );
            }
        }
    }

    /**
     * Process Salary Payout for a user's active salary milestone contracts.
     */
    public function processUserSalaryPayout(User $user): float
    {
        if ($user->status !== 'active') {
            return 0.00;
        }

        // First evaluate qualifications
        $this->checkSalaryQualifications($user);

        $activeSalaries = UserSalary::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereColumn('months_paid', '<', 'total_months')
            ->get();

        if ($activeSalaries->isEmpty()) {
            return 0.00;
        }

        $totalCredited = 0.00;

        foreach ($activeSalaries as $salaryContract) {
            DB::transaction(function () use ($salaryContract, $user, &$totalCredited) {
                $rawMonthlyReward = (float) $salaryContract->monthly_reward;
                $finalReward = $this->capService->checkAndCapWorking($user, $rawMonthlyReward);

                if ($finalReward <= 0) {
                    return;
                }

                $user->increment('earning_wallet', $finalReward);

                $newMonthsPaid = $salaryContract->months_paid + 1;
                $newTotalPaid = (float) $salaryContract->total_paid + $finalReward;
                $newStatus = ($newMonthsPaid >= $salaryContract->total_months) ? 'completed' : 'active';

                $salaryContract->update([
                    'months_paid' => $newMonthsPaid,
                    'total_paid' => $newTotalPaid,
                    'status' => $newStatus,
                    'last_paid_at' => now(),
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'txn_number' => 'TXN-'.rand(10000000, 99999999),
                    'wallet_type' => 'earning_wallet',
                    'amount' => $finalReward,
                    'charge' => 0.00,
                    'post_balance' => $user->fresh()->earning_wallet,
                    'trx_type' => '+',
                    'type' => 'salary_income',
                    'description' => "Received {$salaryContract->rank_name} Salary Reward of \$".number_format($finalReward, 2)." - Month {$newMonthsPaid}/{$salaryContract->total_months}",
                    'reference_id' => $salaryContract->id,
                    'status' => 'completed',
                ]);

                $totalCredited += $finalReward;
            });
        }

        return $totalCredited;
    }

    /**
     * Process Salary Payouts for all active users in the system.
     */
    public function processAllSalaryPayouts(): array
    {
        $users = User::where('status', 'active')->get();
        $processedCount = 0;
        $totalAmountCredited = 0.00;

        foreach ($users as $user) {
            $amount = $this->processUserSalaryPayout($user);
            if ($amount > 0) {
                $processedCount++;
                $totalAmountCredited += $amount;
            }
        }

        return [
            'users_processed' => $processedCount,
            'total_salary_amount' => $totalAmountCredited,
        ];
    }
}
