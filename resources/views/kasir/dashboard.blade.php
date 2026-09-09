@extends('layouts.owner')
@section('title','Dashboard Owner - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">OWNER CONTROL</span><h1>Dashboard</h1><p>Pantau performa bisnis Quattro Coffee secara menyeluruh.</p></div>
    <div class="head-actions"><span class="live"><span class="dot"></span> Live</span><button class="icon-btn" id="ownerRefresh"><i class="fa-solid fa-rotate"></i></button></div>
</header>

<section class="stats">
    <article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div><div><div class="stat-label">Penjualan Hari Ini</div><div class="stat-value">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</div></div></article>
    <article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-label">Total Transaksi</div><div class="stat-value">{{ $transaksiHariIni }}</div></div></article>
    <article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-user-group"></i></div><div><div class="stat-label">Pelanggan Baru</div><div class="stat-value">{{ $pelangganBaruHariIni }}</div><div class="stat-note">Hari ini</div></div></article>
    <article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-chart-line"></i></div><div><div class="stat-label">Laba Bersih</div><div class="stat-value" style="font-size:16px;color:#999;">Belum tersedia</div></div></article>
</section>

<div class="two-col">
<section class="panel">
    <div class="panel-head"><div><h2>Penjualan Mingguan</h2><p>Ringkasan omzet 7 hari terakhir.</p></div><a class="panel-link" href="{{ route('owner.sales') }}">Lihat Detail</a></div>
    @php $maxWeekly = $weeklyTrend->max('total') ?: 1; @endphp
    <div class="chart">
        @foreach($weeklyTrend as $day)
        <div class="bar-item"><div class="bar" style="--h:{{ round(($day['total'] / $maxWeekly) * 100) }}%"></div><span class="bar-label">{{ $day['label'] }}</span></div>
        @endforeach
    </div>
</section>

<section class="panel">
    <div class="panel-head"><div><h2>Status Operasional</h2><p>Ringkasan aktivitas hari ini.</p></div></div>
    <div class="metric-row"><span>Pesanan selesai</span><strong>{{ $pesananSelesai }}</strong></div>
    <div class="metric-row"><span>Pesanan diproses</span><strong>{{ $pesananDiproses }}</strong></div>
    <div class="metric-row"><span>Menu aktif</span><strong>{{ $menuAktif }} item</strong></div>
    <div class="metric-row"><span>Rating toko</span><strong style="color:#999;">Belum tersedia</strong></div>
</section>
</div>

<div class="panel" style="margin-top:22px">
    <div class="panel-head"><div><h2>Transaksi Terbaru</h2><p>Aktivitas pembayaran terbaru.</p></div><a class="panel-link" href="{{ route('owner.sales') }}">Lihat Semua</a></div>
    <div class="table-wrap"><table><thead><tr><th>Order</th><th>Pelanggan</th><th>Kasir</th><th>Total</th><th>Status</th></tr></thead><tbody>
    @forelse($transaksiTerbaru as $order)
    <tr>
        <td><strong>{{ $order->order_number }}</strong></td>
        <td>{{ $order->customer_name }}</td>
        <td>-</td>
        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        <td><span class="badge {{ $order->payment_status === 'Dibayar' ? 'green' : '' }}">{{ $order->payment_status }}</span></td>
    </tr>
    @empty
    <tr><td colspan="5" style="text-align:center;color:#888;padding:24px;">Belum ada transaksi.</td></tr>
    @endforelse
    </tbody></table></div>
</div>
</div>
@endsection