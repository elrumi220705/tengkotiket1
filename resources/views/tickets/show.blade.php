@extends('layouts.app')

@section('title', 'Tiket Digital')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-gradient-primary text-white">
            <h3 class="mb-0"><i class="bi bi-ticket-detailed me-2"></i> Tiket Digital</h3>
        </div>
        <div class="card-body text-center">
            <h4 class="fw-bold">{{ $ticket->order->event->nama_event }}</h4>
            <p class="text-muted mb-1">
                <i class="bi bi-geo-alt"></i> {{ $ticket->order->event->lokasi }}
            </p>
            <p class="text-muted">
                <i class="bi bi-calendar-event"></i>
                {{ \Carbon\Carbon::parse($ticket->order->event->tanggal_mulai)->translatedFormat('d M Y H:i') }}
            </p>

            <hr>

            <h5>Atas Nama</h5>
            <p class="fw-bold">{{ $ticket->order->user->name }}</p>

            <h5>Kode Tiket</h5>
            <p class="fw-bold">{{ $ticket->id }}</p>

            {{-- QR Code --}}
            <div class="my-3">
                {!! QrCode::size(200)->generate(route('tickets.show', $ticket->id)) !!}
            </div>

            <small class="text-muted d-block">Tunjukkan QR Code ini saat memasuki acara.</small>
        </div>
    </div>
</div>
@endsection
