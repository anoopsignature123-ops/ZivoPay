<?php

namespace App\Console\Commands;

use App\Services\RoiIncomeService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RecoverDuplicateRoiCommand extends Command
{
    protected $signature = 'roi:recover-duplicate {--from=2026-11-01 : First date to inspect} {--through= : Last date to inspect} {--user= : Limit to a user ID or referral code} {--apply : Debit recoverable duplicate ROI from Earning Wallets}';

    protected $description = 'Preview or recover duplicate Fund Wallet ROI credits issued within 24-hour cycles';

    public function handle(RoiIncomeService $roiService): int
    {
        $from = Carbon::parse($this->option('from'))->startOfDay();
        $through = Carbon::parse($this->option('through') ?: now())->endOfDay();

        if ($from->gt($through)) {
            $this->error('The --from date must be before --through.');

            return self::FAILURE;
        }

        $userIdentifier = $this->option('user');
        $recoveries = $roiService->previewDuplicateRoiRecovery($from, $through, $userIdentifier);
        $commissionRecoveries = $roiService->previewDuplicateRoiCommissionRecovery($from, $through, $userIdentifier);
        $excessCount = array_sum(array_column($recoveries, 'excess_count'));
        $excessAmount = array_sum(array_column($recoveries, 'excess_amount'));
        $recoveryAmount = array_sum(array_column($recoveries, 'recovery_amount'));
        $unrecoverableAmount = array_sum(array_column($recoveries, 'unrecoverable_amount'));
        $commissionCount = array_sum(array_column($commissionRecoveries, 'excess_count'));
        $commissionAmount = array_sum(array_column($commissionRecoveries, 'excess_amount'));
        $commissionRecoveryAmount = array_sum(array_column($commissionRecoveries, 'recovery_amount'));
        $commissionUnrecoverableAmount = array_sum(array_column($commissionRecoveries, 'unrecoverable_amount'));

        if (! $this->option('apply')) {
            $this->warn('Preview only: no wallet balances or transactions were changed.');
            $this->line("Window: {$from->toDateString()} through {$through->toDateString()}");
            $this->table(
                ['User ID', 'Duplicate entries', 'Excess amount', 'Recoverable now', 'Unavailable now'],
                array_map(fn (array $row): array => [
                    $row['user_id'],
                    $row['excess_count'],
                    '₹'.number_format($row['excess_amount'], 2),
                    '₹'.number_format($row['recovery_amount'], 2),
                    '₹'.number_format($row['unrecoverable_amount'], 2),
                ], $recoveries),
            );
            $this->info("Duplicate ROI entries: {$excessCount}");
            $this->info('Duplicate ROI amount: ₹'.number_format($excessAmount, 2));
            $this->info('Recoverable from current Earning Wallet balances: ₹'.number_format($recoveryAmount, 2));
            $this->warn('Not currently recoverable from available Earning Wallet balances: ₹'.number_format($unrecoverableAmount, 2));
            $this->info("Duplicate ROI-level commission entries: {$commissionCount}");
            $this->info('Duplicate ROI-level commission amount: ₹'.number_format($commissionAmount, 2));
            $this->info('ROI-level commissions recoverable now: ₹'.number_format($commissionRecoveryAmount, 2));
            $this->warn('ROI-level commission amount unavailable now: ₹'.number_format($commissionUnrecoverableAmount, 2));
            $this->warn('Review the per-user amounts before running with --apply.');

            return self::SUCCESS;
        }

        $result = $roiService->recoverDuplicateRoi($from, $through, $userIdentifier);
        $this->info("Recovered {$result['recovered_count']} duplicate ROI entries totaling ₹".number_format($result['recovered_amount'], 2).'.');
        $this->warn('Unrecovered amount due to insufficient current Earning Wallet balances: ₹'.number_format($result['unrecoverable_amount'], 2));

        $commissionResult = $roiService->recoverDuplicateRoiCommissions($from, $through, $userIdentifier);
        $this->info("Recovered {$commissionResult['recovered_count']} duplicate ROI-level commission entries totaling ₹".number_format($commissionResult['recovered_amount'], 2).'.');
        $this->warn('Unrecovered ROI-level commission amount due to insufficient current Earning Wallet balances: ₹'.number_format($commissionResult['unrecoverable_amount'], 2));

        return self::SUCCESS;
    }
}
