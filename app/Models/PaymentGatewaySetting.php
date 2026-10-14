<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'gateway', 'enabled', 'mode', 'test_key_id', 'test_key_secret', 'test_webhook_secret',
        'live_key_id', 'live_key_secret', 'live_webhook_secret',
    ];

    protected $hidden = ['test_key_secret', 'test_webhook_secret', 'live_key_secret', 'live_webhook_secret'];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'test_key_secret' => 'encrypted',
            'test_webhook_secret' => 'encrypted',
            'live_key_secret' => 'encrypted',
            'live_webhook_secret' => 'encrypted',
        ];
    }

    public static function razorpay(): self
    {
        return self::firstOrCreate(['gateway' => 'razorpay'], ['enabled' => false, 'mode' => 'test']);
    }

    public function isReady(): bool
    {
        return $this->enabled && filled($this->keyId()) && filled($this->keySecret());
    }

    public function keyId(): ?string
    {
        return $this->keyIdFor($this->mode);
    }

    public function keyIdFor(string $mode): ?string
    {
        if (! in_array($mode, ['test', 'live'], true)) {
            return null;
        }

        return $this->cleanCredential($this->{$mode.'_key_id'})
            ?? $this->cleanCredential((string) config("gateway.razorpay.{$mode}_key_id"));
    }

    public function keySecret(): ?string
    {
        return $this->keySecretFor($this->mode);
    }

    public function keySecretFor(string $mode): ?string
    {
        if (! in_array($mode, ['test', 'live'], true)) {
            return null;
        }

        return $this->cleanCredential($this->{$mode.'_key_secret'})
            ?? $this->cleanCredential((string) config("gateway.razorpay.{$mode}_key_secret"));
    }

    public function webhookSecretFor(string $mode): ?string
    {
        if (! in_array($mode, ['test', 'live'], true)) {
            return null;
        }

        return $this->cleanCredential($this->{$mode.'_webhook_secret'})
            ?? $this->cleanCredential((string) config("gateway.razorpay.{$mode}_webhook_secret"));
    }

    private function cleanCredential(?string $credential): ?string
    {
        $credential = trim((string) $credential);

        return $credential === '' ? null : $credential;
    }
}
