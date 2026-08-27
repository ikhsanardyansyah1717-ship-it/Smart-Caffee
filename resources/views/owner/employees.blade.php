@extends('layouts.owner')
@section('title','Karyawan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head"><div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Karyawan</h1><p>Kelola staf dan akses operasional Quattro Coffee.</p></div><button class="btn btn-primary" data-demo="Form tambah karyawan dibuka"><i class="fa-solid fa-user-plus"></i> Tambah Karyawan</button></header>
<div class="stats">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-users"></i></div><div><div class="stat-label">Total Karyawan</div><div class="stat-value">14</div></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-user-check"></i></div><div><div class="stat-label">Sedang Aktif</div><div class="stat-value">11</div></div></article>
<article class="stat-card"><div class="stat-icon gold"><i class="fa-solid fa-user-clock"></i></div><div><div class="stat-label">Shift Hari Ini</div><div class="stat-value">8</div></div></article>
<article class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-calendar-xmark"></i></div><div><div class="stat-label">Izin / Cuti</div><div class="stat-value">1</div></div></article>
</div>
<div class="panel"><div class="panel-head"><div><h2>Daftar Karyawan</h2><p>Data staf dan posisi.</p></div><label class="search" style="max-width:280px"><i class="fa-solid fa-magnifying-glass"></i><input data-search="#employeeRows tr" placeholder="Cari karyawan..."></label></div>
<div class="table-wrap"><table><thead><tr><th>Nama</th><th>Posisi</th><th>Shift</th><th>Status</th><th>Kontak</th></tr></thead><tbody id="employeeRows">
<tr><td><strong>Dina Lestari</strong></td><td>Kasir</td><td>Pagi</td><td><span class="badge green">Aktif</span></td><td>0812-***-218</td></tr>
<tr><td><strong>Raka Wijaya</strong></td><td>Kasir</td><td>Siang</td><td><span class="badge green">Aktif</span></td><td>0813-***-427</td></tr>
<tr><td><strong>Fajar Ramadhan</strong></td><td>Kitchen</td><td>Pagi</td><td><span class="badge green">Aktif</span></td><td>0821-***-663</td></tr>
<tr><td><strong>Nadia Putri</strong></td><td>Kitchen</td><td>Siang</td><td><span class="badge">Cuti</span></td><td>0857-***-119</td></tr>
</tbody></table></div></div>
</div>
@endsection
