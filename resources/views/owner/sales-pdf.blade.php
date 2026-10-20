<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Quattro Coffee</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 4px; }
        p { margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3ede4; }
        .text-right { text-align: right; }
        .summary { margin-top: 16px; }
        .summary td { border: none; padding: 4px 8px; }
    </style>
</head>
<body>
    <h2>Laporan Penjualan - Quattro Coffee</h2>
    <p>Periode {{ $periodLabel }}</p>

    <table class="summary">
        <tr><td><strong>Omzet Periode Ini</strong></td><td>: Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}</td></tr>
        <tr><td><strong>Total Transaksi</strong></td><td>: {{ $transaksiBulanIni }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th class="text-right">Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $order->customer_name }}</td>
                <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td>{{ $order->payment_status }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>