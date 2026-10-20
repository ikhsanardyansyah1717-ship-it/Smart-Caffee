@extends('layouts.kasir')

@section('title', 'Pesanan - Quattro Coffee')

@push('styles')
<style>
/* =========================================================
   MODAL PESANAN BARU
   ========================================================= */

.modal {
    position: fixed;
    inset: 0;

    display: none;

    align-items: center;
    justify-content: center;

    padding: 24px;

    background: rgba(73, 48, 30, 0.38);

    backdrop-filter: blur(5px);

    z-index: 9999;
}


.modal.show {
    display: flex;
}


body.modal-open {
    overflow: hidden;
}


.modal-card {
    width: 100%;
    max-width: 760px;

    max-height: 90vh;

    overflow-y: auto;

    background: #fffdf9;

    border: 1px solid #eadfD2;

    border-radius: 24px;

    padding: 32px;

    box-shadow:
        0 25px 70px rgba(93, 55, 18, 0.20);

    animation: modalShow 0.22s ease;
}


@keyframes modalShow {

    from {
        opacity: 0;
        transform: translateY(15px) scale(0.98);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

}


/* =========================================================
   HEADER MODAL
   ========================================================= */

.modal-head {
    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    margin-bottom: 28px;
}


.modal-head h2 {
    margin: 5px 0 0;

    color: #2f2118;

    font-size: 28px;
}


.modal-head button {
    width: 40px;

    height: 40px;

    border: 0;

    border-radius: 50%;

    background: #f5eee6;

    color: #8c5a24;

    font-size: 27px;

    line-height: 1;

    cursor: pointer;

    transition: 0.2s;
}


.modal-head button:hover {
    background: #eadcca;

    transform: rotate(90deg);
}


/* =========================================================
   FORM
   ========================================================= */

.form-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 16px;

    margin-bottom: 18px;
}


.modal-card label {
    display: flex;

    flex-direction: column;

    gap: 8px;

    color: #6f5a49;

    font-size: 14px;

    font-weight: 600;
}


.modal-card input,
.modal-card select {
    width: 100%;

    min-height: 50px;

    padding: 0 15px;

    border: 1px solid #eadfd3;

    border-radius: 12px;

    background: #ffffff;

    color: #3b2b20;

    font-size: 15px;

    outline: none;

    transition: 0.2s;
}


.modal-card input:focus,
.modal-card select:focus {
    border-color: #a25d08;

    box-shadow:
        0 0 0 3px rgba(162, 93, 8, 0.10);
}


.modal-card input::placeholder {
    color: #a99b90;
}


/* =========================================================
   PILIH MENU
   ========================================================= */

.menu-selector {
    margin-top: 8px;

    padding: 20px;

    background: #f9f3ec;

    border: 1px solid #eee1d3;

    border-radius: 16px;
}


.menu-input-row {
    display: grid;

    grid-template-columns: 1fr 130px auto;

    align-items: end;

    gap: 12px;

    margin-top: 14px;
}


.add-menu-btn {
    min-height: 50px;

    padding: 0 20px;

    border: 0;

    border-radius: 12px;

    background: #a25d08;

    color: white;

    font-weight: 700;

    cursor: pointer;

    transition: 0.2s;
}


.add-menu-btn:hover {
    background: #874b05;

    transform: translateY(-1px);
}


.add-menu-btn:active {
    transform: translateY(1px);
}


/* =========================================================
   DAFTAR MENU
   ========================================================= */

.selected-menu-box {
    margin-top: 20px;

    border: 1px solid #eadfd3;

    border-radius: 16px;

    background: #ffffff;

    overflow: hidden;
}


.selected-menu-header {
    padding: 16px 18px;

    border-bottom: 1px solid #eee5dc;

    background: #fcf8f3;
}


.selected-menu-header h3 {
    margin: 0;

    color: #3b2b20;

    font-size: 17px;
}


.selected-menu-header p {
    margin: 4px 0 0;

    color: #9a8878;

    font-size: 13px;
}


#selectedProducts {
    padding: 5px 18px;
}


/* =========================================================
   MENU ITEM
   ========================================================= */

