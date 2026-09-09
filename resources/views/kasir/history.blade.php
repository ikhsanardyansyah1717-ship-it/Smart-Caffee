@extends('layouts.kasir')

@section('title', 'Riwayat - Quattro Coffee')

@section('content')

<style>

/* =========================================================
   RIWAYAT TRANSAKSI
========================================================= */

.history-row {
    cursor: pointer;
    transition: all .2s ease;
}

.history-row:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, .04);
}

.history-row:active {
    transform: scale(.995);
}

.history-click-info {
    display: block;
    font-size: 11px;
    opacity: .5;
    margin-top: 4px;
}

/* =========================================================
   MODAL PREVIEW STRUK
========================================================= */

#historyReceiptModal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 99999;
}

.history-receipt-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .78);
    backdrop-filter: blur(5px);

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 20px;
    overflow-y: auto;
}

.history-receipt-container {
    width: 100%;
    max-width: 430px;
    animation: receiptOpen .25s ease;
}

@keyframes receiptOpen {

    from {
        opacity: 0;
        transform: translateY(20px) scale(.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

}

/* =========================================================
   TOMBOL MODAL
========================================================= */

.history-receipt-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 12px;
}

.history-receipt-actions button {
    border: 0;
    border-radius: 8px;
    padding: 10px 16px;
    cursor: pointer;
    font-weight: 600;
    transition: .2s ease;
}

.history-receipt-actions button:hover {
    transform: translateY(-2px);
}

.history-print-btn {
    background: #f59e0b;
    color: #111;
}

.history-close-btn {
    background: #333;
    color: #fff;
}

/* =========================================================
   AREA STRUK
========================================================= */

#historyReceiptPrintArea {
    width: 80mm;
    max-width: 80mm;
    margin: auto;

    background: #fff;
    color: #111;

    border-radius: 8px;
    overflow: hidden;

    box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
}

/* =========================================================
   KERTAS STRUK
========================================================= */

.history-receipt-paper {
    width: 80mm;
    max-width: 80mm;

    box-sizing: border-box;

    padding: 7mm 5mm;

    background: #fff;
    color: #111;

    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    line-height: 1.4;
}

/* =========================================================
   HEADER STRUK
========================================================= */

.history-receipt-brand {
    text-align: center;
    margin-bottom: 10px;
}

.history-receipt-brand h2 {
    margin: 0;

    font-size: 22px;
    letter-spacing: 2px;
    font-weight: 800;
}

.history-receipt-brand p {
    margin: 2px 0 0;

    font-size: 11px;
    letter-spacing: 3px;
}

/* =========================================================
   GARIS
========================================================= */

.receipt-line {
    border-top: 1px dashed #222;
    margin: 9px 0;
}

/* =========================================================
   INFORMASI TRANSAKSI
========================================================= */

.history-receipt-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.history-receipt-meta div {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}

.history-receipt-meta span {
    color: #444;
}

.history-receipt-meta strong {
    text-align: right;
    font-weight: 600;
    max-width: 45mm;
    word-break: break-word;
}

/* =========================================================
   ITEM
========================================================= */

