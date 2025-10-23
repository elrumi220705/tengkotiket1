<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketOrder;
use App\Models\Event;
use App\Models\User;
use App\Notifications\NewTicketOrder;
use App\Notifications\OrderConfirmation;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id'      => 'required|exists:events,id',
            'quantity'      => 'required|integer|min:1',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $event = Event::findOrFail($request->event_id);
        if (isset($event->stok_tersedia) && $event->stok_tersedia < (int)$request->quantity) {
            return back()
                ->withErrors(['quantity' => 'Stok tidak mencukupi. Sisa: '.$event->stok_tersedia])
                ->withInput();
        }
        $path = $request->file('payment_proof')->store('payments', 'public');
        $order = TicketOrder::create([
            'event_id'      => $event->id,
            'user_id'       => auth()->id(),
            'quantity'      => (int)$request->quantity,
            'total_price'   => $event->harga_dasar * (int)$request->quantity,
            'status'        => 'pending',
            'payment_proof' => $path,
        ]);
        auth()->user()->notify(new OrderConfirmation($order));

        // 2. Kirim notifikasi order baru ke Admin (via Database & Email)
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewTicketOrder($order));
        }
        return redirect()
            ->route('shop.index')
            ->with('ok', 'Pesanan dibuat. Cek email Anda, menunggu verifikasi admin.');
    }
    public function show(TicketOrder $ticketOrder)
    {
        if (auth()->id() !== $ticketOrder->user_id) {
            return redirect()->route('tickets.mine')->with('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }
        $ticketOrder->load(['event', 'user']);
        return view('shop.order_detail', compact('ticketOrder'));
    }
    public function exportPdf()
    {
        $orders = TicketOrder::with('event', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $orders->sum('total_price');
        $pdf = Pdf::loadView('admin.pdf.ticket_orders', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'date' => now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i'),
        ])->setPaper('a4', 'portrait');
        return $pdf->stream('laporan_transaksi_tiket.pdf');
    }
}
