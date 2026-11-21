@extends('layouts.owner')

@section('title', 'Dashboard Owner - Quattro Coffee')

@push('styles')
<style>
.owner-dashboard{display:grid;align-content:start;gap:24px;padding:4px 2px 30px}
.owner-dashboard .page-head{align-items:center;margin:0;padding:4px 2px 2px}
.owner-dashboard .page-head h1{margin:7px 0 6px;font-size:32px;letter-spacing:-1.25px}
.owner-dashboard .page-head p{max-width:560px;line-height:1.6}
.owner-dashboard .head-actions{gap:10px;flex-wrap:wrap;justify-content:flex-end}
.owner-dashboard .stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;margin:0}
.owner-dashboard .stat-card{position:relative;overflow:hidden;min-width:0;min-height:126px;padding:21px;border-color:#eee2d5;border-radius:20px;background:linear-gradient(145deg,#fff 0%,#fffdf9 100%);box-shadow:0 8px 24px rgba(77,48,18,.045);transition:transform .2s ease,box-shadow .2s ease}
.owner-dashboard .stat-card::before{position:absolute;top:0;right:18px;left:18px;height:3px;border-radius:0 0 5px 5px;background:linear-gradient(90deg,#754000,#d99a51 58%,transparent);content:"";opacity:.75}
.owner-dashboard .stat-card:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(77,48,18,.09)}
.owner-dashboard .stat-card>div:last-child{min-width:0}
.owner-dashboard .stat-icon{width:48px;height:48px;border-radius:16px;font-size:18px}
.owner-dashboard .stat-label{font-size:12px;font-weight:600;letter-spacing:.2px}
.owner-dashboard .stat-value{margin-top:7px;font-size:clamp(19px,1.7vw,25px);letter-spacing:-.45px;line-height:1.2;overflow-wrap:anywhere}
.owner-dashboard .stat-note{margin-top:7px;color:#9a8068;font-size:11px;line-height:1.45}
.owner-dashboard .two-col{grid-template-columns:minmax(0,1.55fr) minmax(320px,.95fr);gap:22px;align-items:stretch}
.owner-dashboard .panel{min-width:0;padding:24px;border-color:#eee2d5;border-radius:21px;box-shadow:0 8px 26px rgba(77,48,18,.045)}
.owner-dashboard .panel-head{align-items:center;gap:14px;margin-bottom:20px}
.owner-dashboard .panel-head h2{font-size:17px;letter-spacing:-.2px}
.owner-dashboard .panel-head p{font-size:12px;line-height:1.55}
.owner-dashboard .panel-link{white-space:nowrap;padding:9px 12px;border:1px solid #eadcc9;border-radius:11px;background:#fffaf4}
.owner-dashboard .panel-link:hover{background:#f7ecdf}
.owner-dashboard .weekly-sales-panel{display:flex;flex-direction:column}
.owner-dashboard .chart{height:260px;gap:10px;margin-top:auto;padding:24px 8px 0;border-color:#eadfd1;scrollbar-color:#d9c4a8 transparent;scrollbar-width:thin}
.owner-dashboard .bar-item{min-width:34px!important;gap:9px}
.owner-dashboard .bar{width:min(34px,72%);min-height:5px;background:linear-gradient(180deg,#d99a51 0%,#784108 100%);border-radius:9px 9px 3px 3px;box-shadow:0 4px 9px rgba(117,64,0,.12);transition:filter .2s,transform .2s}
.owner-dashboard .bar-item:hover .bar{filter:brightness(1.08);transform:scaleX(1.06)}
.owner-dashboard .bar-label{font-size:10px;white-space:nowrap}
.owner-dashboard .chart-empty{margin:auto}
.owner-dashboard .operational-panel-head{align-items:flex-start}
.owner-dashboard .operational-eyebrow{font-size:9px;letter-spacing:1.35px}
.owner-dashboard .operational-heading h2{margin-top:5px}
.owner-dashboard .operational-panel{padding:25px}
.owner-dashboard .operational-panel-head{align-items:center;margin-bottom:18px}
.owner-dashboard .operational-live{padding:7px 11px;border:1px solid #dceee3;background:#f1faf4;font-size:11px}
.owner-dashboard .operational-list{display:grid;gap:10px;margin-top:0}
.owner-dashboard .operational-item{position:relative;grid-template-columns:42px minmax(0,1fr) 42px;min-height:72px;gap:13px;padding:12px 13px;border:1px solid #eee5da;border-radius:15px;background:#fff;box-shadow:0 3px 10px rgba(68,45,23,.025);transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease}
.owner-dashboard .operational-item:hover{padding:12px 13px;border-color:#e2d2be;border-radius:15px;background:#fff;transform:translateY(-1px);box-shadow:0 8px 18px rgba(68,45,23,.065)}
.owner-dashboard .operational-icon{width:40px;height:40px;border-radius:13px;font-size:15px}
.owner-dashboard .operational-name{min-width:0;gap:5px}
.owner-dashboard .operational-name strong{font-size:12px;line-height:1.35}
.owner-dashboard .operational-name span{font-size:10px;line-height:1.45}
.owner-dashboard .operational-number{display:flex;align-items:center;justify-content:center;min-width:38px;height:38px;padding:0;border:1px solid #f0e5d6;border-radius:12px;background:#fbf6ef;color:#69400f;font-size:15px;font-variant-numeric:tabular-nums}
.owner-dashboard .operational-item.waiting .operational-number{border-color:#f3e2c4;background:#fff7e9;color:#a86912}
.owner-dashboard .operational-item.processing .operational-number{border-color:#eadbca;background:#f8f1e8;color:#80501d}
.owner-dashboard .operational-item.ready .operational-number{border-color:#d8eee4;background:#eff9f4;color:#168653}
.owner-dashboard .operational-item.menu .operational-number{border-color:#eadfd2;background:#f7f1e9;color:#754000}
.owner-dashboard .top-customers-panel,.owner-dashboard .transaction-panel{margin:0!important}
.owner-dashboard .table-wrap{border:1px solid #f0e7dd;border-radius:14px}
.owner-dashboard table{min-width:650px}
.owner-dashboard th{padding:13px 14px;background:#fdfbf8;font-size:10px}
.owner-dashboard td{padding:14px;font-size:12px}
.owner-dashboard tbody tr{transition:background .15s ease}
.owner-dashboard tbody tr:hover{background:#fffcf7}
.owner-dashboard .transaction-panel .panel-head{display:grid;grid-template-columns:minmax(0,1fr) auto auto}
.owner-dashboard .transaction-panel .search{max-width:310px!important;margin:0}
.owner-dashboard .transaction-panel .panel-link{justify-self:end}
.owner-dashboard .weekly-sales-panel .panel-head{align-items:center}
.owner-dashboard .weekly-sales-panel .owner-trend-chart{width:100%;margin-top:2px}
.owner-dashboard .weekly-sales-panel .trend-chart-meta{min-height:22px}
.owner-dashboard .weekly-sales-panel .trend-chart-meta small{padding:5px 9px;border:1px solid #f0e5d7;border-radius:999px;background:#fffaf4;color:#90765b;white-space:nowrap}
@media(max-width:1180px){.owner-dashboard .stats{grid-template-columns:repeat(2,minmax(0,1fr))}.owner-dashboard .two-col{grid-template-columns:minmax(0,1fr) minmax(280px,.8fr)}}
@media(max-width:900px){.owner-dashboard .two-col{grid-template-columns:1fr}.owner-dashboard .weekly-sales-panel{min-height:330px}.owner-dashboard .operational-panel{min-height:0}}
@media(max-width:680px){.owner-dashboard{gap:18px;padding:0 0 22px}.owner-dashboard .page-head{display:block;padding:0}.owner-dashboard .page-head h1{font-size:26px}.owner-dashboard .head-actions{justify-content:flex-start;margin-top:14px}.owner-dashboard .panel{padding:19px;border-radius:18px}.owner-dashboard .panel-head{align-items:flex-start}.owner-dashboard .transaction-panel .panel-head{grid-template-columns:1fr auto}.owner-dashboard .transaction-panel .search{grid-column:1/-1;grid-row:2;max-width:none!important;width:100%}.owner-dashboard .transaction-panel .panel-link{grid-column:2;grid-row:1}}
@media(max-width:520px){.owner-dashboard .stats{grid-template-columns:1fr;gap:12px}.owner-dashboard .stat-card{min-height:100px;padding:17px}.owner-dashboard .stat-value{font-size:22px}.owner-dashboard .panel-head{flex-direction:column;align-items:stretch}.owner-dashboard .panel-head>.panel-link{align-self:flex-start}.owner-dashboard .transaction-panel .panel-head{display:flex}.owner-dashboard .transaction-panel .search{order:3}.owner-dashboard .transaction-panel .panel-link{align-self:flex-start}.owner-dashboard .chart{height:230px}.owner-dashboard .operational-panel-head{flex-direction:row;align-items:flex-start;justify-content:space-between}.owner-dashboard .operational-panel .operational-live{flex:0 0 auto}.owner-dashboard .operational-item{padding:11px}}
@media(prefers-reduced-motion:reduce){.owner-dashboard .stat-card,.owner-dashboard .bar{transition:none}}
</style>
@endpush

@section('content')

<div class="grid owner-dashboard">

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

            @include('owner.partials.period-picker')

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
                    Total Penjualan
                </div>


                <div class="stat-value">

                    Rp {{ number_format(
                        $penjualanHariIni ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}

                </div>
                <div class="stat-note">Periode {{ $periodLabel }}</div>

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
                    Periode {{ $periodLabel }}
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
                    Periode {{ $periodLabel }}
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


                <div class="stat-value">
                    Rp {{ number_format($financialSummary['laba_bersih'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="stat-note">Periode {{ $periodLabel }}</div>

                @if(($financialSummary['items_without_cost'] ?? 0) > 0)
                    <div class="stat-note">Lengkapi harga pokok untuk laba yang akurat</div>
                @endif

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
                        Penjualan Harian
                    </h2>

                    <p>
                        Ringkasan omzet harian · Periode {{ $periodLabel }}.
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
            @include('owner.partials.sales-trend-chart', ['chartData' => $weeklyTrend ?? collect(), 'chartId' => 'dashboard-sales'])

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
                        Pesanan dibayar · Periode {{ $periodLabel }}.
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
                            Status saat ini · {{ now()->translatedFormat('d M Y') }}
                        </span>

                    </div>


                    <div class="operational-number">

                        {{ $menuAktif ?? 0 }}

                    </div>

                </div>

            </div>

        </section>

    </div>



    <section class="panel top-customers-panel">
        <div class="panel-head">
            <div>
                <h2>Pelanggan dengan Pesanan Terbanyak</h2>
                <p>Peringkat berdasarkan pesanan berhasil · Periode {{ $periodLabel }}.</p>
            </div>
            <label class="search" style="max-width:300px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" data-search="#topCustomerRows tr" placeholder="Cari nama pelanggan..." aria-label="Cari pelanggan">
            </label>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Peringkat</th><th>Pelanggan</th><th>Jumlah Pesanan</th><th>Total Belanja</th><th>Periode</th></tr></thead>
                <tbody id="topCustomerRows">
                    @forelse($pelangganTeratas as $index => $pelanggan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pelanggan->customer_name ?: 'Pelanggan Tamu' }}</td>
                            <td>{{ $pelanggan->total_orders }} pesanan</td>
                            <td>Rp {{ number_format($pelanggan->total_spent, 0, ',', '.') }}</td>
                            <td>{{ $periodLabel }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#888;padding:24px;">Belum ada pesanan berhasil pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

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
                    Aktivitas pembayaran terbaru · Periode {{ $periodLabel }}.
                </p>

            </div>


            <label class="search" style="max-width:280px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" data-search=".owner-transaction-table tbody tr" placeholder="Cari order atau pelanggan..." aria-label="Cari transaksi">
            </label>

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

                    <col class="col-date">

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

                        <th>Tanggal</th>

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

                            <td>{{ $order->payment?->paid_at?->translatedFormat('d M Y, H:i') ?? '-' }}</td>



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

                                @php
                                    $cashierName = $order->payment?->cashier_name
                                        ?: ($order->user?->role === 'kasir' ? $order->user->name : null);
                                @endphp
                                @if($cashierName)

                                    <span class="transaction-cashier">

                                        {{ $cashierName }}

                                    </span>

                                @else

                                    <span class="transaction-empty-value">
                                        {{ $order->payment?->payment_method === 'QRIS' ? 'Online' : 'Belum tercatat' }}
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
                                colspan="6"
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
