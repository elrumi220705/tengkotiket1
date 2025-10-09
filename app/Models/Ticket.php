<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_order_id',
        'code',
        'qr_path',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(TicketOrder::class, 'ticket_order_id');
    }

    // Helper agar gampang dipakai di blade
    public function getIsUsedAttribute(): bool
    {
        return !is_null($this->used_at);
    }
}
