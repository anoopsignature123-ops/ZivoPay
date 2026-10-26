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
        if ($user->is_subscription_active) {
            return false;
        }

        if ($user->deposit_wallet < self::ACTIVATION_FEE) {
            return false;
        }

        return DB::transaction(function () use ($user) {
            // Deduct ₹3,000 mandatory package activation fee from user's Fund Wallet
            $user->deposit_wallet -= self::ACTIVATION_FEE;
            $user->is_subscription_active = true;
            $user->status = 'active';
            $user->activated_at = now();
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

            // Distribute 30-Level Referral Incomes (L1: 0% band, L2: 8%, L3-L8: 2%, L9-L16: 1%, L17-L30: 0.5%)
            $this->distributeSubscriptionReferralIncome($user);

            return true;
        });
    }

    private function distributeSubscriptionReferralIncome(User $user): void
    {
        $currentSponsorCode = $user->sponsor_code;
        $level = 1;

        while (! empty($currentSponsorCode) && $level <= 30) {
            $sponsor = User::where('referral_code', $currentSponsorCode)->first();
            if (! $sponsor) {
                break;
            }

            // Only distribute subscription referral income if sponsor account is active
            if (! $sponsor->isActiveForIncome()) {
                $currentSponsorCode = $sponsor->sponsor_code;
                $level++;

                continue;
            }

            // L1: 0% (band — no income), L2: 8%, L3-L8: 2%, L9-L16: 1%, L17-L30: 0.5%
            if ($level === 1) {
                // L1 is banded — no income on ₹3,000 activation for direct referrer
                $currentSponsorCode = $sponsor->sponsor_code;
                $level++;

                continue;
            } elseif ($level === 2) {
                $percentage = 8.00;
            } elseif ($level <= 8) {
                $percentage = 2.00;
            } elseif ($level <= 16) {
                $percentage = 1.00;
            } else {
                $percentage = 0.50;
            }

            $commission = (self::ACTIVATION_FEE * $percentage) / 100;

            if ($commission > 0) {
                $sponsor->earning_wallet += $commission;
                $sponsor->save();

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $user->id,
                    'trx_id' => 'SUBINC-L'.$level.'-'.strtoupper(Str::random(8)),
                    'type' => 'subscription_level',
                    'wallet_type' => 'earning',
                    'trx_type' => '+',
                    'amount' => $commission,
                    'charge' => 0.00,
                    'post_balance' => $sponsor->earning_wallet,
                    'description' => "Level {$level} Team Level Bonus Referral Income ({$percentage}%) from ₹3,000 package activation of {$user->name} ({$user->referral_code})",
                ]);
            }

            $currentSponsorCode = $sponsor->sponsor_code;
            $level++;
        }
    }
}
