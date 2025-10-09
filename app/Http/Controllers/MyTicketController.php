<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class MyTicketController extends Controller
{
    public function index()
    {
        // tampilkan tiket milik user dari order status paid saja
        $tickets = Ticket::whereHas('order', function ($q) {
                $q->where('user_id', auth()->id())->where('status','paid');
            })
            ->with(['order.event'])
            ->latest()
            ->get();

        return view('shop.my_tickets', compact('tickets'));
    }
}
