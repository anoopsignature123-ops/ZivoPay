<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Services\User\DepositService;
use Illuminate\Console\Command;

class CheckPendingDeposits extends Command
{
    protected $signature = 'deposits:check-pending';

    protected $description = 'Check pending deposit payment statuses from gateway and auto-credit if paid.';

    public function handle(DepositService $depositService): int
    {
        $pendingDeposits = Deposit::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        $count = 0;

        foreach ($pendingDeposits as $deposit) {
            try {
                $isPaid = $depositService->verifyAndProcessDeposit($deposit);
                if ($isPaid) {
                    $count++;
                }
            } catch (\Exception $e) {
                // Skip transient errors
            }
        }

        $this->info("Checked pending deposits. Successfully verified & credited: {$count}");

        return self::SUCCESS;
    }
}
