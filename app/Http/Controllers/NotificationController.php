<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    // Fungsi untuk menandai notifikasi sudah dibaca
    public function markAsRead(DatabaseNotification $notification)
    {
        // Pastikan notifikasi ini milik user yang sedang login untuk keamanan
        if (auth()->id() !== $notification->notifiable_id) {
            abort(403, 'Akses ditolak.');
        }

        $notification->markAsRead();

        // Redirect kembali ke link yang ada di data notifikasi (misalnya halaman order)
        // Jika tidak ada link, redirect ke dashboard.
        return redirect()->to($notification->data['link'] ?? url('/'));
    }
}
