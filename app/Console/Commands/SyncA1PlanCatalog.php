<?php

namespace App\Console\Commands;

use App\Models\A1PlanCatalog;
use App\Models\A1PlanSyncRun;
use App\Services\A1PlanService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncA1PlanCatalog extends Command
{
    protected $signature = 'a1plan:sync-catalogs {type=all : Catalog type: masters, mobile, dth, or all} {--force : Run paid catalog requests without interactive confirmation}';
    //   php artisan a1plan:sync-catalogs all
    protected $description = 'Sync A1Plan operator/circle masters and live mobile/DTH plans into the local database.';

    public function handle(A1PlanService $a1PlanService): int
    {
        $type = strtolower((string) $this->argument('type'));
        if (! in_array($type, ['masters', 'mobile', 'dth', 'all'], true)) {
            $this->error('Type must be masters, mobile, dth, or all.');

            return self::INVALID;
        }

        $this->syncReferenceMasters();
        if ($type === 'masters') {
            $this->info('A1Plan operator and circle masters synced from configured reference data.');

            return self::SUCCESS;
        }

        $requestCount = $this->requestCount($type);
        $this->warn("This sync can make {$requestCount} provider calls. Plan Fetch may be charged or count against the provider quota.");
        if (! $this->option('force') && (! $this->input->isInteractive() || ! $this->confirm('Continue with the provider catalog sync?'))) {
            $this->info('Sync cancelled. No provider plan calls were made.');

            return self::SUCCESS;
        }

        $run = A1PlanSyncRun::create([
            'catalog_type' => $type,
            'status' => 'running',
            'requested_count' => $requestCount,
            'started_at' => now(),
        ]);

        $successCount = 0;
        $errors = [];
        $syncTargets = [];
        if ($type === 'mobile' || $type === 'all') {
            foreach ($this->mobileOperatorCodes() as $operatorCode) {
                foreach (config('a1plan.circles', []) as $circle) {
                    $syncTargets[] = ['mobile', $operatorCode, (string) $circle['code']];
                }
            }
        }
        if ($type === 'dth' || $type === 'all') {
            foreach ($this->dthOperatorCodes() as $operatorCode) {
                $syncTargets[] = ['dth', $operatorCode, ''];
            }
        }

        $fatalSyncError = false;
        $totalTargets = count($syncTargets);

        foreach ($syncTargets as $index => [$catalogType, $operatorCode, $circleCode]) {
            $targetNumber = $index + 1;
            $targetLabel = "{$catalogType}: {$operatorCode}" . ($circleCode !== '' ? " / {$circleCode}" : '');
            $this->line("[{$targetNumber}/{$totalTargets}] Fetching {$targetLabel}");

            try {
                $payload = $catalogType === 'mobile'
                    ? $a1PlanService->planFetch($operatorCode, $circleCode)
                    : $a1PlanService->dthPlanFetch($operatorCode);

                if (! $this->providerPayloadSucceeded($payload)) {
                    $message = $this->providerMessage($payload);
                    $errors[] = [
                        'catalog_type' => $catalogType,
                        'operator_code' => $operatorCode,
                        'circle_code' => $circleCode ?: null,
                        'message' => $message,
                    ];
                    $this->error("[{$targetNumber}/{$totalTargets}] Failed {$targetLabel}: {$message}");

                    if ($this->isFatalSyncError($message)) {
                        $fatalSyncError = true;
                        break;
                    }

                    continue;
                }

                DB::transaction(function () use ($catalogType, $operatorCode, $circleCode, $payload): void {
                    A1PlanCatalog::updateOrCreate(
                        [
                            'catalog_type' => $catalogType,
                            'operator_code' => $operatorCode,
                            'circle_code' => $circleCode,
                        ],
                        [
                            'payload' => $payload,
                            'synced_at' => now(),
                        ]
                    );
                });
                $successCount++;
                $this->info("[{$targetNumber}/{$totalTargets}] Synced {$targetLabel}");
            } catch (Throwable $exception) {
                $message = mb_substr($exception->getMessage(), 0, 180);
                $errors[] = [
                    'catalog_type' => $catalogType,
                    'operator_code' => $operatorCode,
                    'circle_code' => $circleCode ?: null,
                    'message' => $message,
                ];
                $this->error("[{$targetNumber}/{$totalTargets}] Failed {$targetLabel}: {$message}");

                if ($this->isFatalSyncError($message)) {
                    $fatalSyncError = true;
                    break;
                }
            }
        }

        $run->update([
            'status' => count($errors) === 0 ? 'completed' : ($successCount > 0 ? 'partial' : 'failed'),
            'success_count' => $successCount,
            'failure_count' => count($errors),
            'summary' => [
                'errors' => $errors,
                'aborted_early' => $fatalSyncError,
            ],
            'finished_at' => Carbon::now(),
        ]);

        $this->info("A1Plan sync finished: {$successCount} succeeded, ".count($errors).' failed.');

        if ($fatalSyncError) {
            $this->error('Sync stopped early because the provider rejected access or the connection is unavailable. Fix the issue before retrying to avoid wasting provider calls.');
        }

        return count($errors) > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function isFatalSyncError(string $message): bool
    {
        $message = strtolower($message);

        foreach ([
            'not whitelisted',
            'whitelist',
            'e-kyc',
            'ekyc',
            'unauthorized',
            'authentication failed',
            'invalid credentials',
            'credentials are not configured',
            'could not connect',
            'temporarily unavailable',
            'ca bundle',
        ] as $fatalPhrase) {
            if (str_contains($message, $fatalPhrase)) {
                return true;
            }
        }

        return false;
    }

    private function syncReferenceMasters(): void
    {
        $operatorsByCategory = $this->referenceOperatorsByCategory();
        $circleCodes = collect(config('a1plan.circles', []))->pluck('code')->map(fn ($code): string => (string) $code)->all();

        foreach ($operatorsByCategory as $category => $operators) {
            $operatorCodes = collect($operators)->pluck('code')->map(fn ($code): string => (string) $code)->all();
            A1PlanCatalog::query()
                ->where('catalog_type', 'operator')
                ->where('category', $category)
                ->whereNotIn('operator_code', $operatorCodes)
                ->delete();
        }
        A1PlanCatalog::query()
            ->where('catalog_type', 'circle')
            ->whereNotIn('circle_code', $circleCodes)
            ->delete();
        A1PlanCatalog::query()
            ->where('catalog_type', 'mobile')
            ->whereNotIn('circle_code', $circleCodes)
            ->delete();

        foreach ($operatorsByCategory as $category => $operators) {
            foreach ($operators as $operator) {
                A1PlanCatalog::updateOrCreate(
                    ['catalog_type' => 'operator', 'operator_code' => (string) $operator['code'], 'circle_code' => ''],
                    ['category' => (string) $category, 'name' => (string) $operator['name'], 'synced_at' => now()]
                );
            }
        }

        foreach (config('a1plan.circles', []) as $circle) {
            A1PlanCatalog::updateOrCreate(
                ['catalog_type' => 'circle', 'operator_code' => '', 'circle_code' => (string) $circle['code']],
                ['category' => 'mobile', 'name' => (string) $circle['name'], 'synced_at' => now()]
            );
        }
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    private function referenceOperatorsByCategory(): array
    {
        $operatorsByCategory = config('a1plan.operators', []);
        $topupOperators = config('a1topup.operators', []);
        foreach (['postpaid', 'electricity', 'fastag'] as $category) {
            $operatorsByCategory[$category] = $topupOperators[$category] ?? [];
        }

        return $operatorsByCategory;
    }

    /** @return list<string> */
    private function mobileOperatorCodes(): array
    {
        return ['A', 'RC', 'V', 'I', 'BT', 'BR'];
    }

    /** @return list<string> */
    private function dthOperatorCodes(): array
    {
        return ['ATV', 'TTV', 'VTV', 'STV', 'DTV'];
    }

    private function requestCount(string $type): int
    {
        $mobileCalls = count($this->mobileOperatorCodes()) * count(config('a1plan.circles', []));
        $dthCalls = count($this->dthOperatorCodes());

        return match ($type) {
            'mobile' => $mobileCalls,
            'dth' => $dthCalls,
            default => $mobileCalls + $dthCalls,
        };
    }

    /** @param array<string, mixed> $payload */
    private function providerPayloadSucceeded(array $payload): bool
    {
        $error = (string) ($payload['ERROR'] ?? $payload['error'] ?? '');
        $status = (string) ($payload['STATUS'] ?? $payload['status'] ?? '');

        return $error === '0' && ($status === '' || $status === '0');
    }

    /** @param array<string, mixed> $payload */
    private function providerMessage(array $payload): string
    {
        return mb_substr((string) ($payload['MESSAGE'] ?? $payload['Message'] ?? 'Provider rejected catalog request.'), 0, 180);
    }
}