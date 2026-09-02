@extends('layouts.customer')

@section('title', 'Pesanan - Quattro Coffee')

@section('content')

<div class="app-container">

    {{-- HEADER --}}
    <div class="top-nav-bar">
        <h3>
            Pesanan Saya
        </h3>
    </div>


    {{-- TABS --}}
    <div class="order-tabs">

        <button
            class="tab-btn active"
            onclick="switchOrderTab('cart')"
        >
            Keranjang
            (<span id="cart-count">0</span>)
        </button>

        <button
            class="tab-btn"
            onclick="switchOrderTab('history')"
        >
            Riwayat
        </button>

    </div>


    {{-- CART --}}
    <div id="tab-cart">

        <div id="cart-items-container"></div>


        {{-- SUMMARY --}}
        <div
            id="cart-summary-box"
            class="order-summary"
            style="display:none;"
        >

            <div class="summary-row">
                <span>Subtotal</span>

                <span id="subtotal-val">
                    Rp 0
                </span>
            </div>


            <div class="summary-row">

                <span>
                    Pajak & Layanan (10%)
                </span>

                <span id="tax-val">
                    Rp 0
                </span>

            </div>


            <div class="summary-row total">

                <span>
                    Total Pembayaran
                </span>

                <span id="total-val">
                    Rp 0
                </span>

            </div>


            {{-- CHECKOUT BUTTON --}}
            <button
                type="button"
                class="btn-primary checkout-open-btn"
                style="margin-top:15px;"
                onclick="openPaymentModal()"
            >
                <i class="fa-solid fa-credit-card"></i>
                Bayar Sekarang
            </button>

        </div>

    </div>


    {{-- HISTORY --}}
    <div
        id="tab-history"
        style="display:none"
    >

        <div id="history-container"></div>

    </div>


    {{-- ===================================================== --}}
    {{-- PAYMENT MODAL --}}
    {{-- ===================================================== --}}

    <div
        id="payment-modal"
        class="payment-modal"
    >

        <div
            class="payment-overlay"
            onclick="closePaymentModal()"
        ></div>


        <div class="payment-modal-content">

            {{-- HEADER MODAL --}}
            <div class="payment-header">

                <button
                    type="button"
                    class="payment-close"
                    onclick="closePaymentModal()"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div>

                    <span class="payment-small-title">
                        CHECKOUT
                    </span>

                    <h3>
                        Pembayaran
                    </h3>

                </div>

            </div>


            {{-- TOTAL --}}
            <div class="payment-total-card">

                <div class="payment-total-icon">

                    <i class="fa-solid fa-receipt"></i>

                </div>

                <div>

                    <span>
                        Total Pembayaran
                    </span>

                    <strong id="payment-total-display">
                        Rp 0
                    </strong>

                </div>

            </div>


            {{-- TABLE NUMBER --}}
            <div class="payment-field">

                <label for="table-number">
                    Nomor Meja
                    <span>(opsional)</span>
                </label>

                <div class="payment-input">

                    <i class="fa-solid fa-chair"></i>

                    <input
                        type="text"
                        id="table-number"
                        placeholder="Contoh: A01"
                        maxlength="50"
                    >

                </div>

            </div>


