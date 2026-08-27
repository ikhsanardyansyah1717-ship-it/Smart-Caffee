@extends('layouts.kasir')
@section('title','Kasir Dashboard - Quattro Coffee')

@section('content')
<header class="topbar">
    <div>
        <span class="eyebrow">CASHIER CONTROL</span>
        <h1>Dashboard</h1>
        <p>Kelola transaksi dan pantau penjualan cafe hari ini.</p>
    </div>
    <div class="top-actions">
        <span class="live"><i class="fa-solid fa-circle"></i> Live</span>
        <button class="icon-btn" onclick="location.reload()"><i class="fa-solid fa-rotate"></i></button>
    </div>
</header>

<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-receipt"></i></div>
        <div><span>Pesanan Hari Ini</span><strong>48</strong><small class="up">+12% dari kemarin</small></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-hourglass-half"></i></div>
        <div><span>Menunggu Bayar</span><strong>5</strong><small>Perlu diproses</small></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div><span>Penjualan Hari Ini</span><strong>Rp 2,48 jt</strong><small class="up">+8.4%</small></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div><span>Pesanan Prioritas</span><strong>3</strong><small>Perlu perhatian</small></div>
    </div>
</section>

<section class="dashboard-grid">
    <div class="panel">
        <div class="panel-head">
            <div><h2>Pesanan Terbaru</h2><p>Transaksi terbaru yang masuk ke kasir.</p></div>
            <a href="{{ route('kasir.orders') }}" class="outline-btn">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>ID</th><th>Pelanggan</th><th>Meja</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td><strong>{{ $order['id'] }}</strong><small>{{ $order['time'] }}</small></td>
                        <td>{{ $order['customer'] }}</td>
                        <td>{{ $order['table'] }}</td>
                        <td>Rp {{ number_format($order['total'],0,',','.') }}</td>
                        <td><span class="badge {{ strtolower($order['status']) }}">{{ $order['status'] }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div><h2>Ringkasan Penjualan</h2><p>Performa transaksi hari ini.</p></div></div>
        <div class="sales-highlight"><span>Total pendapatan</span><strong>Rp 2.480.000</strong><small><i class="fa-solid fa-arrow-trend-up"></i> 8.4% meningkat</small></div>
        <div class="mini-list">
            <div><i class="fa-solid fa-receipt"></i><span>Transaksi selesai</span><strong>43</strong></div>
            <div><i class="fa-solid fa-users"></i><span>Pelanggan hari ini</span><strong>39</strong></div>
            <div><i class="fa-solid fa-star"></i><span>Rating layanan</span><strong>4.9/5</strong></div>
        </div>
        <a href="{{ route('kasir.payment') }}" class="primary-btn full"><i class="fa-solid fa-cash-register"></i> Proses Pembayaran</a>
    </div>
</section>
@endsection
