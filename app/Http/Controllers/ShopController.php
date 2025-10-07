<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; // <-- PASTIKAN BARIS INI ADA!

class ShopController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'published')
                       ->orderBy('tanggal_mulai', 'asc')
                       ->get();

        return view('pages.shop', compact('events'));
    }
}