{{-- PAYMENT METHOD --}}
<div class="payment-method-section">

    <div class="payment-section-title">
        <h4>Pilih Metode Pembayaran</h4>

        <span>
            Pilih salah satu
        </span>
    </div>


    {{-- ================= CASH ================= --}}

    <label
        class="payment-method"
        for="payment-cash"
    >

        <input
            type="radio"
            name="payment_method"
            id="payment-cash"
            value="Cash"
        >

        <div class="payment-method-icon cash-icon">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>

        <div class="payment-method-info">

            <strong>
                Cash
            </strong>

            <span>
                Bayar langsung dengan uang tunai
            </span>

        </div>

        <div class="payment-check">
            <i class="fa-solid fa-check"></i>
        </div>

    </label>


    {{-- ================= QRIS ================= --}}

    <label
        class="payment-method"
        for="payment-qris"
    >

        <input
            type="radio"
            name="payment_method"
            id="payment-qris"
            value="QRIS"
        >

        <div class="payment-method-icon qris-icon">
            <i class="fa-solid fa-qrcode"></i>
        </div>

        <div class="payment-method-info">

            <strong>
                QRIS
            </strong>

            <span>
                Scan QR untuk pembayaran
            </span>

        </div>

        <div class="payment-check">
            <i class="fa-solid fa-check"></i>
        </div>

    </label>


    {{-- ================= DEBIT ================= --}}

    <label
        class="payment-method"
        for="payment-debit"
    >

        <input
            type="radio"
            name="payment_method"
            id="payment-debit"
            value="Debit"
        >

        <div class="payment-method-icon debit-icon">
            <i class="fa-solid fa-credit-card"></i>
        </div>

        <div class="payment-method-info">

            <strong>
                Debit
            </strong>

            <span>
                Pilih bank kartu debit
            </span>

        </div>

        <div class="payment-check">
            <i class="fa-solid fa-check"></i>
        </div>

    </label>


    {{-- ================================================= --}}
    {{-- DETAIL PEMBAYARAN --}}
    {{-- ================================================= --}}

    <div
        id="checkout-payment-details"
        class="checkout-payment-details"
        style="display:none;"
    >


        {{-- ================= CASH DETAIL ================= --}}

        <div
            id="checkout-cash-detail"
            class="checkout-detail-card"
            style="display:none;"
        >

            <div class="checkout-detail-title">

                <div class="checkout-detail-icon cash">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>

                <div>

                    <strong>
                        Pembayaran Cash
                    </strong>

                    <span>
                        Masukkan uang yang diterima
                    </span>

                </div>

            </div>


            <label
                class="checkout-input-label"
                for="cash-received"
            >
                Uang Diterima
            </label>


            <div class="checkout-money-input">

                <span>
                    Rp
                </span>

                <input
                    type="number"
                    id="cash-received"
                    min="0"
                    placeholder="100000"
                >

            </div>


            <div class="checkout-change">

                <span>
                    Kembalian
                </span>

                <strong id="cash-change">
                    Rp 0
                </strong>

            </div>

        </div>


        {{-- ================= QRIS DETAIL ================= --}}

        <div
            id="checkout-qris-detail"
            class="checkout-detail-card"
            style="display:none;"
        >

            <div class="checkout-detail-title">

                <div class="checkout-detail-icon qris">
                    <i class="fa-solid fa-qrcode"></i>
                </div>

                <div>

                    <strong>
                        Pembayaran QRIS
                    </strong>

                    <span>
                        Scan QR menggunakan aplikasi pembayaran
                    </span>

                </div>

            </div>


            <div class="checkout-qris-wrapper">

                <div class="checkout-qris-label">
                    QRIS
                </div>


                {{-- QR DEMO --}}
                <div class="checkout-qris">

                    <div class="checkout-qr-grid">

                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>

                    </div>


                    <div class="checkout-qr-center">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>

                </div>


                <strong
                    id="checkout-qris-total"
                    class="checkout-qris-total"
                >
                    Rp 0
                </strong>


                <p>
                    Scan menggunakan aplikasi yang mendukung QRIS.
                </p>

            </div>

        </div>


        {{-- ================= DEBIT DETAIL ================= --}}

        <div
            id="checkout-debit-detail"
            class="checkout-detail-card"
            style="display:none;"
        >

            <div class="checkout-detail-title">

                <div class="checkout-detail-icon debit">
                    <i class="fa-solid fa-credit-card"></i>
                </div>

                <div>

                    <strong>
                        Pilih Bank
                    </strong>

                    <span>
                        Pilih bank kartu debit kamu
                    </span>

                </div>

            </div>


            <div class="checkout-bank-grid">


                {{-- BCA --}}

                <label class="checkout-bank-option">

                    <input
                        type="radio"
                        name="debit_bank"
                        value="BCA"
                    >

                    <div class="checkout-bank-logo bca">
                        BCA
                    </div>

                    <div class="checkout-bank-info">

                        <strong>
                            BCA
                        </strong>

                        <span>
                            Debit
                        </span>

                    </div>

                    <i class="fa-solid fa-check"></i>

                </label>


                {{-- MANDIRI --}}

                <label class="checkout-bank-option">

                    <input
                        type="radio"
                        name="debit_bank"
                        value="Mandiri"
                    >

                    <div class="checkout-bank-logo mandiri">
                        BM
                    </div>

                    <div class="checkout-bank-info">

                        <strong>
                            Mandiri
                        </strong>

                        <span>
                            Debit
                        </span>

                    </div>

                    <i class="fa-solid fa-check"></i>

                </label>


                {{-- BNI --}}

                <label class="checkout-bank-option">

                    <input
                        type="radio"
                        name="debit_bank"
                        value="BNI"
                    >

                    <div class="checkout-bank-logo bni">
                        BNI
                    </div>

                    <div class="checkout-bank-info">

                        <strong>
                            BNI
                        </strong>

                        <span>
                            Debit
                        </span>

                    </div>

                    <i class="fa-solid fa-check"></i>

                </label>


                {{-- BRI --}}

                <label class="checkout-bank-option">

                    <input
                        type="radio"
                        name="debit_bank"
                        value="BRI"
                    >

                    <div class="checkout-bank-logo bri">
                        BRI
                    </div>

                    <div class="checkout-bank-info">

                        <strong>
                            BRI
                        </strong>

                        <span>
                            Debit
                        </span>

                    </div>

                    <i class="fa-solid fa-check"></i>

                </label>


            </div>

        </div>

    </div>

