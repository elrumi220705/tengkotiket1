@extends('admin.layouts.app')

@section('title', 'Daftar Pesanan Tiket')
@section('page-title', 'Manajemen Pesanan Tiket')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="bi bi-receipt me-2"></i> Daftar Pesanan Tiket</h3>
    </div>
    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif

        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Event</th>
                    <th>Pemesan</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Bukti Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->event->nama_event ?? 'Event Dihapus' }}</td>
                        <td>{{ $order->user->name ?? 'User Dihapus' }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badgeClass = match($order->status) {
                                    'paid' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'rejected' => 'bg-danger',
                                    'refunded' => 'bg-secondary',
                                    default => 'bg-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>
                            @if($order->payment_proof)
                                <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            {{-- Tombol Update Status --}}
                            @if($order->status !== 'paid')
                                <form action="{{ route('admin.ticket-orders.updateStatus', [$order->id, 'paid']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Verifikasi</button>
                                </form>
                            @endif

                            @if($order->status !== 'rejected')
                                <form action="{{ route('admin.ticket-orders.updateStatus', [$order->id, 'rejected']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                </form>
                            @endif

                            {{-- Tombol Refund --}}
                            @if($order->status === 'paid')
                                <form action="{{ route('admin.ticket-orders.refund', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Refund</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada pesanan tiket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $orders->links() }}
    </div>
</div>
@endsection
