<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'mobile',
        'wallet_address',
        'referral_code',
        'sponsor_code',
        'position',
        'status',
        'is_subscription_active',
        'subscription_activated_at',
        'is_bot_active',
        'bot_activated_at',
        'deposit_wallet',
        'earning_wallet',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activated_at' => 'datetime',
            'is_subscription_active' => 'boolean',
            'subscription_activated_at' => 'datetime',
            'is_bot_active' => 'boolean',
            'bot_activated_at' => 'datetime',
            'password' => 'hashed',
            'deposit_wallet' => 'decimal:2',
            'earning_wallet' => 'decimal:2',
        ];
    }

    /**
     * Relationship with Role model.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship with Sponsor User by referral_code.
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_code', 'referral_code');
    }

    /**
     * Relationship for all direct referrals sponsored by this user.
     */
    public function directMembers(): HasMany
    {
        return $this->hasMany(User::class, 'sponsor_code', 'referral_code');
    }

    /**
     * Relationship for user investments.
     */
    public function investments(): HasMany
    {
        return $this->hasMany(UserInvestment::class);
    }

    /**
     * Relationship for user transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relationship for user rewards.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(UserReward::class);
    }

    /**
     * Relationship for user withdrawals.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Direct Sponsored Team Members.
     */
    public function directs(): HasMany
    {
        return $this->hasMany(User::class, 'sponsor_code', 'referral_code');
    }

    /**
     * Get all downline user IDs recursively for unilevel tree.
     */
    public function getBranchUserIds(): array
    {
        $ids = [$this->id];
        $directs = User::where('sponsor_code', $this->referral_code)->get();

        foreach ($directs as $directUser) {
            $ids = array_merge($ids, $directUser->getBranchUserIds());
        }

        return array_values(array_unique($ids));
    }

    /**
     * Helper to check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1 || ($this->role && $this->role->slug === 'admin');
    }

    /**
     * Generate unique random referral code (e.g., ZIVO-0967542).
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = 'ZIVO-'.str_pad((string) rand(100000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }
}