.selected-menu-item {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding: 15px 0;

    border-bottom: 1px solid #eee5dc;
}


.selected-menu-item:last-child {
    border-bottom: 0;
}


.menu-info {
    display: flex;

    flex-direction: column;

    gap: 4px;
}


.menu-info strong {
    color: #3b2b20;

    font-size: 15px;
}


.menu-info small {
    color: #9a8878;

    font-size: 13px;
}


.menu-subtotal {
    display: flex;

    align-items: center;

    gap: 14px;
}


.menu-subtotal strong {
    color: #925308;

    white-space: nowrap;
}


.remove-menu-btn {
    width: 35px;

    height: 35px;

    border: 0;

    border-radius: 9px;

    background: #f9e9e5;

    color: #c85a4b;

    cursor: pointer;

    transition: 0.2s;
}


.remove-menu-btn:hover {
    background: #f2d5cf;

    transform: translateY(-1px);
}


/* =========================================================
   MENU KOSONG
   ========================================================= */

.empty-menu {
    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    min-height: 80px;

    color: #a9998b;

    font-size: 14px;
}


.empty-menu i {
    color: #c4935d;

    font-size: 18px;
}


/* =========================================================
   TOTAL
   ========================================================= */

.order-total-box {
    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 18px;

    padding: 20px;

    border-radius: 16px;

    background: #f5eadc;

    border: 1px solid #ead9c7;
}


.order-total-box span {
    color: #765e49;

    font-size: 15px;

    font-weight: 600;
}


.order-total-box strong {
    color: #8e5006;

    font-size: 25px;
}


/* =========================================================
   TOMBOL SIMPAN
   ========================================================= */

.primary-btn.full {
    width: 100%;

    margin-top: 20px;

    min-height: 52px;

    border-radius: 12px;
}


/* =========================================================
   CUSTOM MESSAGE
   Tidak menggunakan popup Chrome
   ========================================================= */

.custom-message {
    position: fixed;

    top: 25px;

    right: 25px;

    z-index: 10001;

    width: 380px;

    max-width: calc(100vw - 40px);

    display: flex;

    align-items: center;

    gap: 13px;

    padding: 16px 18px;

    background: #fffdf9;

    border: 1px solid #eadccf;

    border-left: 4px solid #b56a16;

    border-radius: 15px;

    box-shadow:
        0 15px 40px rgba(70, 45, 25, 0.16);

    animation: messageShow 0.25s ease;
}


@keyframes messageShow {

    from {
        opacity: 0;
        transform: translateX(20px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }

}


.custom-message-icon {
    width: 38px;

    height: 38px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #f8ead9;

    color: #a25d08;
}


.custom-message-content {
    display: flex;

    flex-direction: column;

    gap: 3px;

    flex: 1;
}


.custom-message-content strong {
    color: #3b2b20;

    font-size: 14px;
}


.custom-message-content span {
    color: #806f61;

    font-size: 13px;

    line-height: 1.4;
}


.custom-message > button {
    border: 0;

    background: transparent;

    color: #a08d7d;

    font-size: 21px;

    cursor: pointer;
}


.custom-message > button:hover {
    color: #704513;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 700px) {

    .modal {
        padding: 12px;
    }


    .modal-card {
        padding: 22px;

        border-radius: 18px;
    }


    .form-grid {
        grid-template-columns: 1fr;
    }


    .menu-input-row {
        grid-template-columns: 1fr;
    }


    .add-menu-btn {
        width: 100%;
    }


    .selected-menu-item {
        align-items: flex-start;

        flex-direction: column;

        gap: 10px;
    }


    .menu-subtotal {
        width: 100%;

        justify-content: space-between;
    }


    .order-total-box {
        gap: 10px;
    }


    .order-total-box strong {
        font-size: 21px;
    }

}
</style>
@endpush

@section('content')

<header class="topbar">

    <div>
        <span class="eyebrow">CASHIER CONTROL</span>

        <h1>Pesanan</h1>

        <p>
            Kelola pesanan pelanggan dan buat transaksi baru.
        </p>
    </div>


    <div class="top-actions">

        <button
            type="button"
            class="primary-btn"
            onclick="openModal()"
        >
            <i class="fa-solid fa-plus"></i>

            Pesanan Baru
        </button>

    </div>

</header>


{{-- Notifikasi proses pembayaran dan meja --}}
@if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())

    <div class="alert alert-danger">

        <i class="fa-solid fa-circle-exclamation"></i>

        {{ $errors->first() }}

    </div>

