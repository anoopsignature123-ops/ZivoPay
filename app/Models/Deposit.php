<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deposit_ref',
        'amount',
        'payment_gateway',
        'wallet_address',
        'gateway_reference',
        'txn_hash',
        'proof_image',
        'status',
        'admin_notes',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Deposit $deposit) {
            if (empty($deposit->deposit_ref)) {
                $deposit->deposit_ref = 'DEP'.strtoupper(Str::random(10));
            }
        });
    }

    public function getDepositRefAttribute(): string
    {
        if (! empty($this->attributes['deposit_ref'])) {
            return $this->attributes['deposit_ref'];
        }

        return 'DEP'.strtoupper(substr(md5('DEP_KEY_'.$this->id.'_'.($this->created_at ? $this->created_at->timestamp : 0)), 0, 10));
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'reference_id')->where('type', 'deposit');
    }
}
