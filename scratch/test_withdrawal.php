<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Contracts\Console\Kernel;

$user = User::where('email', 'user@dextrade.com')->first();
if ($user) {
    $withdrawal = Withdrawal::create([
        'user_id' => $user->id,
        'trx_number' => 'WD-TEST'.rand(10000, 99999),
        'amount' => 100.00,
        'charge' => 10.00,
        'net_amount' => 90.00,
        'usdt_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
        'wallet_type' => 'earning_wallet',
        'status' => 'pending',
    ]);

    echo "SUCCESS: Pending withdrawal created with ID: {$withdrawal->id}\n";
    echo 'Pending Count in DB: '.Withdrawal::where('status', 'pending')->count()."\n";
} else {
    echo "User not found\n";
}
