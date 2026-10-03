<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RechargeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Application API Routes - ZIVO PAY
|--------------------------------------------------------------------------
*/

// Public Authentication & Verification Endpoints
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('check-sponsor', [AuthController::class, 'checkSponsor']);
Route::get('sponsor/{code?}', [AuthController::class, 'checkSponsor']);

// Public Webhook Callback Endpoint for Gateway
Route::match(['get', 'post'], 'recharge/callback', [RechargeController::class, 'callback']);

// Authenticated Sanctum Protected Endpoints
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard, Home & Transaction Details API
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('home', [DashboardController::class, 'index']);


    Route::get('transaction-details', [DashboardController::class, 'transactionDetails']);

    // User & Profile Endpoints
    Route::get('profile', [ProfileController::class, 'show']);
    Route::get('user', [ProfileController::class, 'show']);
    Route::post('profile-update', [ProfileController::class, 'update']);
    Route::post('profile', [ProfileController::class, 'update']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Add Fund / Deposit Endpoints
    Route::get('fund-history', [DepositController::class, 'index']);
    Route::post('add-fund', [DepositController::class, 'store']);

    // Master & Unified Recharge Endpoints
    Route::get('recharge/operators', [RechargeController::class, 'operators']);
    Route::get('recharge/circles', [RechargeController::class, 'circles']);
    Route::get('recharge/fetch-operator', [RechargeController::class, 'fetchOperator']);
    Route::get('recharge/plans', [RechargeController::class, 'plans']);
    Route::get('recharge/history', [RechargeController::class, 'history']);
    Route::get('recharge/status/{orderId?}', [RechargeController::class, 'status']);

    // Dedicated Category Operator Listing GET Endpoints
    Route::get('recharge/operators/mobile', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'mobile');
    Route::get('recharge/operators/dth', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'dth');
    Route::get('recharge/operators/postpaid', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'postpaid');
    Route::get('recharge/operators/electricity', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'electricity');
    Route::get('recharge/operators/gas', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'gas');
    Route::get('recharge/operators/fastag', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'fastag');
    Route::get('recharge/operators/insurance', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'insurance');
    Route::get('recharge/operators/voucher', [RechargeController::class, 'operatorsByCategory'])->defaults('category', 'voucher');
    Route::get('recharge/operators/{category}', [RechargeController::class, 'operatorsByCategory']);

    // Dedicated Recharge POST Action Endpoints
    Route::post('recharge/do', [RechargeController::class, 'store']);
    Route::post('recharge/mobile', [RechargeController::class, 'mobile']);
    Route::post('recharge/dth', [RechargeController::class, 'dth']);
    Route::post('recharge/postpaid', [RechargeController::class, 'postpaid']);
    Route::post('recharge/electricity', [RechargeController::class, 'electricity']);
    Route::post('recharge/gas', [RechargeController::class, 'gas']);
    Route::post('recharge/fastag', [RechargeController::class, 'fastag']);
    Route::post('recharge/insurance', [RechargeController::class, 'insurance']);
    Route::post('recharge/voucher', [RechargeController::class, 'voucher']);
});