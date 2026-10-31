<?php

use App\Http\Controllers\Api\A1PlanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\IpaymentWalletWebhookController;
use App\Http\Controllers\Api\PageApiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\Shop\AddressController as ShopAddressController;
use App\Http\Controllers\Api\Shop\CartController as ShopCartController;
use App\Http\Controllers\Api\Shop\CatalogController as ShopCatalogController;
use App\Http\Controllers\Api\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Api\Shop\PromoCodeController as ShopPromoCodeController;
use App\Http\Controllers\Api\Shop\RatingController as ShopRatingController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\UpigatewayPaymentController;
use App\Http\Controllers\Api\UpigatewayWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Application API Routes - ZIVO PAY
|--------------------------------------------------------------------------
*/

// Public Authentication & Verification Endpoints
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [ForgotPasswordController::class, 'sendLink'])
    ->middleware('throttle:5,1');
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('login/send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('login/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('login-otp', [AuthController::class, 'verifyOtp']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('login/resend-otp', [AuthController::class, 'resendOtp']);
Route::get('check-sponsor', [AuthController::class, 'checkSponsor']);
Route::get('sponsor/{code?}', [AuthController::class, 'checkSponsor']);
Route::get('support/contact', [SupportController::class, 'contact']);
Route::get('support', [SupportController::class, 'contact']);
Route::get('pages', [PageApiController::class, 'index']);
Route::get('pages/{slug}', [PageApiController::class, 'show']);

// Public Webhook Callback Endpoint for Gateway
Route::match(['get', 'post'], 'recharge/callback', [RechargeController::class, 'callback']);
Route::post('webhook/upigateway', UpigatewayWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('api.upigateway.webhook');
Route::post('webhook/ipaymentwallet', IpaymentWalletWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('api.ipaymentwallet.webhook');

// Authenticated Sanctum Protected Endpoints
Route::middleware('auth:sanctum')->group(function () {
    // E-commerce shop APIs
    Route::prefix('shop')->group(function (): void {
        Route::get('categories', [ShopCatalogController::class, 'categories']);
        Route::get('categories/{category}/products', [ShopCatalogController::class, 'categoryProducts']);
        Route::get('products/search', [ShopCatalogController::class, 'search']);
        Route::get('products', [ShopCatalogController::class, 'home']);
        Route::get('products/{product}', [ShopCatalogController::class, 'show']);
        Route::post('ratings', [ShopRatingController::class, 'store'])->middleware('throttle:10,1');
        Route::get('my-cart', [ShopCartController::class, 'show']);
        Route::post('add-to-cart', [ShopCartController::class, 'addItem'])->middleware('throttle:60,1');
        Route::post('update-cart', [ShopCartController::class, 'updateItem']);
        Route::post('remove-from-cart', [ShopCartController::class, 'removeItem']);
        Route::post('clear-cart', [ShopCartController::class, 'clear']);
        Route::post('address-get', [ShopAddressController::class, 'index']);
        Route::post('addresses-store', [ShopAddressController::class, 'store']);
        Route::post('addresses-update', [ShopAddressController::class, 'update']);
        Route::post('addresses-delete', [ShopAddressController::class, 'destroy']);
        Route::post('promos-check', [ShopPromoCodeController::class, 'check'])->middleware('throttle:30,1');
        Route::post('place/orders', [ShopOrderController::class, 'store'])->middleware('throttle:10,1')->name('api.shop.orders.store');
        Route::get('orders', [ShopOrderController::class, 'index']);
        Route::get('orders/{order}', [ShopOrderController::class, 'show']);
        Route::post('orders-cancel', [ShopOrderController::class, 'cancel'])->middleware('throttle:10,1');
        Route::post('orders-return', [ShopOrderController::class, 'requestReturn'])->middleware('throttle:10,1');
    });

    // Help & Support Endpoints
    Route::get('support/tickets', [SupportController::class, 'tickets']);
    Route::post('support/tickets', [SupportController::class, 'createTicket']);
    Route::get('support/tickets/{id}', [SupportController::class, 'showTicket']);
    Route::get('support/tickets-detail/{id?}', [SupportController::class, 'showTicket']);

    // Dashboard, Home & Transaction Details API
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('home', [DashboardController::class, 'index']);

    Route::get('transaction/{id}', [DashboardController::class, 'transactionDetails']);
    Route::get('transaction-details/{id?}', [DashboardController::class, 'transactionDetails']);

    // User & Profile Endpoints
    Route::get('profile', [ProfileController::class, 'show']);
    Route::get('user', [ProfileController::class, 'show']);
    Route::post('profile-update', [ProfileController::class, 'update']);
    Route::post('profile', [ProfileController::class, 'update']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Add Fund / Deposit Endpoints
    Route::get('fund-history', [DepositController::class, 'index']);

    Route::get('add-fund-config', [UpigatewayPaymentController::class, 'configuration'])
        ->name('api.add-fund.config');
    Route::post('add-fund', [UpigatewayPaymentController::class, 'createOrder'])
        ->middleware('throttle:10,1')
        ->name('api.add-fund.start');
    Route::get('add-fund-status', [UpigatewayPaymentController::class, 'status'])
        ->middleware('throttle:30,1')
        ->name('api.add-fund.status');

    // Master & Unified Recharge Endpoints
    Route::prefix('recharge/a1plan')->name('api.recharge.a1plan.')->group(function (): void {
        // Catalog aliases for integrations that use the A1Plan-specific paths.
        // The established /recharge/* endpoints below remain available as well.
        Route::get('operators', [RechargeController::class, 'operators'])->name('catalog.operators');
        Route::get('circles', [RechargeController::class, 'circles'])->name('catalog.circles');
        Route::get('catalog/mobile', [RechargeController::class, 'a1PlanMobileCatalog'])->name('catalog.mobile');
        Route::get('catalog/dth', [RechargeController::class, 'a1PlanDthCatalog'])->name('catalog.dth');
        Route::post('dth/basic-info', [A1PlanController::class, 'dthBasicInfo'])->middleware('throttle:10,1')->name('dth.basic-info');
        Route::post('dth/info-check', [A1PlanController::class, 'dthInfoCheck'])->middleware('throttle:10,1')->name('dth.info-check');
        Route::post('dth/info-with-last-recharge', [A1PlanController::class, 'dthInfoWithLastRecharge'])->middleware('throttle:10,1')->name('dth.info-with-last-recharge');
        Route::post('dth/operator-check', [A1PlanController::class, 'dthOperatorCheck'])->middleware('throttle:10,1')
            ->name('dth.operator-check');
        Route::post('bills/electricity', [A1PlanController::class, 'electricityBillFetch'])->middleware('throttle:10,1')
            ->name('bills.electricity');
        Route::post('bills/postpaid', [A1PlanController::class, 'postpaidBillFetch'])->middleware('throttle:10,1')
            ->name('bills.postpaid');
        Route::post('bills/fastag', [A1PlanController::class, 'fastagBillFetch'])->middleware('throttle:10,1')->name('bills.fastag');

        Route::post('operator-circle', [A1PlanController::class, 'operatorCircleFetch'])->middleware('throttle:20,1')
            ->name('operator-circle');

        Route::post('plans', [A1PlanController::class, 'planFetch'])
            ->middleware('throttle:5,1')->name('plans');
        Route::post('roffer-check', [A1PlanController::class, 'rofferCheck'])->middleware('throttle:20,1')->name('roffer-check');
    });

    Route::get('recharge/operators', [RechargeController::class, 'operators']);
    Route::get('recharge/circles', [RechargeController::class, 'circles']);
    Route::get('recharge/fetch-operator', [RechargeController::class, 'fetchOperator']);
    Route::get('recharge/plans', [RechargeController::class, 'plans']);
    Route::get('recharge/history', [RechargeController::class, 'history']);
    Route::get('recharge/status/{orderId?}', [RechargeController::class, 'status']);

    // Category paths (for example, /recharge/operators/mobile) use this route.
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
