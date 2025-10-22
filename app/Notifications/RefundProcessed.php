<?php

namespace App\Notifications;

use App\Models\TicketOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public TicketOrder $order;

    public function __construct(TicketOrder $order)
    {
        $this->order = $order->load('event');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $eventName = $this->order->event->nama_event ?? 'Event';

        return (new MailMessage)
            ->subject('💸 Refund Pesanan Tiket Berhasil')
            ->greeting('Halo ' . $this->order->user->name . ',')
            ->line('Pesanan tiket Anda untuk event **' . $eventName . '** berhasil direfund.')
            ->line('Jumlah tiket: ' . $this->order->quantity)
            ->line('Total: Rp ' . number_format($this->order->total_price, 0, ',', '.'))
            ->action('Lihat Pesanan Anda', route('tickets.mine'))
            ->line('Dana akan segera dikembalikan sesuai kebijakan.');
    }

    public function toDatabase(object $notifiable): array
    {
        $eventName = $this->order->event->nama_event ?? 'Event';

        return [
            'message' => 'Pesanan ' . $eventName . ' (ID #' . $this->order->id . ') berhasil direfund.',
            'link' => route('tickets.mine'),
            'order_id' => $this->order->id,
        ];
    }
}
