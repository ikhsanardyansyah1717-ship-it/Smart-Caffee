@extends('layouts.owner')
@section('title','Pelanggan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head"><div><span class="eyebrow">CUSTOMER MANAGEMENT</span><h1>Pelanggan</h1><p>Lihat pertumbuhan dan aktivitas pelanggan.</p></div><button class="btn btn-primary" data-demo="Data pelanggan berhasil diperbarui"><i class="fa-solid fa-arrows-rotate"></i> Sinkronkan</button></header>
<section class="stats">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-user-group"></i></div><div><div class="stat-label">Total Pelanggan</div><div class="stat-value">1.248</div><div class="stat-note">+6,3% bulan ini</div></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-user-plus"></i></div><div><div class="stat-label">Pelanggan Baru</div><div class="stat-value">86</div></div></article>
<article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-crown"></i></div><div><div class="stat-label">Member Aktif</div><div class="stat-value">392</div></div></article>
<article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-repeat"></i></div><div><div class="stat-label">Repeat Order</div><div class="stat-value">68%</div></div></article>
</section>
<div class="panel"><div class="panel-head"><div><h2>Pelanggan Teratas</h2><p>Berdasarkan total transaksi.</p></div><label class="search" style="max-width:280px"><i class="fa-solid fa-magnifying-glass"></i><input data-search="#customerRows tr" placeholder="Cari pelanggan..."></label></div>
<div class="table-wrap"><table><thead><tr><th>Pelanggan</th><th>Member</th><th>Transaksi</th><th>Total Belanja</th><th>Terakhir Order</th></tr></thead><tbody id="customerRows">
<tr><td><strong>Andi Pratama</strong></td><td><span class="badge green">Gold</span></td><td>38</td><td>Rp 2.480.000</td><td>Hari ini</td></tr>
<tr><td><strong>Salsa Putri</strong></td><td><span class="badge">Silver</span></td><td>24</td><td>Rp 1.620.000</td><td>Hari ini</td></tr>
<tr><td><strong>Bagas Saputra</strong></td><td><span class="badge green">Gold</span></td><td>21</td><td>Rp 1.490.000</td><td>Kemarin</td></tr>
</tbody></table></div></div>
</div>
@endsection