</div>




            {{-- CONFIRM BUTTON --}}
            <button
                type="button"
                class="payment-confirm-btn"
                onclick="confirmPaymentSelection()"
            >

                <span>
                    Lanjutkan Pembayaran
                </span>

                <i class="fa-solid fa-arrow-right"></i>

            </button>


            <button
                type="button"
                class="payment-cancel-btn"
                onclick="closePaymentModal()"
            >
                Kembali ke Keranjang
            </button>

        </div>

    </div>


    {{-- NAV --}}
    @include('customer.partials.nav')

</div>

@endsection


@push('styles')

<style>

    /* =====================================================
       PAYMENT MODAL
    ===================================================== */

    .payment-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: flex-end;
        justify-content: center;
    }

    .payment-modal.show {
        display: flex;
    }


    /* OVERLAY */

    .payment-overlay {
        position: absolute;
        inset: 0;
        background: rgba(20, 15, 10, 0.58);
        backdrop-filter: blur(5px);
        animation: paymentFadeIn .25s ease;
    }


    /* CONTENT */

    .payment-modal-content {
        position: relative;
        z-index: 2;

        width: min(100%, 520px);

        max-height: 92vh;
        overflow-y: auto;

        background: #fffaf5;

        border-radius: 28px 28px 0 0;

        padding: 25px;

        box-shadow:
            0 -15px 50px rgba(0,0,0,.20);

        animation: paymentSlideUp .35s ease;
    }


    /* HEADER */

    .payment-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .payment-header h3 {
        margin: 3px 0 0;
        font-size: 25px;
    }

    .payment-small-title {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        opacity: .55;
    }

    .payment-close {
        width: 42px;
        height: 42px;

        border: none;
        border-radius: 14px;

        background: #f1e8df;

        cursor: pointer;

        font-size: 18px;

        transition: .2s;
    }

    .payment-close:hover {
        transform: rotate(5deg);
    }


    /* TOTAL */

    .payment-total-card {
        display: flex;
        align-items: center;
        gap: 14px;

        padding: 18px;

        border-radius: 20px;

        background: linear-gradient(
            135deg,
            #4b2e22,
            #754936
        );

        color: white;

        margin-bottom: 22px;
    }

    .payment-total-icon {
        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 15px;

        background: rgba(255,255,255,.15);

        font-size: 20px;
    }

    .payment-total-card span {
        display: block;
        font-size: 12px;
        opacity: .75;
        margin-bottom: 3px;
    }

    .payment-total-card strong {
        display: block;
        font-size: 23px;
    }


    /* TABLE */

    .payment-field {
        margin-bottom: 23px;
    }

    .payment-field label {
        display: block;

        margin-bottom: 9px;

        font-size: 14px;
        font-weight: 700;
    }

    .payment-field label span {
        font-size: 11px;
        font-weight: 400;
        opacity: .55;
    }

    .payment-input {
        display: flex;
        align-items: center;

        gap: 10px;

        background: white;

        border: 1px solid #e5d9cf;

        border-radius: 15px;

        padding: 0 15px;

        transition: .2s;
    }

    .payment-input:focus-within {
        border-color: #754936;

        box-shadow:
            0 0 0 3px rgba(117,73,54,.10);
    }

    .payment-input i {
        opacity: .55;
    }

    .payment-input input {
        width: 100%;

        border: none;
        outline: none;

        padding: 14px 0;

        background: transparent;

        font-size: 14px;
    }


    /* METHOD */

    .payment-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-bottom: 12px;
    }

    .payment-section-title h4 {
        margin: 0;
        font-size: 15px;
    }

    .payment-section-title span {
        font-size: 11px;
        opacity: .55;
    }


    /* PAYMENT CARD */

    .payment-method {
        position: relative;

        display: flex;
        align-items: center;

        gap: 13px;

        padding: 14px;

        margin-bottom: 10px;

        background: white;

        border: 1.5px solid #e6dcd4;

        border-radius: 17px;

        cursor: pointer;

        transition:
            border-color .2s,
            transform .2s,
            box-shadow .2s;
    }

    .payment-method:hover {
        transform: translateY(-1px);
    }

    .payment-method input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }


    /* SELECTED */

    .payment-method:has(input:checked) {
        border-color: #704432;

        background: #fff8f2;

        box-shadow:
            0 5px 15px rgba(80,50,35,.08);
    }


    /* ICON */

    .payment-method-icon {
        width: 45px;
        height: 45px;

        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 13px;

        font-size: 18px;
    }

    .cash-icon {
        background: #e8f4e9;
        color: #3d7a43;
    }

    .qris-icon {
        background: #eee9f9;
        color: #64469b;
    }

    .debit-icon {
        background: #e8eef7;
        color: #42618c;
    }


    /* INFO */

    .payment-method-info {
        flex: 1;
    }

    .payment-method-info strong {
        display: block;

        font-size: 14px;

        margin-bottom: 3px;
    }

    .payment-method-info span {
        display: block;

        font-size: 11px;

        opacity: .58;
    }


    /* CHECK */

    .payment-check {
        width: 23px;
        height: 23px;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        border: 1.5px solid #d5cbc3;

        color: transparent;

        transition: .2s;
    }

    .payment-method:has(input:checked) .payment-check {
        background: #704432;

        border-color: #704432;

        color: white;
    }

    .payment-check i {
        font-size: 11px;
    }


    /* CONFIRM */

    .payment-confirm-btn {
        width: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        gap: 10px;

        border: none;

        padding: 15px;

        margin-top: 18px;

        border-radius: 16px;

        background: #704432;

        color: white;

        font-size: 14px;
        font-weight: 700;

        cursor: pointer;

        transition: .2s;
    }

    .payment-confirm-btn:hover {
        transform: translateY(-2px);

        box-shadow:
            0 8px 20px rgba(112,68,50,.22);
    }


    /* CANCEL */

    .payment-cancel-btn {
        width: 100%;

        border: none;

        background: transparent;

        padding: 12px;

        margin-top: 4px;

        color: #704432;

        font-size: 13px;

        cursor: pointer;
    }


    /* ANIMATION */

    @keyframes paymentSlideUp {

        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }

    }

    @keyframes paymentFadeIn {

        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }

    }


    /* DESKTOP */

    @media (min-width: 700px) {

        .payment-modal {
            align-items: center;
        }

        .payment-modal-content {
            border-radius: 28px;

            max-height: 90vh;

            animation: paymentDesktopIn .3s ease;
        }

        @keyframes paymentDesktopIn {

            from {
                transform: scale(.95) translateY(15px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }

        }

    }

</style>

@endpush


@push('scripts')

<script>

window.quattroUser = @json([
    'id' => auth()->id(),
    'name' => auth()->user()->name,
    'email' => auth()->user()->email
]);

</script>

<script src="{{ asset('js/customer.js') }}"></script>


<script>

    /* =====================================================
       PAYMENT UI
    ===================================================== */

    function openPaymentModal() {

        if (
            typeof appState === 'undefined' ||
            !appState.cart ||
            !appState.cart.length
        ) {

            if (typeof showToast === 'function') {
                showToast('Keranjang masih kosong');
            }

            return;
        }


        // Ambil total dari keranjang
        const subtotal = appState.cart.reduce(
            (sum, item) =>
                sum + Number(item.price) * item.qty,
            0
        );

        const tax = subtotal * 0.10;

        const total = subtotal + tax;


        // Tampilkan total
        const totalElement =
            document.getElementById(
                'payment-total-display'
            );

        if (totalElement) {

            totalElement.innerText =
                `Rp ${total.toLocaleString('id-ID')}`;

        }


        // Reset pilihan pembayaran
        document
            .querySelectorAll(
                'input[name="payment_method"]'
            )
            .forEach(input => {

                input.checked = false;

            });


        document
            .getElementById('payment-modal')
            ?.classList.add('show');


        document.body.style.overflow = 'hidden';

    }


    function closePaymentModal() {

        document
            .getElementById('payment-modal')
            ?.classList.remove('show');

        document.body.style.overflow = '';

    }


  
async function confirmPaymentSelection() {

    if (
        typeof validatePaymentSelection === 'function' &&
        !validatePaymentSelection()
    ) {
        return;
    }


    const selected =
        document.querySelector(
            'input[name="payment_method"]:checked'
        );


    if (!selected) {

        showToast(
            'Silakan pilih metode pembayaran'
        );

        return;
    }


    const tableInput =
        document.getElementById(
            'table-number'
        );


    const paymentData = {

        payment_method:
            selected.value,

        debit_bank:
            window.selectedDebitBank ||
            null,

        cash_received:
            window.selectedCashReceived ||
            null,

        table_number:
            tableInput
                ? tableInput.value.trim()
                : null

    };


    console.log(
        'CHECKOUT PAYMENT DATA:',
        paymentData
    );


    /* Jangan close sebelum checkout selesai */

    await checkout(
        paymentData
    );

}



</script>

@endpush