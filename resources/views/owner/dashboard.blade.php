@extends('layouts.owner')
@section('title','Dashboard Owner - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">OWNER CONTROL</span><h1>Dashboard</h1><p>Pantau performa bisnis Quattro Coffee secara menyeluruh.</p></div>
    <div class="head-actions"><span class="live"><span class="dot"></span> Live</span><button class="icon-btn" id="ownerRefresh"><i class="fa-solid fa-rotate"></i></button></div>
</header>
<section class="stats">
    <article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div><div><div class="stat-label">Penjualan Hari Ini</div><div class="stat-value">Rp 4,85 jt</div><div class="stat-note">+12,4% dari kemarin</div></div></article>
    <article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-label">Total Transaksi</div><div class="stat-value">128</div><div class="stat-note">+8,2% minggu ini</div></div></article>
    <article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-user-group"></i></div><div><div class="stat-label">Pelanggan</div><div class="stat-value">86</div><div class="stat-note">+6 pelanggan baru</div></div></article>
    <article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-chart-line"></i></div><div><div class="stat-label">Laba Bersih</div><div class="stat-value">Rp 1,72 jt</div><div class="stat-note">Margin 35,5%</div></div></article>
</section>

<div class="two-col">
<section class="panel">
    <div class="panel-head"><div><h2>Penjualan Mingguan</h2><p>Ringkasan omzet 7 hari terakhir.</p></div><a class="panel-link" href="{{ route('owner.sales') }}">Lihat Detail</a></div>
    <div class="chart">
        <div class="bar-item"><div class="bar" style="--h:45%"></div><span class="bar-label">Sen</span></div>
        <div class="bar-item"><div class="bar" style="--h:62%"></div><span class="bar-label">Sel</span></div>
        <div class="bar-item"><div class="bar" style="--h:54%"></div><span class="bar-label">Rab</span></div>
        <div class="bar-item"><div class="bar" style="--h:78%"></div><span class="bar-label">Kam</span></div>
        <div class="bar-item"><div class="bar" style="--h:68%"></div><span class="bar-label">Jum</span></div>
        <div class="bar-item"><div class="bar" style="--h:90%"></div><span class="bar-label">Sab</span></div>
        <div class="bar-item"><div class="bar" style="--h:73%"></div><span class="bar-label">Min</span></div>
    </div>
</section>

<section class="panel">
    <div class="panel-head"><div><h2>Status Operasional</h2><p>Ringkasan aktivitas hari ini.</p></div></div>
    <div class="progress-row"><div class="progress-top"><span>Target penjualan</span><span>78%</span></div><div class="progress"><span style="width:78%"></span></div></div>
    <div class="metric-row"><span>Pesanan selesai</span><strong>104</strong></div>
    <div class="metric-row"><span>Pesanan diproses</span><strong>17</strong></div>
    <div class="metric-row"><span>Menu aktif</span><strong>24 item</strong></div>
    <div class="metric-row"><span>Rating toko</span><strong>4.9/5</strong></div>
</section>
</div>

<div class="panel" style="margin-top:22px">
    <div class="panel-head"><div><h2>Transaksi Terbaru</h2><p>Aktivitas pembayaran terbaru.</p></div><a class="panel-link" href="{{ route('owner.sales') }}">Lihat Semua</a></div>
    <div class="table-wrap"><table><thead><tr><th>Order</th><th>Pelanggan</th><th>Kasir</th><th>Total</th><th>Status</th></tr></thead><tbody>
    <tr><td><strong>ORD-1048</strong></td><td>Andi Pratama</td><td>Dina</td><td>Rp 68.000</td><td><span class="badge green">Lunas</span></td></tr>
    <tr><td><strong>ORD-1047</strong></td><td>Salsa Putri</td><td>Raka</td><td>Rp 52.000</td><td><span class="badge green">Lunas</span></td></tr>
    <tr><td><strong>ORD-1046</strong></td><td>Bagas Saputra</td><td>Dina</td><td>Rp 91.000</td><td><span class="badge">Diproses</span></td></tr>
    </tbody></table></div>
</div>
</div>
@endsection
