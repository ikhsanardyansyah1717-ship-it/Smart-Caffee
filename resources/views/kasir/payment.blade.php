@extends('layouts.kasir')

@section('title', 'Pembayaran - Quattro Coffee')

@section('content')
<style>
.receipt-success {
    margin-bottom: 18px;
    padding: 16px 18px;
    border: 1px solid #d9ead7;
    border-left: 4px solid #2f9e44;
    border-radius: 14px;
    background: #f5fbf4;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}
.receipt-success strong { color: #245c2d; display:block; margin-bottom:4px; }
.receipt-success span { color:#5e7461; font-size:13px; }
.receipt-print-btn {
    border:0; border-radius:10px; padding:11px 16px;
    background:#a25d08; color:#fff; font-weight:700; cursor:pointer;
    white-space:nowrap;
}
.receipt-print-btn:hover { background:#874b05; }
.receipt-overlay {
    position:fixed; inset:0; display:none; align-items:center; justify-content:center;
    padding:20px; background:rgba(45,30,20,.48); backdrop-filter:blur(6px); z-index:11000;
}
.receipt-overlay.show { display:flex; }
.receipt-modal {
    width:100%; max-width:430px; max-height:92vh; overflow:auto; background:#fff;
    border-radius:18px; box-shadow:0 25px 70px rgba(40,25,15,.28);
}
.receipt-actions { display:flex; gap:10px; padding:15px 18px; border-top:1px solid #eee; background:#fff; position:sticky; bottom:0; }
.receipt-actions button { flex:1; min-height:44px; border:0; border-radius:10px; font-weight:700; cursor:pointer; }
.receipt-close { background:#f2ede7; color:#634d3b; }
.receipt-print { background:#a25d08; color:#fff; }
.receipt-paper { padding:28px 25px 20px; color:#211a15; font-family:Arial,sans-serif; }
.receipt-brand { text-align:center; border-bottom:1px dashed #aaa; padding-bottom:16px; margin-bottom:15px; }
.receipt-brand h2 { margin:0; font-size:23px; letter-spacing:2px; }
.receipt-brand p { margin:4px 0 0; font-size:11px; letter-spacing:2px; color:#777; }
.receipt-meta { font-size:12px; line-height:1.7; margin-bottom:15px; }
.receipt-meta div { display:flex; justify-content:space-between; gap:15px; }
.receipt-items { border-top:1px dashed #aaa; border-bottom:1px dashed #aaa; padding:12px 0; }
.receipt-item { margin-bottom:10px; font-size:12px; }
.receipt-item:last-child { margin-bottom:0; }
.receipt-item-top, .receipt-item-bottom { display:flex; justify-content:space-between; gap:10px; }
.receipt-item-top strong { font-size:13px; }
.receipt-item-bottom { color:#666; margin-top:3px; }
.receipt-total { padding-top:13px; font-size:12px; }
.receipt-total div { display:flex; justify-content:space-between; margin-bottom:7px; }
.receipt-total .grand { font-size:17px; font-weight:800; padding-top:7px; border-top:1px solid #ddd; }
.receipt-thanks { text-align:center; border-top:1px dashed #aaa; margin-top:14px; padding-top:15px; font-size:12px; color:#666; }
@media print {

    @page {
        size: 80mm auto;
        margin: 0;
    }

    html,
    body {
        width: 80mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    body * {
        visibility: hidden !important;
    }

    #receiptModal,
    #receiptModal * {
        visibility: visible !important;
    }

    #receiptModal {
        position: static !important;
        display: block !important;
        width: 80mm !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        background: #fff !important;
        overflow: visible !important;
    }

    #receiptPrintArea {
        position: static !important;
        width: 80mm !important;
        max-width: 80mm !important;
        min-height: 0 !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        background: #fff !important;
    }

    .receipt-paper {
        width: 80mm !important;
        box-sizing: border-box !important;
        padding: 8mm 5mm !important;
        margin: 0 !important;
    }

    .receipt-actions {
        display: none !important;
    }

    .receipt-brand {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .receipt-meta {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .receipt-items {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .receipt-item {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .receipt-total {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .receipt-thanks {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
}
}
@media (max-width:700px) {
    .receipt-success { align-items:flex-start; flex-direction:column; }
    .receipt-print-btn { width:100%; }
}
</style>


@if(isset($receiptOrder) && $receiptOrder)
    <div class="receipt-success">
        <div>
            <strong><i class="fa-solid fa-circle-check"></i> Pembayaran berhasil!</strong>
            <span>Transaksi {{ $receiptOrder->order_number }} sudah selesai.</span>
        </div>
        <button type="button" class="receipt-print-btn" onclick="openReceiptModal()">
            <i class="fa-solid fa-receipt"></i> Cetak Struk
        </button>
    </div>
@endif


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

                <input
                    type="hidden"
                    name="cash_received"
                    id="cashReceivedInput"
                    value="0"
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
{{-- MODAL STRUK --}}
{{-- ================================================= --}}
@if(isset($receiptOrder) && $receiptOrder)
<div id="receiptModal" class="receipt-overlay">
    <div class="receipt-modal" id="receiptPrintArea">
        <div class="receipt-paper">
            <div class="receipt-brand">
                <h2>QUATTRO</h2>
                <p>COFFEE</p>
            </div>

            <div class="receipt-meta">
                <div><span>No. Transaksi</span><strong>{{ $receiptOrder->order_number }}</strong></div>
                <div><span>Tanggal</span><strong>{{ $receiptOrder->created_at->format('d/m/Y H:i') }}</strong></div>
                <div><span>Pelanggan</span><strong>{{ $receiptOrder->customer_name }}</strong></div>
                <div><span>Meja</span><strong>{{ $receiptOrder->table_number ?? 'Take Away' }}</strong></div>
                <div><span>Pembayaran</span><strong>{{ optional($receiptOrder->payment)->payment_method ?? '-' }}</strong></div>
            </div>

            <div class="receipt-items">
                @foreach($receiptOrder->items as $item)
                    <div class="receipt-item">
                        <div class="receipt-item-top">
                            <strong>{{ $item->product_name }}</strong>
                            <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                        </div>
                        <div class="receipt-item-bottom">
                            <span>{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="receipt-total">
                <div><span>Subtotal</span><strong>Rp {{ number_format($receiptOrder->subtotal, 0, ',', '.') }}</strong></div>
                <div><span>Pajak</span><strong>Rp {{ number_format($receiptOrder->tax, 0, ',', '.') }}</strong></div>
                <div class="grand"><span>Total</span><strong>Rp {{ number_format($receiptOrder->total, 0, ',', '.') }}</strong></div>
                <div><span>Uang Diterima</span><strong>Rp {{ number_format($receiptCashReceived ?? 0, 0, ',', '.') }}</strong></div>
                <div><span>Kembalian</span><strong>Rp {{ number_format($receiptChange ?? 0, 0, ',', '.') }}</strong></div>
            </div>

            <div class="receipt-thanks">
                Terima kasih sudah berkunjung ke Quattro Coffee.<br>Semoga harimu menyenangkan ☕
            </div>
        </div>

        <div class="receipt-actions">
            <button type="button" class="receipt-close" onclick="closeReceiptModal()">Tutup</button>
            <button type="button" class="receipt-print" onclick="printReceipt()"><i class="fa-solid fa-print"></i> Cetak</button>
        </div>
    </div>
</div>
@endif


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

        document.getElementById(
            'cashReceivedInput'
        ).value =
            document.getElementById(
                'cashInput'
            ).value || 0;


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



    /*
    |--------------------------------------------------------------------------
    | STRUK
    |--------------------------------------------------------------------------
    */
    function openReceiptModal() {
        const modal = document.getElementById('receiptModal');
        if (!modal) return;
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeReceiptModal() {
        const modal = document.getElementById('receiptModal');
        if (!modal) return;
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    function printReceipt() {
        window.print();
    }

    @if(isset($receiptOrder) && $receiptOrder)
        document.addEventListener('DOMContentLoaded', function () {
            openReceiptModal();
        });
    @endif

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('receiptModal');
        if (modal && event.target === modal) {
            closeReceiptModal();
        }
    });

</script>

@endsection