@extends('layouts.app')

@section('title', $mainEvent->nama_event ?? 'Event Details')

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
                    <div class="small-image"><img src="{{ asset('images/pestapora1.jpg') }}" alt="Side Image 1"></div>
                    <div class="small-image"><img src="{{ asset('images/pestapora2.jpg') }}" alt="Side Image 2"></div>
                    <div class="small-image"><img src="{{ asset('images/pestapora3.jpg') }}" alt="Side Image 3"></div>
                    <div class="small-image"><img src="{{ asset('images/pestapora4.jpg') }}" alt="Side Image 4"></div>
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
                    <!-- Title & Rating -->
                    <div class="festival-header">
                        <h1 class="festival-title">{{ $mainEvent->nama_event }}</h1>
                        <div class="festival-actions">
                            <button class="btn-action"><i class="bi bi-heart"></i> Add to favorites</button>
                            <button class="btn-action"><i class="bi bi-share"></i> Share this event</button>
                            <div class="rating">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <span class="rating-text">4.7 Ratings</span>
                            </div>
                        </div>
                    </div>

                    <!-- When & Where -->
                    <div class="info-section">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-header">
                                    <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
                                    <h3 class="info-title">When</h3>
                                </div>
                                <div class="info-content">
                                    <div class="info-date">
                                        {{ $mainEvent->tanggal_mulai->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="info-time">
                                        {{ $mainEvent->tanggal_mulai->format('H:i') }} - {{ $mainEvent->tanggal_selesai->format('H:i') }}
                                    </div>
                                    <div class="info-timezone">(WIB - Western Indonesia Time)</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-header">
                                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                                    <h3 class="info-title">Where</h3>
                                </div>
                                <div class="info-content">
                                    <div class="info-location">{{ $mainEvent->lokasi }}</div>
                                    <div class="info-address">Indonesia</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Event -->
                    <div class="info-section">
                        <h2 class="section-title">About this event</h2>
                        <div class="event-description">
                            <p>{{ $mainEvent->deskripsi }}</p>
                            <p><strong>Lineup Artist:</strong> Hindia, Tulus, Niki, Reality Club, Sheila on 7, Noah, dll.</p>
                            <p><strong>Fasilitas:</strong> Area kuliner, merchandise, charging station, prayer room, dan medical center.</p>
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
                                    <!-- Tombol langsung ke halaman checkout -->
                                    <a href="{{ route('shop.checkout', $mainEvent->id) }}" 
                                       class="btn btn-success btn-ticket">
                                       🛒 Pilih & Beli Tiket
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="age-restriction">
                            <i class="bi bi-info-circle"></i>
                            Minimal usia 17 tahun untuk mengikuti event ini
                        </div>
                    </div>

                    <!-- Lokasi Peta -->
                    <div class="sidebar-card">
                        <h2 class="card-title">Lokasi di peta</h2>
                        <div class="location-map">
                            <iframe
                                src="https://www.google.com/maps?q={{ urlencode($mainEvent->lokasi) }}&output=embed"
                                width="100%" height="250" style="border:0; border-radius:10px;"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= EVENT SERUPA ================= -->
            <div class="more-events-section mt-5">
                <div class="more-events-header">
                    <h2 class="section-title">Event serupa lainnya</h2>
                    <div class="events-filters">
                        <select class="events-filter-select">
                            <option>Hari kerja ▼</option>
                            <option>Weekend</option>
                        </select>
                        <select class="events-filter-select">
                            <option>Jenis event ▼</option>
                            <option>Festival</option>
                            <option>Konser</option>
                        </select>
                        <select class="events-filter-select">
                            <option>Semua kategori ▼</option>
                            <option>Musik</option>
                            <option>Seni</option>
                        </select>
                    </div>
                </div>

                <div class="events-grid">
                    @forelse ($similarEvents as $event)
                        <div class="event-card">
                            <div class="event-card-header">
                                <div class="event-price-badge">
                                    <span>From</span> Rp {{ number_format($event->harga_dasar, 0, ',', '.') }}
                                </div>
                                <div class="event-actions">
                                    <div class="event-plus">+</div>
                                    <div class="event-favorite"><i class="bi bi-heart"></i></div>
                                </div>
                                <div class="event-image">
                                    <img src="{{ asset('storage/' . ($event->gambar ?? 'default.jpg')) }}" alt="{{ $event->nama_event }}">
                                </div>
                            </div>
                            <div class="event-content">
                                <div class="event-from">
                                    <span>From</span><span>ID</span>
                                </div>
                                <div class="event-date">
                                    <span class="date-badge">{{ $event->tanggal_mulai->format('d') }}</span>
                                    <span class="date-text">{{ $event->tanggal_mulai->translatedFormat('M Y') }}</span>
                                </div>
                                <h3 class="event-title">{{ $event->nama_event }}</h3>
                                <p class="event-description-short">{{ Str::limit($event->deskripsi, 80) }}</p>
                                <div class="event-time">
                                    <i class="bi bi-clock"></i> {{ $event->tanggal_mulai->format('H:i') }} WIB
                                </div>
                                <a href="{{ route('shop.checkout', $event->id) }}" class="btn-buy-ticket">
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
