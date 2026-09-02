@extends('layouts.kasir')

@section('title', 'Pembayaran - Quattro Coffee')

@section('content')

<header class="topbar">
    <div>
        <span class="eyebrow">CASHIER CONTROL</span>
        <h1>Pembayaran</h1>
        <p>Proses pembayaran pelanggan dengan cepat dan mudah.</p>
    </div>
</header>

<section class="payment-grid">

    {{-- ========================================= --}}
    {{-- DAFTAR PESANAN --}}
    {{-- ========================================= --}}

    <div class="panel">

        <div class="panel-head">
            <div>
                <h2>Pilih Pesanan</h2>
                <p>Pilih transaksi yang akan dibayar.</p>
            </div>
        </div>

        <div class="payment-orders">

            @forelse($orders as $order)

                <button
                    type="button"
                    class="payment-order"
                    onclick="selectPayment(
                        this,
                        '{{ $order->order_number }}',
                        {{ $order->total }}
                    )"
                >

                    {{-- Nomor Order --}}
                    <span class="payment-id">
                        {{ $order->order_number }}
                    </span>

                    {{-- Nama Customer --}}
                    <strong>
                        {{ $order->customer_name }}
                    </strong>

                    {{-- Meja --}}
                    <span style="font-size: 13px; opacity: .7;">
                        Meja:
                        {{ $order->table_number ?? 'Take Away' }}
                    </span>

                    {{-- Daftar Produk --}}
                    <small class="order-items">

                        @forelse($order->items as $item)

                            <span>
                                {{ $item->product_name }}
                                × {{ $item->quantity }}
                            </span>

                            @if(!$loop->last)
                                <br>
                            @endif

                        @empty

                            Tidak ada detail produk.

                        @endforelse

                    </small>

                    {{-- Total --}}
                    <b>
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </b>

                </button>

            @empty

                <div style="padding: 30px; text-align: center;">
                    <i class="fa-solid fa-receipt"
                       style="font-size: 35px; opacity: .4;"></i>

                    <p>
                        Belum ada pesanan.
                    </p>
                </div>

            @endforelse

        </div>

    </div>


    {{-- ========================================= --}}
    {{-- DETAIL PEMBAYARAN --}}
    {{-- ========================================= --}}

    <div class="panel payment-card">

        <div class="panel-head">

            <div>

                <h2>Detail Pembayaran</h2>

                <p id="selectedText">
                    Belum ada pesanan dipilih.
                </p>

            </div>

        </div>


        {{-- TOTAL --}}
        <div class="amount-box">

            <span>Total</span>

            <strong id="paymentTotal">
                Rp 0
            </strong>

        </div>


        {{-- METODE PEMBAYARAN --}}
        <label>
            Metode Pembayaran

            <select id="paymentMethod">

                <option value="Cash">
                    Cash
                </option>

                <option value="QRIS">
                    QRIS
                </option>

                <option value="Debit / E-Wallet">
                    Debit / E-Wallet
                </option>

            </select>

        </label>


        {{-- UANG DITERIMA --}}
        <label>
            Uang Diterima

            <input
                id="cashInput"
                type="number"
                min="0"
                placeholder="Masukkan nominal"
                oninput="calculateChange()"
            >

        </label>


        {{-- KEMBALIAN --}}
        <div class="change-row">

            <span>
                Kembalian
            </span>

            <strong id="changeAmount">
                Rp 0
            </strong>

        </div>


        {{-- TOMBOL BAYAR --}}
        <button
            type="button"
            class="primary-btn full"
            onclick="processPayment()"
        >

            <i class="fa-solid fa-check-circle"></i>

            Selesaikan Pembayaran

        </button>

    </div>

</section>


{{-- ========================================= --}}
{{-- JAVASCRIPT PEMBAYARAN --}}
{{-- ========================================= --}}

<script>

    let selectedOrder = null;
    let selectedTotal = 0;


    /*
    |--------------------------------------------------------------------------
    | Pilih Pesanan
    |--------------------------------------------------------------------------
    */

    function selectPayment(element, orderNumber, total)
    {
        selectedOrder = orderNumber;
        selectedTotal = Number(total);


        // Hapus class active dari semua pesanan
        document.querySelectorAll('.payment-order')
            .forEach(function(order) {

                order.classList.remove('active');

            });


        // Tandai pesanan yang dipilih
        element.classList.add('active');


        // Tampilkan informasi order
        document.getElementById('selectedText').innerText =
            'Pesanan ' + orderNumber + ' dipilih.';


        // Tampilkan total
        document.getElementById('paymentTotal').innerText =
            formatRupiah(selectedTotal);


        // Reset uang diterima
        document.getElementById('cashInput').value = '';


        // Reset kembalian
        document.getElementById('changeAmount').innerText =
            'Rp 0';
    }


    /*
    |--------------------------------------------------------------------------
    | Hitung Kembalian
    |--------------------------------------------------------------------------
    */

    function calculateChange()
    {
        const cash =
            Number(document.getElementById('cashInput').value);


        if (!selectedOrder)
        {
            return;
        }


        const change =
            cash - selectedTotal;


        if (change >= 0)
        {
            document.getElementById('changeAmount').innerText =
                formatRupiah(change);
        }
        else
        {
            document.getElementById('changeAmount').innerText =
                'Kurang ' + formatRupiah(Math.abs(change));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Format Rupiah
    |--------------------------------------------------------------------------
    */

    function formatRupiah(number)
    {
        return 'Rp ' + Number(number)
            .toLocaleString('id-ID');
    }


    /*
    |--------------------------------------------------------------------------
    | Proses Pembayaran
    |--------------------------------------------------------------------------
    */

    function processPayment()
    {

        if (!selectedOrder)
        {
            alert('Silakan pilih pesanan terlebih dahulu.');
            return;
        }


        const method =
            document.getElementById('paymentMethod').value;


        const cash =
            Number(document.getElementById('cashInput').value);


        // Untuk pembayaran cash
        if (method === 'Cash')
        {

            if (!cash)
            {
                alert('Masukkan uang yang diterima.');
                return;
            }


            if (cash < selectedTotal)
            {
                alert(
                    'Uang yang diterima belum mencukupi.'
                );

                return;
            }

        }


        alert(
            'Pembayaran ' +
            selectedOrder +
            ' berhasil diproses dengan metode ' +
            method +
            '.'
        );

    }

</script>

@endsection