@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Daftar Ticket Orders</div>
        <div class="card-body">
            @if (session('ok'))
                <div class="alert alert-success">{{ session('ok') }}</div>
            @endif

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Event</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->event->nama_event }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $map = [
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $map[$order->status] ?? 'secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($order->payment_proof)
                                    <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            {{-- ... kolom Aksi --}}
                            <td class="text-end">
                                @if ($order->status === 'pending')
                                    <a href="{{ route('admin.ticket-orders.updateStatus', [$order, 'paid']) }}"
                                        class="btn btn-sm btn-success me-2">Verifikasi</a>
                                    <a href="{{ route('admin.ticket-orders.updateStatus', [$order, 'rejected']) }}"
                                        class="btn btn-sm btn-danger">Tolak</a>
                                @elseif($order->status === 'paid')
                                    <span class="badge bg-success me-2">Terverifikasi</span>
                                    <a href="{{ route('admin.ticket-orders.tickets', $order) }}"
                                        class="btn btn-sm btn-outline-primary me-2">Lihat Tiket</a>
                                    <a href="{{ route('admin.ticket-orders.updateStatus', [$order, 'pending']) }}"
                                        class="btn btn-sm btn-outline-secondary">Set Pending</a>
                                @elseif($order->status === 'rejected')
                                    <span class="badge bg-danger me-2">Ditolak</span>
                                    <a href="{{ route('admin.ticket-orders.updateStatus', [$order, 'pending']) }}"
                                        class="btn btn-sm btn-outline-secondary">Set Pending</a>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
