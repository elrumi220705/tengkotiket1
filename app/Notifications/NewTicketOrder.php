<?php

namespace App\Notifications;

use App\Models\TicketOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketOrder extends Notification implements ShouldQueue
{
    use Queueable;
    public TicketOrder $order;

    public function __construct(TicketOrder $order)
    {
        // 🔥 PERBAIKAN: Load relasi 'event' di constructor
        $this->order = $order->load('event');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Admin: Database (Dashboard) & Email
    }

    public function toMail(object $notifiable): MailMessage
    {
        // 🔥 PERBAIKAN: Ubah akses dari ->name menjadi ->nama_event
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal (Abaikan)';

        return (new MailMessage)
                    ->subject('🔔 PESANAN BARU MASUK: #' . $this->order->id)
                    ->greeting('Halo Admin,')
                    ->line('Pesanan tiket baru telah masuk dan menunggu verifikasi Anda:')
                    ->line('**Event:** ' . $eventName)
                    ->line('**Total:** Rp ' . number_format($this->order->total_price, 0, ',', '.'))
                    ->action('Verifikasi Pembayaran', route('admin.ticket-orders.index'))
                    ->line('Mohon segera dicek.');
    }

    public function toDatabase(object $notifiable): array
    {
        // Opsional: Load nama event untuk database notification juga
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal';

        return [
            'message' => 'Pesanan baru ' . $eventName . ' (ID #' . $this->order->id . ') menunggu verifikasi.',
            'link' => route('admin.ticket-orders.index'),
            'order_id' => $this->order->id,
        ];
    }
}
