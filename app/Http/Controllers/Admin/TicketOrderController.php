<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketOrder;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // 🔥 TAMBAH INI
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Import 2 Notifikasi Verifikasi
use App\Notifications\PaymentVerified;
use App\Notifications\PaymentRejected;

class TicketOrderController extends Controller
{
    public function index()
    {
        $orders = TicketOrder::with('event','user')->latest()->get();

        // 🔥 TAMBAHAN WAJIB: Ambil notifikasi yang belum dibaca
        $unreadNotifications = Auth::user()->unreadNotifications;

        // Kirim $orders DAN $unreadNotifications ke view
        return view('admin.ticket_orders.index', compact('orders', 'unreadNotifications'));
    }

    public function updateStatus(TicketOrder $ticketOrder, $status)
    {
        $allowedStatuses = ['pending', 'paid', 'rejected'];
        if (!in_array($status, $allowedStatuses)) {
            return back()->with('error', 'Status pesanan tidak valid.');
        }

        $oldStatus = $ticketOrder->status;
        $userToNotify = $ticketOrder->user; // Simpan user untuk notifikasi setelah transaksi

        try {
            DB::transaction(function () use ($ticketOrder, $status, $oldStatus) {
                // ... (Logika kunci event, cek stok, dan update stok) ...
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
                            $ticket = Ticket::create([
                                'ticket_order_id' => $ticketOrder->id,
                                'code'            => $code,
                                'qr_path'         => '',
                            ]);

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

                // Update status terakhir (di dalam transaksi)
                $ticketOrder->update(['status' => $status]);
            });

            // ---------------------------------------------
            // 🔥 LOGIKA NOTIFIKASI: STATUS BERUBAH (DI LUAR DB::transaction)
            // ---------------------------------------------
            $message = 'Status pesanan berhasil diperbarui menjadi: ' . ucfirst($status);

            if ($oldStatus !== $status) {
                if ($status === 'paid') {
                    // Kirim notifikasi LUNAS (PaymentVerified) ke User
                    $userToNotify->notify(new PaymentVerified($ticketOrder));
                    $message = 'Status pesanan #' . $ticketOrder->id . ' berhasil diubah menjadi LUNAS (Paid). Notifikasi ke pengguna terkirim.';

                } elseif ($status === 'rejected') {
                    // Kirim notifikasi DITOLAK (PaymentRejected) ke User
                    // User model perlu di-load ulang untuk memastikan notifikasi terkirim dengan benar jika objek userToNotify sudah di luar scope
                    $userToNotify->notify(new PaymentRejected($ticketOrder));
                    $message = 'Status pesanan #' . $ticketOrder->id . ' berhasil diubah menjadi DITOLAK (Rejected). Notifikasi ke pengguna terkirim.';
                }
            }

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui status. ' . $e->getMessage());
        }

        return back()->with('ok', $message);
    }

    public function tickets(TicketOrder $ticketOrder)
    {
        $ticketOrder->load('tickets','event','user');

        // 🔥 TAMBAHAN WAJIB: Ambil notifikasi yang belum dibaca (Karena ini juga memakai layout admin)
        $unreadNotifications = Auth::user()->unreadNotifications;

        return view('admin.ticket_orders.tickets', compact('ticketOrder', 'unreadNotifications'));
    }
}
