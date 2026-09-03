@extends('layouts.kasir')

@section('title', 'Pembayaran - Quattro Coffee')

@section('content')

<div class="grid">

    {{-- ========================================= --}}
    {{-- HEADER --}}
    {{-- ========================================= --}}

    <header class="topbar">

        <div>

            <span class="eyebrow">
                CASHIER CONTROL
            </span>

            <h1>
                Pembayaran
            </h1>

            <p>
                Proses pembayaran pelanggan dengan cepat dan mudah.
            </p>

        </div>

    </header>



    {{-- ========================================= --}}
    {{-- PAYMENT GRID --}}
    {{-- ========================================= --}}

    <section class="payment-grid">

        {{-- ========================================= --}}
        {{-- DAFTAR PESANAN --}}
        {{-- ========================================= --}}

        <div class="panel">

            <div class="panel-head">

                <div>

                    <h2>
                        Pilih Pesanan
                    </h2>

                    <p>
                        Pilih transaksi yang akan dibayar.
                    </p>

                </div>

            </div>


            <div class="payment-orders">

                @forelse($orders as $order)

                    <button
                        type="button"
                        class="payment-order"

                        onclick="selectPayment(
                            this,
                            '{{ $order->id }}',
                            '{{ $order->order_number }}',
                            {{ $order->total }}
                        )"
                    >

                        {{-- NOMOR ORDER --}}
                        <span class="payment-id">

                            {{ $order->order_number }}

                        </span>


                        {{-- CUSTOMER --}}
                        <strong>

                            {{ $order->customer_name }}

                        </strong>


                        {{-- MEJA --}}
                        <span style="font-size: 13px; opacity: .7;">

                            Meja:

                            {{ $order->table_number ?? 'Take Away' }}

                        </span>


                        {{-- PRODUK --}}
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


                        {{-- TOTAL --}}
                        <b>

                            Rp {{ number_format($order->total, 0, ',', '.') }}

                        </b>

                    </button>

                @empty

                    <div style="padding: 30px; text-align: center;">

                        <i
                            class="fa-solid fa-receipt"
                            style="font-size: 35px; opacity: .4;"
                        ></i>

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

                    <h2>
                        Detail Pembayaran
                    </h2>

                    <p id="selectedText">

                        Belum ada pesanan dipilih.

                    </p>

                </div>

            </div>


            {{-- TOTAL --}}
            <div class="amount-box">

                <span>
                    Total
                </span>

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


            {{-- FORM PEMBAYARAN --}}
            <form
                id="paymentForm"
                method="POST"
                style="display: none;"
            >

                @csrf

                <input
                    type="hidden"
                    name="payment_method"
                    id="paymentMethodInput"
                >

            </form>


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

</div>



{{-- ================================================= --}}
{{-- MODAL NOTIFIKASI --}}
{{-- ================================================= --}}

<div
    id="paymentModal"
    class="delete-modal-overlay"
>

    <div class="delete-modal">

        {{-- ICON --}}
        <div
            class="delete-modal-icon"
            id="paymentModalIcon"
        >

            <i
                class="fa-solid fa-circle-exclamation"
                id="paymentModalIconElement"
            ></i>

        </div>


        {{-- JUDUL --}}
        <h2 id="paymentModalTitle">
            Perhatian
        </h2>


        {{-- PESAN --}}
        <p id="paymentModalMessage">
            Silakan periksa pembayaran.
        </p>


        {{-- DETAIL TAMBAHAN --}}
        <div
            class="delete-warning"
            id="paymentModalWarning"
            style="display: none;"
        >
        </div>


        {{-- TOMBOL --}}
        <div class="delete-modal-actions">

            {{-- BATAL --}}
            <button
                type="button"
                class="delete-cancel-btn"
                id="paymentCancelButton"
                onclick="closePaymentModal()"
            >

                Batal

            </button>


            {{-- KONFIRMASI --}}
            <button
                type="button"
                class="delete-confirm-btn"
                id="paymentConfirmButton"
                onclick="confirmPayment()"
            >

                <i class="fa-solid fa-check"></i>

                Ya, Bayar

            </button>

        </div>

    </div>

