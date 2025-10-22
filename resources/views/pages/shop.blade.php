@extends('layouts.app')

@section('title', 'Shop')
@section('page-title', 'Official Merchandise Shop')

@push('styles')
<link href="{{ asset('css/shop.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="shop-container">
    <div class="shop-header text-center mb-5">
        <h1 class="shop-title fw-bold">Shop Tiket Event</h1>
        <p class="shop-subtitle text-muted">Temukan dan beli tiket festival favoritmu di seluruh Indonesia.</p>
    </div>

    <!-- 🔍 Filter & Search -->
    <div class="filter-container mb-5">
        <form method="GET" action="{{ route('shop.index') }}" id="filterForm" class="filter-form">
            <div class="filter-grid">
                <div class="filter-item full-width">
                    <div class="search-wrapper">
                        <input type="text" name="search" class="filter-input-search" 
                            placeholder="Cari event atau festival..."
                            value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="filter-item">
                    <select name="kategori" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                {{ ucfirst($kategori) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <select name="lokasi" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>
                                {{ $lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <select name="bulan" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Bulan</option>
                        @foreach ($bulanList as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- 🧾 Event Grid -->
    <div class="event-list-grid">
        @if ($events->isNotEmpty())
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
                            <a href="{{ route('shop.show', $event) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-ticket-perforated"></i> Beli
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada event ditemukan.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
