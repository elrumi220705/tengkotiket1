@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Daftar Ticket Orders</div>
        <div class="card-body">

            {{-- Menampilkan pesan sukses atau error dari Controller --}}
            @if (session('ok'))
                <div class="alert alert-success">{{ session('ok') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Order ID</th>
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
                    @php
                        // Definisikan map warna status di sini untuk kenyamanan
                        $map = [
                            'paid' => 'success',
                            'pending' => 'warning',
                            'rejected' => 'danger',
                        ];
                    @endphp

                    @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->event->nama_event }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>
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

                            {{-- Kolom Aksi dengan Form POST --}}
                            <td class="text-end">
                                @if ($order->status === 'pending')
                                    {{-- Tombol VERIFIKASI (Paid) --}}
                                    <form action="{{ route('admin.ticket-orders.updateStatus', ['ticketOrder' => $order->id, 'status' => 'paid']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-2" onclick="return confirm('Yakin verifikasi pembayaran pesanan #{{ $order->id }}?')">
                                            Verifikasi
                                        </button>
                                    </form>

                                    {{-- Tombol TOLAK (Rejected) --}}
                                    <form action="{{ route('admin.ticket-orders.updateStatus', ['ticketOrder' => $order->id, 'status' => 'rejected']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin tolak pesanan #{{ $order->id }}? Stok akan dikembalikan.')">
                                            Tolak
                                        </button>
                                    </form>

                                @elseif($order->status === 'paid')
                                    <span class="badge bg-success me-2">Terverifikasi</span>
                                    <a href="{{ route('admin.ticket-orders.tickets', $order) }}"
                                        class="btn btn-sm btn-outline-primary me-2">Lihat Tiket</a>

                                    {{-- Tombol SET PENDING (dari Paid) --}}
                                    <form action="{{ route('admin.ticket-orders.updateStatus', ['ticketOrder' => $order->id, 'status' => 'pending']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Yakin ubah ke PENDING? Tiket dan QR akan dihapus dan stok dikembalikan.')">
                                            Set Pending
                                        </button>
                                    </form>

                                @elseif($order->status === 'rejected')
                                    <span class="badge bg-danger me-2">Ditolak</span>

                                    {{-- Tombol SET PENDING (dari Rejected) --}}
                                    <form action="{{ route('admin.ticket-orders.updateStatus', ['ticketOrder' => $order->id, 'status' => 'pending']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Yakin ubah ke PENDING? Stok akan dikembalikan.')">
                                            Set Pending
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