@endif



{{-- ========================================================= --}}
{{-- DAFTAR PESANAN --}}
{{-- ========================================================= --}}

<div class="panel">

    <div class="panel-head">

        <div>

            <h2>Daftar Pesanan</h2>

            <p>
                Semua pesanan yang sedang berjalan.
            </p>

        </div>


        <div class="search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                id="orderSearch"
                type="text"
                placeholder="Cari pesanan..."
                onkeyup="filterTable('orderSearch','ordersTable')"
            >

        </div>

    </div>


    <div class="table-wrap">

        <table id="ordersTable">

            <thead>

                <tr>

                    <th>ID Pesanan</th>

                    <th>Tanggal Pesanan</th>

                    <th>Pelanggan</th>

                    <th>Meja</th>

                    <th>Pesanan</th>

                    <th>Total</th>

                    <th>Status</th>

                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>

                @forelse($orders as $order)

                    <tr>

                        {{-- ID PESANAN --}}
                        <td>

                            <strong>
                                {{ $order->order_number }}
                            </strong>

                            <small>
                                {{ $order->created_at->format('H:i') }}
                            </small>

                        </td>


                        {{-- TANGGAL PESANAN --}}
                        <td>
                            <strong>
                                {{ optional($order->created_at)->format('d/m/Y') }}
                            </strong>
                            <small style="display:block; margin-top:4px; color:#8b7a6b;">
                                {{ optional($order->created_at)->format('H:i') }} WIB
                            </small>
                        </td>


                        {{-- PELANGGAN --}}
                        <td>

                            {{ $order->customer_name }}

                        </td>


                        {{-- MEJA --}}
                        <td>
                            @if($order->table_number === 'Take Away' || !$order->table_number)
                                <span style="display:inline-block;padding:5px 9px;border-radius:8px;background:#f8eee2;color:#9a5709;font-weight:700;">Take Away</span>
                            @else
                                {{ $order->table_number }}
                            @endif
                            @if($order->table_number && $order->table_number !== 'Take Away' && in_array($order->status, ['Menunggu', 'Diproses', 'Selesai']))
                                <small style="display:block;margin-top:5px;color:#b06b00;font-weight:700;">Sedang Dipakai</small>
                            @elseif($order->table_number && $order->table_number !== 'Take Away' && $order->status === 'Sudah Diambil')
                                <small style="display:block;margin-top:5px;color:#2e8b57;font-weight:700;">Tersedia</small>
                            @endif
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

                                <span>
                                    Tidak ada item
                                </span>

                            @endforelse

                        </td>


                        {{-- TOTAL --}}
                        <td>

                            Rp
                            {{ number_format($order->total, 0, ',', '.') }}

                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span
                                class="badge {{ strtolower($order->status) }}"
                            >

                                {{ $order->status }}

                            </span>

                            @if($order->payment_status === 'Dibayar')
                                <small style="display:block; margin-top:4px; color:#2e8b57;">
                                    Sudah Dibayar
                                </small>
                            @else
                                <small style="display:block; margin-top:4px; color:#b06b00;">
                                    Belum Dibayar
                                </small>
                            @endif

                        </td>


                        {{-- AKSI --}}
                        <td>

                            @if($order->table_number && $order->table_number !== 'Take Away' && in_array($order->status, ['Menunggu', 'Diproses', 'Selesai']))
                                <button type="button" class="table-action" style="border:0;cursor:pointer;width:auto;padding:8px 12px;display:inline-flex;align-items:center;gap:6px;background:#2e8b57;color:white;" onclick="openFinishTableModal({{ $order->id }}, @js($order->table_number), @js(route('kasir.orders.finishTable', $order->id)))">
                                    <i class="fa-solid fa-check"></i> Selesaikan Meja
                                </button>
                            @endif

                            @if($order->payment_status !== 'Dibayar')

                                <button
                                    type="button"
                                    class="table-action"
                                    title="Konfirmasi pembayaran"
                                    style="border:0; cursor:pointer; width:auto; padding:8px 12px; display:inline-flex; align-items:center; gap:6px;"
                                    onclick="openConfirmPayment({{ $order->id }}, '{{ $order->order_number }}', {{ (float) $order->total }})"
                                >
                                    <i class="fa-solid fa-check"></i>
                                    Konfirmasi
                                </button>

                            @else

                                <span
                                    class="table-action"
                                    title="Pesanan sudah dibayar"
                                    style="opacity:.55; cursor:default;"
                                >
                                    <i class="fa-solid fa-check-double"></i>
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            style="text-align:center; padding:40px;"
                        >

                            Belum ada pesanan dari Customer.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>



