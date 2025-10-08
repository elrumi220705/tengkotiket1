<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil event dengan tanggal terdekat (belum lewat)
        $mainEvent = Event::where('status', 'published')
        ->orderBy('tanggal_mulai', 'asc')
        ->first();

        $similarEvents = Event::where('status', 'published')
        ->where('id', '!=', $mainEvent?->id)
        ->orderBy('tanggal_mulai', 'asc')
        ->take(4)
        ->get();

        // Siapkan data tambahan (supaya tetap seperti tampilan awal)
        $festivalData = [
            'title' => $mainEvent->nama_event ?? 'Belum Ada Event',
            'rating' => 4.7,
            'date' => $mainEvent?->tanggal_mulai?->translatedFormat('l, d F Y') ?? '-',
            'time' => $mainEvent 
                ? $mainEvent->tanggal_mulai->format('H:i') . ' - ' . $mainEvent->tanggal_selesai->format('H:i') . ' WIB'
                : '-',
            'location' => $mainEvent->lokasi ?? '-',
            'description' => $mainEvent->deskripsi ?? '-',
            'min_age' => 17,
            'ticket_prices' => [
                [
                    'type' => 'Reguler',
                    'price' => 'Rp ' . number_format($mainEvent->harga_dasar ?? 0, 0, ',', '.'),
                    'description' => 'Tiket akses standar',
                ],
            ],
        ];

        return view('pages.dashboard', compact('festivalData', 'mainEvent', 'similarEvents'));
    }
}
