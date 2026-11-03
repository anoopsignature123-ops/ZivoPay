<?php

namespace App\Providers;

use App\Models\Withdrawal;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useTailwind();
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        View::composer('admin.layouts.sidebar', function (ViewInstance $view): void {
            $counts = Withdrawal::query()
                ->where('status', 'pending')
                ->selectRaw('COALESCE(SUM(CASE WHEN payment_method IN (?, ?) THEN 1 ELSE 0 END), 0) as inr', ['INR UPI', 'INR Bank Transfer'])
                ->selectRaw('COALESCE(SUM(CASE WHEN payment_method = ? THEN 1 ELSE 0 END), 0) as usd', ['USDT BEP20'])
                ->first();

            $view->with([
                'pendingInrWithdrawalCount' => (int) ($counts?->inr ?? 0),
                'pendingUsdWithdrawalCount' => (int) ($counts?->usd ?? 0),
            ]);
        });
    }
}
