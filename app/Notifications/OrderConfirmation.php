<?php

namespace App\Notifications;

use App\Models\TicketOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
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
        return ['mail']; // User: Hanya Email
    }

    public function toMail(object $notifiable): MailMessage
    {
        // 🔥 PERBAIKAN: Ubah akses dari ->name menjadi ->nama_event
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal';

        return (new MailMessage)
                    ->subject('✅ KONFIRMASI PESANAN TIKET ANDA')
                    ->greeting('Halo ' . $this->order->user->name . ',')
                    ->line('Pesanan tiket Anda untuk **' . $eventName . '** telah kami terima.')
                    ->line('**STATUS: MENUNGGU PEMBAYARAN**')
                    ->line('**Total Pembayaran:** Rp ' . number_format($this->order->total_price, 0, ',', '.'))
                    ->action('Lihat Pesanan & Bayar', route('tickets.mine'))
                    ->line('Mohon segera lakukan pembayaran dan unggah bukti transfer. Batas waktu pembayaran berlaku.');
    }
}
