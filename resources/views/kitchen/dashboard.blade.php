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
        <form method="GET" action="{{ route('kitchen.dashboard') }}" class="period-form">
            <label for="dashboard-date"><i class="fa-regular fa-calendar"></i><span>Periode</span></label>
            <input id="dashboard-date" type="date" name="date" value="{{ $date }}">
            <button class="outline-btn" type="submit">Tampilkan</button>
        </form>
        <span class="live">
            <i class="fa-solid fa-circle"></i> Live
        </span>

        <button class="icon-btn" onclick="refreshKitchen()">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>
</header>

<p class="period-caption">Data dashboard untuk {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</p>
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

<div class="filter-bar dashboard-search-bar">
    <div class="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input id="kitchen-search" type="search" placeholder="Cari pesanan, pelanggan, atau menu..." aria-label="Cari pesanan, pelanggan, atau menu">
    </div>
</div>

<section class="dashboard-grid">

    <div class="panel">

        <div class="panel-head">
            <div>
                <h2>Pesanan Aktif</h2>
                <p>Pesanan terbaru yang menunggu atau sedang diproses.</p>
            </div>

            <a href="{{ route('kitchen.incoming') }}" class="outline-btn">
                Lihat Semua
            </a>
        </div>

        <div id="dashboard-orders" class="order-stack">

            @forelse($orders->whereIn('status', ['Menunggu', 'Diproses'])->take(5) as $order)

                <div class="order-card kitchen-order-card" data-searchable>

                    <div class="order-top">

                        <div>
                            <div class="order-id">
                                #{{ $order->order_number }}
                            </div>

                            <div class="order-meta">
                                {{ $order->customer_name }} · {{ $order->created_at->format('d/m/Y H:i') }}

                                @if($order->table_number)
                                    • Meja {{ $order->table_number }}
                                @endif
                            </div>
                        </div>

                        <span class="status-pill {{ $order->status === 'Diproses' ? 'status-processing' : 'status-new' }}">{{ $order->status }}</span>

                    </div>

                    <div class="items">

                        @foreach($order->items as $item)

                            <div class="item-line">

                                <span>
                                    {{ $item->product_name }}
                                    x{{ $item->quantity }}
                                    @if($item->customization_summary)
                                        <small class="order-item-customization">{{ $item->customization_summary }}</small>
                                    @endif
                                </span>

                                <span>
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                    <div class="order-actions kitchen-order-actions">
                        @if($order->status === 'Diproses')
                            @php
                                $startedAt = $order->kitchen_started_at ?? $order->updated_at;
                            @endphp
                            <div class="order-timer" data-started-at="{{ $startedAt?->toIso8601String() }}"><i class="fa-regular fa-clock"></i> <span class="timer-label">Waktu proses 00:00</span></div>
                            <form action="{{ route('kitchen.orders.complete', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="action-btn btn-success"><i class="fa-solid fa-circle-check"></i> Siap Diambil</button>
                            </form>
                        @else

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
                        @endif

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <i class="fa-solid fa-receipt"></i>

                    <p>
                        Tidak ada pesanan aktif pada {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    <div class="panel">

        <div class="panel-head">

            <div>
                <h2>Status Kitchen</h2>
                <p>Ringkasan aktivitas pada {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}.</p>
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
                <strong>{{ $menuAverages->isNotEmpty() ? round($menuAverages->avg(), 1) . ' menit' : 'Belum ada data' }}</strong>
            </div>

            <div>
                <i class="fa-solid fa-fire"></i>
                <span>Menu aktif</span>
                <strong>{{ $orders->flatMap->items->pluck('product_name')->unique()->count() }} menu</strong>
            </div>

        </div>

        <div class="menu-average-list">
            <h3>Rata-rata waktu per menu</h3>
            @forelse($menuAverages as $menu => $minutes)
                <div><span>{{ $menu }}</span><strong>{{ $minutes }} menit</strong></div>
            @empty
                <p>Belum ada pesanan selesai untuk dihitung.</p>
            @endforelse
            @if($menuAverages->isNotEmpty())<small>Berdasarkan pesanan selesai sampai {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}.</small>@endif
        </div>

    </div>

</section>

</div>

@endsection