{{-- MODAL KONFIRMASI SELESAIKAN MEJA --}}
<div class="modal" id="finishTableModal" role="dialog" aria-modal="true" aria-labelledby="finishTableTitle">
    <div class="modal-card" style="max-width:440px;">
        <div class="modal-head">
            <div>
                <span class="eyebrow">KONFIRMASI MEJA</span>
                <h2 id="finishTableTitle">Selesaikan Meja?</h2>
                <p id="finishTableMessage" style="margin-top:8px;color:#806f61;">Pastikan pelanggan sudah selesai menggunakan meja.</p>
            </div>
            <button type="button" aria-label="Tutup" onclick="closeFinishTableModal()">&times;</button>
        </div>
        <form id="finishTableForm" method="POST">
            @csrf
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;">
                <button type="button" class="table-action" style="width:auto;border:1px solid #eadfd3;cursor:pointer;" onclick="closeFinishTableModal()">Batal</button>
                <button type="submit" class="primary-btn" style="width:auto;border:0;cursor:pointer;background:#2e8b57;">Ya, Selesaikan Meja</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL KONFIRMASI PEMBAYARAN MANUAL --}}
{{-- ========================================================= --}}
<div class="modal" id="confirmPaymentModal">
    <div class="modal-card" style="max-width:520px;">
        <div class="modal-head">
            <div>
                <span class="eyebrow">KONFIRMASI PEMBAYARAN</span>
                <h2>Konfirmasi Pesanan</h2>
                <p id="confirmPaymentOrder" style="margin-top:8px;color:#806f61;"></p>
            </div>
            <button type="button" onclick="closeConfirmPayment()">&times;</button>
        </div>

        <form id="confirmPaymentForm" method="POST">
            @csrf

            <label style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;color:#6f5a49;font-size:14px;font-weight:600;">
                Metode Pembayaran
                <select name="payment_method" id="paymentMethod" required onchange="toggleCashInput()"
                        style="width:100%;min-height:50px;padding:0 15px;border:1px solid #eadfd3;border-radius:12px;background:#fff;color:#3b2b20;font-size:15px;">
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Cash">Cash</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </label>

            <div id="cashInputWrap" style="display:none;margin-bottom:16px;">
                <label style="display:flex;flex-direction:column;gap:8px;color:#6f5a49;font-size:14px;font-weight:600;">
                    Uang Diterima
                    <input type="number" name="cash_received" id="cashReceived" min="0" step="0.01"
                           placeholder="Masukkan uang yang diterima"
                           style="width:100%;min-height:50px;padding:0 15px;border:1px solid #eadfd3;border-radius:12px;background:#fff;color:#3b2b20;font-size:15px;">
                </label>
                <small id="changePreview" style="display:block;margin-top:7px;color:#806f61;"></small>
            </div>

            <div class="order-total-box" style="margin-top:0;">
                <span>Total Pembayaran</span>
                <strong id="confirmPaymentTotal">Rp 0</strong>
            </div>

            <button type="submit" class="primary-btn full" style="border:0;cursor:pointer;">
                <i class="fa-solid fa-check-double"></i>
                Konfirmasi & Bayar
            </button>
        </form>
    </div>
</div>


