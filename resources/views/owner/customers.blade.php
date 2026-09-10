@extends('layouts.owner')

@section('title', 'Pelanggan - Quattro Coffee')

@section('content')

<div class="grid">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <header class="page-head">

        <div>

            <span class="eyebrow">
                CUSTOMER MANAGEMENT
            </span>

            <h1>
                Pelanggan
            </h1>

            <p>
                Lihat pertumbuhan dan aktivitas pelanggan.
            </p>

        </div>

    </header>


    {{-- =========================================================
         ALERT SUCCESS
    ========================================================== --}}
    @if(session('success'))

        <div class="customer-alert success">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =========================================================
         ALERT ERROR
    ========================================================== --}}
    @if($errors->any())

        <div class="customer-alert error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <div>

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        </div>

    @endif


    {{-- =========================================================
         STATISTIK PELANGGAN
    ========================================================== --}}
    <section class="stats">


        {{-- TOTAL PELANGGAN --}}
        <article class="stat-card">

            <div class="stat-icon">

                <i class="fa-solid fa-user-group"></i>

            </div>

            <div>

                <div class="stat-label">
                    Total Pelanggan
                </div>

                <div class="stat-value">
                    {{ $totalCustomers ?? 0 }}
                </div>

            </div>

        </article>



        {{-- PELANGGAN BARU --}}
        <article class="stat-card">

            <div class="stat-icon green">

                <i class="fa-solid fa-user-plus"></i>

            </div>

            <div>

                <div class="stat-label">
                    Pelanggan Baru
                </div>

                <div class="stat-value">
                    {{ $newThisMonth ?? 0 }}
                </div>

                <div class="stat-note">
                    Bulan ini
                </div>

            </div>

        </article>

    </section>



    {{-- =========================================================
         DAFTAR PELANGGAN
    ========================================================== --}}
    <section class="panel customer-panel">


        {{-- PANEL HEADER --}}
        <div class="panel-head">

            <div>

                <h2>
                    Daftar Pelanggan
                </h2>

                <p>
                    Semua akun dengan role customer.
                </p>

            </div>


            {{-- =================================================
                 SEARCH
            ================================================== --}}
            <form
                method="GET"
                action="{{ route('owner.customers.index') }}"
                class="customer-search"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari pelanggan..."
                    autocomplete="off"
                >

                @if(request('q'))

                    <a
                        href="{{ route('owner.customers.index') }}"
                        class="search-clear"
                        title="Hapus pencarian"
                    >

                        <i class="fa-solid fa-xmark"></i>

                    </a>

                @endif

            </form>

        </div>



        {{-- =====================================================
             TABEL PELANGGAN
        ====================================================== --}}
        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Jumlah Pesanan
                        </th>

                        <th>
                            Bergabung
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($customers as $customer)

                        <tr>

                            {{-- =================================
                                 NAMA PELANGGAN
                            ================================== --}}
                            <td>

                                <div class="customer-name">

                                    <div class="customer-avatar">

                                        {{ strtoupper(
                                            substr($customer->name, 0, 1)
                                        ) }}

                                    </div>


                                    <div class="customer-info">

                                        <strong>
                                            {{ $customer->name }}
                                        </strong>

                                        <span>
                                            Customer
                                        </span>

                                    </div>

                                </div>

                            </td>



                            {{-- =================================
                                 EMAIL
                            ================================== --}}
                            <td>

                                <span class="customer-email">

                                    {{ $customer->email }}

                                </span>

                            </td>



                            {{-- =================================
                                 JUMLAH PESANAN
                            ================================== --}}
                            <td>

                                <span class="order-count">

                                    {{ $customer->orders_count }}

                                    <small>
                                        pesanan
                                    </small>

                                </span>

                            </td>



                            {{-- =================================
                                 TANGGAL BERGABUNG
                            ================================== --}}
                            <td>

                                <span class="join-date">

                                    {{ $customer->created_at->format('d M Y') }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        {{-- =====================================
                             DATA KOSONG
                        ====================================== --}}
                        <tr>

                            <td
                                colspan="4"
                                class="table-empty"
                            >

                                <div class="empty-icon">

                                    <i class="fa-solid fa-users"></i>

                                </div>

                                <strong>
                                    Belum ada pelanggan
                                </strong>

                                <span>
                                    Pelanggan yang melakukan registrasi
                                    akan muncul di sini.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection