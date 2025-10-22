<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketOrder; // 🔥 Perlu diimpor

class MyTicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Tiket yang sudah Lunas (status: PAID)
        $tickets = Ticket::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status','paid');
            })
            ->with(['order.event'])
            ->latest()
            ->get();

        // 🔥 2. Pesanan yang Belum Lunas/Selesai (status: PENDING, REJECTED)
        $pendingOrders = TicketOrder::where('user_id', $user->id)
                                    ->whereIn('status', ['pending', 'rejected'])
                                    ->with('event')
                                    ->latest()
                                    ->get();

        // 3. Ambil Notifikasi
        $userNotifications = $user->notifications()->latest()->paginate(10);

        // Mengirimkan SEMUA data ke view
        return view('shop.my_tickets', compact('tickets', 'pendingOrders', 'userNotifications'));
    }

    /**
     * Menampilkan tiket digital satuan
     */
    public function show(Ticket $ticket)
    {
        // Hanya pemilik tiket yang boleh melihat
        if (auth()->id() !== $ticket->order->user_id) {
            return redirect()->route('tickets.mine')->with('error', 'Anda tidak punya akses ke tiket ini.');
        }

        return view('tickets.show', compact('ticket'));
    }
}
