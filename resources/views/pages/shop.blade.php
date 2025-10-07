@extends('layouts.app')

@section('title', 'Shop')
@section('page-title', 'Official Merchandise Shop')

@push('styles')
<link href="{{ asset('css/shop.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="shop-container">
    <div class="shop-header">
        <h1 class="shop-title">Shop Tiket Event</h1>
        <p class="shop-subtitle">Temukan dan beli tiket untuk festival Indonesia favorit Anda.</p>
    </div>

    <hr>

    {{-- LOGIKA MENAMPILKAN EVENT --}}
    <div class="event-list-grid mt-4">
        @if (isset($events) && $events->isNotEmpty())
            {{-- Loop untuk menampilkan setiap Event --}}
            @foreach ($events as $event)
                <div class="event-card">
                    <a href="{{ route('shop.show', $event) }}">
                        @php
                            // Tampilkan gambar dari storage
                            $imageUrl = $event->gambar ? asset('storage/' . $event->gambar) : asset('images/default-event.jpg');
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $event->nama_event }}" class="event-image">
                    </a>
                    <div class="event-body">
                        <h5 class="event-name">{{ $event->nama_event }}</h5>
                        <p class="event-location"><i class="bi bi-geo-alt"></i> {{ $event->lokasi }}</p>

                        {{-- Menggunakan Carbon untuk format tanggal --}}
                        <p class="event-date"><i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y') }}</p>

                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Mulai dari</span>
                                <span class="price-value">Rp{{ number_format($event->harga_dasar, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('shop.show', $event) }}" class="btn btn-primary btn-sm">Beli Tiket</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info text-center" role="alert" style="grid-column: 1 / -1;">
                Mohon maaf, saat ini belum ada event yang tersedia untuk dijual.
            </div>
        @endif
    </div>

</div>
@endsection
