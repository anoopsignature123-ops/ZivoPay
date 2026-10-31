<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionService
{
    public const ACTIVATION_FEE = 3000.00;

    public function activateSubscription(User $user): bool
    {
        return DB::transaction(function () use ($user): bool {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($user->is_subscription_active || (float) $user->deposit_wallet < self::ACTIVATION_FEE) {
                return false;
            }

            // Deduct ₹3,000 mandatory package activation fee from user's Fund Wallet
            $user->deposit_wallet -= self::ACTIVATION_FEE;
            $user->shopping_wallet = round((float) $user->shopping_wallet + self::ACTIVATION_FEE, 2);
            $user->is_subscription_active = true;
            $user->status = 'active';
            $user->activated_at = now();
            $user->subscription_activated_at = now();
            $user->save();

            // Log Fund Wallet debit transaction for Zivo Family Kit ₹3,000 subscription
            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'SUB-'.strtoupper(Str::random(10)),
                'type' => 'subscription',
                'wallet_type' => 'fund',
                'trx_type' => '-',
                'amount' => self::ACTIVATION_FEE,
                'charge' => 0.00,
                'post_balance' => $user->deposit_wallet,
                'description' => 'Mandatory ₹3,000 Zivo Family Kit Account Activation Subscription',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'SHOPCREDIT-'.strtoupper(Str::random(12)),
                'type' => 'shopping_wallet_credit',
                'wallet_type' => 'shopping_wallet',
                'trx_type' => '+',
                'amount' => self::ACTIVATION_FEE,
                'charge' => 0.00,
                'post_balance' => $user->shopping_wallet,
                'description' => '₹3,000 Shopping Wallet credit from Zivo Family Kit activation',
            ]);

            return true;
        });
    }
}
