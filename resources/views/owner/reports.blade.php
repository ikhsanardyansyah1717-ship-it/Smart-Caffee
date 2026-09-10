@extends('layouts.owner')
@section('title','Laporan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">BUSINESS REPORT</span><h1>Laporan</h1><p>Buat dan pantau laporan operasional serta keuangan.</p></div>
    <div class="head-actions">
        <a href="{{ route('owner.reports.export.excel') }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Excel</a>
        <a href="{{ route('owner.reports.export.pdf') }}" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
</header>
<section class="panel"><div class="panel-head"><div><h2>Ringkasan Keuangan</h2><p>Periode {{ $periodLabel }}.</p></div></div>
<div class="metric-row"><span>Total Pendapatan</span><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></div>
<div class="metric-row"><span>Harga Pokok Penjualan</span><strong style="color:#999;">Belum tersedia</strong></div>
<div class="metric-row"><span>Biaya Operasional</span><strong style="color:#999;">Belum tersedia</strong></div>
<div class="metric-row"><span>Laba Bersih</span><strong style="color:#999;">Belum tersedia</strong></div>
<div class="progress-row" style="margin-top:22px"><div class="progress-top"><span>Margin laba bersih</span><span style="color:#999;">Belum tersedia</span></div><div class="progress"><span style="width:0%"></span></div></div>
</section>
<div class="panel" style="margin-top:22px"><div class="panel-head"><div><h2>Produk Terlaris</h2><p>Performa menu berdasarkan jumlah terjual (bulan ini).</p></div></div>
@forelse($topProducts as $product)
<div class="progress-row"><div class="progress-top"><span>{{ $product->product_name }}</span><span>{{ $product->total_qty }} cup</span></div><div class="progress"><span style="width:{{ round(($product->total_qty / $maxQty) * 100) }}%"></span></div></div>
@empty
<p style="padding:24px;text-align:center;color:#888;">Belum ada penjualan produk bulan ini.</p>
@endforelse
</div>
</div>
@endsection