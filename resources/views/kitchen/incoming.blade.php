@extends('layouts.kitchen')
@section('title','Pesanan Baru - Quattro Coffee')
@section('content')
<div class="kitchen-shell"><aside class="sidebar">
<div class="brand"><div class="brand-icon"><i class="fa-solid fa-mug-hot"></i></div><div><strong>QUATTRO</strong><span>KITCHEN</span></div></div>
<nav>
<a href="{{ route('kitchen.dashboard') }}" class="side-link"><i class="fa-solid fa-chart-pie"></i><span>Dashboard</span></a>
<a href="{{ route('kitchen.incoming') }}" class="side-link"><i class="fa-solid fa-bell"></i><span>Pesanan Baru</span><b id="new-count">0</b></a>
<a href="{{ route('kitchen.processing') }}" class="side-link"><i class="fa-solid fa-fire-burner"></i><span>Sedang Diproses</span><b id="process-count">0</b></a>
<a href="{{ route('kitchen.completed') }}" class="side-link"><i class="fa-solid fa-circle-check"></i><span>Siap Diambil</span><b id="ready-count">0</b></a>
<a href="{{ route('kitchen.history') }}" class="side-link"><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat</span></a>
</nav><div class="kitchen-user"><div class="avatar">K</div><div><strong>Kitchen Staff</strong><small>Quattro Coffee</small></div></div>
</aside><main class="main-content">
<header class="topbar"><div><span class="eyebrow">QUATTRO COFFEE • KITCHEN</span><h1>Pesanan Baru</h1><p>Kelola pesanan pelanggan dengan cepat dan rapi.</p></div><div class="top-actions"><span class="live"><i class="fa-solid fa-circle"></i> Live</span><button class="icon-btn" onclick="refreshKitchen()"><i class="fa-solid fa-rotate"></i></button></div></header>
<div class="filter-bar"><div class="search"><i class="fa-solid fa-magnifying-glass"></i><input id="kitchen-search" placeholder="Cari ID pesanan atau nama pelanggan..."></div><select id="priority-filter"><option value="all">Semua Prioritas</option><option value="priority">Prioritas</option><option value="normal">Normal</option></select></div>
<section class="panel"><div class="panel-head"><div><h2>Pesanan Baru</h2><p>Daftar pesanan berdasarkan status.</p></div><span class="order-count" id="page-count">0 pesanan</span></div><div id="order-list" class="order-grid"></div></section>
</main></div>
@endsection