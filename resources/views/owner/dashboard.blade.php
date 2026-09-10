@extends('layouts.owner')

@section('title', 'Dashboard Owner - Quattro Coffee')

@section('content')

<div class="grid">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <header class="page-head">

        <div class="page-head-content">

            <span class="eyebrow">
                OWNER CONTROL
            </span>

            <h1>
                Dashboard
            </h1>

            <p>
                Pantau performa bisnis Quattro Coffee secara menyeluruh.
            </p>

        </div>


        <div class="head-actions">

            {{-- LIVE STATUS --}}
            <span class="live">
                <span class="dot"></span>
                Live
            </span>


            {{-- REFRESH --}}
            <button
                class="icon-btn owner-refresh-btn"
                id="ownerRefresh"
                type="button"
                title="Refresh data dashboard"
                aria-label="Refresh data dashboard"
            >
                <i class="fa-solid fa-rotate"></i>
            </button>

        </div>

    </header>



    {{-- =========================================================
         STATISTIK
    ========================================================== --}}
    <section class="stats">


        {{-- =====================================================
             PENJUALAN HARI INI
        ====================================================== --}}
        <article class="stat-card">

            <div class="stat-icon">

                <i class="fa-solid fa-money-bill-wave"></i>

            </div>


            <div>

                <div class="stat-label">
                    Penjualan Hari Ini
                </div>


                <div class="stat-value">

                    Rp {{ number_format(
                        $penjualanHariIni ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}

                </div>

            </div>

        </article>



        {{-- =====================================================
             TOTAL TRANSAKSI
        ====================================================== --}}
        <article class="stat-card">

            <div class="stat-icon gold">

                <i class="fa-solid fa-receipt"></i>

            </div>


            <div>

                <div class="stat-label">
                    Total Transaksi
                </div>


                <div class="stat-value">

                    {{ $transaksiHariIni ?? 0 }}

                </div>


                <div class="stat-note">
                    Hari ini
                </div>

            </div>

        </article>



        {{-- =====================================================
             PELANGGAN BARU
        ====================================================== --}}
        <article class="stat-card">

            <div class="stat-icon green">

                <i class="fa-solid fa-user-group"></i>

            </div>


            <div>

                <div class="stat-label">
                    Pelanggan Baru
                </div>


                <div class="stat-value">

                    {{ $pelangganBaruHariIni ?? 0 }}

                </div>


                <div class="stat-note">
                    Hari ini
                </div>

            </div>

        </article>



        {{-- =====================================================
             LABA BERSIH
        ====================================================== --}}
        <article class="stat-card">

            <div class="stat-icon red">

                <i class="fa-solid fa-chart-line"></i>

            </div>


            <div>

                <div class="stat-label">
                    Laba Bersih
                </div>


                <div class="stat-value stat-value-unavailable">
                    Belum tersedia
                </div>

            </div>

        </article>

    </section>



    {{-- =========================================================
         KOLOM UTAMA
    ========================================================== --}}
    <div class="two-col">


        {{-- =====================================================
             PENJUALAN MINGGUAN
        ====================================================== --}}
        <section class="panel weekly-sales-panel">

            <div class="panel-head">

                <div>

                    <h2>
                        Penjualan Mingguan
                    </h2>

                    <p>
                        Ringkasan omzet 7 hari terakhir.
                    </p>

                </div>


                <a
                    class="panel-link"
                    href="{{ route('owner.sales') }}"
                >
                    Lihat Detail
                </a>

            </div>



            {{-- =================================================
                 DATA GRAFIK
            ================================================== --}}
            @php

                $weeklyTrend = $weeklyTrend ?? collect();

                $maxWeekly = $weeklyTrend->max('total') ?: 1;

            @endphp



            <div class="chart">

                @forelse($weeklyTrend as $day)

                    @php

                        $total = (float) ($day['total'] ?? 0);

                        $label = $day['label'] ?? '';

                        $height = $total > 0
                            ? max(
                                12,
                                round(
                                    ($total / $maxWeekly) * 100
                                )
                            )
                            : 4;

                    @endphp


                    <div class="bar-item">

                        <div
                            class="bar"
                            style="--h:{{ $height }}%"
                            title="Rp {{ number_format(
                                $total,
                                0,
                                ',',
                                '.'
                            ) }}"
                        ></div>


                        <span class="bar-label">
                            {{ $label }}
                        </span>

                    </div>

                @empty

                    <div class="chart-empty">

                        <i class="fa-solid fa-chart-column"></i>

                        <span>
                            Belum ada data penjualan.
                        </span>

                    </div>

                @endforelse

            </div>

        </section>



        {{-- =====================================================
             STATUS OPERASIONAL
        ====================================================== --}}
        <section class="panel operational-panel">


            {{-- =================================================
                 HEADER OPERASIONAL
            ================================================== --}}
            <div class="panel-head operational-panel-head">

                <div class="operational-heading">

                    <span class="operational-eyebrow">
                        OPERASIONAL HARI INI
                    </span>

                    <h2>
                        Status Operasional
                    </h2>

                    <p>
                        Pantau aktivitas Quattro Coffee secara real-time.
                    </p>

                </div>


                {{-- =================================================
                     SINGLE ACTIVE STATUS
                ================================================== --}}
                <div
                    class="operational-live"
                    aria-label="Sistem aktif"
                >

                    <span class="operational-live-dot"></span>

                    <span>
                        Aktif
                    </span>

                </div>

            </div>



            {{-- =====================================================
                 LIST STATUS OPERASIONAL
            ====================================================== --}}
            <div class="operational-list">


                {{-- =================================================
                     MENUNGGU KITCHEN
                ================================================== --}}
                <div class="operational-item waiting">

                    <div class="operational-icon">

                        <i class="fa-solid fa-clock"></i>

                    </div>


                    <div class="operational-name">

                        <strong>
                            Menunggu Kitchen
                        </strong>

                        <span>
                            Pesanan baru menunggu diproses
                        </span>

                    </div>


                    <div class="operational-number">

                        {{ $pesananMenunggu ?? 0 }}

                    </div>

                </div>



                {{-- =================================================
                     SEDANG DIPROSES
                ================================================== --}}
                <div class="operational-item processing">

                    <div class="operational-icon">

                        <i class="fa-solid fa-mug-hot"></i>

                    </div>


                    <div class="operational-name">

                        <strong>
                            Sedang Diproses
                        </strong>

                        <span>
                            Pesanan sedang dibuat
                        </span>

                    </div>


                    <div class="operational-number">

                        {{ $pesananDiproses ?? 0 }}

                    </div>

                </div>



                {{-- =================================================
                     SIAP DIAMBIL
                ================================================== --}}
                <div class="operational-item ready">

                    <div class="operational-icon">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>


                    <div class="operational-name">

                        <strong>
                            Siap Diambil
                        </strong>

                        <span>
                            Menunggu pelanggan mengambil
                        </span>

                    </div>


                    <div class="operational-number">

                        {{ $pesananSelesai ?? 0 }}

                    </div>

                </div>



                {{-- =================================================
                     MENU AKTIF
                ================================================== --}}
                <div class="operational-item menu">

                    <div class="operational-icon">

                        <i class="fa-solid fa-mug-saucer"></i>

                    </div>


                    <div class="operational-name">

                        <strong>
                            Menu Aktif
                        </strong>

                        <span>
                            Menu tersedia untuk pelanggan
                        </span>

                    </div>


                    <div class="operational-number">

                        {{ $menuAktif ?? 0 }}

                    </div>

                </div>

            </div>

        </section>

    </div>



    {{-- =========================================================
         TRANSAKSI TERBARU
         FULL WIDTH
    ========================================================== --}}
    <section class="panel transaction-panel">


        {{-- =====================================================
             HEADER TRANSAKSI
        ====================================================== --}}
        <div class="panel-head">

            <div>

                <h2>
                    Transaksi Terbaru
                </h2>

                <p>
                    Aktivitas pembayaran terbaru.
                </p>

            </div>


            <a
                class="panel-link"
                href="{{ route('owner.sales') }}"
            >
                Lihat Semua
            </a>

        </div>



        {{-- =====================================================
             TABEL TRANSAKSI
        ====================================================== --}}
        <div class="table-wrap">

            <table class="owner-transaction-table">


                {{-- =================================================
                     LEBAR KOLOM
                ================================================== --}}
                <colgroup>

                    <col class="col-order">

                    <col class="col-customer">

                    <col class="col-cashier">

                    <col class="col-total">

                    <col class="col-status">

                </colgroup>



                {{-- =================================================
                     TABLE HEADER
                ================================================== --}}
                <thead>

                    <tr>

                        <th>
                            Order
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Kasir
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>



                {{-- =================================================
                     TABLE BODY
                ================================================== --}}
                <tbody>

                    @forelse(
                        ($transaksiTerbaru ?? collect()) as $order
                    )

                        <tr>


                            {{-- =====================================
                                 ORDER
                            ====================================== --}}
                            <td>

                                <strong class="order-number">

                                    {{ $order->order_number }}

                                </strong>

                            </td>



                            {{-- =====================================
                                 PELANGGAN
                            ====================================== --}}
                            <td>

                                <span class="transaction-customer">

                                    {{ $order->customer_name ?: '-' }}

                                </span>

                            </td>



                            {{-- =====================================
                                 KASIR
                            ====================================== --}}
                            <td>

                                @if(
                                    $order->payment &&
                                    $order->payment->cashier_name
                                )

                                    <span class="transaction-cashier">

                                        {{ $order->payment->cashier_name }}

                                    </span>

                                @else

                                    <span class="transaction-empty-value">
                                        -
                                    </span>

                                @endif

                            </td>



                            {{-- =====================================
                                 TOTAL
                            ====================================== --}}
                            <td>

                                <strong class="transaction-total">

                                    Rp {{ number_format(
                                        $order->total ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </td>



                            {{-- =====================================
                                 STATUS
                            ====================================== --}}
                            <td>


                                {{-- BELUM DIBAYAR --}}
                                @if(
                                    $order->payment_status === 'Belum Dibayar'
                                )

                                    <span class="badge">

                                        Belum Dibayar

                                    </span>


                                {{-- MENUNGGU --}}
                                @elseif(
                                    $order->status === 'Menunggu'
                                )

                                    <span class="badge green">

                                        Menunggu Kitchen

                                    </span>


                                {{-- DIPROSES --}}
                                @elseif(
                                    $order->status === 'Diproses'
                                )

                                    <span class="badge green">

                                        Diproses

                                    </span>


                                {{-- SELESAI --}}
                                @elseif(
                                    $order->status === 'Selesai'
                                )

                                    <span class="badge green">

                                        Siap Diambil

                                    </span>


                                {{-- SUDAH DIAMBIL --}}
                                @elseif(
                                    $order->status === 'Sudah Diambil'
                                )

                                    <span class="badge green">

                                        Sudah Diambil

                                    </span>


                                {{-- DIBATALKAN --}}
                                @elseif(
                                    $order->status === 'Dibatalkan'
                                )

                                    <span class="badge red">

                                        Dibatalkan

                                    </span>


                                {{-- STATUS LAIN --}}
                                @else

                                    <span class="badge">

                                        {{ $order->status ?: '-' }}

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty


                        {{-- =========================================
                             EMPTY STATE
                        ========================================== --}}
                        <tr>

                            <td
                                colspan="5"
                                class="table-empty"
                            >

                                <div class="table-empty-content">

                                    <i class="fa-solid fa-receipt"></i>

                                    <span>
                                        Belum ada transaksi.
                                    </span>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>



{{-- =============================================================
     REFRESH DASHBOARD
============================================================= --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const refreshButton =
        document.getElementById('ownerRefresh');


    if (!refreshButton) {
        return;
    }


    refreshButton.addEventListener('click', function () {

        const button = this;

        const icon =
            button.querySelector('i');


        /*
         * Mencegah tombol ditekan
         * berkali-kali saat reload.
         */
        if (button.classList.contains('is-refreshing')) {
            return;
        }


        /*
         * Tandai sedang refresh.
         */
        button.classList.add('is-refreshing');

        button.disabled = true;


        /*
         * Jalankan animasi icon.
         */
        if (icon) {
            icon.classList.add('fa-spin');
        }


        /*
         * Berikan sedikit jeda agar
         * animasi terlihat sebelum reload.
         */
        setTimeout(function () {

            window.location.reload();

        }, 450);

    });

});

</script>

@endpush

@endsection