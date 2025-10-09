@extends('layouts.app')

@section('title','My Tickets')
@section('page-title','My Tickets')

@push('styles')
<style>
  .tickets-wrap { max-width: 1100px; margin: 0 auto; }
  .ticket-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:20px; }
  .t-card { background:#fff; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,.08); overflow:hidden; }
  .t-body { padding:14px; }
  .t-title { font-weight:700; margin-bottom:2px; }
  .t-meta { font-size:13px; color:#6b7280; }
  .qr-box { display:flex; align-items:center; justify-content:center; padding:12px; background:#f8fafc; }
  .qr-img { width: 200px; height: 200px; object-fit: contain; }
  .used-badge { position:absolute; top:8px; right:8px; }
</style>
@endpush

@section('content')
<div class="tickets-wrap mt-3">
  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  @if($tickets->isEmpty())
    <div class="alert alert-info">Belum ada tiket. Beli tiket dan tunggu verifikasi admin.</div>
  @else
    <div class="ticket-grid">
      @foreach($tickets as $t)
        @php
          $event = $t->order->event;
          $path  = $t->qr_path ? asset('storage/'.$t->qr_path) : null;
        @endphp
        <div class="t-card position-relative">
          @if($t->used_at)
            <span class="badge bg-secondary used-badge">Used</span>
          @endif
          <div class="qr-box">
            @if($path)
              <img src="{{ $path }}" alt="QR {{ $t->code }}" class="qr-img">
            @else
              <small class="text-muted">QR tidak tersedia</small>
            @endif
          </div>
          <div class="t-body">
            <div class="t-title">{{ $event->nama_event }}</div>
            <div class="t-meta">
              <i class="bi bi-calendar"></i>
              {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y H:i') }}
            </div>
            <div class="t-meta">
              <i class="bi bi-geo-alt"></i> {{ $event->lokasi }}
            </div>
            <div class="t-meta">Kode: <code>{{ $t->code }}</code></div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection

