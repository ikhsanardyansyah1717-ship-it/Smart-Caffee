@extends('layouts.kitchen')

@section('title', 'Sedang Diproses - Quattro Coffee')

@section('content')
<header class="topbar">
    <div>
        <span class="eyebrow">QUATTRO COFFEE • KITCHEN</span>
        <h1>Sedang Diproses</h1>
        <p>Kelola pesanan pelanggan dengan cepat dan rapi.</p>
    </div>
    <div class="top-actions">
        <span class="live"><i class="fa-solid fa-circle"></i> Live</span>
        <button class="icon-btn" onclick="refreshKitchen()"><i class="fa-solid fa-rotate"></i></button>
    </div>
</header>

<div class="filter-bar">
    <div class="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input id="kitchen-search" placeholder="Cari ID pesanan atau nama pelanggan...">
    </div>
    <select id="priority-filter">
        <option value="all">Semua Prioritas</option>
        <option value="priority">Prioritas</option>
        <option value="normal">Normal</option>
    </select>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Sedang Diproses</h2>
            <p>Daftar pesanan berdasarkan status.</p>
        </div>
        <span class="order-count" id="page-count">0 pesanan</span>
    </div>
    <div id="order-list" class="order-grid"></div>
</section>
@endsection