{{-- ========================================================= --}}
{{-- MODAL PESANAN BARU --}}
{{-- ========================================================= --}}

<div
    class="modal"
    id="orderModal"
>

    <div class="modal-card">


        {{-- HEADER MODAL --}}
        <div class="modal-head">

            <div>

                <span class="eyebrow">
                    TRANSAKSI BARU
                </span>

                <h2>
                    Buat Pesanan
                </h2>

            </div>


            <button
                type="button"
                onclick="closeModal()"
            >
                &times;
            </button>

        </div>



        {{-- ================================================= --}}
        {{-- FORM PESANAN --}}
        {{-- ================================================= --}}

        <form
            action="{{ route('kasir.orders.store') }}"
            method="POST"
            id="orderForm"
        >

            @csrf


            {{-- DATA CUSTOMER --}}
            <div class="form-grid">


                {{-- NAMA CUSTOMER --}}
                <label>

                    Nama Pelanggan

                    <input
                        name="customer"
                        required
                        placeholder="Contoh: Andi"
                    >

                </label>


                {{-- TIPE PESANAN --}}
                <label>
                    Tipe Pesanan
                    <select id="orderType" name="order_type" required onchange="updateTableOptions()">
                        <option value="meja">Meja</option>
                        <option value="take_away">Take Away</option>
                    </select>
                </label>

                {{-- NOMOR MEJA --}}
                <label id="tableField">
                    Nomor Meja
                    <select id="tableSelect" name="table" required>
                        <option value="">-- Pilih Nomor Meja --</option>
                    </select>
                </label>

            </div>



            {{-- ================================================= --}}
            {{-- PILIH MENU --}}
            {{-- ================================================= --}}

            <div class="menu-selector">


                <label>

                    Pilih Menu

                    <select
                        id="productSelect"
                        onchange="updateProductPrice()"
                    >

                        <option value="">
                            -- Pilih Menu --
                        </option>


                        @foreach($products as $product)

                            <option
                                value="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                            >

                                {{ $product->name }}
                                -
                                Rp {{ number_format($product->price, 0, ',', '.') }}

                            </option>

                        @endforeach

                    </select>

                </label>


                {{-- HARGA --}}
                <div class="menu-input-row">


                    <label>

                        Harga

                        <input
                            type="text"
                            id="productPrice"
                            readonly
                            placeholder="Rp 0"
                        >

                    </label>


                    {{-- JUMLAH --}}
                    <label>

                        Jumlah

                        <input
                            type="number"
                            id="productQuantity"
                            min="1"
                            value="1"
                        >

                    </label>


                    {{-- TOMBOL TAMBAH --}}
                    <button
                        type="button"
                        class="add-menu-btn"
                        onclick="addProduct()"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Tambah

                    </button>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- DAFTAR MENU YANG DIPILIH --}}
            {{-- ================================================= --}}

            <div class="selected-menu-box">

                <div class="selected-menu-header">

                    <div>

                        <h3>
                            Pesanan
                        </h3>

                        <p>
                            Menu yang dipilih
                        </p>

                    </div>

                </div>


                <div id="selectedProducts">

                    <div class="empty-menu">

                        <i class="fa-solid fa-mug-hot"></i>

                        <span>
                            Belum ada menu dipilih
                        </span>

                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- TOTAL --}}
            {{-- ================================================= --}}

            <div class="order-total-box">

                <span>
                    Total Pembayaran
                </span>

                <strong id="totalDisplay">
                    Rp 0
                </strong>

            </div>



            {{-- ================================================= --}}
            {{-- INPUT TERSEMBUNYI --}}
            {{-- ================================================= --}}

            <input
                type="hidden"
                name="items"
                id="itemsInput"
            >


            <input
                type="hidden"
                name="total"
                id="totalInput"
                value="0"
            >



            {{-- ================================================= --}}
            {{-- SIMPAN --}}
            {{-- ================================================= --}}

            <button
                class="primary-btn full"
                type="submit"
                id="saveOrderBtn"
            >

                <i class="fa-solid fa-check"></i>

                Simpan Pesanan

            </button>

        </form>

    </div>

</div>



