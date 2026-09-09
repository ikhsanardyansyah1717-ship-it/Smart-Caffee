@extends('layouts.kitchen')

@section('title', 'Siap Diambil - Quattro Coffee')

@section('content')

<header class="topbar">

    <div>

        <span class="eyebrow">
            QUATTRO COFFEE • KITCHEN
        </span>

        <h1>Siap Diambil</h1>

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

            <h2>Siap Diambil</h2>

            <p>
                Daftar pesanan yang sudah selesai dibuat.
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

                    <span class="status-pill status-ready">
                        Siap Diambil
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

                    <div class="done-text">

                        <i class="fa-solid fa-circle-check"></i>

                        Pesanan siap diambil

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-state">

                <i class="fa-solid fa-circle-check"></i>

                <p>
                    Belum ada pesanan yang siap diambil.
                </p>

            </div>

        @endforelse

    </div>

</section>

@endsection