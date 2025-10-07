@extends('layouts.app')

@section('title', 'Shop')
@section('page-title', 'Official Merchandise Shop')

@push('styles')
<link href="{{ asset('css/shop.css') }}" rel="stylesheet">
<style>
    .event-card {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
        margin-bottom: 24px; /* 🔥 jarak antar event card */
    }
    .event-card:hover {
        transform: translateY(-4px);
    }
    .event-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .event-body {
        padding: 15px;
    }
    .event-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
    }
    .price-label {
        font-size: 12px;
        color: #666;
    }
    .price-value {
        font-size: 16px;
        font-weight: bold;
        color: #007bff;
    }

    /* 🔥 tambahan untuk grid agar ada spasi */
    .event-list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px; /* jarak horizontal & vertikal antar card */
    }
</style>
@endpush

@section('content')
<div class="shop-container">
    <div class="shop-header text-center mb-4">
        <h1 class="shop-title fw-bold">Shop Tiket Event</h1>
        <p class="shop-subtitle text-muted">Temukan dan beli tiket untuk festival Indonesia favorit Anda.</p>
    </div>

    {{-- Gunakan grid agar rapih --}}
    <div class="event-list-grid">
        @if (isset($events) && $events->isNotEmpty())
            @foreach ($events as $event)
                <div class="event-card">
                    <a href="{{ route('shop.show', $event) }}">
                        @php
                            $imageUrl = $event->gambar ? asset('storage/' . $event->gambar) : asset('images/default-event.jpg');
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $event->nama_event }}" class="event-image">
                    </a>
                    <div class="event-body">
                        <h5 class="event-name">{{ $event->nama_event }}</h5>
                        <p class="event-location text-muted mb-1">
                            <i class="bi bi-geo-alt"></i> {{ $event->lokasi }}
                        </p>
                        <p class="event-date text-muted mb-2">
                            <i class="bi bi-calendar"></i>
                            {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y') }}
                        </p>

                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Mulai dari</span><br>
                                <span class="price-value">Rp{{ number_format($event->harga_dasar, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('shop.show', $event) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-ticket-perforated"></i> Beli
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    Mohon maaf, saat ini belum ada event yang tersedia untuk dijual.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
