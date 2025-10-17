<?php

namespace App\Notifications;

use App\Models\TicketOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerified extends Notification implements ShouldQueue
{
    use Queueable;

    public TicketOrder $order;

    /**
     * Create a new notification instance.
     *
     * @param TicketOrder $order
     */
    public function __construct(TicketOrder $order)
    {
        // Memuat relasi 'event' secara paksa (eager loading)
        // Ini memastikan $this->order->event tersedia saat toMail dipanggil.
        $this->order = $order->load('event');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // User: Database & Email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Menggunakan nama kolom yang benar: nama_event (bukan name)
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal';

        return (new MailMessage)
                    ->subject('🎉 PEMBAYARAN BERHASIL & TIKET SIAP!')
                    ->greeting('Selamat ' . $this->order->user->name . '!')
                    ->line('Pembayaran Anda untuk pesanan **' . $eventName . '** telah **BERHASIL DIVERIFIKASI**.')
                    ->line('Tiket digital Anda sudah tersedia di halaman My Tickets.')
                    ->action('Lihat Tiket Anda', route('tickets.mine'))
                    ->line('Sampai jumpa di event!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Event name untuk notifikasi DB (jika diperlukan)
        $eventName = $this->order->event->nama_event ?? 'Event Tidak Dikenal';

        return [
            'message' => 'Pembayaran pesanan ' . $eventName . ' (ID #' . $this->order->id . ') telah lunas. Tiket siap!',
            'link' => route('tickets.mine'),
            'order_id' => $this->order->id,
        ];
    }
}
