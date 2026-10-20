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
        .summary div { margin-bottom: 6px; }
        .formula { color: #666; font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Bisnis - Quattro Coffee</h1>
    <p class="sub">Periode {{ $periodLabel }}</p>

    <div class="summary">
        <div><strong>Total Pendapatan:</strong> Rp {{ number_format($financialSummary['pendapatan'], 0, ',', '.') }}</div>
        <div><strong>Harga Pokok Penjualan:</strong> Rp {{ number_format($financialSummary['hpp'], 0, ',', '.') }}</div>
        <div><strong>Laba Kotor:</strong> Rp {{ number_format($financialSummary['laba_kotor'], 0, ',', '.') }}</div>
        <div><strong>Biaya Operasional:</strong> Rp {{ number_format($financialSummary['biaya_operasional'], 0, ',', '.') }}</div>
        <div><strong>Laba Operasional:</strong> Rp {{ number_format($financialSummary['laba_operasional'], 0, ',', '.') }}</div>
        <div><strong>Pemasukan Lain:</strong> Rp {{ number_format($financialSummary['pemasukan_lain'], 0, ',', '.') }}</div>
        <div><strong>Laba Bersih:</strong> Rp {{ number_format($financialSummary['laba_bersih'], 0, ',', '.') }}</div>
        <div><strong>Margin Laba Bersih:</strong> {{ number_format($financialSummary['margin'], 1, ',', '.') }}%</div>
        <p class="formula">Rumus laba bersih = pendapatan - HPP - biaya operasional + pemasukan lain. Margin laba bersih = laba bersih / total pendapatan x 100.</p>
    </div>

    <h3>Penjualan dan Laba per Produk · Periode {{ $periodLabel }}</h3>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Terjual · {{ $periodLabel }}</th>
                <th>Harga Jual / Unit</th>
                <th>Harga Modal / Unit</th>
                <th>Laba Kotor / Unit</th>
                <th>Total Laba Kotor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->total_qty }} cup</td>
                    <td>Rp {{ number_format(round($product->total_sales / max(1, $product->total_qty)), 0, ',', '.') }}</td>
                    <td>Rp {{ number_format(round($product->total_cost / max(1, $product->total_qty)), 0, ',', '.') }}</td>
                    <td>Rp {{ number_format(round($product->total_profit / max(1, $product->total_qty)), 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($product->total_profit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada penjualan produk pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
