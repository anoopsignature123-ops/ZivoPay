<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\IncomeReportController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NetworkController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\AdminGuest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes & Security Middlewares
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Guest Admin Routes (Redirects to Dashboard if already logged in)
    Route::middleware(AdminGuest::class)->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Authenticated Admin Routes (Requires Admin Authentication & Admin Role)
    Route::middleware(AdminAuth::class)->group(function () {
        Route::get('/', [DashboardController::class, '__invoke']);
        Route::get('dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

        // Global Live Search Suggestions API
        Route::get('global-search-suggestions', [UserController::class, 'globalSearchSuggestions'])->name('global-search-suggestions');

        // System Financial Transaction Logs & Audit Route
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

        // Comprehensive Income Reports Routes (7 Dex Trade Incomes)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('summary', [IncomeReportController::class, 'summary'])->name('summary');
            Route::get('roi', [IncomeReportController::class, 'roi'])->name('roi');
            Route::get('direct', [IncomeReportController::class, 'direct'])->name('direct');
            Route::get('matching', [IncomeReportController::class, 'matching'])->name('matching');
            Route::get('referral-roi', [IncomeReportController::class, 'referralRoi'])->name('referral-roi');
            Route::get('matching-roi', [IncomeReportController::class, 'matchingRoi'])->name('matching-roi');
            Route::get('upline-matching', [IncomeReportController::class, 'uplineMatching'])->name('upline-matching');
            Route::get('salary', [IncomeReportController::class, 'salary'])->name('salary');
        });

        // Package Management Routes
        Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
        Route::get('packages/history', [PackageController::class, 'history'])->name('packages.history');
        Route::get('packages/create', [PackageController::class, 'create'])->name('packages.create');
        Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
        Route::get('packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
        Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update');
        Route::post('packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

        // Deposit Requests Management Routes
        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::post('deposits/{deposit}/approve', [DepositController::class, 'approve'])->name('deposits.approve');
        Route::post('deposits/{deposit}/reject', [DepositController::class, 'reject'])->name('deposits.reject');

        // Withdrawal Requests Management Routes (PDF Slide 20)
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('withdrawals/bulk-approve', [WithdrawalController::class, 'bulkApprove'])->name('withdrawals.bulk-approve');
        Route::post('withdrawals/bulk-complete', [WithdrawalController::class, 'bulkComplete'])->name('withdrawals.bulk-complete');
        Route::post('withdrawals/bulk-reject', [WithdrawalController::class, 'bulkReject'])->name('withdrawals.bulk-reject');
        Route::post('withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('withdrawals/{withdrawal}/complete', [WithdrawalController::class, 'complete'])->name('withdrawals.complete');
        Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // User Management Routes
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('users/directory', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/add-fund', [UserController::class, 'addFund'])->name('users.add-fund');

        // Impersonate / Login as User Route
        Route::get('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');

        // Network & Binary Team Tree Routes
        Route::get('network/direct', [NetworkController::class, 'directMembers'])->name('network.direct');
        Route::get('network/tree', [NetworkController::class, 'treeView'])->name('network.tree');

        // Profile & Password Management
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');

        // Admin Support Ticket Management Routes
        Route::get('tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
        Route::post('tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');

        // System Gateway & Payment Settings Routes
        Route::get('settings/gateway', [SettingController::class, 'gatewaySettings'])->name('settings.gateway');
        Route::post('settings/gateway', [SettingController::class, 'updateGatewaySettings'])->name('settings.gateway.update');

        // Logout Route
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});