</div>



{{-- ================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ================================================= --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | DATA PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    let selectedOrderId = null;

    let selectedOrder = null;

    let selectedTotal = 0;



    /*
    |--------------------------------------------------------------------------
    | PILIH PESANAN
    |--------------------------------------------------------------------------
    */

    function selectPayment(
        element,
        orderId,
        orderNumber,
        total
    ) {

        selectedOrderId = orderId;

        selectedOrder = orderNumber;

        selectedTotal = Number(total);


        // Hapus active dari semua pesanan
        document.querySelectorAll('.payment-order')
            .forEach(function(order) {

                order.classList.remove('active');

            });


        // Tambahkan active
        element.classList.add('active');


        // Tampilkan order
        document.getElementById('selectedText').innerText =
            'Pesanan ' + orderNumber + ' dipilih.';


        // Tampilkan total
        document.getElementById('paymentTotal').innerText =
            formatRupiah(selectedTotal);


        // Reset uang
        document.getElementById('cashInput').value = '';


        // Reset kembalian
        document.getElementById('changeAmount').innerText =
            'Rp 0';

    }



    /*
    |--------------------------------------------------------------------------
    | HITUNG KEMBALIAN
    |--------------------------------------------------------------------------
    */

    function calculateChange()
    {

        const cash =
            Number(
                document.getElementById('cashInput').value
            );


        if (!selectedOrderId)
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
                'Kurang ' +
                formatRupiah(Math.abs(change));

        }

    }



    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    function formatRupiah(number)
    {

        return 'Rp ' +
            Number(number).toLocaleString('id-ID');

    }



    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL
    |--------------------------------------------------------------------------
    */

    function openPaymentModal(
        title,
        message,
        warning = '',
        type = 'warning',
        showConfirm = false
    )
    {

        const modal =
            document.getElementById('paymentModal');

        const modalTitle =
            document.getElementById('paymentModalTitle');

        const modalMessage =
            document.getElementById('paymentModalMessage');

        const modalWarning =
            document.getElementById('paymentModalWarning');

        const modalIcon =
            document.getElementById('paymentModalIconElement');

        const confirmButton =
            document.getElementById('paymentConfirmButton');

        const cancelButton =
            document.getElementById('paymentCancelButton');


        /*
        |--------------------------------------------------------------------------
        | Isi Modal
        |--------------------------------------------------------------------------
        */

        modalTitle.innerText = title;

        modalMessage.innerText = message;


        /*
        |--------------------------------------------------------------------------
        | Warning Tambahan
        |--------------------------------------------------------------------------
        */

        if (warning)
        {

            modalWarning.innerText = warning;

            modalWarning.style.display = 'block';

        }
        else
        {

            modalWarning.innerText = '';

            modalWarning.style.display = 'none';

        }


        /*
        |--------------------------------------------------------------------------
        | ICON
        |--------------------------------------------------------------------------
        */

        if (type === 'success')
        {

            modalIcon.className =
                'fa-solid fa-circle-check';

        }
        else if (type === 'danger')
        {

            modalIcon.className =
                'fa-solid fa-circle-xmark';

        }
        else
        {

            modalIcon.className =
                'fa-solid fa-circle-exclamation';

        }


        /*
        |--------------------------------------------------------------------------
        | Tombol Konfirmasi
        |--------------------------------------------------------------------------
        */

        if (showConfirm)
        {

            confirmButton.style.display =
                'inline-flex';

            cancelButton.style.display =
                'inline-flex';

        }
        else
        {

            confirmButton.style.display =
                'none';

            cancelButton.innerText =
                'Tutup';

            cancelButton.style.display =
                'inline-flex';

        }


        /*
        |--------------------------------------------------------------------------
        | Tampilkan Modal
        |--------------------------------------------------------------------------
        */

        modal.classList.add('show');

        document.body.classList.add('modal-open');

    }



    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL
    |--------------------------------------------------------------------------
    */

    function closePaymentModal()
    {

        const modal =
            document.getElementById('paymentModal');


        modal.classList.remove('show');

        document.body.classList.remove('modal-open');


        // Kembalikan tombol
        document.getElementById(
            'paymentCancelButton'
        ).innerText = 'Batal';

    }



    /*
    |--------------------------------------------------------------------------
    | PROSES PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    function processPayment()
    {

        /*
        |--------------------------------------------------------------------------
        | BELUM PILIH PESANAN
        |--------------------------------------------------------------------------
        */

        if (!selectedOrderId)
        {

            openPaymentModal(
                'Pilih Pesanan',
                'Silakan pilih pesanan terlebih dahulu.',
                'Pilih salah satu pesanan pada daftar di sebelah kiri.',
                'warning',
                false
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | METODE PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        const method =
            document.getElementById('paymentMethod').value;


        /*
        |--------------------------------------------------------------------------
        | UANG DITERIMA
        |--------------------------------------------------------------------------
        */

        const cash =
            Number(
                document.getElementById('cashInput').value
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI CASH
        |--------------------------------------------------------------------------
        */

        if (method === 'Cash')
        {

            /*
            | Uang belum dimasukkan
            */

            if (!cash || cash <= 0)
            {

                openPaymentModal(
                    'Uang Belum Dimasukkan',
                    'Silakan masukkan uang yang diterima.',
                    'Nominal uang diterima harus diisi sebelum pembayaran diproses.',
                    'warning',
                    false
                );

                return;

            }


            /*
            | Uang tidak cukup
            */

            if (cash < selectedTotal)
            {

                const kurang =
                    selectedTotal - cash;


                openPaymentModal(
                    'Uang Tidak Cukup',
                    'Uang yang diterima belum mencukupi.',
                    'Kekurangan: ' + formatRupiah(kurang),
                    'danger',
                    false
                );

                return;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        let detail =
            'Pesanan: ' +
            selectedOrder +
            '\n' +
            'Total: ' +
            formatRupiah(selectedTotal) +
            '\n' +
            'Metode: ' +
            method;


        if (method === 'Cash')
        {

            const change =
                cash - selectedTotal;


            detail +=
                '\nKembalian: ' +
                formatRupiah(change);

        }


        openPaymentModal(
            'Konfirmasi Pembayaran',
            'Apakah kamu yakin ingin menyelesaikan pembayaran ini?',
            detail,
            'warning',
            true
        );

    }



    /*
    |--------------------------------------------------------------------------
    | KONFIRMASI BAYAR
    |--------------------------------------------------------------------------
    */

    function confirmPayment()
    {

        if (!selectedOrderId)
        {

            closePaymentModal();

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Ambil form
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById('paymentForm');


        /*
        |--------------------------------------------------------------------------
        | URL PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        form.action =
            '/kasir/payment/' +
            selectedOrderId +
            '/complete';


        /*
        |--------------------------------------------------------------------------
        | METODE PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'paymentMethodInput'
        ).value =
            document.getElementById(
                'paymentMethod'
            ).value;


        /*
        |--------------------------------------------------------------------------
        | Submit
        |--------------------------------------------------------------------------
        */

        form.submit();

    }



    /*
    |--------------------------------------------------------------------------
    | ESCAPE UNTUK MENUTUP MODAL
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function(event)
        {

            if (event.key === 'Escape')
            {

                closePaymentModal();

            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | KLIK AREA LUAR MODAL
    |--------------------------------------------------------------------------
    */

    document.getElementById('paymentModal')
        .addEventListener(
            'click',
            function(event)
            {

                if (event.target === this)
                {

                    closePaymentModal();

                }

            }
        );

</script>

@endsection