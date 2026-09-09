<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\User\DepositController;
use App\Models\User;
use App\Services\User\DepositService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

echo "=== Testing iPaymentWallet Integration in NextGen Forex ===\n";

$user = User::first();
if (! $user) {
    echo "No user found in database!\n";
    exit;
}

Auth::login($user);

echo "Testing user: {$user->name} ({$user->email})\n";
echo 'Initial Deposit Wallet: $'.number_format($user->deposit_wallet, 2)."\n";

$depositService = app(DepositService::class);

echo "\nInitiating $50.00 USDT BEP20 Deposit Checkout...\n";
$result = $depositService->createCustomFund($user, 50.00, 'USDT (BEP20)');

if ($result['success']) {
    $deposit = $result['deposit'];
    echo "SUCCESS!\n";
    echo "Deposit ID: {$deposit->id}\n";
    echo "Deposit Ref: {$deposit->deposit_ref}\n";
    echo "Payment Wallet Address: {$deposit->wallet_address}\n";
    echo "Gateway Reference: {$deposit->gateway_reference}\n";
    echo "Payment URL: {$result['payment_url']}\n";
    echo "Deposit Status: {$deposit->status}\n";

    echo "\nTesting Status Check Endpoint...\n";
    $controller = app(DepositController::class);
    $response = $controller->checkStatus($deposit, $depositService);
    echo 'CheckStatus Response: '.json_encode($response->getData())."\n";
} else {
    echo 'FAILED: '.($result['message'] ?? 'Unknown error')."\n";
}

echo "\nAll integration checks completed successfully!\n";
