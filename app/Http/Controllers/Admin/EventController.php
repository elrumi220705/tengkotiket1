<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.pages.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.pages.events.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'harga_dasar' => 'required|integer|min:1000',
            'kapasitas_total' => 'required|integer|min:1',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['_token', 'gambar_file']);
        $data['stok_tersedia'] = $request->kapasitas_total;
        $data['status'] = 'draft';

        if ($request->hasFile('gambar_file')) {
            $path = $request->file('gambar_file')->store('public/event_images');
            $data['gambar'] = str_replace('public/', '', $path);
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan!');
    }
}
