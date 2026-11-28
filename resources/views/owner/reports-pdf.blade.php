<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Bisnis - Quattro Coffee</title>
    <style>
        @page { margin: 34px 38px 48px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #30251d; line-height: 1.45; }
        .topline { height: 6px; background: #713f12; margin: -34px -38px 24px; }
        .brand { color: #9a5b18; font-size: 9px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; }
        h1 { margin: 5px 0 3px; font-size: 22px; color: #2f241c; }
        .subtitle { margin: 0; color: #806d5a; font-size: 10px; }
        .period { display: inline-block; margin-top: 12px; padding: 6px 10px; border-radius: 12px; background: #f6eadb; color: #713f12; font-size: 9px; font-weight: bold; }
        .section-title { margin: 22px 0 9px; color: #713f12; font-size: 12px; }
        .cards { width: 100%; border-spacing: 7px; margin: 3px -7px 0; }
        .cards td { width: 25%; padding: 11px 12px; border: 1px solid #eadcc9; border-radius: 7px; background: #fffdfa; vertical-align: top; }
        .cards .label { color: #806d5a; font-size: 8px; }
        .cards .value { margin-top: 4px; color: #30251d; font-size: 13px; font-weight: bold; }
        .cards .highlight { color: #713f12; }
        .metrics { width: 100%; border-collapse: collapse; margin-top: 9px; }
        .metrics td { padding: 7px 9px; border-bottom: 1px solid #eee5da; }
        .metrics td:last-child { text-align: right; font-weight: bold; }
        .metrics tr:last-child td { border-top: 1px solid #cba77e; border-bottom: 0; color: #713f12; font-size: 10px; font-weight: bold; }
        .formula { margin-top: 8px; color: #806d5a; font-size: 8px; }
        .products { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .products th { padding: 8px 7px; background: #713f12; color: #fff; text-align: left; font-size: 8px; }
        .products td { padding: 7px; border-bottom: 1px solid #eadcc9; }
        .products tbody tr:nth-child(even) { background: #fbf7f1; }
        .products .number { text-align: right; white-space: nowrap; }
        .empty { padding: 14px !important; color: #806d5a; text-align: center; }
        .footer { position: fixed; bottom: -29px; left: 0; right: 0; padding-top: 7px; border-top: 1px solid #eadcc9; color: #806d5a; font-size: 8px; }
        .footer .right { float: right; }
    </style>
</head>
<body>
    <div class="topline"></div>
    <div class="brand">Quattro Coffee · Business Report</div>
    <h1>Laporan Keuangan & Penjualan</h1>
    <p class="subtitle">Ringkasan performa bisnis dan penjualan produk.</p>
    <div class="period">Periode {{ $periodLabel }}</div>

    <h2 class="section-title">Ringkasan Keuangan</h2>
    <table class="cards">
        <tr>
            <td><div class="label">Total Pendapatan</div><div class="value">Rp {{ number_format($financialSummary['pendapatan'], 0, ',', '.') }}</div></td>
            <td><div class="label">Harga Pokok Penjualan</div><div class="value">Rp {{ number_format($financialSummary['hpp'], 0, ',', '.') }}</div></td>
            <td><div class="label">Laba Kotor</div><div class="value">Rp {{ number_format($financialSummary['laba_kotor'], 0, ',', '.') }}</div></td>
            <td><div class="label">Margin Laba Bersih</div><div class="value highlight">{{ number_format($financialSummary['margin'], 1, ',', '.') }}%</div></td>
        </tr>
    </table>
    <table class="metrics">
        <tr><td>Biaya Operasional</td><td>Rp {{ number_format($financialSummary['biaya_operasional'], 0, ',', '.') }}</td></tr>
        <tr><td>Laba Operasional</td><td>Rp {{ number_format($financialSummary['laba_operasional'], 0, ',', '.') }}</td></tr>
        <tr><td>Pemasukan Lain</td><td>Rp {{ number_format($financialSummary['pemasukan_lain'], 0, ',', '.') }}</td></tr>
        <tr><td>Laba Bersih</td><td>Rp {{ number_format($financialSummary['laba_bersih'], 0, ',', '.') }}</td></tr>
    </table>
    <p class="formula">Laba bersih = pendapatan − HPP − biaya operasional + pemasukan lain. Margin = laba bersih ÷ total pendapatan × 100.</p>
    @if($financialSummary['items_without_cost'] > 0)<p class="formula">{{ $financialSummary['items_without_cost'] }} unit terjual belum memiliki HPP; HPP produk tersebut dihitung Rp0 sampai nilainya diisi.</p>@endif

    <h2 class="section-title">Penjualan dan Laba per Produk</h2>
    <table class="products">
        <thead><tr><th>Produk</th><th class="number">Terjual</th><th class="number">Harga Jual / Unit</th><th class="number">Laba Kotor Produk</th><th class="number">Laba Kotor / Unit</th><th class="number">Laba Bersih Produk</th></tr></thead>
        <tbody>
            @php
                $productRevenueTotal = max(1, (float) $topProducts->sum('total_sales'));
            @endphp
            @forelse($topProducts as $product)
                @php
                    $productQuantity = max(1, (int) $product->total_qty);
                    $productRevenueShare = (float) $product->total_sales / $productRevenueTotal;
                    $productAllocatedExpense = round((float) $financialSummary['biaya_operasional'] * $productRevenueShare);
                    $productNetProfit = (float) $product->total_profit - $productAllocatedExpense;
                    $productHasMissingCost = (int) $product->missing_cost_qty > 0;
                @endphp
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td class="number">{{ number_format($product->total_qty, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format(round($product->total_sales / $productQuantity), 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($product->total_profit, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format(round($product->total_profit / $productQuantity), 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($productNetProfit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada penjualan produk pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="formula">Laba kotor produk = omzet produk − HPP produk. Laba kotor per unit = laba kotor produk ÷ unit terjual. Laba bersih produk = laba kotor produk − alokasi biaya operasional berdasarkan porsi omzet; pemasukan lain dicatat terpisah.</p>
    <div class="footer"><span>Quattro Coffee · Laporan internal</span><span class="right">Dicetak {{ now()->format('d/m/Y H:i') }}</span></div>
</body>
</html>
