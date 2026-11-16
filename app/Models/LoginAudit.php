<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel',
        'login_method',
        'ip_address',
        'device_name',
        'device_model',
        'platform',
        'os_version',
        'app_version',
        'user_agent',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
