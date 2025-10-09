<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketOrder;
use App\Models\Event;

class TicketOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id'      => 'required|exists:events,id',
            'quantity'      => 'required|integer|min:1',
            // izinkan gambar & pdf, maks 2MB
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $event = Event::findOrFail($request->event_id);

        // pastikan stok cukup (cek dulu, belum mengurangi)
        if (isset($event->stok_tersedia) && $event->stok_tersedia < (int)$request->quantity) {
            return back()
                ->withErrors(['quantity' => 'Stok tidak mencukupi. Sisa: '.$event->stok_tersedia])
                ->withInput();
        }

        $path = $request->file('payment_proof')->store('payments', 'public');

        TicketOrder::create([
            'event_id'      => $event->id,
            'user_id'       => auth()->id(),
            'quantity'      => (int)$request->quantity,
            'total_price'   => $event->harga_dasar * (int)$request->quantity,
            'status'        => 'pending',          // admin yang set ke 'paid' saat verifikasi
            'payment_proof' => $path,
        ]);

        return redirect()
            ->route('shop.index')
            ->with('ok', 'Pesanan dibuat. Menunggu verifikasi admin.');
    }
}
