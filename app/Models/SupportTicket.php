<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category',
        'subject',
        'message',
        'priority',
        'status',
        'admin_reply',
        'replied_at',
        'attachment',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get user associated with support ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate unique ticket number (e.g. TKT100001).
     */
    public static function generateTicketNumber(): string
    {
        do {
            $ticketNumber = 'TKT'.rand(100000, 999999);
        } while (self::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }
}
