<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan - Quattro Coffee</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 0; }
        p.sub { color: #888; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
        .summary { margin-top: 12px; }
        .summary div { margin-bottom: 4px; }
    </style>
</head>
<body>
    <h1>Laporan Bisnis - Quattro Coffee</h1>
    <p class="sub">Periode {{ $periodLabel }}</p>

    <div class="summary">
        <div><strong>Total Pendapatan:</strong> Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
    </div>

    <h3>Produk Terlaris</h3>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->total_qty }} cup</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Belum ada penjualan produk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>