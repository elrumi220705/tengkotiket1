<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        // Filter publish
        if (Schema::hasColumn('events', 'status')) {
            $query->where('status', 'published');
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where('nama_event', 'like', '%' . $request->search . '%');
        }

        // Filter kategori
        if ($request->filled('kategori') && Schema::hasColumn('events', 'kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter lokasi
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $bulan = (int)$request->bulan;
            $query->whereMonth('tanggal_mulai', $bulan);
        }

        $events = $query->orderBy('tanggal_mulai', 'asc')->get();

        $kategoris = Event::select('kategori')->distinct()->pluck('kategori');
        $lokasis = Event::select('lokasi')->distinct()->pluck('lokasi');

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('pages.shop', compact('events', 'kategoris', 'lokasis', 'bulanList'));
    }

    public function show(Event $event)
    {
        return view('shop.show', compact('event'));
    }

    public function checkout(Event $event)
    {
        return view('shop.checkout', compact('event'));
    }
}
