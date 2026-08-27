@extends('layouts.owner')
@section('title','Laporan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head"><div><span class="eyebrow">BUSINESS REPORT</span><h1>Laporan</h1><p>Buat dan pantau laporan operasional serta keuangan.</p></div><button class="btn btn-primary" data-demo="Laporan berhasil dibuat"><i class="fa-solid fa-file-export"></i> Export Laporan</button></header>
<div class="two-col">
<section class="panel"><div class="panel-head"><div><h2>Ringkasan Keuangan</h2><p>Periode Agustus 2026.</p></div></div>
<div class="metric-row"><span>Total Pendapatan</span><strong>Rp 148.620.000</strong></div>
<div class="metric-row"><span>Harga Pokok Penjualan</span><strong>Rp 62.410.000</strong></div>
<div class="metric-row"><span>Biaya Operasional</span><strong>Rp 31.820.000</strong></div>
<div class="metric-row"><span>Laba Bersih</span><strong>Rp 54.390.000</strong></div>
<div class="progress-row" style="margin-top:22px"><div class="progress-top"><span>Margin laba bersih</span><span>36,6%</span></div><div class="progress"><span style="width:73%"></span></div></div>
</section>
<section class="panel"><div class="panel-head"><div><h2>Laporan Cepat</h2><p>Pilih laporan yang ingin dilihat.</p></div></div>
<div class="mini-list">
<button class="mini-item" data-demo="Laporan penjualan dibuka"><span class="mini-left"><span class="mini-icon"><i class="fa-solid fa-chart-line"></i></span><span>Penjualan Bulanan</span></span><i class="fa-solid fa-chevron-right"></i></button>
<button class="mini-item" data-demo="Laporan produk dibuka"><span class="mini-left"><span class="mini-icon"><i class="fa-solid fa-mug-hot"></i></span><span>Produk Terlaris</span></span><i class="fa-solid fa-chevron-right"></i></button>
<button class="mini-item" data-demo="Laporan karyawan dibuka"><span class="mini-left"><span class="mini-icon"><i class="fa-solid fa-users"></i></span><span>Performa Karyawan</span></span><i class="fa-solid fa-chevron-right"></i></button>
<button class="mini-item" data-demo="Laporan pelanggan dibuka"><span class="mini-left"><span class="mini-icon"><i class="fa-solid fa-user-group"></i></span><span>Pertumbuhan Pelanggan</span></span><i class="fa-solid fa-chevron-right"></i></button>
</div></section>
</div>
<div class="panel" style="margin-top:22px"><div class="panel-head"><div><h2>Produk Terlaris</h2><p>Performa menu berdasarkan jumlah terjual.</p></div></div>
<div class="progress-row"><div class="progress-top"><span>Caramel Macchiato</span><span>284 cup</span></div><div class="progress"><span style="width:92%"></span></div></div>
<div class="progress-row"><div class="progress-top"><span>Hazelnut Latte</span><span>246 cup</span></div><div class="progress"><span style="width:80%"></span></div></div>
<div class="progress-row"><div class="progress-top"><span>Matcha Cream Latte</span><span>219 cup</span></div><div class="progress"><span style="width:71%"></span></div></div>
<div class="progress-row"><div class="progress-top"><span>Americano</span><span>187 cup</span></div><div class="progress"><span style="width:61%"></span></div></div>
</div>
</div>
@endsection
