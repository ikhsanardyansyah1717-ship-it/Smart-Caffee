@extends('layouts.owner')
@section('title','Penjualan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head"><div><span class="eyebrow">SALES ANALYTICS</span><h1>Penjualan</h1><p>Analisis omzet dan transaksi bisnis.</p></div><div class="head-actions"><button class="btn btn-light" data-demo="Filter periode dibuka"><i class="fa-solid fa-calendar"></i> Bulan Ini</button><button class="btn btn-primary" data-demo="Laporan penjualan siap dicetak"><i class="fa-solid fa-print"></i> Cetak</button></div></header>
<section class="stats">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-wallet"></i></div><div><div class="stat-label">Omzet Bulan Ini</div><div class="stat-value">Rp 48,6 jt</div><div class="stat-note">+14,8%</div></div></article>
<article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-label">Transaksi</div><div class="stat-value">1.284</div><div class="stat-note">Rata-rata 42/hari</div></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-basket-shopping"></i></div><div><div class="stat-label">Rata-rata Order</div><div class="stat-value">Rp 37,8 rb</div><div class="stat-note">+5,2%</div></div></article>
<article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-arrow-trend-up"></i></div><div><div class="stat-label">Laba Bersih</div><div class="stat-value">Rp 17,1 jt</div><div class="stat-note">Margin 35,2%</div></div></article>
</section>
<div class="two-col">
<section class="panel"><div class="panel-head"><div><h2>Tren Penjualan</h2><p>Performa omzet per hari.</p></div></div><div class="chart">
<div class="bar-item"><div class="bar" style="--h:50%"></div><span class="bar-label">01</span></div><div class="bar-item"><div class="bar" style="--h:67%"></div><span class="bar-label">05</span></div><div class="bar-item"><div class="bar" style="--h:58%"></div><span class="bar-label">10</span></div><div class="bar-item"><div class="bar" style="--h:82%"></div><span class="bar-label">15</span></div><div class="bar-item"><div class="bar" style="--h:73%"></div><span class="bar-label">20</span></div><div class="bar-item"><div class="bar" style="--h:92%"></div><span class="bar-label">25</span></div><div class="bar-item"><div class="bar" style="--h:80%"></div><span class="bar-label">30</span></div>
</div></section>
<section class="panel"><div class="panel-head"><div><h2>Metode Pembayaran</h2><p>Distribusi transaksi.</p></div></div>
<div class="progress-row"><div class="progress-top"><span>QRIS</span><span>42%</span></div><div class="progress"><span style="width:42%"></span></div></div>
<div class="progress-row"><div class="progress-top"><span>Cash</span><span>31%</span></div><div class="progress"><span style="width:31%"></span></div></div>
<div class="progress-row"><div class="progress-top"><span>Debit</span><span>18%</span></div><div class="progress"><span style="width:18%"></span></div></div>
<div class="progress-row"><div class="progress-top"><span>E-Wallet</span><span>9%</span></div><div class="progress"><span style="width:9%"></span></div></div>
</section></div>
<div class="panel" style="margin-top:22px"><div class="panel-head"><div><h2>Riwayat Penjualan</h2><p>Daftar transaksi terbaru.</p></div><label class="search" style="max-width:300px"><i class="fa-solid fa-magnifying-glass"></i><input data-search="#salesRows tr" placeholder="Cari transaksi..."></label></div>
<div class="table-wrap"><table><thead><tr><th>Order</th><th>Tanggal</th><th>Pelanggan</th><th>Kasir</th><th>Total</th><th>Status</th></tr></thead><tbody id="salesRows">
<tr><td><strong>ORD-1048</strong></td><td>26 Agu 2026, 09:12</td><td>Andi Pratama</td><td>Dina</td><td>Rp 68.000</td><td><span class="badge green">Lunas</span></td></tr>
<tr><td><strong>ORD-1047</strong></td><td>26 Agu 2026, 09:08</td><td>Salsa Putri</td><td>Raka</td><td>Rp 52.000</td><td><span class="badge green">Lunas</span></td></tr>
<tr><td><strong>ORD-1046</strong></td><td>26 Agu 2026, 08:56</td><td>Bagas Saputra</td><td>Dina</td><td>Rp 91.000</td><td><span class="badge">Diproses</span></td></tr>
</tbody></table></div></div>
</div>
@endsection
