@extends('layouts.kasir')

@section('title','Kasir Dashboard - Quattro Coffee')

@section('content')

<header class="topbar">

    <div>

        <span class="eyebrow">
            CASHIER CONTROL
        </span>

        <h1>
            Dashboard
        </h1>

        <p>
            Kelola transaksi dan pantau penjualan cafe hari ini.
        </p>

    </div>

    <div class="top-actions">

        <span class="live">
            <i class="fa-solid fa-circle"></i>
            Live
        </span>

        <button
            class="icon-btn"
            onclick="location.reload()"
        >
            <i class="fa-solid fa-rotate"></i>
        </button>

    </div>

</header>


{{-- ====================================================== --}}
{{-- STATISTIK --}}
{{-- ====================================================== --}}

<section class="stats-grid">

    {{-- Pesanan Hari Ini --}}
    <div class="stat-card">

        <div class="stat-icon brown">
            <i class="fa-solid fa-receipt"></i>
        </div>

        <div>

            <span>
                Pesanan Hari Ini
            </span>

            <strong>
                {{ $pesananHariIni }}
            </strong>

            <small>
                Total pesanan hari ini
            </small>

        </div>

    </div>


    {{-- Menunggu Bayar --}}
    <div class="stat-card">

        <div class="stat-icon orange">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>

        <div>

            <span>
                Menunggu Bayar
            </span>

            <strong>
                {{ $menungguBayar }}
            </strong>

            <small>
                Perlu diproses
            </small>

        </div>

    </div>


    {{-- Penjualan Hari Ini --}}
    <div class="stat-card">

        <div class="stat-icon green">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>

        <div>

            <span>
                Penjualan Hari Ini
            </span>

            <strong>
                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
            </strong>

            <small>
                Total transaksi lunas
            </small>

        </div>

    </div>


    {{-- Pesanan Prioritas --}}
    <div class="stat-card">

        <div class="stat-icon red">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div>

            <span>
                Pesanan Prioritas
            </span>

            <strong>
                {{ $pesananPrioritas }}
            </strong>

            <small>
                Pesanan yang masih menunggu
            </small>

        </div>

    </div>

</section>


{{-- ====================================================== --}}
{{-- DASHBOARD --}}
{{-- ====================================================== --}}

<section class="dashboard-grid">


    {{-- ================================================== --}}
    {{-- PESANAN TERBARU --}}
    {{-- ================================================== --}}

    <div class="panel">

        <div class="panel-head">

            <div>

                <h2>
                    Pesanan Terbaru
                </h2>

                <p>
                    Transaksi terbaru yang masuk ke kasir.
                </p>

            </div>

            <a
                href="{{ route('kasir.orders') }}"
                class="outline-btn"
            >
                Lihat Semua
            </a>

        </div>


        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Meja
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($orders as $order)

                        <tr>

                            {{-- ID --}}
                            <td>

                                <strong>
                                    {{ $order->order_number ?? $order->id }}
                                </strong>

                                <small>
                                    {{ $order->created_at
                                        ? $order->created_at->format('H:i')
                                        : '-' }}
                                </small>

                            </td>


                            {{-- PELANGGAN --}}
                            <td>

                                {{ $order->customer_name ?? '-' }}

                            </td>


                            {{-- MEJA --}}
                            <td>

                                {{ $order->table_number ?? 'Take Away' }}

                            </td>


                            {{-- TOTAL --}}
                            <td>

                                Rp
                                {{ number_format(
                                    $order->total,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span
                                    class="badge {{ strtolower($order->status) }}"
                                >
                                    {{ $order->status }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                style="text-align:center; padding:30px;"
                            >

                                Belum ada pesanan.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- RINGKASAN PENJUALAN --}}
    {{-- ================================================== --}}

    <div class="panel">

        <div class="panel-head">

            <div>

                <h2>
                    Ringkasan Penjualan
                </h2>

                <p>
                    Performa transaksi hari ini.
                </p>

            </div>

        </div>


        <div class="sales-highlight">

            <span>
                Total pendapatan
            </span>

            <strong>
                Rp {{ number_format(
                    $penjualanHariIni,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

            <small>

                <i class="fa-solid fa-arrow-trend-up"></i>

                Penjualan hari ini

            </small>

        </div>


        <div class="mini-list">


            {{-- TRANSAKSI SELESAI --}}
            <div>

                <i class="fa-solid fa-receipt"></i>

                <span>
                    Transaksi selesai
                </span>

                <strong>
                    {{ $transaksiSelesai }}
                </strong>

            </div>


            {{-- PELANGGAN --}}
            <div>

                <i class="fa-solid fa-users"></i>

                <span>
                    Pelanggan hari ini
                </span>

                <strong>
                    {{ $pelangganHariIni }}
                </strong>

            </div>


            {{-- RATING --}}
            <div>

                <i class="fa-solid fa-star"></i>

                <span>
                    Rating layanan
                </span>

                <strong>
                    4.9/5
                </strong>

            </div>

        </div>


        <a
            href="{{ route('kasir.payment') }}"
            class="primary-btn full"
        >

            <i class="fa-solid fa-cash-register"></i>

            Proses Pembayaran

        </a>

    </div>

</section>

@endsection