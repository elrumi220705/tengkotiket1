<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketOrder;
use Illuminate\Support\Facades\DB;

class TicketOrderController extends Controller
{
    public function index()
    {
        $orders = TicketOrder::with('event','user')->latest()->get();
        return view('admin.ticket_orders.index', compact('orders'));
    }

    public function updateStatus(TicketOrder $ticketOrder, $status)
    {
        $allowedStatuses = ['pending', 'paid', 'rejected'];
        if (!in_array($status, $allowedStatuses)) {
            return back()->with('error', 'Status pesanan tidak valid.');
        }

        $oldStatus = $ticketOrder->status;

        try {
            DB::transaction(function () use ($ticketOrder, $status, $oldStatus) {
                // Lock baris event agar stok konsisten
                $event = $ticketOrder->event()->lockForUpdate()->first();

                // Jika belum ada kolom stok, lewati penyesuaian stok (biar tidak error di skema lama)
                $hasStok = $event && array_key_exists('stok_tersedia', $event->getAttributes());

                if ($hasStok) {
                    // Transisi: pending/rejected -> paid (kurangi stok)
                    if ($oldStatus !== 'paid' && $status === 'paid') {
                        if ($event->stok_tersedia < $ticketOrder->quantity) {
                            // lempar exception agar transaksi rollback
                            throw new \RuntimeException('Stok tidak mencukupi untuk memverifikasi pesanan ini.');
                        }
                        $event->stok_tersedia -= $ticketOrder->quantity;
                        $event->save();
                    }

                    // Transisi: paid -> pending/rejected (kembalikan stok)
                    if ($oldStatus === 'paid' && $status !== 'paid') {
                        $event->stok_tersedia += $ticketOrder->quantity;
                        $event->save();
                    }
                }

                // Terakhir, update status order
                $ticketOrder->update(['status' => $status]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui status. '.$e->getMessage());
        }

        return back()->with('ok', 'Status pesanan berhasil diperbarui menjadi: '.ucfirst($status));
    }
}
