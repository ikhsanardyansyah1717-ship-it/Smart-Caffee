@extends('layouts.owner')
@section('title','Laporan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">BUSINESS REPORT</span><h1>Laporan</h1><p>Buat dan pantau laporan operasional serta keuangan.</p></div>
    <div class="head-actions">
        @include('owner.partials.period-picker')
        <a href="{{ route('owner.reports.export.excel', ['start' => $start->toDateString(), 'end' => $end->toDateString()]) }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Excel</a>
        <a href="{{ route('owner.reports.export.pdf', ['start' => $start->toDateString(), 'end' => $end->toDateString()]) }}" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
</header>
<section class="panel"><div class="panel-head"><div><h2>Ringkasan Keuangan</h2><p>Periode {{ $periodLabel }}.</p></div></div>
<div class="metric-row"><span>Total Pendapatan</span><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Harga Pokok Penjualan</span><strong>Rp {{ number_format($financialSummary['hpp'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Laba Kotor <small>(Pendapatan − HPP)</small></span><strong>Rp {{ number_format($financialSummary['laba_kotor'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Biaya Operasional</span><strong>Rp {{ number_format($financialSummary['biaya_operasional'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Laba Operasional <small>(Laba kotor − biaya operasional)</small></span><strong>Rp {{ number_format($financialSummary['laba_operasional'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Pemasukan Lain</span><strong>Rp {{ number_format($financialSummary['pemasukan_lain'], 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Laba Bersih</span><strong>Rp {{ number_format($financialSummary['laba_bersih'], 0, ',', '.') }}</strong></div>
<div class="progress-row" style="margin-top:22px"><div class="progress-top"><span>Margin laba bersih</span><span>{{ number_format($financialSummary['margin'], 1, ',', '.') }}%</span></div><div class="progress"><span style="width:{{ min(100, abs($financialSummary['margin'])) }}%;{{ $financialSummary['margin'] < 0 ? 'background:#b42318;' : '' }}"></span></div></div>
<p style="margin-top:12px;color:#8a7461;font-size:13px;">Rumus: laba bersih ÷ total pendapatan × 100. Perhitungan laba bersih = pendapatan − HPP − biaya operasional + pemasukan lain.</p>
@if($financialSummary['items_without_cost'] > 0)
    <p style="margin-top:14px;color:#8a5b00;font-size:13px;">{{ $financialSummary['items_without_cost'] }} item penjualan belum memiliki harga pokok. Lengkapi harga pokok produk agar laba bersih lebih akurat.</p>
@endif
</section>
<div class="panel" style="margin-top:22px"><div class="panel-head"><div><h2>Penjualan dan Laba per Produk</h2><p>Harga rata-rata dan laba kotor per unit · Periode {{ $periodLabel }}.</p></div><label class="search" style="max-width:300px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" data-search="#reportProducts tr.report-product" placeholder="Cari produk..." aria-label="Cari produk"></label></div>
<div class="table-wrap" id="reportProducts">
<table><thead><tr><th>Produk · {{ $periodLabel }}</th><th>Terjual</th><th>Harga Jual / Unit</th><th>Harga Modal / Unit</th><th>Laba Kotor / Unit</th><th>Total Laba Kotor</th></tr></thead><tbody>
@forelse($topProducts as $product)
<tr class="report-product">
    <td>{{ $product->product_name }}</td>
    <td>{{ $product->total_qty }} cup</td>
    <td>Rp {{ number_format(round($product->total_sales / max(1, $product->total_qty)), 0, ',', '.') }}</td>
    <td>Rp {{ number_format(round($product->total_cost / max(1, $product->total_qty)), 0, ',', '.') }}</td>
    <td style="color:{{ $product->total_profit < 0 ? '#b42318' : '#26734d' }};font-weight:700;">Rp {{ number_format(round($product->total_profit / max(1, $product->total_qty)), 0, ',', '.') }}</td>
    <td style="color:{{ $product->total_profit < 0 ? '#b42318' : '#26734d' }};font-weight:700;">Rp {{ number_format($product->total_profit, 0, ',', '.') }}</td>
</tr>
@empty
<tr><td colspan="6" style="padding:24px;text-align:center;color:#888;">Belum ada penjualan produk pada periode ini.</td></tr>
@endforelse
</tbody></table>
</div>
<p style="margin:12px 0 0;color:#8a7461;font-size:13px;">Laba kotor per unit = harga jual rata-rata − harga modal rata-rata. Contoh: modal Rp 30.000 dan jual Rp 20.000 berarti rugi kotor Rp 10.000 per unit.</p>
</div>
</div>
@endsection
