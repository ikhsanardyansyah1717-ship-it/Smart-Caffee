@extends('layouts.owner')
@section('title','Penjualan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">SALES ANALYTICS</span><h1>Penjualan</h1><p>Analisis omzet dan transaksi bisnis.</p></div>
    <div class="head-actions">
        <form method="GET" action="{{ route('owner.sales') }}">
            <input type="date" name="tanggal" value="{{ $selectedDate }}" onchange="this.form.submit()" style="padding:9px 12px;border:1px solid #e2e2e2;border-radius:10px;font-size:14px;background:#fff;">
        </form>
        <a href="{{ route('owner.sales.export.excel', ['tanggal' => $selectedDate]) }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Excel</a>
        <a href="{{ route('owner.sales.export.pdf', ['tanggal' => $selectedDate]) }}" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
</header>

<section class="stats">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-wallet"></i></div><div><div class="stat-label">Omzet Periode Ini</div><div class="stat-value">Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}</div></div></article>
<article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-label">Transaksi</div><div class="stat-value">{{ $transaksiBulanIni }}</div></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-basket-shopping"></i></div><div><div class="stat-label">Rata-rata Order</div><div class="stat-value">Rp {{ number_format($rataRataOrder, 0, ',', '.') }}</div></div></article>
<article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-arrow-trend-up"></i></div><div><div class="stat-label">Laba Bersih</div><div class="stat-value" style="font-size:16px;color:#999;">Belum tersedia</div></div></article>
</section>

<div class="two-col">
<section class="panel"><div class="panel-head"><div><h2>Tren Penjualan</h2><p>Performa omzet per hari, periode terpilih.</p></div></div>
<div class="chart" style="overflow-x:auto;">
@php $maxTren = $dailyTrend->max('total') ?: 1; @endphp
@foreach($dailyTrend as $day)
<div class="bar-item"><div class="bar" style="--h:{{ max(12, round(($day['total'] / $maxTren) * 100)) }}%"></div><span class="bar-label">{{ $day['label'] }}</span></div>
@endforeach
</div></section>

<section class="panel"><div class="panel-head"><div><h2>Metode Pembayaran</h2><p>Distribusi transaksi berhasil, periode terpilih.</p></div></div>
@forelse(['QRIS', 'Cash', 'Debit', 'E-Wallet'] as $method)
@php $jumlah = $metodePembayaran[$method] ?? 0; $persen = $totalMetode > 0 ? round(($jumlah / $totalMetode) * 100) : 0; @endphp
<div class="progress-row"><div class="progress-top"><span>{{ $method }}</span><span>{{ $persen }}%</span></div><div class="progress"><span style="width:{{ $persen }}%"></span></div></div>
@empty
<p style="color:#888;">Belum ada data pembayaran.</p>
@endforelse
</section></div>

<div class="panel" style="margin-top:22px"><div class="panel-head"><div><h2>Riwayat Penjualan</h2><p>Daftar transaksi pada periode terpilih.</p></div><label class="search" style="max-width:300px"><i class="fa-solid fa-magnifying-glass"></i><input data-search="#salesRows tr" placeholder="Cari transaksi..."></label></div>
<div class="table-wrap"><table><thead><tr><th>Order</th><th>Tanggal</th><th>Pelanggan</th><th>Kasir</th><th>Total</th><th>Status</th></tr></thead><tbody id="salesRows">
@forelse($riwayatPenjualan as $order)
<tr>
    <td><strong>{{ $order->order_number }}</strong></td>
    <td>{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>-</td>
    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
    <td><span class="badge {{ $order->payment_status === 'Dibayar' ? 'green' : '' }}">{{ $order->payment_status }}</span></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;color:#888;padding:24px;">Belum ada transaksi pada periode ini.</td></tr>
@endforelse
</tbody></table></div></div>
</div>
@endsection