{{-- ========================================================= --}}
{{-- JAVASCRIPT PESANAN --}}
{{-- ========================================================= --}}

<script>

    /**
     * ========================================================
     * DATA MENU YANG DIPILIH
     * ========================================================
     */

    let selectedProducts = [];


    document.addEventListener('DOMContentLoaded', function () {
        generateTableOptions();
        updateTableOptions();
    });


    /**
     * ========================================================
     * TIPE PESANAN & NOMOR MEJA
     * ========================================================
     */

    function generateTableOptions() {
        const tableSelect = document.getElementById('tableSelect');
        if (!tableSelect) return;

        const previousValue = tableSelect.value;
        tableSelect.innerHTML = '<option value="">-- Pilih Nomor Meja --</option>';
        const occupiedTables = @json($occupiedTables ?? []);

        for (let letterCode = 65; letterCode <= 90; letterCode++) {
            const letter = String.fromCharCode(letterCode);
            for (let number = 1; number <= 100; number++) {
                const tableNumber = letter + String(number).padStart(2, '0');
                const isOccupied = occupiedTables.includes(tableNumber);
                const option = document.createElement('option');
                option.value = tableNumber;
                option.textContent = isOccupied ? `${tableNumber} — Sedang Dipakai` : `${tableNumber} — Tersedia`;
                option.disabled = isOccupied;
                tableSelect.appendChild(option);
            }
        }
        if (previousValue && !occupiedTables.includes(previousValue)) tableSelect.value = previousValue;
    }

    function updateTableOptions() {
        const orderType = document.getElementById('orderType');
        const tableSelect = document.getElementById('tableSelect');
        const tableField = document.getElementById('tableField');
        if (!orderType || !tableSelect) return;

        if (orderType.value === 'take_away') {
            tableSelect.innerHTML = '<option value="Take Away">Take Away</option>';
            tableSelect.value = 'Take Away';
            tableSelect.required = true;
            tableSelect.disabled = false;
            if (tableField) tableField.style.display = 'none';
        } else {
            if (tableField) tableField.style.display = '';
            tableSelect.disabled = false;
            tableSelect.required = true;
            generateTableOptions();
            if (tableSelect.value === 'Take Away') tableSelect.value = '';
        }
    }


    /**
     * ========================================================
     * FORMAT RUPIAH
     * ========================================================
     */

    function formatRupiah(number) {

        return 'Rp ' + Number(number).toLocaleString('id-ID');

    }



    /**
     * ========================================================
     * BUKA MODAL
     * ========================================================
     */

    let confirmPaymentTotalValue = 0;

    function openFinishTableModal(orderId, tableNumber, actionUrl) {
        const modal = document.getElementById('finishTableModal');
        const form = document.getElementById('finishTableForm');
        const message = document.getElementById('finishTableMessage');
        if (!modal || !form) return;
        form.action = actionUrl;
        message.textContent = `Pastikan pelanggan di meja ${tableNumber} sudah selesai. Meja akan tersedia kembali setelah dikonfirmasi.`;
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeFinishTableModal() {
        const modal = document.getElementById('finishTableModal');
        if (modal) modal.classList.remove('show');
        if (!document.querySelector('.modal.show')) document.body.classList.remove('modal-open');
    }

    function openConfirmPayment(orderId, orderNumber, total) {
        const modal = document.getElementById('confirmPaymentModal');
        const form = document.getElementById('confirmPaymentForm');
        const method = document.getElementById('paymentMethod');
        const cash = document.getElementById('cashReceived');
        const totalEl = document.getElementById('confirmPaymentTotal');
        const orderEl = document.getElementById('confirmPaymentOrder');

        confirmPaymentTotalValue = Number(total) || 0;
        form.action = `{{ url('/kasir/orders') }}/${orderId}/confirm`;
        orderEl.textContent = `Pesanan ${orderNumber}`;
        totalEl.textContent = formatRupiah(confirmPaymentTotalValue);
        method.value = '';
        cash.value = '';
        document.getElementById('changePreview').textContent = '';
        toggleCashInput();

        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeConfirmPayment() {
        document.getElementById('confirmPaymentModal').classList.remove('show');
        if (!document.getElementById('orderModal').classList.contains('show')) {
            document.body.classList.remove('modal-open');
        }
    }

    function toggleCashInput() {
        const method = document.getElementById('paymentMethod').value;
        const wrap = document.getElementById('cashInputWrap');
        const cash = document.getElementById('cashReceived');
        const preview = document.getElementById('changePreview');

        if (method === 'Cash') {
            wrap.style.display = 'block';
            cash.required = true;
        } else {
            wrap.style.display = 'none';
            cash.required = false;
            cash.value = '';
            preview.textContent = '';
        }
    }

    document.addEventListener('input', function(event) {
        if (event.target.id !== 'cashReceived') return;
        const received = Number(event.target.value) || 0;
        const change = received - confirmPaymentTotalValue;
        document.getElementById('changePreview').textContent =
            change >= 0
                ? `Kembalian: ${formatRupiah(change)}`
                : `Kurang: ${formatRupiah(Math.abs(change))}`;
    });

    document.getElementById('confirmPaymentForm').addEventListener('submit', function(event) {
        const method = document.getElementById('paymentMethod').value;
        const cash = Number(document.getElementById('cashReceived').value) || 0;

        if (!method) {
            event.preventDefault();
            showMessage('Silakan pilih metode pembayaran.');
            return;
        }

        if (method === 'Cash' && cash < confirmPaymentTotalValue) {
            event.preventDefault();
            showMessage('Uang yang diterima belum mencukupi.');
        }
    });


    function openModal() {

        document.getElementById('orderForm').reset();
        selectedProducts = [];
        renderProducts();

        document
            .getElementById('orderModal')
            .classList.add('show');

        document
            .body
            .classList.add('modal-open');

        generateTableOptions();
        updateTableOptions();
    }



    /**
     * ========================================================
     * TUTUP MODAL
     * ========================================================
     */

    function closeModal() {

        document
            .getElementById('orderModal')
            .classList.remove('show');

        document
            .body
            .classList.remove('modal-open');

    }



    /**
     * ========================================================
     * KETIKA MENU DIPILIH
     * ========================================================
     */

    function updateProductPrice() {

        const select =
            document.getElementById('productSelect');

        const priceInput =
            document.getElementById('productPrice');


        if (!select.value) {

            priceInput.value = '';

            return;

        }


        const option =
            select.options[select.selectedIndex];


        const price =
            Number(option.dataset.price);


        priceInput.value =
            formatRupiah(price);

    }



    /**
     * ========================================================
     * TAMBAH MENU
     * ========================================================
     */

    function addProduct() {

        const select =
            document.getElementById('productSelect');

        const quantityInput =
            document.getElementById('productQuantity');


        /**
         * Pastikan menu dipilih
         */
        if (!select.value) {

            showMessage(
                'Silakan pilih menu terlebih dahulu.'
            );

            return;

        }


        const option =
            select.options[select.selectedIndex];


        const productId =
            Number(select.value);


        const productName =
            option.dataset.name;


        const price =
            Number(option.dataset.price);


        const quantity =
            Math.max(
                1,
                Number(quantityInput.value) || 1
            );


        /**
         * Cek apakah produk sudah ada
         */
        const existing =
            selectedProducts.find(
                item => item.product_id === productId
            );


        if (existing) {

            existing.quantity += quantity;

        } else {

            selectedProducts.push({

                product_id: productId,

                product_name: productName,

                price: price,

                quantity: quantity

            });

        }


        /**
         * Render ulang
         */
        renderProducts();


        /**
         * Reset pilihan
         */
        select.value = '';

        document
            .getElementById('productPrice')
            .value = '';

        quantityInput.value = 1;

    }



    /**
     * ========================================================
     * TAMPILKAN MENU
     * ========================================================
     */

    function renderProducts() {

        const container =
            document.getElementById('selectedProducts');


        /**
         * Jika kosong
         */
        if (selectedProducts.length === 0) {

            container.innerHTML = `

                <div class="empty-menu">

                    <i class="fa-solid fa-mug-hot"></i>

                    <span>
                        Belum ada menu dipilih
                    </span>

                </div>

            `;

            updateTotal();

            return;

        }


        /**
         * Buat HTML menu
         */
        container.innerHTML =
            selectedProducts.map((item, index) => {

                const subtotal =
                    item.price * item.quantity;


                return `

                    <div class="selected-menu-item">

                        <div class="menu-info">

                            <strong>
                                ${escapeHtml(item.product_name)}
                            </strong>

                            <small>
                                ${formatRupiah(item.price)}
                                ×
                                ${item.quantity}
                            </small>

                        </div>


                        <div class="menu-subtotal">

                            <strong>
                                ${formatRupiah(subtotal)}
                            </strong>

                            <button
                                type="button"
                                class="remove-menu-btn"
                                onclick="removeProduct(${index})"
                                title="Hapus menu"
                            >

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </div>

                    </div>

                `;

            }).join('');


        updateTotal();

    }



    /**
     * ========================================================
     * HAPUS MENU
     * ========================================================
     */

    function removeProduct(index) {

        selectedProducts.splice(index, 1);

        renderProducts();

    }



    /**
     * ========================================================
     * HITUNG TOTAL
     * ========================================================
     */

    function updateTotal() {

        let total = 0;


        selectedProducts.forEach(item => {

            total +=
                item.price *
                item.quantity;

        });


        document
            .getElementById('totalDisplay')
            .textContent =
                formatRupiah(total);


        document
            .getElementById('totalInput')
            .value = total;


        /**
         * Kirim data menu ke controller
         */
        document
            .getElementById('itemsInput')
            .value =
                JSON.stringify(
                    selectedProducts
                );

    }



    /**
     * ========================================================
     * CEGAH HTML INJECTION
     * ========================================================
     */

    function escapeHtml(text) {

        const div =
            document.createElement('div');

        div.textContent = text;

        return div.innerHTML;

    }



    /**
     * ========================================================
     * PESAN INFORMASI
     * ========================================================
     *
     * Menggunakan desain internal website,
     * bukan popup Chrome.
     */

    function showMessage(message) {

        const old =
            document.querySelector(
                '.custom-message'
            );


        if (old) {
            old.remove();
        }


        const box =
            document.createElement('div');


        box.className =
            'custom-message';


        box.innerHTML = `

            <div class="custom-message-icon">

                <i class="fa-solid fa-circle-exclamation"></i>

            </div>

            <div class="custom-message-content">

                <strong>
                    Perhatian
                </strong>

                <span>
                    ${escapeHtml(message)}
                </span>

            </div>

            <button
                type="button"
                onclick="this.parentElement.remove()"
            >
                &times;
            </button>

        `;


        document.body.appendChild(box);


        setTimeout(() => {

            if (box) {
                box.remove();
            }

        }, 3000);

    }



    /**
     * ========================================================
     * VALIDASI SEBELUM SIMPAN
     * ========================================================
     */

    document
        .getElementById('orderForm')
        .addEventListener('submit', function(event) {


            /**
             * Tidak boleh simpan kalau belum pilih menu
             */
            if (selectedProducts.length === 0) {

                event.preventDefault();


                showMessage(
                    'Silakan pilih menu terlebih dahulu.'
                );


                return;

            }


            const orderType = document.getElementById('orderType').value;
            const tableSelect = document.getElementById('tableSelect');

            if (orderType === 'meja' && !tableSelect.value) {
                event.preventDefault();
                showMessage('Silakan pilih nomor meja terlebih dahulu.');
                return;
            }

            if (orderType === 'take_away') {
                tableSelect.value = 'Take Away';
            }

            // Pastikan daftar menu terbaru terkirim untuk semua tipe pesanan.
            updateTotal();

            /**
             * Update data terakhir
             */
            updateTotal();

        });



    /**
     * ========================================================
     * ESC UNTUK TUTUP MODAL
     * ========================================================
     */

    document.getElementById('finishTableModal')?.addEventListener('click', function(event) {
        if (event.target === this) closeFinishTableModal();
    });

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {
                closeModal();
                closeConfirmPayment();
                closeFinishTableModal();
            }

        }
    );

</script>

@endsection
