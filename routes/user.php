<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\IncomeController;
use App\Http\Controllers\User\InvestmentController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\NetworkController as UserNetworkController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\User\RewardController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\WithdrawalController;
use App\Http\Middleware\UserAuth;
use App\Http\Middleware\UserGuest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Member Routes & Security Middlewares - ZIVO PAY
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->group(function () {

    // Public Live Sponsor Check API Endpoint
    Route::get('check-sponsor', [RegisterController::class, 'checkSponsor'])->name('check-sponsor');

    // Registration Routes (Accessible by both Guests and Authenticated Members for Downline Registration)
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Guest User Routes (Redirects to User Dashboard if already logged in)
    Route::middleware(UserGuest::class)->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Authenticated User Routes (Requires User Authentication)
    Route::middleware(UserAuth::class)->group(function () {
        Route::get('/', [DashboardController::class, '__invoke']);
        Route::get('dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

        // Stop Impersonating Route
        Route::get('stop-impersonate', [\App\Http\Controllers\Admin\UserController::class, 'stopImpersonating'])->name('stop-impersonate');

        // Fund & Wallet Transfer Routes
        Route::get('wallet/transfer', [WalletController::class, 'showTransferForm'])->name('wallet.transfer');
        Route::post('wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer.store');

        // Buy Package ₹3,000 Activation Routes
        Route::get('package/buy', [WalletController::class, 'showBuyPackageForm'])->name('package.buy');
        Route::post('package/buy', [WalletController::class, 'activateSubscription'])->name('wallet.subscription.activate');

        // My Network Module Routes
        Route::get('network/direct', [UserNetworkController::class, 'directMembers'])->name('network.direct');
        Route::get('network/tree', [UserNetworkController::class, 'treeView'])->name('network.tree');

        // My Profile & Account Settings Routes
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');

        // Investment Packages Plan Routes
        Route::get('investment/plan', [InvestmentController::class, 'index'])->name('investment.index');
        Route::post('investment/plan', [InvestmentController::class, 'store'])->name('investment.store');

        // Income & Transaction Ledger Routes
        Route::get('income', [IncomeController::class, 'index'])->name('income.index');
        Route::get('reports/deposits', [IncomeController::class, 'depositHistory'])->name('reports.deposits');
        Route::get('reports/package-history', [IncomeController::class, 'packageHistory'])->name('reports.package-history');
        Route::get('reports/investments', [IncomeController::class, 'investmentHistory'])->name('reports.investments');
        Route::get('income/subscription-direct', [IncomeController::class, 'subscriptionDirectIncome'])->name('income.subscription-direct');
        Route::get('income/subscription-team', [IncomeController::class, 'subscriptionTeamIncome'])->name('income.subscription-team');
        Route::get('income/roi', [IncomeController::class, 'roiIncome'])->name('income.roi');
        Route::get('income/level-direct', [IncomeController::class, 'levelDirectIncome'])->name('income.level-direct');
        Route::get('income/direct-business', [IncomeController::class, 'directBusinessIncome'])->name('income.direct-business');
        Route::get('income/level-roi', [IncomeController::class, 'levelRoiIncome'])->name('income.level-roi');
        Route::get('rewards', [RewardController::class, 'index'])->name('rewards.index');

        // 24x7 Withdrawal Portal Routes
        Route::get('withdrawal', [WithdrawalController::class, 'index'])->name('withdrawal.index');
        Route::post('withdrawal', [WithdrawalController::class, 'store'])->name('withdrawal.store');

        // Logout Route
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});
