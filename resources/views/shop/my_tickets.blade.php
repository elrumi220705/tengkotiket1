@extends('layouts.app')

@section('title','My Tickets')
@section('page-title','My Tickets')

@push('styles')
<style>
    /* Menggunakan Bootstrap dan sedikit custom style */
    .tickets-wrap { max-width: 1200px; margin: 0 auto; padding-bottom: 50px; }
    .ticket-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:25px; }

    /* Card untuk Tiket Lunas (QR) */
    .t-card {
        background:#fff;
        border: 1px solid #e0e0e0;
        border-radius:12px;
        box-shadow:0 8px 24px rgba(0,0,0,.08);
        overflow:hidden;
        transition: transform 0.2s;
    }
    .t-card:hover {
        transform: translateY(-3px);
    }
    .qr-box {
        display:flex;
        align-items:center;
        justify-content:center;
        padding:20px;
        background:#f8fafc;
        border-bottom: 1px dashed #e0e0e0;
    }
    .qr-img { width: 180px; height: 180px; object-fit: contain; }
    .t-body { padding:16px; }
    .t-title { font-size: 1.25rem; font-weight:700; margin-bottom:4px; color:#343a40; }
    .t-meta { font-size:14px; color:#6c757d; line-height: 1.5; margin-bottom: 2px; display: flex; align-items: center; }
    .t-meta i { margin-right: 6px; font-size: 14px; }
    .t-code code { font-size: 0.8rem; background-color: #f1f1f1; padding: 2px 6px; border-radius: 4px; }

    /* Badge & Notifikasi */
    .used-badge { position:absolute; top:15px; right:15px; z-index: 10; }
    .list-group-item-action:hover { background-color: #f8f9fa; }
    .bg-pending-subtle { background-color: #fff8e1 !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; }

    /* Card Order Pending */
    .order-pending-card {
        background: #fff;
        border: 1px solid #ffecb3; /* Warna border kuning */
        border-left: 5px solid #ffc107; /* Garis tebal kuning */
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,.05);
        transition: box-shadow 0.2s;
    }
    .order-pending-card:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,.1);
    }
    /* Gaya khusus untuk REJECTED */
    .order-pending-card.rejected {
        border-left: 5px solid #dc3545; /* Merah untuk Rejected */
        border-color: #f8d7da;
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="tickets-wrap mt-4">
    @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    {{-- 🔥 RIWAYAT NOTIFIKASI PENGGUNA (Area yang diperbaiki) --}}
    <div class="card mb-5 border-0 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-bell-fill me-2"></i> Riwayat Status Pesanan (Notifikasi)
        </div>
        {{-- Menggunakan list-group Bootstrap untuk tampilan yang rapi --}}
        <div class="list-group list-group-flush">
            @forelse($userNotifications as $notification)
                @php
                    $isNew = is_null($notification->read_at);
                    $msg = strtolower($notification->data['message']);

                    // Tentukan ikon & warna berdasarkan keyword pesan
                    if (strpos($msg, 'lunas') !== false || strpos( $msg, 'siap') !== false) {
                        $iconClass = 'bi-check-circle-fill text-success';
                        $listClass = '';
                    } elseif (strpos($msg, 'menunggu verifikasi') !== false || strpos($msg, 'menunggu pembayaran') !== false) {
                        $iconClass = 'bi-hourglass-split text-warning';
                        $listClass = '';
                    } elseif (strpos($msg, 'tolak') !== false || strpos($msg, 'dibatalkan') !== false) {
                        $iconClass = 'bi-x-octagon-fill text-danger';
                        $listClass = '';
                    } else {
                        $iconClass = 'bi-info-circle-fill text-primary';
                        $listClass = '';
                    }

                    if ($isNew) {
                       // Warna background lebih lembut untuk notif baru
                        $listClass = 'bg-pending-subtle';
                        $iconClass = str_replace('text-success', 'text-success-emphasis', $iconClass);
                        $iconClass = str_replace('text-warning', 'text-warning-emphasis', $iconClass);
                    }
                @endphp

                {{-- Tautan notifikasi kini memiliki styling list-group-item-action --}}
                <a href="{{ $notification->data['link'] ?? '#' }}"
                    class="list-group-item list-group-item-action {{ $listClass }}"
                    title="{{ $isNew ? 'Belum Dibaca' : 'Sudah Dibaca' }}"
                    {{-- Aksi klik untuk menandai sudah dibaca --}}
                    @if($isNew) onclick="event.preventDefault(); document.getElementById('read-form-{{ $notification->id }}').submit();" @endif
                    >

                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <h6 class="mb-0 d-flex align-items-center flex-grow-1">
                            <i class="bi {{ $iconClass }} me-2 fs-5"></i>
                            <span class="text-wrap">{{ $notification->data['message'] }}</span>
                            @if($isNew)
                                <span class="badge bg-danger ms-2">BARU</span>
                            @endif
                        </h6>
                        <small class="{{ $isNew ? 'text-dark' : 'text-muted' }} ms-3">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </a>

                {{-- Form tersembunyi untuk menandai sudah dibaca (menggunakan route PATCH) --}}
                <form id="read-form-{{ $notification->id }}" method="POST" action="{{ route('user.notifications.read', $notification->id) }}" style="display: none;">
                    @csrf
                    @method('PATCH')
                </form>
            @empty
                <div class="list-group-item text-center text-muted">
                    Tidak ada riwayat notifikasi terkait pesanan Anda.
                </div>
            @endforelse
        </div>
        @if(method_exists($userNotifications, 'links'))
            <div class="card-footer bg-white border-0">
                {{ $userNotifications->links() }}
            </div>
        @endif
    </div>
    {{-- 🔥 AKHIR PERBAIKAN NOTIFIKASI --}}

    {{-- TIKET YANG BELUM LUNAS (PENDING/REJECTED/dll) --}}
    <h5 class="mt-4 mb-3 d-flex align-items-center">
        <i class="bi bi-hourglass-split me-2 text-warning"></i> Pesanan Anda Yang Menunggu Proses ({{ $pendingOrders->count() }})
    </h5>

    @if($pendingOrders->isEmpty())
        <div class="alert alert-light border text-muted">Tidak ada pesanan yang sedang menunggu pembayaran/verifikasi.</div>
    @else
        @foreach($pendingOrders as $order)
            @php
                $isRejected = $order->status === 'rejected';
                $badgeClass = match($order->status) {
                    'pending' => 'bg-warning text-dark',
                    'rejected' => 'bg-danger',
                    default => 'bg-secondary'
                };
            @endphp

            <div class="order-pending-card d-flex justify-content-between align-items-center {{ $isRejected ? 'rejected' : '' }}">
                <div>
                    <h6 class="fw-bold mb-1 {{ $isRejected ? 'text-danger' : 'text-primary' }}">
                        {{ $order->event->nama_event ?? 'Event Tidak Dikenal' }}
                    </h6>
                    <p class="mb-1 text-muted small">Order ID: #{{ $order->id }} &bull; Qty: {{ $order->quantity }}</p>
                    <p class="mb-0 fs-5 fw-bold {{ $isRejected ? 'text-danger' : 'text-primary' }}">
                        Total: Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge {{ $badgeClass }} fs-6 mb-2">{{ ucfirst($order->status) }}</span>

                    @if ($isRejected)
                        <p class="text-danger small fw-bold mb-1">Pesanan ditolak admin.</p>
                        <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                            Lihat Detail (Ditolak)
                        </button>
                    @else
                        {{-- Menggunakan route('order.detail', ...) --}}
                        <a href="{{ route('order.detail', $order->id) }}" class="btn btn-sm btn-primary">
                            Lihat Detail & Bayar
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    <hr class="mt-5 mb-5">

    {{-- TIKET YANG SUDAH LUNAS --}}
    <h5 class="mt-4 mb-3 d-flex align-items-center">
        <i class="bi bi-ticket-perforated-fill me-2 text-success"></i> Tiket Anda Yang Sudah Lunas ({{ $tickets->count() }})
    </h5>

    @if($tickets->isEmpty())
        <div class="alert alert-info border text-muted">Belum ada tiket yang lunas. Tiket akan muncul di sini setelah pembayaran diverifikasi admin.</div>
    @else
        <div class="ticket-grid">
            @foreach($tickets as $t)
                @php
                    $event = $t->order->event;
                    // Pastikan QR path lengkap
                    $path = $t->qr_path ? asset('storage/'.$t->qr_path) : null;
                @endphp
                <div class="t-card position-relative">
                    @if($t->used_at)
                        <span class="badge bg-secondary used-badge"><i class="bi bi-check-circle-fill me-1"></i> Telah Digunakan</span>
                    @endif
                    <div class="qr-box">
                        @if($path)
                            <img src="{{ $path }}" alt="QR {{ $t->code }}" class="qr-img">
                        @else
                            <small class="text-muted">QR tidak tersedia</small>
                        @endif
                    </div>
                    <div class="t-body">
                        <div class="t-title">{{ $event->nama_event ?? 'Event Tidak Dikenal' }}</div>
                        <div class="t-meta">
                            <i class="bi bi-calendar"></i>
                            {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y H:i') }}
                        </div>
                        <div class="t-meta">
                            <i class="bi bi-geo-alt"></i> {{ $event->lokasi ?? 'Lokasi Tidak Diketahui' }}
                        </div>
                        <div class="t-meta t-code mt-2">
                            Kode: <code>{{ $t->code }}</code>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
