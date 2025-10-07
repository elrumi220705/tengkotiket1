<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_event',
        'deskripsi',
        'gambar',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'harga_dasar',
        'kapasitas_total',
        'stok_tersedia',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'harga_dasar' => 'integer',
        'kapasitas_total' => 'integer',
        'stok_tersedia' => 'integer',
    ];

    public function isPublished()
    {
        return $this->status === 'published';
    }
}
