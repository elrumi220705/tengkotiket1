<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Penjualan Tiket</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .footer { text-align: right; font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Laporan Transaksi Penjualan Tiket Festival Indonesia</h2>
    <p><strong>Tanggal Cetak:</strong> {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Event</th>
                <th>Pemesan</th>
                <th>Jumlah Tiket</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Tanggal Order</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $order->event->nama_event ?? '-' }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <strong>Total Pendapatan:</strong> Rp{{ number_format($totalRevenue, 0, ',', '.') }}
    </div>
</body>
</html>
