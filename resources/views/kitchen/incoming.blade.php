@extends('layouts.kitchen')

@section('title', 'Pesanan - Quattro Coffee')

@section('content')

<div class="kitchen-page">

<header class="topbar">

    <div>
        <span class="eyebrow">QUATTRO COFFEE • KITCHEN</span>

        <h1>Pesanan</h1>

        <p>
            Pesanan baru dan yang sedang dikerjakan, dalam satu daftar.
        </p>
    </div>

    <div class="top-actions">

        <span class="live">
            <i class="fa-solid fa-circle"></i>
            Live
        </span>

        <button class="icon-btn" onclick="refreshKitchen()">
            <i class="fa-solid fa-rotate"></i>
        </button>

    </div>

</header>


<div class="filter-bar">

    <div class="search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            id="kitchen-search"
            type="text"
            placeholder="Cari ID pesanan atau nama pelanggan..."
        >

    </div>

    <select id="priority-filter">

        <option value="all">
            Semua Prioritas
        </option>

        <option value="priority">
            Prioritas
        </option>

        <option value="normal">
            Normal
        </option>

    </select>

</div>


<section class="panel">

    <div class="panel-head">

        <div>

            <h2>Semua Pesanan Aktif</h2>

            <p>
                Pesanan dibayar yang menunggu atau sedang dikerjakan.
            </p>

        </div>

        <span class="order-count" id="page-count">
            {{ $orders->count() }} pesanan
        </span>

    </div>


    <div id="order-list" class="order-grid">

        @forelse($orders as $order)

            <div
                class="order-card kitchen-order-card"
                data-priority="{{ $order->priority ?? 'normal' }}"
            >

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

                        <button
                            type="submit"
                            class="action-btn btn-primary"
                        >

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
                    Tidak ada pesanan aktif.
                </p>

            </div>

        @endforelse

    </div>

</section>

</div>

@endsection
