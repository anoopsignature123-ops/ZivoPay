<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvestmentAdminController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NetworkController;
use App\Http\Controllers\Admin\PackageAdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\AdminGuest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes & Security Middlewares - ZIVO PAY
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Guest Admin Routes (Redirects to Dashboard if already logged in)
    Route::middleware(AdminGuest::class)->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Authenticated Admin Routes
    Route::middleware(AdminAuth::class)->group(function () {
        Route::get('/', [DashboardController::class, '__invoke']);
        Route::get('dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

        // Global Live Search Suggestions API
        Route::get('global-search-suggestions', [UserController::class, 'globalSearchSuggestions'])->name('global-search-suggestions');

        // User Management Routes
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('users/directory', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/add-fund', [UserController::class, 'addFund'])->name('users.add-fund');

        // Impersonate / Login as User Route
        Route::get('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');

        // Network & Binary Team Tree Routes
        Route::get('network/tree', [NetworkController::class, 'treeView'])->name('network.tree');

        // Investment & Daily ROI Admin Control Routes
        Route::get('investments', [InvestmentAdminController::class, 'investments'])->name('investments');
        Route::post('trigger-daily-roi', [InvestmentAdminController::class, 'triggerDailyRoi'])->name('trigger-daily-roi');

        // Capital Packages Admin Management Routes
        Route::get('packages', [PackageAdminController::class, 'index'])->name('packages.index');
        Route::get('packages/create', [PackageAdminController::class, 'create'])->name('packages.create');
        Route::post('packages', [PackageAdminController::class, 'store'])->name('packages.store');
        Route::get('packages/{package}/edit', [PackageAdminController::class, 'edit'])->name('packages.edit');
        Route::put('packages/{package}', [PackageAdminController::class, 'update'])->name('packages.update');
        Route::post('packages/{package}/toggle-status', [PackageAdminController::class, 'toggleStatus'])->name('packages.toggle-status');
        Route::delete('packages/{package}', [PackageAdminController::class, 'destroy'])->name('packages.destroy');

        // Admin Withdrawal Approvals Routes
        Route::get('withdrawals', [InvestmentAdminController::class, 'withdrawals'])->name('withdrawals');
        Route::post('withdrawals/{withdrawal}/status', [InvestmentAdminController::class, 'updateWithdrawalStatus'])->name('withdrawals.update');

        // Reports & Financial History Routes
        Route::get('reports/deposits', [ReportController::class, 'depositHistory'])->name('reports.deposits');
        Route::get('reports/transactions', [ReportController::class, 'transactionHistory'])->name('reports.transactions');
        Route::get('reports/subscriptions', [ReportController::class, 'subscriptionHistory'])->name('reports.subscriptions');
        Route::get('reports/subscription-direct', [ReportController::class, 'directSubscriptionHistory'])->name('reports.subscription-direct');
        Route::get('reports/subscription-team', [ReportController::class, 'teamSubscriptionHistory'])->name('reports.subscription-team');
        Route::get('reports/roi', [ReportController::class, 'roiHistory'])->name('reports.roi');
        Route::get('reports/level-direct', [ReportController::class, 'levelDirectHistory'])->name('reports.level-direct');
        Route::get('reports/direct-business', [ReportController::class, 'directBusinessHistory'])->name('reports.direct-business');
        Route::get('reports/level-roi', [ReportController::class, 'levelRoiHistory'])->name('reports.level-roi');
        Route::get('reports/rewards', [ReportController::class, 'rewardHistory'])->name('reports.rewards');

        // Profile & Password Management
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');

        // Logout Route
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});
