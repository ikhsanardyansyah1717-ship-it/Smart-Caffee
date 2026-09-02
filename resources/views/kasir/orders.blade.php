@extends('layouts.kasir')
@section('title','Pesanan - Quattro Coffee')

@section('content')
<header class="topbar">
    <div><span class="eyebrow">CASHIER CONTROL</span><h1>Pesanan</h1><p>Kelola pesanan pelanggan dan buat transaksi baru.</p></div>
    <div class="top-actions"><button class="primary-btn" onclick="openModal()"><i class="fa-solid fa-plus"></i> Pesanan Baru</button></div>
</header>

<div class="panel">
    <div class="panel-head">
        <div><h2>Daftar Pesanan</h2><p>Semua pesanan yang sedang berjalan.</p></div>
        <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input id="orderSearch" type="text" placeholder="Cari pesanan..." onkeyup="filterTable('orderSearch','ordersTable')"></div>
    </div>
    <div class="table-wrap">
        <table id="ordersTable">
            <thead><tr><th>ID Pesanan</th><th>Pelanggan</th><th>Meja</th><th>Pesanan</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($orders as $order)

    <tr>

        {{-- ID PESANAN --}}
        <td>
            <strong>{{ $order->order_number }}</strong>
            <small>
                {{ $order->created_at->format('H:i') }}
            </small>
        </td>


        {{-- PELANGGAN --}}
        <td>
            {{ $order->customer_name }}
        </td>


        {{-- MEJA --}}
        <td>
            {{ $order->table_number ?? 'Take Away' }}
        </td>


        {{-- PESANAN --}}
        <td>

            @forelse($order->items as $item)

                <div style="margin-bottom: 4px;">
                    <strong>
                        {{ $item->product_name }}
                    </strong>

                    <span>
                        × {{ $item->quantity }}
                    </span>
                </div>

            @empty

                <span>Tidak ada item</span>

            @endforelse

        </td>


        {{-- TOTAL --}}
        <td>
            Rp {{ number_format($order->total, 0, ',', '.') }}
        </td>


        {{-- STATUS --}}
        <td>
            <span class="badge {{ strtolower($order->status) }}">
                {{ $order->status }}
            </span>
        </td>


        {{-- AKSI --}}
        <td>

            <a
                class="table-action"
                href="{{ route('kasir.payment') }}"
            >
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </td>

    </tr>

@empty

    <tr>
        <td colspan="7" style="text-align:center; padding:40px;">
            Belum ada pesanan dari Customer.
        </td>
    </tr>

@endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="orderModal">
    <div class="modal-card">
        <div class="modal-head"><div><span class="eyebrow">TRANSAKSI BARU</span><h2>Buat Pesanan</h2></div><button onclick="closeModal()">&times;</button></div>
        <form action="{{ route('kasir.orders.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <label>Nama Pelanggan<input name="customer" required placeholder="Contoh: Andi"></label>
                <label>Meja / Tipe<input name="table" required placeholder="Contoh: A01 / Take Away"></label>
            </div>
            <label>Pesanan<input name="items" required placeholder="Contoh: Cappuccino x2, Croissant x1"></label>
            <label>Total Pembayaran<input name="total" type="number" min="0" required placeholder="80000"></label>
            <button class="primary-btn full" type="submit"><i class="fa-solid fa-check"></i> Simpan Pesanan</button>
        </form>
    </div>
</div>
@endsection
