@extends('layouts.kitchen')

@section('title', 'Kitchen Dashboard - Quattro Coffee')

@section('content')

<div class="kitchen-page">

@php
    $totalOrders = $orders->count();

    $newOrders = $orders->where('status', 'Menunggu')->count();
    $processingOrders = $orders->where('status', 'Diproses')->count();
    $readyOrders = $orders->where('status', 'Selesai')->count();

    $completedPercent = $totalOrders > 0
        ? round(($readyOrders / $totalOrders) * 100)
        : 0;

    $priorityOrders = 0;

    if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'priority')) {
        $priorityOrders = $orders->where('priority', 'priority')->count();
    }
@endphp

<header class="topbar">
    <div>
        <span class="eyebrow">KITCHEN CONTROL</span>
        <h1>Dashboard</h1>
        <p>Pantau dan kelola pesanan pelanggan secara real-time.</p>
    </div>

    <div class="top-actions">
        <span class="live">
            <i class="fa-solid fa-circle"></i> Live
        </span>

        <button class="icon-btn" onclick="refreshKitchen()">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>
</header>

<section class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon brown">
            <i class="fa-solid fa-bell"></i>
        </div>

        <div>
            <span>Pesanan Baru</span>
            <strong id="stat-new">{{ $newOrders }}</strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fa-solid fa-fire-burner"></i>
        </div>

        <div>
            <span>Sedang Diproses</span>
            <strong id="stat-processing">{{ $processingOrders }}</strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div>
            <span>Siap Diambil</span>
            <strong id="stat-ready">{{ $readyOrders }}</strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div>
            <span>Prioritas</span>
            <strong id="stat-priority">{{ $priorityOrders }}</strong>
        </div>
    </div>

</section>

<section class="dashboard-grid">

    <div class="panel">

        <div class="panel-head">
            <div>
                <h2>Pesanan Masuk</h2>
                <p>Pesanan terbaru yang perlu segera diproses.</p>
            </div>

            <a href="{{ route('kitchen.incoming') }}" class="outline-btn">
                Lihat Semua
            </a>
        </div>

        <div id="dashboard-orders" class="order-stack">

            @forelse($orders->where('status', 'Menunggu')->take(5) as $order)

                <div class="order-card kitchen-order-card">

                    <div class="order-top">

                        <div>
                            <div class="order-id">
                                #{{ $order->order_number }}
                            </div>

                            <div class="order-meta">
                                {{ $order->customer_name }}

                                @if($order->table_number)
                                    • Meja {{ $order->table_number }}
                                @endif
                            </div>
                        </div>

                        <span class="status-pill status-new">
                            Menunggu
                        </span>

                    </div>

                    <div class="items">

                        @foreach($order->items as $item)

                            <div class="item-line">

                                <span>
                                    {{ $item->product_name }}
                                    x{{ $item->quantity }}
                                </span>

                                <span>
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                    <div class="order-actions kitchen-order-actions">

                        <form
                            action="{{ route('kitchen.orders.process', $order->id) }}"
                            method="POST"
                        >

                            @csrf

                            <button type="submit" class="action-btn btn-primary">
                                <i class="fa-solid fa-play"></i>
                                Proses Pesanan
                            </button>

                        </form>

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <i class="fa-solid fa-receipt"></i>

                    <p>
                        Belum ada pesanan baru.
                    </p>

                </div>

            @endforelse

        </div>

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

            <strong id="completed-percent">
                {{ $completedPercent }}%
            </strong>

        </div>

        <div class="progress">
            <span
                id="completed-progress"
                style="width: {{ $completedPercent }}%"
            ></span>
        </div>

        <div class="mini-list">

            <div>
                <i class="fa-solid fa-clock"></i>
                <span>Rata-rata proses</span>
                <strong>12 menit</strong>
            </div>

            <div>
                <i class="fa-solid fa-fire"></i>
                <span>Menu aktif</span>
                <strong>18 item</strong>
            </div>

            <div>
                <i class="fa-solid fa-star"></i>
                <span>Rating kitchen</span>
                <strong>4.9/5</strong>
            </div>

        </div>

    </div>

</section>

</div>

@endsection