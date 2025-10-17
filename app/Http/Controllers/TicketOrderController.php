<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketOrder;
use App\Models\Event;
use App\Models\User;
use App\Notifications\NewTicketOrder;
use App\Notifications\OrderConfirmation;

class TicketOrderController extends Controller
{
    /**
     * Menyimpan pesanan tiket baru yang dibuat oleh pengguna.
     */
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

        $order = TicketOrder::create([
            'event_id'      => $event->id,
            'user_id'       => auth()->id(),
            'quantity'      => (int)$request->quantity,
            'total_price'   => $event->harga_dasar * (int)$request->quantity,
            'status'        => 'pending',       // admin yang set ke 'paid' saat verifikasi
            'payment_proof' => $path,
        ]);

        // ---------------------------------------------
        // 🔥 LOGIKA NOTIFIKASI: ORDER DIBUAT (STATUS PENDING)
        // ---------------------------------------------

        // 1. Kirim notifikasi konfirmasi ke Pengguna (via Email)
        auth()->user()->notify(new OrderConfirmation($order));

        // 2. Kirim notifikasi order baru ke Admin (via Database & Email)
        // Cari admin pertama di sistem
        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            $admin->notify(new NewTicketOrder($order));
        }

        // ---------------------------------------------

        return redirect()
            ->route('shop.index')
            ->with('ok', 'Pesanan dibuat. Cek email Anda, menunggu verifikasi admin.');
    }

    /**
     * Menampilkan detail pesanan tiket tertentu (untuk rute order.detail).
     * Menggunakan Route Model Binding untuk mengambil data berdasarkan ID.
     */
    public function show(TicketOrder $ticketOrder)
    {
        // Guardrail: Pastikan user hanya bisa melihat order miliknya sendiri
        if (auth()->id() !== $ticketOrder->user_id) {
            return redirect()->route('tickets.mine')->with('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Tampilkan view detail order. View ini HARUS dibuat di resources/views/shop/order_detail.blade.php
        return view('shop.order_detail', compact('ticketOrder'));
    }
}
