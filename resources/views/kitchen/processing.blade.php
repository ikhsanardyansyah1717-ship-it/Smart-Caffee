@extends('layouts.kitchen')

@section('title', 'Sedang Diproses - Quattro Coffee')

@section('content')

<div class="kitchen-page">

<header class="topbar">

    <div>

        <span class="eyebrow">
            QUATTRO COFFEE • KITCHEN
        </span>

        <h1>Sedang Diproses</h1>

        <p>
            Kelola pesanan yang sedang dibuat oleh kitchen.
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

            <h2>Sedang Diproses</h2>

            <p>
                Daftar pesanan yang sedang dikerjakan.
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

                            {{ $order->customer_name }}

                            @if($order->table_number)
                                • Meja {{ $order->table_number }}
                            @endif

                        </div>

                    </div>

                    <span class="status-pill status-processing">
                        Diproses
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
                        action="{{ route('kitchen.orders.complete', $order->id) }}"
                        method="POST"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="action-btn btn-success"
                        >

                            <i class="fa-solid fa-circle-check"></i>

                            Siap Diambil

                        </button>

                    </form>

                </div>

            </div>

        @empty

            <div class="empty-state">

                <i class="fa-solid fa-fire-burner"></i>

                <p>
                    Tidak ada pesanan yang sedang diproses.
                </p>

            </div>

        @endforelse

    </div>

</section>

</div>

@endsection