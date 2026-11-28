@extends('layouts.owner')
@section('title','Laporan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">BUSINESS REPORT</span><h1>Laporan</h1><p>Buat dan pantau laporan operasional serta keuangan.</p></div>
    <div class="head-actions">
        @include('owner.partials.period-picker')
        <a href="{{ route('owner.reports.export.customers') }}" class="btn btn-light"><i class="fa-solid fa-users"></i> Ekspor Pelanggan</a>
        <a href="{{ route('owner.reports.export.excel', ['start' => $start->toDateString(), 'end' => $end->toDateString()]) }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Excel</a>
        <a href="{{ route('owner.reports.export.pdf', ['start' => $start->toDateString(), 'end' => $end->toDateString()]) }}" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
</header>
<section class="panel"><div class="panel-head"><div><h2>Ringkasan Keuangan</h2><p>Periode {{ $periodLabel }}.</p></div></div>
<div class="metric-row"><span style="color:#287a4b;font-weight:700;">Total Pendapatan</span><strong style="color:#287a4b;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></div>
<div class="metric-row"><span style="color:#b42318;font-weight:700;">Harga Pokok Penjualan</span><strong style="color:#b42318;">Rp {{ number_format($financialSummary['hpp'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span style="color:{{ $financialSummary['laba_kotor'] > 0 ? '#287a4b' : ($financialSummary['laba_kotor'] < 0 ? '#b42318' : '#6b7280') }};font-weight:700;">Laba Kotor <small>(pendapatan − HPP)</small></span><strong style="color:{{ $financialSummary['laba_kotor'] > 0 ? '#287a4b' : ($financialSummary['laba_kotor'] < 0 ? '#b42318' : '#6b7280') }}">Rp {{ number_format($financialSummary['laba_kotor'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span style="color:#b42318;font-weight:700;">Biaya Operasional</span><strong style="color:#b42318;">Rp {{ number_format($financialSummary['biaya_operasional'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span style="color:{{ $financialSummary['laba_operasional'] > 0 ? '#287a4b' : ($financialSummary['laba_operasional'] < 0 ? '#b42318' : '#6b7280') }};font-weight:700;">Laba Operasional <small>(laba kotor − biaya operasional)</small></span><strong style="color:{{ $financialSummary['laba_operasional'] > 0 ? '#287a4b' : ($financialSummary['laba_operasional'] < 0 ? '#b42318' : '#6b7280') }}">Rp {{ number_format($financialSummary['laba_operasional'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span style="color:#287a4b;font-weight:700;">Pemasukan Lain</span><strong style="color:#287a4b;">Rp {{ number_format($financialSummary['pemasukan_lain'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span style="color:{{ $financialSummary['laba_bersih'] > 0 ? '#287a4b' : ($financialSummary['laba_bersih'] < 0 ? '#b42318' : '#6b7280') }};font-weight:700;">Laba Bersih</span><strong style="color:{{ $financialSummary['laba_bersih'] > 0 ? '#287a4b' : ($financialSummary['laba_bersih'] < 0 ? '#b42318' : '#6b7280') }}">Rp {{ number_format($financialSummary['laba_bersih'], 0, ',', '.') }}</strong></div>
<div class="progress-row" style="margin-top:22px"><div class="progress-top"><span style="color:{{ $financialSummary['margin'] < 0 ? '#b42318' : '#23945e' }};font-weight:700">Margin laba bersih</span><span style="color:{{ $financialSummary['margin'] < 0 ? '#b42318' : '#23945e' }}">{{ number_format($financialSummary['margin'], 1, ',', '.') }}%</span></div><div class="progress"><span style="width:{{ min(100, abs($financialSummary['margin'])) }}%;background:{{ $financialSummary['margin'] < 0 ? '#b42318' : '#23945e' }}"></span></div></div>
<p style="margin-top:12px;color:#8a7461;font-size:13px;">Rumus: laba bersih ÷ total pendapatan × 100. Perhitungan laba bersih = pendapatan − HPP − biaya operasional + pemasukan lain.</p>
@if($financialSummary['items_without_cost'] > 0)
    <p style="margin-top:14px;color:#8a5b00;font-size:13px;">{{ $financialSummary['items_without_cost'] }} unit terjual belum memiliki Harga Pokok Produksi; HPP produk tersebut dihitung Rp0 sampai nilainya diisi.</p>
@endif
</section>
<div class="panel" style="margin-top:22px"><div class="panel-head"><div><h2>Penjualan dan Laba per Produk</h2><p>Laba produk dan pembagian laba bersih · Periode {{ $periodLabel }}.</p></div><label class="search" style="max-width:300px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" data-search="#reportProducts tr.report-product" placeholder="Cari produk..." aria-label="Cari produk"></label></div>
<div class="table-wrap" id="reportProducts">
<table><thead><tr><th>Produk · {{ $periodLabel }}</th><th>Terjual</th><th>Harga Jual / Unit</th><th>Laba Kotor Produk</th><th>Laba Kotor / Unit</th><th>Laba Bersih Produk</th></tr></thead><tbody>
@php
    $productRevenueTotal = max(1, (float) $topProducts->sum('total_sales'));
@endphp
@forelse($topProducts as $product)
@php
    $productQuantity = max(1, (int) $product->total_qty);
    $productRevenueShare = (float) $product->total_sales / $productRevenueTotal;
    $productHasMissingCost = (int) $product->missing_cost_qty > 0;
    $productAllocatedExpense = round((float) $financialSummary['biaya_operasional'] * $productRevenueShare);
    $productNetProfit = (float) $product->total_profit - $productAllocatedExpense;
@endphp
<tr class="report-product">
    <td>{{ $product->product_name }}</td>
    <td>{{ $product->total_qty }} cup</td>
    <td>Rp {{ number_format(round($product->total_sales / $productQuantity), 0, ',', '.') }}</td>
    <td style="color:{{ $productHasMissingCost ? '#8a5b00' : ($product->total_profit < 0 ? '#b42318' : '#26734d') }};font-weight:700;">Rp {{ number_format($product->total_profit, 0, ',', '.') }}</td>
    <td style="color:{{ $productHasMissingCost ? '#8a5b00' : ($product->total_profit < 0 ? '#b42318' : '#26734d') }};font-weight:700;">Rp {{ number_format(round($product->total_profit / $productQuantity), 0, ',', '.') }}</td>
    <td style="color:{{ $productHasMissingCost ? '#8a5b00' : ($productNetProfit < 0 ? '#b42318' : '#26734d') }};font-weight:700;">Rp {{ number_format($productNetProfit, 0, ',', '.') }}</td>
</tr>
@empty
<tr><td colspan="6" style="padding:24px;text-align:center;color:#888;">Belum ada penjualan produk pada periode ini.</td></tr>
@endforelse
</tbody></table>
</div>
<p style="margin:12px 0 0;color:#8a7461;font-size:13px;line-height:1.7;">Rumus: laba kotor produk = omzet produk − HPP produk. Laba kotor per unit = laba kotor produk ÷ unit terjual. Laba bersih produk = laba kotor produk − alokasi biaya operasional berdasarkan porsi omzet; pemasukan lain dicatat terpisah.</p>
</div>
</div>
@endsection