.history-receipt-items {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.receipt-product-name {
    font-weight: 700;
    margin-bottom: 2px;
    word-break: break-word;
}

.receipt-product-detail {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}

.receipt-product-detail span:first-child {
    color: #444;
}

.receipt-product-detail span:last-child {
    font-weight: 600;
    text-align: right;
    white-space: nowrap;
}

/* =========================================================
   TOTAL
========================================================= */

.history-receipt-total {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.history-receipt-total > div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.history-receipt-total .grand {
    font-size: 15px;
    font-weight: 800;
    margin-top: 3px;
}

/* =========================================================
   PEMBAYARAN
========================================================= */

.history-receipt-payment {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.history-receipt-payment strong {
    text-align: right;
}

/* =========================================================
   FOOTER
========================================================= */

.history-receipt-thanks {
    text-align: center;
    margin-top: 14px;

    font-size: 11px;
    line-height: 1.5;
}


/* =========================================================
   PRINT THERMAL 80MM
   KERTAS = 80MM
   ISI = 72MM
   POSISI = TENGAH
========================================================= */

@media print {

    @page {
        size: 80mm auto;
        margin: 0;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {

        width: 80mm !important;
        min-width: 80mm !important;
        max-width: 80mm !important;

        margin: 0 !important;
        padding: 0 !important;

        background: #fff !important;
    }

    body {
        overflow: visible !important;
    }

    /* Sembunyikan seluruh halaman */
    body * {
        visibility: hidden !important;
    }

    /* Tampilkan hanya modal struk */
    #historyReceiptModal,
    #historyReceiptModal * {
        visibility: visible !important;
    }

    #historyReceiptModal {

        display: block !important;

        position: absolute !important;

        top: 0 !important;
        left: 0 !important;

        width: 80mm !important;
        min-width: 80mm !important;
        max-width: 80mm !important;

        margin: 0 !important;
        padding: 0 !important;

        background: #fff !important;
    }

    /* Overlay */
    .history-receipt-overlay {

        position: static !important;

        display: block !important;

        width: 80mm !important;
        min-width: 80mm !important;
        max-width: 80mm !important;

        margin: 0 !important;
        padding: 0 !important;

        background: #fff !important;

        overflow: visible !important;
    }

    /* Container */
    .history-receipt-container {

        display: block !important;

        width: 80mm !important;
        min-width: 80mm !important;
        max-width: 80mm !important;

        margin: 0 auto !important;
        padding: 0 !important;

        background: #fff !important;
    }

    /* Hilangkan tombol saat print */
    .history-receipt-actions {
        display: none !important;
    }

    /* =====================================================
       AREA CETAK
       80MM KERTAS
       72MM ISI
       AUTO CENTER
    ===================================================== */

    #historyReceiptPrintArea {

        display: block !important;

        width: 72mm !important;
        min-width: 72mm !important;
        max-width: 72mm !important;

        margin: 0 auto !important;
        padding: 0 !important;

        background: #fff !important;

        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;

        overflow: visible !important;
    }

    /* =====================================================
       KERTAS STRUK
    ===================================================== */

    .history-receipt-paper {

        display: block !important;

        width: 72mm !important;
        min-width: 72mm !important;
        max-width: 72mm !important;

        height: auto !important;
        min-height: 0 !important;

        margin: 0 auto !important;

        padding: 4mm 2mm !important;

        box-sizing: border-box !important;

        background: #fff !important;
        color: #000 !important;

        font-family: Arial, Helvetica, sans-serif !important;

        font-size: 11px !important;
        line-height: 1.35 !important;

        overflow: visible !important;

        page-break-before: avoid !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;

        break-before: avoid !important;
        break-after: avoid !important;
        break-inside: avoid !important;
    }

    /* Jangan pecahkan bagian struk */
    .history-receipt-brand,
    .history-receipt-meta,
    .history-receipt-items,
    .history-receipt-total,
    .history-receipt-payment,
    .history-receipt-thanks,
    .receipt-line {

        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    /* Header */
    .history-receipt-brand {

        text-align: center !important;

        margin-bottom: 6px !important;
    }

    .history-receipt-brand h2 {

        margin: 0 !important;

        font-size: 20px !important;

        letter-spacing: 2px !important;

        font-weight: 800 !important;
    }

    .history-receipt-brand p {

        margin: 1px 0 0 !important;

        font-size: 10px !important;

        letter-spacing: 3px !important;
    }

    /* Garis */
    .receipt-line {

        border-top: 1px dashed #000 !important;

        margin: 6px 0 !important;
    }

    /* Meta */
    .history-receipt-meta {

        display: flex !important;

        flex-direction: column !important;

        gap: 3px !important;
    }

    .history-receipt-meta div {

        display: flex !important;

        justify-content: space-between !important;

        align-items: flex-start !important;

        gap: 8px !important;
    }

    .history-receipt-meta span {

        color: #000 !important;
    }

    .history-receipt-meta strong {

        color: #000 !important;

        text-align: right !important;

        font-weight: 600 !important;

        max-width: 45mm !important;

        word-break: break-word !important;
    }

    /* Items */
    .history-receipt-items {

        display: flex !important;

        flex-direction: column !important;

        gap: 5px !important;
    }

    .receipt-product-name {

        font-weight: 700 !important;

        margin-bottom: 1px !important;

        word-break: break-word !important;
    }

    .receipt-product-detail {

        display: flex !important;

        justify-content: space-between !important;

        align-items: flex-start !important;

        gap: 8px !important;
    }

    .receipt-product-detail span:first-child {

        color: #000 !important;

        max-width: 42mm !important;
    }

    .receipt-product-detail span:last-child {

        color: #000 !important;

        font-weight: 600 !important;

        text-align: right !important;

        white-space: nowrap !important;
    }

    /* Total */
    .history-receipt-total {

        display: flex !important;

        flex-direction: column !important;

        gap: 3px !important;
    }

    .history-receipt-total > div {

        display: flex !important;

        justify-content: space-between !important;

        align-items: center !important;

        gap: 8px !important;
    }

    .history-receipt-total .grand {

        font-size: 14px !important;

        font-weight: 800 !important;

        margin-top: 2px !important;
    }

    /* Pembayaran */
    .history-receipt-payment {

        display: flex !important;

        justify-content: space-between !important;

        align-items: center !important;

        gap: 8px !important;
    }

    .history-receipt-payment strong {

        text-align: right !important;
    }

    /* Footer */
    .history-receipt-thanks {

        text-align: center !important;

        margin-top: 9px !important;

        font-size: 10px !important;

        line-height: 1.4 !important;
    }
}

</style>


<!-- =========================================================
     HALAMAN RIWAYAT
========================================================= -->

<div class="grid">

    <!-- HEADER -->

    <header class="topbar">

        <div>

            <span class="eyebrow">
                CASHIER CONTROL
            </span>

            <h1>
                Riwayat Transaksi
            </h1>

            <p>
                Lihat transaksi yang sudah selesai.
            </p>

        </div>

    </header>


    <!-- PANEL -->

    <section class="panel history-panel">

        <div class="panel-head">

            <div>

                <h2>
                    Transaksi Selesai
                </h2>

                <p>
                    Klik transaksi untuk melihat preview struk.
                </p>

            </div>


            <!-- SEARCH -->

            <div class="history-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="historySearch"
                    placeholder="Cari transaksi..."
                    onkeyup="searchHistory()"
                >

            </div>

        </div>


        <!-- TABEL -->

        <div class="history-table-wrapper">

            <table class="history-table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Pesanan
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Pembayaran
                        </th>

                    </tr>

                </thead>


                <tbody id="historyTableBody">

                    @forelse($orders as $order)

                        <tr
                            class="history-row"
                            onclick="openHistoryReceipt({{ $order->id }})"
                        >

                            <!-- ID -->

                            <td>

                                <strong>
                                    {{ $order->id }}
                                </strong>

                                <small class="history-click-info">
                                    Klik untuk preview
                                </small>

                            </td>


                            <!-- PELANGGAN -->

                            <td>

                                <div class="customer-info">

                                    <strong>
                                        {{ $order->customer_name }}
                                    </strong>

                                    <small>

                                        Meja:

                                        {{ $order->table_number ?? 'Take Away' }}

                                    </small>

                                </div>

                            </td>


                            <!-- PESANAN -->

                            <td>

                                <div class="history-items">

                                    @forelse($order->items as $item)

                                        <div class="history-item">

                                            <span class="item-name">
                                                {{ $item->product_name }}
                                            </span>

                                            <span class="item-qty">
                                                × {{ $item->quantity }}
                                            </span>

                                        </div>

                                    @empty

                                        <span class="empty-item">
                                            Tidak ada produk
                                        </span>

                                    @endforelse

                                </div>

                            </td>


                            <!-- TOTAL -->

                            <td>

                                <strong class="history-total">

                                    Rp
                                    {{ number_format($order->total, 0, ',', '.') }}

                                </strong>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span class="history-badge success">

                                    <i class="fa-solid fa-circle-check"></i>

                                    {{ $order->status }}

                                </span>

                            </td>


                            <!-- PEMBAYARAN -->

                            <td>

                                <div class="payment-info">

                                    <span class="history-badge paid">

                                        <i class="fa-solid fa-check"></i>

                                        {{ $order->payment_status }}

                                    </span>


                                    @if($order->payment)

                                        <small>

                                            {{ $order->payment->payment_method }}

                                        </small>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="history-empty"
                            >

                                <i class="fa-solid fa-receipt"></i>

                                <h3>
                                    Belum Ada Riwayat
                                </h3>

                                <p>
                                    Transaksi yang sudah selesai akan muncul di sini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>


<!-- =========================================================
     MODAL PREVIEW STRUK
========================================================= -->

<div
    id="historyReceiptModal"
    style="display:none;"
>

    <div
        class="history-receipt-overlay"
        onclick="closeHistoryReceipt(event)"
    >

        <div
            class="history-receipt-container"
            onclick="event.stopPropagation()"
        >


            <!-- TOMBOL -->

            <div class="history-receipt-actions">

                <button
                    type="button"
                    class="history-print-btn"
                    onclick="printHistoryReceipt()"
                >

                    <i class="fa-solid fa-print"></i>

                    Cetak Struk

                </button>


                <button
                    type="button"
                    class="history-close-btn"
                    onclick="closeHistoryReceipt()"
                >

                    <i class="fa-solid fa-xmark"></i>

                    Tutup

                </button>

            </div>


            <!-- =================================================
                 AREA CETAK
            ================================================== -->

            <div id="historyReceiptPrintArea">

                <div class="history-receipt-paper">


                    <!-- BRAND -->

                    <div class="history-receipt-brand">

                        <h2>
                            QUATTRO
                        </h2>

                        <p>
                            COFFEE
                        </p>

                    </div>


                    <div class="receipt-line"></div>


                    <!-- INFORMASI TRANSAKSI -->

                    <div class="history-receipt-meta">

                        <div>

                            <span>
                                No. Transaksi
                            </span>

                            <strong id="historyReceiptOrderNumber">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                Tanggal
                            </span>

                            <strong id="historyReceiptDate">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                Pelanggan
                            </span>

                            <strong id="historyReceiptCustomer">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                Meja
                            </span>

                            <strong id="historyReceiptTable">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                Pembayaran
                            </span>

                            <strong id="historyReceiptPayment">
                                -
                            </strong>

                        </div>

                    </div>


                    <div class="receipt-line"></div>


                    <!-- ITEM -->

                    <div
                        class="history-receipt-items"
                        id="historyReceiptItems"
                    >
                    </div>


                    <div class="receipt-line"></div>


                    <!-- TOTAL -->

                    <div class="history-receipt-total">

                        <div>

                            <span>
                                Subtotal
                            </span>

                            <strong id="historyReceiptSubtotal">
                                Rp 0
                            </strong>

                        </div>


                        <div>

                            <span>
                                Pajak
                            </span>

                            <strong id="historyReceiptTax">
                                Rp 0
                            </strong>

                        </div>


                        <div class="grand">

                            <span>
                                TOTAL
                            </span>

                            <strong id="historyReceiptTotal">
                                Rp 0
                            </strong>

                        </div>

                    </div>


                    <div class="receipt-line"></div>


                    <!-- STATUS -->

                    <div class="history-receipt-payment">

                        <span>
                            Status
                        </span>

                        <strong>
                            LUNAS
                        </strong>

                    </div>


                    <!-- FOOTER -->

                    <div class="history-receipt-thanks">

                        Terima kasih sudah berkunjung ke
                        Quattro Coffee.

                        <br>

                        Semoga harimu menyenangkan ☕

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     DATA TRANSAKSI + JAVASCRIPT
========================================================= -->

<script>

/* =========================================================
   DATA TRANSAKSI
========================================================= */

const historyOrders = {

    @foreach($orders as $order)

        "{{ $order->id }}": {

            id: {{ $order->id }},

            order_number: @json($order->order_number),

            created_at: @json(
                optional($order->created_at)->format('d/m/Y H:i')
            ),

            customer_name: @json(
                $order->customer_name
            ),

            table_number: @json(
                $order->table_number ?? 'Take Away'
            ),

            subtotal: {{ (float) $order->subtotal }},

            tax: {{ (float) $order->tax }},

            total: {{ (float) $order->total }},

            payment_method: @json(
                optional($order->payment)->payment_method ?? '-'
            ),

            items: [

                @foreach($order->items as $item)

                    {

                        product_name: @json(
                            $item->product_name
                        ),

                        quantity: {{ (int) $item->quantity }},

                        price: {{ (float) $item->price }},

                        subtotal: {{ (float) $item->subtotal }}

                    }

                    @if(!$loop->last)
                        ,
                    @endif

                @endforeach

            ]

        }

        @if(!$loop->last)
            ,
        @endif

    @endforeach

};


/* =========================================================
   FORMAT RUPIAH
========================================================= */

function formatRupiah(number) {

    return new Intl.NumberFormat('id-ID', {

        style: 'currency',

        currency: 'IDR',

        minimumFractionDigits: 0

    }).format(number);

}


/* =========================================================
   BUKA PREVIEW STRUK
========================================================= */

function openHistoryReceipt(orderId) {

    const order = historyOrders[String(orderId)];

    if (!order) {

        alert('Data transaksi tidak ditemukan.');

        return;
    }


    /* =====================================================
       DATA TRANSAKSI
    ===================================================== */

    document.getElementById(
        'historyReceiptOrderNumber'
    ).textContent = order.order_number;


    document.getElementById(
        'historyReceiptDate'
    ).textContent = order.created_at;


    document.getElementById(
        'historyReceiptCustomer'
    ).textContent = order.customer_name;


    document.getElementById(
        'historyReceiptTable'
    ).textContent = order.table_number;


    document.getElementById(
        'historyReceiptPayment'
    ).textContent = order.payment_method;


    /* =====================================================
       ITEM PESANAN
    ===================================================== */

    const itemsContainer =
        document.getElementById(
            'historyReceiptItems'
        );

    itemsContainer.innerHTML = '';


    if (
        !order.items ||
        order.items.length === 0
    ) {

        itemsContainer.innerHTML = `
            <div>
                Tidak ada produk.
            </div>
        `;

    }

    else {

        order.items.forEach(function(item) {

            const itemElement =
                document.createElement('div');


            itemElement.innerHTML = `

                <div class="receipt-product-name">

                    ${escapeHtml(
                        item.product_name
                    )}

                </div>

                <div class="receipt-product-detail">

                    <span>

                        ${item.quantity}
                        x
                        ${formatRupiah(item.price)}

                    </span>

                    <span>

                        ${formatRupiah(
                            item.subtotal
                        )}

                    </span>

                </div>

            `;


            itemsContainer.appendChild(
                itemElement
            );

        });

    }


    /* =====================================================
       TOTAL
    ===================================================== */

    document.getElementById(
        'historyReceiptSubtotal'
    ).textContent =
        formatRupiah(order.subtotal);


    document.getElementById(
        'historyReceiptTax'
    ).textContent =
        formatRupiah(order.tax);


    document.getElementById(
        'historyReceiptTotal'
    ).textContent =
        formatRupiah(order.total);


    /* =====================================================
       TAMPILKAN MODAL
    ===================================================== */

    document.getElementById(
        'historyReceiptModal'
    ).style.display = 'block';


    document.body.style.overflow = 'hidden';

}


/* =========================================================
   TUTUP PREVIEW
========================================================= */

function closeHistoryReceipt(event) {

    /*
     * Jika klik bagian isi modal,
     * jangan tutup modal.
     */

    if (
        event &&
        event.target !== event.currentTarget
    ) {

        return;
    }


    const modal =
        document.getElementById(
            'historyReceiptModal'
        );


    modal.style.display = 'none';


    document.body.style.overflow = '';

}


/* =========================================================
   CETAK STRUK
========================================================= */

function printHistoryReceipt() {

    const modal =
        document.getElementById(
            'historyReceiptModal'
        );


    if (
        !modal ||
        modal.style.display === 'none'
    ) {

        alert(
            'Silakan pilih transaksi terlebih dahulu.'
        );

        return;
    }


    window.print();

}


/* =========================================================
   SEARCH RIWAYAT
========================================================= */

function searchHistory() {

    const input =
        document
            .getElementById('historySearch')
            .value
            .toLowerCase();


    const rows =
        document.querySelectorAll(
            '.history-row'
        );


    rows.forEach(function(row) {

        const text =
            row.innerText.toLowerCase();


        if (text.includes(input)) {

            row.style.display = '';

        }

        else {

            row.style.display = 'none';

        }

    });

}


/* =========================================================
   ESCAPE HTML
========================================================= */

function escapeHtml(value) {

    return String(value)

        .replace(
            /&/g,
            '&amp;'
        )

        .replace(
            /</g,
            '&lt;'
        )

        .replace(
            />/g,
            '&gt;'
        )

        .replace(
            /"/g,
            '&quot;'
        )

        .replace(
            /'/g,
            '&#039;'
        );

}


/* =========================================================
   TOMBOL ESC
========================================================= */

document.addEventListener(
    'keydown',
    function(event) {

        if (event.key === 'Escape') {

            closeHistoryReceipt();

        }

    }
);


/* =========================================================
   SETELAH SELESAI PRINT
========================================================= */

window.addEventListener(
    'afterprint',
    function() {

        /*
         * Setelah selesai print,
         * modal tetap terbuka supaya user
         * masih bisa melihat struk.
         */

        document.body.style.overflow = 'hidden';

    }
);

</script>

@endsection