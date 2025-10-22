<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketOrder;
use App\Notifications\PaymentVerified;
use App\Notifications\RefundProcessed;
use Illuminate\Http\Request;

class AdminTicketOrderController extends Controller
{
    /**
     * Tampilkan semua pesanan tiket
     */
    public function index()
    {
        $orders = TicketOrder::with(['event', 'user'])->latest()->paginate(10);
        return view('admin.pages.ticket_orders.index', compact('orders'));
    }

    /**
     * Update status order (paid / pending / rejected)
     */
    public function updateStatus(TicketOrder $ticketOrder, $status)
    {
        $ticketOrder->update(['status' => $status]);

        // Kalau status "paid", kirim notifikasi PaymentVerified
        if ($status === 'paid') {
            $ticketOrder->user->notify(new PaymentVerified($ticketOrder));
        }

        return back()->with('success', 'Status pesanan #' . $ticketOrder->id . ' diubah ke ' . $status);
    }

    /**
     * Refund pesanan tiket
     */
    public function refund(TicketOrder $ticketOrder)
    {
        $ticketOrder->update(['status' => 'refunded']);
        $ticketOrder->event->increment('stok_tersedia', $ticketOrder->quantity);

        $ticketOrder->user->notify(new RefundProcessed($ticketOrder));

        return back()->with('success', 'Pesanan #' . $ticketOrder->id . ' berhasil direfund.');
    }
}
