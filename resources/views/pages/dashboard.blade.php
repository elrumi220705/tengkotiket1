@extends('layouts.app')

@section('title', $mainEvent->nama_event ?? 'Detail Event')

@section('content')
<div class="dashboard-container">
    <!-- ================= IMAGE GALLERY ================= -->
    <section class="image-gallery">
        <div class="container">
            <div class="gallery-grid">
                <!-- Main Image -->
                <div class="main-image">
                    <img src="{{ asset('storage/' . ($mainEvent->gambar ?? 'default.jpg')) }}" alt="{{ $mainEvent->nama_event }}">
                </div>

                <!-- Side Images -->
                <div class="side-images">
                    @foreach (['gambar2', 'gambar3', 'gambar4', 'gambar5'] as $img)
                        @if (!empty($mainEvent->$img))
                            <div class="small-image">
                                <img src="{{ asset('storage/' . $mainEvent->$img) }}" alt="{{ $mainEvent->nama_event }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ================= EVENT DETAILS ================= -->
    <section class="festival-details">
        <div class="container">
            <div class="details-grid">
                <!-- LEFT COLUMN -->
                <div class="festival-info">
                    <!-- Title -->
                    <div class="festival-header">
                        <h1 class="festival-title">{{ $mainEvent->nama_event }}</h1>
                    </div>

                    <!-- When & Where -->
                    <div class="info-section">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-header">
                                    <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
                                    <h3 class="info-title">Waktu</h3>
                                </div>
                                <div class="info-content">
                                    <div class="info-date">
                                        {{ $mainEvent->tanggal_mulai->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="info-time">
                                        {{ $mainEvent->tanggal_mulai->format('H:i') }} - {{ $mainEvent->tanggal_selesai->format('H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-header">
                                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                                    <h3 class="info-title">Lokasi</h3>
                                </div>
                                <div class="info-content">
                                    <div class="info-location">{{ $mainEvent->lokasi }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Event -->
                    <div class="info-section">
                        <h2 class="section-title">Tentang Event</h2>
                        <div class="event-description">
                            <p>{{ $mainEvent->deskripsi }}</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="festival-sidebar">
                    <!-- Harga Tiket -->
                    <div class="sidebar-card">
                        <h2 class="card-title">Harga Tiket</h2>
                        <div class="ticket-options">
                            <div class="ticket-option selected">
                                <div class="ticket-info">
                                    <h4 class="ticket-type">Regular</h4>
                                    <p class="ticket-desc">Tiket akses standar</p>
                                </div>
                                <div class="ticket-price">
                                    <span class="price">Rp {{ number_format($mainEvent->harga_dasar, 0, ',', '.') }}</span>
                                    <form action="{{ route('shop.checkout', $mainEvent->id) }}" method="GET">
                                        @csrf
                                        <button type="submit" class="btn-ticket">
                                            <i class="bi bi-cart"></i> Pilih & Beli Tiket
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lokasi Peta -->
                    <div class="sidebar-card">
                        <h2 class="card-title">Lokasi di Peta</h2>
                        <div class="location-map">
                            <iframe
                                src="https://www.google.com/maps?q={{ urlencode($mainEvent->lokasi) }}&output=embed"
                                width="100%" height="250" style="border:0; border-radius:10px;"
                                allowfullscreen="" loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= EVENT SERUPA ================= -->
            <div class="more-events-section mt-5">
                <h2 class="section-title">Event Serupa</h2>
                <div class="events-grid">
                    @forelse ($similarEvents as $event)
                        <div class="event-card">
                            <div class="event-card-header">
                                <div class="event-price-badge">
                                    <span>From</span> Rp {{ number_format($event->harga_dasar, 0, ',', '.') }}
                                </div>
                                <div class="event-image">
                                    <img src="{{ asset('storage/' . ($event->gambar ?? 'default.jpg')) }}" alt="{{ $event->nama_event }}">
                                </div>
                            </div>
                            <div class="event-content">
                                <div class="event-date">
                                    <span class="date-badge">{{ $event->tanggal_mulai->format('d') }}</span>
                                    <span class="date-text">{{ $event->tanggal_mulai->translatedFormat('M Y') }}</span>
                                </div>
                                <h3 class="event-title">{{ $event->nama_event }}</h3>
                                <p class="event-description-short">{{ Str::limit($event->deskripsi, 80) }}</p>
                                <a href="{{ route('shop.show', $event->id) }}" class="btn-buy-ticket">
                                    <i class="bi bi-ticket-perforated"></i> Beli Tiket
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada event serupa.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
