@extends('layouts.app')

@section('title', $event->nama_event)
@section('page-title', 'Detail Event')

@push('styles')
<style>
  .event-detail-wrap { max-width: 980px; margin: 0 auto; }
  .event-card {
    border-radius: 14px; overflow: hidden; background: #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
  }
  .event-image { width:100%; height:360px; object-fit:cover; display:block; }
  .event-body { padding: 20px 22px 8px; }
  .event-title { font-weight:800; font-size:28px; color:#1f2d3d; margin-bottom:6px; }
  .event-meta { color:#697386; font-size:14px; margin: 2px 0; display:flex; align-items:center; gap:8px; }
  .event-desc { color:#4b5563; line-height:1.6; margin-top:12px; }

  .event-footer {
    display:flex; justify-content:space-between; align-items:center;
    padding: 14px 22px 20px;
  }
  .price-label { font-size:12px; color:#6b7280; }
  .price-value { font-size:22px; font-weight:800; color:#0d6efd; }

  /* section kecil di bawah untuk info cepat */
  .quick-stats { display:grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-top:14px; }
  .qs-item {
    background:#f8fafc; border:1px solid #eef2f7; border-radius:10px; padding:10px 12px;
    display:flex; gap:10px; align-items:flex-start;
  }
  .qs-item i { font-size:18px; color:#0d6efd; margin-top:2px; }
  .qs-item .qs-title { font-size:12px; color:#6b7280; margin-bottom:2px; }
  .qs-item .qs-value { font-weight:600; color:#1f2d3d; }
</style>
@endpush

@section('content')
<div class="event-detail-wrap mt-4">

  {{-- Card Detail Event --}}
  <div class="event-card">
    @php
      $imageUrl = $event->gambar ? asset('storage/'.$event->gambar) : asset('images/default-event.jpg');
    @endphp
    <img src="{{ $imageUrl }}" alt="{{ $event->nama_event }}" class="event-image">

    <div class="event-body">
      <h1 class="event-title">{{ $event->nama_event }}</h1>

      <div class="event-meta">
        <i class="bi bi-geo-alt"></i>
        <span>{{ $event->lokasi }}</span>
      </div>
      <div class="event-meta">
        <i class="bi bi-calendar"></i>
        <span>{{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y H:i') }}</span>
      </div>

      @if(!empty($event->deskripsi))
        <p class="event-desc">{{ $event->deskripsi }}</p>
      @endif

      {{-- Quick stats (opsional: stok/kapasitas kalau ada kolomnya) --}}
      <div class="quick-stats">
        <div class="qs-item">
          <i class="bi bi-cash-coin"></i>
          <div>
            <div class="qs-title">Harga Dasar</div>
            <div class="qs-value">Rp{{ number_format($event->harga_dasar,0,',','.') }}</div>
          </div>
        </div>

        @if(isset($event->kapasitas_total))
        <div class="qs-item">
          <i class="bi bi-people"></i>
          <div>
            <div class="qs-title">Kapasitas</div>
            <div class="qs-value">{{ $event->kapasitas_total }}</div>
          </div>
        </div>
        @endif

        @if(isset($event->stok_tersedia))
        <div class="qs-item">
          <i class="bi bi-box-seam"></i>
          <div>
            <div class="qs-title">Stok Tersedia</div>
            <div class="qs-value">{{ $event->stok_tersedia }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>

    <div class="event-footer">
      <div>
        <div class="price-label">Mulai dari</div>
        <div class="price-value">Rp{{ number_format($event->harga_dasar,0,',','.') }}</div>
      </div>
      <a href="{{ route('shop.checkout', $event) }}" class="btn btn-primary btn-lg">
        <i class="bi bi-ticket-perforated"></i> Beli Tiket
      </a>
    </div>
  </div>

  {{-- Link kembali --}}
  <div class="text-center mt-3">
    <a href="{{ route('shop.index') }}" class="text-decoration-none">
      <i class="bi bi-arrow-left-short"></i> Kembali ke daftar event
    </a>
  </div>
</div>
@endsection
