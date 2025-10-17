@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $ticketOrder->id)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">Rincian Pesanan #{{ $ticketOrder->id }}</h4>
                    <p class="mb-0 small">{{ $ticketOrder->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <div class="card-body p-4">

                    {{-- Status Pesanan --}}
                    @php
                        $statusText = ucfirst($ticketOrder->status);
                        $statusClass = match($ticketOrder->status) {
                            'pending' => 'bg-warning text-dark',
                            'paid' => 'bg-success',
                            'rejected' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <h5 class="mb-0">Status:</h5>
                        <span class="badge {{ $statusClass }} fs-5 py-2 px-3">{{ $statusText }}</span>
                    </div>

                    {{-- Detail Event --}}
                    <h5 class="text-primary mb-3"><i class="bi bi-calendar-event me-2"></i> Detail Event</h5>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nama Event
                            <span class="fw-bold">{{ $ticketOrder->event->nama_event ?? 'Event Tidak Dikenal' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tanggal
                            <span>{{ \Carbon\Carbon::parse($ticketOrder->event->tanggal_mulai)->translatedFormat('d F Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Lokasi
                            <span>{{ $ticketOrder->event->lokasi ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Harga Satuan
                            <span>Rp{{ number_format($ticketOrder->event->price ?? 0, 0, ',', '.') }}</span>
                        </li>
                    </ul>

                    {{-- Detail Pembelian --}}
                    <h5 class="text-primary mb-3"><i class="bi bi-receipt me-2"></i> Detail Pembelian</h5>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Jumlah Tiket
                            <span class="fw-bold">{{ $ticketOrder->quantity }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            <h6 class="mb-0 fw-bold">TOTAL HARGA</h6>
                            <h6 class="mb-0 text-success fw-bold">Rp{{ number_format($ticketOrder->total_price, 0, ',', '.') }}</h6>
                        </li>
                    </ul>

                    {{-- Aksi --}}
                    <div class="mt-4 pt-3 border-top text-center">
                        @if ($ticketOrder->status === 'pending')
                            <div class="alert alert-warning">
                                Segera lakukan pembayaran agar pesanan Anda diproses.
                            </div>
                            {{-- Placeholder Aksi Pembayaran --}}
                            <a href="{{ asset('storage/' . $ticketOrder->payment_proof) }}" target="_blank" class="btn btn-outline-secondary mb-3">Lihat Bukti Bayar Diunggah</a>
                            <a href="#" class="btn btn-success btn-lg px-5">Lanjutkan ke Pembayaran (Ganti Bukti Bayar/Hubungi Admin)</a>
                        @elseif ($ticketOrder->status === 'rejected')
                            <div class="alert alert-danger">
                                Pesanan ini **DITOLAK** oleh admin. Silakan buat pesanan baru.
                            </div>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Kembali ke Shop</a>
                        @else
                            <div class="alert alert-success">
                                Pesanan ini sudah **LUNAS**. Tiket Anda dapat dilihat di halaman My Tickets.
                            </div>
                            <a href="{{ route('tickets.mine') }}" class="btn btn-success btn-lg px-5">Lihat Tiket Saya</a>
                        @endif
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    Terima kasih telah berbelanja.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
