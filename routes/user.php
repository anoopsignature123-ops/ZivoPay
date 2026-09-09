<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DepositController;
use App\Http\Controllers\User\IncomeReportController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\NetworkController as UserNetworkController;
use App\Http\Controllers\User\PackageController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\User\TicketController as UserTicketController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\WithdrawalController;
use App\Http\Middleware\UserAuth;
use App\Http\Middleware\UserGuest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Member Routes & Security Middlewares
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->group(function () {

    // Public Live Sponsor Check API Endpoint
    Route::get('check-sponsor', [RegisterController::class, 'checkSponsor'])->name('check-sponsor');

    // Guest User Routes (Redirects to User Dashboard if already logged in)
    Route::middleware(UserGuest::class)->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);
    });

    // Authenticated User Routes (Requires User Authentication)
    Route::middleware(UserAuth::class)->group(function () {
        Route::get('/', [DashboardController::class, '__invoke']);
        Route::get('dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

        // Add Fund / Deposit Wallet Routes
        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::post('deposits', [DepositController::class, 'store'])->name('deposits.store');
        Route::get('deposits/history', [DepositController::class, 'history'])->name('deposits.history');
        Route::get('deposits/payment/{deposit}', [DepositController::class, 'paymentView'])->name('deposits.payment');
        Route::get('deposits/{deposit}/check-status', [DepositController::class, 'checkStatus'])->name('deposits.check-status');
        Route::post('deposits/{deposit}/simulate-payment', [DepositController::class, 'simulatePayment'])->name('deposits.simulate-payment');
        Route::get('deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show');

        // Earning Wallet Withdrawal Routes (PDF Slide 20: Min $10, 10% Deduction, USDT BEP20)
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::get('withdrawals/history', [WithdrawalController::class, 'history'])->name('withdrawals.history');

        // Buy Package & Investment History Routes
        Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
        Route::post('packages/buy', [PackageController::class, 'buy'])->name('packages.buy');
        Route::get('packages/history', [PackageController::class, 'history'])->name('packages.history');

        // Detailed Financial Transaction Log Route
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

        // Comprehensive User Income Reports Routes
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

        // My Network Module Routes
        Route::get('network/direct', [UserNetworkController::class, 'directMembers'])->name('network.direct');
        Route::get('network/tree', [UserNetworkController::class, 'treeView'])->name('network.tree');

        // My Profile & Account Settings Routes
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');

        Route::get('stop-impersonate', [AdminUserController::class, 'stopImpersonating'])->name('stop-impersonate');

        // User Support Ticket System Routes
        Route::get('tickets', [UserTicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/create', [UserTicketController::class, 'create'])->name('tickets.create');
        Route::post('tickets', [UserTicketController::class, 'store'])->name('tickets.store');
        Route::get('tickets/{ticket}', [UserTicketController::class, 'show'])->name('tickets.show');
        Route::post('tickets/{ticket}/reply', [UserTicketController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/close', [UserTicketController::class, 'close'])->name('tickets.close');

        // Logout Route
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});
