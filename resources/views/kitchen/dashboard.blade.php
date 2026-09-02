@extends('layouts.kitchen')

@section('title', 'Kitchen Dashboard - Quattro Coffee')

@section('content')
<header class="topbar">
    <div>
        <span class="eyebrow">KITCHEN CONTROL</span>
        <h1>Dashboard</h1>
        <p>Pantau dan kelola pesanan pelanggan secara real-time.</p>
    </div>
    <div class="top-actions">
        <span class="live"><i class="fa-solid fa-circle"></i> Live</span>
        <button class="icon-btn" onclick="refreshKitchen()"><i class="fa-solid fa-rotate"></i></button>
    </div>
</header>

<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-bell"></i></div>
        <div><span>Pesanan Baru</span><strong id="stat-new">0</strong></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-fire-burner"></i></div>
        <div><span>Sedang Diproses</span><strong id="stat-processing">0</strong></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div><span>Siap Diambil</span><strong id="stat-ready">0</strong></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div><span>Prioritas</span><strong id="stat-priority">0</strong></div>
    </div>
</section>

<section class="dashboard-grid">
    <div class="panel">
        <div class="panel-head">
            <div>
                <h2>Pesanan Masuk</h2>
                <p>Pesanan terbaru yang perlu segera diproses.</p>
            </div>
            <a href="{{ route('kitchen.incoming') }}" class="outline-btn">Lihat Semua</a>
        </div>
        <div id="dashboard-orders" class="order-stack"></div>
    </div>
    
    <div class="panel">
        <div class="panel-head">
            <div>
                <h2>Status Kitchen</h2>
                <p>Ringkasan aktivitas hari ini.</p>
            </div>
        </div>
        <div class="progress-row">
            <span>Pesanan selesai</span>
            <strong id="completed-percent">0%</strong>
        </div>
        <div class="progress"><span id="completed-progress"></span></div>
        <div class="mini-list">
            <div><i class="fa-solid fa-clock"></i><span>Rata-rata proses</span><strong>12 menit</strong></div>
            <div><i class="fa-solid fa-fire"></i><span>Menu aktif</span><strong>18 item</strong></div>
            <div><i class="fa-solid fa-star"></i><span>Rating kitchen</span><strong>4.9/5</strong></div>
        </div>
    </div>
</section>
@endsection