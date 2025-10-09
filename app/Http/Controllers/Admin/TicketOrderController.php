<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketOrder;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                // ==== Kunci event untuk konsistensi stok ====
                $event = $ticketOrder->event()->lockForUpdate()->first();
                $hasStok = $event && array_key_exists('stok_tersedia', $event->getAttributes());

                // Transisi stok
                if ($hasStok) {
                    if ($oldStatus !== 'paid' && $status === 'paid') {
                        if ($event->stok_tersedia < $ticketOrder->quantity) {
                            throw new \RuntimeException('Stok tidak mencukupi untuk memverifikasi pesanan ini.');
                        }
                        $event->stok_tersedia -= $ticketOrder->quantity;
                        $event->save();
                    }
                    if ($oldStatus === 'paid' && $status !== 'paid') {
                        $event->stok_tersedia += $ticketOrder->quantity;
                        $event->save();
                    }
                }

                // ==== Tiket & QR ====
                if ($oldStatus !== 'paid' && $status === 'paid') {
                    // Generate tiket kalau belum ada
                    if ($ticketOrder->tickets()->count() === 0) {
                        for ($i = 0; $i < $ticketOrder->quantity; $i++) {
                            $code = (string) Str::uuid();

                            // simpan tiket dulu agar dapat ID
                            $ticket = Ticket::create([
                                'ticket_order_id' => $ticketOrder->id,
                                'code'            => $code,
                                'qr_path'         => '',
                            ]);

                            // payload QR (bisa dipakai untuk check-in)
                            $payload = json_encode([
                                'type'     => 'ticket',
                                'v'        => 1,
                                'ticketId' => $ticket->id,
                                'code'     => $code,
                                'orderId'  => $ticketOrder->id,
                                'eventId'  => $ticketOrder->event_id,
                                'userId'   => $ticketOrder->user_id,
                            ]);

                            // === Generate QR SVG & simpan ke storage/public ===
                            $fileName = 'qrcodes/'.$code.'.svg';
                            try {
                                $svg = QrCode::format('svg')->size(500)->margin(1)->generate($payload);
                                Storage::disk('public')->put($fileName, $svg);
                            } catch (\Throwable $e) {
                                throw new \RuntimeException('Gagal generate QR: '.$e->getMessage());
                            }

                            // simpan path relatif di DB
                            $ticket->update(['qr_path' => $fileName]);
                        }
                    }
                }

                // Kalau turun dari paid → hapus tiket & file-nya
                if ($oldStatus === 'paid' && $status !== 'paid') {
                    $ticketOrder->load('tickets');
                    foreach ($ticketOrder->tickets as $t) {
                        if ($t->qr_path && Storage::disk('public')->exists($t->qr_path)) {
                            Storage::disk('public')->delete($t->qr_path);
                        }
                    }
                    $ticketOrder->tickets()->delete();
                }

                // Update status terakhir
                $ticketOrder->update(['status' => $status]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui status. '.$e->getMessage());
        }

        return back()->with('ok', 'Status pesanan berhasil diperbarui menjadi: '.ucfirst($status));
    }

    public function tickets(TicketOrder $ticketOrder)
    {
        $ticketOrder->load('tickets','event','user');
        return view('admin.ticket_orders.tickets', compact('ticketOrder'));
    }
}
