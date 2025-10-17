<?php

namespace App\Notifications;

use App\Models\TicketOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification implements ShouldQueue
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
        return ['database', 'mail']; // User: Database & Email
    }

    public function toMail(object $notifiable): MailMessage
    {
        // 🔥 PERBAIKAN: Ubah akses dari ->name menjadi ->nama_event
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal';

        return (new MailMessage)
                    ->subject('❌ PEMBAYARAN DITOLAK: Pesanan Dibatalkan')
                    ->greeting('Perhatian ' . $this->order->user->name . ',')
                    ->line('Pesanan tiket Anda untuk **' . $eventName . '** dengan ID **#' . $this->order->id . '** telah **DITOLAK/DIBATALKAN**.')
                    ->line('Ini disebabkan bukti pembayaran tidak valid atau batas waktu terlampaui. Silakan buat pesanan baru.')
                    ->action('Beli Tiket Lagi', route('shop.index'));
    }

    public function toDatabase(object $notifiable): array
    {
        // Opsional: Load nama event untuk database notification juga
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal';

        return [
            'message' => 'Pesanan ' . $eventName . ' (ID #' . $this->order->id . ') dibatalkan. Pembayaran ditolak.',
            'link' => route('tickets.mine'),
            'order_id' => $this->order->id,
        ];
    }
}
