<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Application API Routes - ZIVO PAY
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('register', [AuthController::class, 'register'])->name('api.register');
Route::post('login', [AuthController::class, 'login'])->name('api.login');

// Authenticated Sanctum Protected Endpoints
Route::middleware('auth:sanctum')->group(function () {
    // User & Profile Endpoints
    Route::get('user', [ProfileController::class, 'show'])->name('api.user');
    Route::get('profile', [ProfileController::class, 'show'])->name('api.profile.show');
    Route::post('profile', [ProfileController::class, 'update'])->name('api.profile.update_alt');
    Route::post('profile-update', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::post('change-password', [ProfileController::class, 'changePassword'])->name('api.change_password');
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');

    // Add Fund / Deposit Endpoints
    Route::get('fund-history', [DepositController::class, 'index'])->name('api.add_fund.history');
    Route::post('add-fund', [DepositController::class, 'store'])->name('api.add_fund.store');
});