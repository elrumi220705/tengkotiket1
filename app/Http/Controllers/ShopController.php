<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShopController extends Controller
{
    /**
     * /shop — list event
     */
    public function index()
    {
        $query = Event::query();

        // filter publish kalau kolom 'status' memang ada
        if (Schema::hasColumn('events', 'status')) {
            $query->where('status', 'published');
        }

        $events = $query->orderBy('tanggal_mulai', 'asc')->get();

        // -> resources/views/pages/shop.blade.php
        return view('pages.shop', compact('events'));
    }

    /**
     * /event/{event} — detail event
     */
    public function show(Event $event)
    {
        // -> resources/views/shop/show.blade.php
        return view('shop.show', compact('event'));
    }

    /**
     * /checkout/{event} — checkout manual payment
     */
    public function checkout(Event $event)
    {
        // -> resources/views/shop/checkout.blade.php
        return view('shop.checkout', compact('event'));
    }
}
