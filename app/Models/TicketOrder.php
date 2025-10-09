<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'quantity', 'total_price', 'status', 'payment_proof'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahan: relasi ke tiket
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
