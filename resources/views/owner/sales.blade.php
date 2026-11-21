@extends('layouts.owner')
@section('title','Penjualan - Quattro Coffee')
@section('content')
@push('styles')
<style>
.owner-sales-page{display:grid;align-content:start;gap:24px;padding-bottom:28px}.owner-sales-page .page-head{align-items:center;margin-bottom:0}.owner-sales-page .stats{margin:0;gap:18px}.owner-sales-page .stat-card{position:relative;overflow:hidden;min-height:122px;padding:21px;border-radius:19px;background:linear-gradient(145deg,#fff 0%,#fffdf9 100%);transition:transform .2s,box-shadow .2s}.owner-sales-page .stat-card::before{position:absolute;top:0;right:18px;left:18px;height:3px;border-radius:0 0 5px 5px;background:linear-gradient(90deg,#754000,#d99a51 58%,transparent);content:"";opacity:.75}.owner-sales-page .stat-card:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(77,48,18,.09)}.owner-sales-page .two-col{grid-template-columns:minmax(0,1.6fr) minmax(270px,.9fr);gap:22px;align-items:stretch}.owner-sales-page .panel{min-width:0;padding:24px;border-radius:21px}.owner-sales-page .sales-trend-panel{display:flex;flex-direction:column}.owner-sales-page .sales-trend-panel .owner-trend-chart{margin-top:auto}.owner-sales-page .panel-head h2{margin-top:4px}.sales-panel-kicker{display:block;margin-bottom:4px;color:#b85c43;font-size:9px;font-weight:800;letter-spacing:1.2px}.sales-period-pill{display:inline-flex;align-items:center;gap:7px;padding:8px 11px;border:1px solid #efe3d4;border-radius:999px;background:#fffaf4;color:#80521f;font-size:10px;font-weight:700;white-space:nowrap}.owner-sales-page>.panel:last-child{margin:0!important}.owner-sales-page>.panel:last-child .panel-head{align-items:center}.owner-sales-page .progress-row{margin:22px 0}.owner-sales-page .progress{height:9px;background:#f0e8de}.owner-sales-page .progress>span{background:linear-gradient(90deg,#d99a51,#754000)}.owner-sales-page .progress-top{font-size:12px}.owner-sales-page .table-wrap{border:1px solid #f0e7dd;border-radius:14px}.owner-sales-page table{min-width:720px}.owner-sales-page th{padding:13px 14px;background:#fdfbf8}.owner-sales-page td{padding:14px}.owner-sales-page tbody tr:hover{background:#fffcf7}
@media(max-width:1050px){.owner-sales-page .stats{grid-template-columns:repeat(2,minmax(0,1fr))}.owner-sales-page .two-col{grid-template-columns:1fr}}
@media(max-width:680px){.owner-sales-page{gap:18px}.owner-sales-page .page-head{display:block}.owner-sales-page .head-actions{justify-content:flex-start;flex-wrap:wrap}.owner-sales-page .panel{padding:19px;border-radius:18px}.owner-sales-page .panel-head{align-items:flex-start}}
@media(max-width:520px){.owner-sales-page .stats{grid-template-columns:1fr;gap:12px}.owner-sales-page .panel-head{flex-direction:column}.owner-sales-page .sales-period-pill{align-self:flex-start}}
</style>
@endpush
<div class="grid owner-sales-page">
<header class="page-head">
    <div><span class="eyebrow">SALES ANALYTICS</span><h1>Penjualan</h1><p>Analisis omzet dan transaksi · Periode {{ $periodLabel }}.</p></div>
    <div class="head-actions">
        @include('owner.partials.period-picker')
        <a href="{{ route('owner.sales.export.excel', ['start' => $periodStart->toDateString(), 'end' => $periodEnd->toDateString()]) }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Excel</a>
        <a href="{{ route('owner.sales.export.pdf', ['start' => $periodStart->toDateString(), 'end' => $periodEnd->toDateString()]) }}" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
</header>

<section class="stats">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-wallet"></i></div><div><div class="stat-label">Omzet</div><div class="stat-value">Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}</div><div class="stat-note">Periode {{ $periodLabel }}</div></div></article>
<article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-label">Transaksi</div><div class="stat-value">{{ $transaksiBulanIni }}</div><div class="stat-note">Periode {{ $periodLabel }}</div></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-basket-shopping"></i></div><div><div class="stat-label">Rata-rata Order</div><div class="stat-value">Rp {{ number_format($rataRataOrder, 0, ',', '.') }}</div><div class="stat-note">Periode {{ $periodLabel }}</div></div></article>
<article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-arrow-trend-up"></i></div><div><div class="stat-label">Laba Bersih</div><div class="stat-value">Rp {{ number_format($financialSummary['laba_bersih'] ?? 0, 0, ',', '.') }}</div><div class="stat-note">Periode {{ $periodLabel }}</div>@if(($financialSummary['items_without_cost'] ?? 0) > 0)<div class="stat-note">Lengkapi harga pokok untuk laba yang akurat</div>@endif</div></article>
</section>

<div class="two-col">
<section class="panel sales-trend-panel"><div class="panel-head"><div><span class="sales-panel-kicker">RINGKASAN OMZET</span><h2>Tren Penjualan</h2><p>Performa omzet harian pada periode {{ $periodLabel }}.</p></div><span class="sales-period-pill"><i class="fa-regular fa-calendar"></i> {{ $periodLabel }}</span></div>
@include('owner.partials.sales-trend-chart', ['chartData' => $dailyTrend, 'chartId' => 'sales-page'])
</section>

<section class="panel"><div class="panel-head"><div><h2>Metode Pembayaran</h2><p>Distribusi transaksi berhasil, periode terpilih.</p></div></div>
@forelse(['QRIS', 'Cash', 'Bank Transfer'] as $method)
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
    <td>{{ $order->payment?->paid_at?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>{{ $order->payment?->cashier_name ?: ($order->user?->role === 'kasir' ? $order->user->name : ($order->payment?->payment_method === 'QRIS' ? 'Online' : 'Belum tercatat')) }}</td>
    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
    <td><span class="badge {{ $order->payment_status === 'Dibayar' ? 'green' : '' }}">{{ $order->payment_status }}</span></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;color:#888;padding:24px;">Belum ada transaksi pada periode ini.</td></tr>
@endforelse
</tbody></table></div></div>
</div>
@endsection
