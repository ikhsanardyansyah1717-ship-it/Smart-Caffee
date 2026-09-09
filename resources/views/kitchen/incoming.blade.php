@extends('layouts.kitchen')

@section('title', 'Pesanan Baru - Quattro Coffee')

@section('content')

<header class="topbar">

    <div>
        <span class="eyebrow">QUATTRO COFFEE • KITCHEN</span>

        <h1>Pesanan Baru</h1>

        <p>
            Kelola pesanan pelanggan dengan cepat dan rapi.
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

            <h2>Pesanan Baru</h2>

            <p>
                Pesanan yang sudah dibayar dan menunggu diproses.
            </p>

        </div>

        <span class="order-count" id="page-count">
            {{ $orders->count() }} pesanan
        </span>

    </div>


    <div id="order-list" class="order-grid">

        @forelse($orders as $order)

            <div
                class="order-card"
                data-priority="{{ $order->priority ?? 'normal' }}"
            >

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


                <div class="order-actions">

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

</section>

@endsection