<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key with optional default fallback.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, ?string $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Check if payment testing / simulation mode is active.
     */
    public static function isPaymentTestMode(): bool
    {
        $dbMode = static::getValue('payment_test_mode');

        if ($dbMode !== null) {
            return filter_var($dbMode, FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    /**
     * Get active Payment Gateway API key.
     */
    public static function getPaymentApiKey(): string
    {
        $dbKey = static::getValue('payment_api_key');

        if (! empty(trim((string) $dbKey))) {
            return trim($dbKey);
        }

        return env('PAYMENT_GATEWAY_API_KEY', '');
    }

    /**
     * Get active USDT Wallet Address.
     */
    public static function getUsdtWalletAddress(): string
    {
        $dbAddress = static::getValue('usdt_wallet_address');

        if (! empty(trim((string) $dbAddress))) {
            return trim($dbAddress);
        }

        return env('USDT_WALLET_ADDRESS', '');
    }
}
