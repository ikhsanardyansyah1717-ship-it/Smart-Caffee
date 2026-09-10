@extends('layouts.kasir')

@section('title', 'Pesanan - Quattro Coffee')

@push('styles')
<style>

/* =========================================================
   MODAL PESANAN
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
    border: 1px solid #eadfd2;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 25px 70px rgba(93, 55, 18, 0.20);
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
    box-shadow: 0 0 0 3px rgba(162, 93, 8, 0.10);
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
   NOTIFIKASI
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
    box-shadow: 0 15px 40px rgba(70, 45, 25, 0.16);
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


/* =========================================================
   MODAL LOGOUT
   ========================================================= */

.logout-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(45, 30, 20, 0.45);
    backdrop-filter: blur(6px);
    z-index: 10000;
}

.logout-modal.show {
    display: flex;
}

.logout-modal-card {
    width: 100%;
    max-width: 420px;
    padding: 32px;
    text-align: center;
    background: #fffdf9;
    border: 1px solid #eadfd3;
    border-radius: 22px;
    box-shadow: 0 25px 70px rgba(60, 40, 20, 0.25);
    animation: logoutModalShow 0.25s ease;
}

@keyframes logoutModalShow {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.logout-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8ead9;
    color: #a25d08;
    font-size: 27px;
    animation: logoutIconPulse 1.5s infinite;
}

@keyframes logoutIconPulse {

    0% {
        box-shadow: 0 0 0 0 rgba(162, 93, 8, 0.25);
    }

    70% {
        box-shadow: 0 0 0 12px rgba(162, 93, 8, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(162, 93, 8, 0);
    }

}

.logout-modal-card h2 {
    margin: 0 0 8px;
    color: #2f2118;
    font-size: 23px;
}

.logout-modal-card p {
    margin: 0;
    color: #806f61;
    font-size: 14px;
    line-height: 1.6;
}

.logout-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.logout-actions button {
    flex: 1;
    min-height: 48px;
    border: 0;
    border-radius: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s ease;
}

.logout-cancel-btn {
    background: #f3eee8;
    color: #6f5a49;
}

.logout-cancel-btn:hover {
    background: #e7ddd2;
    transform: translateY(-1px);
}

.logout-confirm-btn {
    background: #a25d08;
    color: white;
}

.logout-confirm-btn:hover {
    background: #874b05;
    transform: translateY(-1px);
}

.logout-confirm-btn:active,
.logout-cancel-btn:active {
    transform: scale(0.97);
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

    .logout-modal-card {
        padding: 25px 20px;
    }

    .logout-actions {
        flex-direction: column;
    }

}
</style>
@endpush


@section('content')

<header class="topbar">

    <div>

        <span class="eyebrow">
            CASHIER CONTROL
        </span>

        <h1>
            Pesanan
        </h1>

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


{{-- ========================================================= --}}
{{-- NOTIFIKASI LARAVEL --}}
{{-- ========================================================= --}}

@if(session('success'))

    <div class="alert alert-success">

        <i class="fa-solid fa-circle-check"></i>

        {{ session('success') }}

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

            <h2>
                Daftar Pesanan
            </h2>

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

                        <td>

                            <strong>
                                {{ $order->order_number }}
                            </strong>

                            <small>
                                {{ $order->created_at->format('H:i') }}
                            </small>

                        </td>


                        <td>
                            {{ $order->customer_name }}
                        </td>


                        <td>
                            {{ $order->table_number ?? 'Take Away' }}
                        </td>


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


                        <td>

                            Rp
                            {{ number_format($order->total, 0, ',', '.') }}

                        </td>


                        <td>

                            <span class="badge {{ strtolower($order->status) }}">

                                {{ $order->status }}

                            </span>

                        </td>


                        <td>

                            <a
                                class="table-action"
                                href="{{ route('kasir.payment') }}"
                            >

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
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


{{-- ========================================================= --}}
{{-- MODAL PESANAN BARU --}}
{{-- ========================================================= --}}

<div
    class="modal"
    id="orderModal"
>

    <div class="modal-card">

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


        <form
            action="{{ route('kasir.orders.store') }}"
            method="POST"
            id="orderForm"
        >

            @csrf


            {{-- CUSTOMER + TIPE PESANAN --}}

            <div class="form-grid">

                <label>

                    Nama Pelanggan

                    <input
                        name="customer"
                        required
                        maxlength="100"
                        placeholder="Contoh: Andi"
                    >

                </label>


                <label>

                    Tipe Pesanan

                    <select
                        id="orderType"
                        name="order_type"
                        required
                        onchange="updateTableOptions()"
                    >

                        <option value="meja">
                            Meja
                        </option>

                        <option value="take_away">
                            Take Away
                        </option>

                    </select>

                </label>


                <label>

                    Nomor Meja

                    <select
                        id="tableSelect"
                        name="table"
                        required
                    >

                        <option value="">
                            -- Pilih Nomor Meja --
                        </option>

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

                                Rp
                                {{ number_format($product->price, 0, ',', '.') }}

                            </option>

                        @endforeach

                    </select>

                </label>


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


                    <label>

                        Jumlah

                        <input
                            type="number"
                            id="productQuantity"
                            min="1"
                            value="1"
                        >

                    </label>


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
            {{-- DAFTAR MENU --}}
            {{-- ================================================= --}}

            <div class="selected-menu-box">

                <div class="selected-menu-header">

                    <h3>
                        Pesanan
                    </h3>

                    <p>
                        Menu yang dipilih
                    </p>

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
            {{-- HIDDEN --}}
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
{{-- MODAL KONFIRMASI LOGOUT --}}
{{-- ========================================================= --}}

<div
    class="logout-modal"
    id="logoutModal"
>

    <div class="logout-modal-card">

        <div class="logout-icon">

            <i class="fa-solid fa-right-from-bracket"></i>

        </div>


        <h2>
            Keluar dari Sistem?
        </h2>


        <p>
            Apakah kamu yakin ingin logout dari halaman kasir?
        </p>


        <div class="logout-actions">

            <button
                type="button"
                class="logout-cancel-btn"
                onclick="closeLogoutModal()"
            >

                Batal

            </button>


            <button
                type="button"
                class="logout-confirm-btn"
                onclick="confirmLogout()"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                Ya, Logout

            </button>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- FORM LOGOUT TERSEMBUNYI --}}
{{-- ========================================================= --}}

<form
    action="{{ route('logout') }}"
    method="POST"
    id="logoutForm"
    style="display:none;"
>

    @csrf

</form>


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>


// =========================================================
// DATA
// =========================================================

const occupiedTables = @json($occupiedTables ?? []);

let selectedProducts = [];


// =========================================================
// SAAT HALAMAN SELESAI DIMUAT
// =========================================================

document.addEventListener('DOMContentLoaded', function () {

    generateTableOptions();

    updateTableOptions();

});


// =========================================================
// GENERATE NOMOR MEJA
// =========================================================

function generateTableOptions() {

    const tableSelect =
        document.getElementById('tableSelect');

    if (!tableSelect) return;


    tableSelect.disabled = false;

    tableSelect.innerHTML =
        '<option value="">-- Pilih Nomor Meja --</option>';


    const occupied =
        occupiedTables.map(function(table) {

            return String(table)
                .trim()
                .toUpperCase();

        });


    for (
        let letterCode = 65;
        letterCode <= 90;
        letterCode++
    ) {

        const letter =
            String.fromCharCode(letterCode);


        for (
            let number = 1;
            number <= 100;
            number++
        ) {

            const numberText =
                String(number).padStart(2, '0');


            const tableNumber =
                letter + numberText;


            if (occupied.includes(tableNumber)) {

                continue;

            }


            const option =
                document.createElement('option');


            option.value =
                tableNumber;


            option.textContent =
                tableNumber;


            tableSelect.appendChild(option);

        }

    }


    const firstAvailable =
        tableSelect.querySelector(
            'option[value]:not([value=""])'
        );


    if (firstAvailable) {

        tableSelect.value =
            firstAvailable.value;

    }

}


// =========================================================
// TIPE PESANAN
// =========================================================

function updateTableOptions() {

    const orderType =
        document.getElementById('orderType');

    const tableSelect =
        document.getElementById('tableSelect');


    if (!orderType || !tableSelect) {

        return;

    }


    if (orderType.value === 'take_away') {

        tableSelect.disabled = false;

        tableSelect.innerHTML =
            '<option value="Take Away">Take Away</option>';

        tableSelect.value =
            'Take Away';

    } else {

        generateTableOptions();

    }

}


// =========================================================
// FORMAT RUPIAH
// =========================================================

function formatRupiah(number) {

    return 'Rp ' +
        Number(number).toLocaleString('id-ID');

}


// =========================================================
// BUKA MODAL PESANAN
// =========================================================

function openModal() {

    const form =
        document.getElementById('orderForm');


    form.reset();


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


// =========================================================
// TUTUP MODAL PESANAN
// =========================================================

function closeModal() {

    document
        .getElementById('orderModal')
        .classList.remove('show');


    document
        .body
        .classList.remove('modal-open');

}


// =========================================================
// UPDATE HARGA MENU
// =========================================================

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


// =========================================================
// TAMBAH MENU
// =========================================================

function addProduct() {

    const select =
        document.getElementById('productSelect');

    const quantityInput =
        document.getElementById('productQuantity');


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


    const existing =
        selectedProducts.find(function(item) {

            return item.product_id === productId;

        });


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


    renderProducts();


    select.value = '';

    document.getElementById('productPrice').value = '';

    quantityInput.value = 1;

}


// =========================================================
// RENDER MENU
// =========================================================

function renderProducts() {

    const container =
        document.getElementById('selectedProducts');


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


    container.innerHTML =
        selectedProducts.map(function(item, index) {

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
                        >

                            <i class="fa-solid fa-trash"></i>

                        </button>

                    </div>

                </div>

            `;

        }).join('');


    updateTotal();

}


// =========================================================
// HAPUS MENU
// =========================================================

function removeProduct(index) {

    selectedProducts.splice(index, 1);

    renderProducts();

}


// =========================================================
// HITUNG TOTAL
// =========================================================

function updateTotal() {

    let total = 0;


    selectedProducts.forEach(function(item) {

        total +=
            Number(item.price) *
            Number(item.quantity);

    });


    document
        .getElementById('totalDisplay')
        .textContent =
            formatRupiah(total);


    document
        .getElementById('totalInput')
        .value =
            total;


    document
        .getElementById('itemsInput')
        .value =
            JSON.stringify(selectedProducts);

}


// =========================================================
// ESCAPE HTML
// =========================================================

function escapeHtml(text) {

    const div =
        document.createElement('div');


    div.textContent =
        text;


    return div.innerHTML;

}


// =========================================================
// NOTIFIKASI
// =========================================================

function showMessage(message) {

    const old =
        document.querySelector('.custom-message');


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


    setTimeout(function() {

        if (box) {

            box.remove();

        }

    }, 3000);

}


// =========================================================
// SUBMIT PESANAN
// =========================================================

document
    .getElementById('orderForm')
    .addEventListener('submit', function(event) {


        if (selectedProducts.length === 0) {

            event.preventDefault();


            showMessage(
                'Silakan pilih menu terlebih dahulu.'
            );


            return;

        }


        const orderType =
            document.getElementById('orderType').value;


        const tableSelect =
            document.getElementById('tableSelect');


        if (
            orderType === 'meja' &&
            !tableSelect.value
        ) {

            event.preventDefault();


            showMessage(
                'Silakan pilih nomor meja terlebih dahulu.'
            );


            return;

        }


        if (orderType === 'take_away') {

            tableSelect.value =
                'Take Away';

        }


        updateTotal();


        const itemsInput =
            document.getElementById('itemsInput');


        if (
            !itemsInput.value ||
            itemsInput.value === '[]'
        ) {

            event.preventDefault();


            showMessage(
                'Data menu belum siap dikirim.'
            );


            return;

        }

    });


// =========================================================
// MODAL LOGOUT
// =========================================================

function openLogoutModal() {

    const modal =
        document.getElementById('logoutModal');


    if (!modal) return;


    modal.classList.add('show');

    document
        .body
        .classList.add('modal-open');

}


function closeLogoutModal() {

    const modal =
        document.getElementById('logoutModal');


    if (!modal) return;


    modal.classList.remove('show');


    document
        .body
        .classList.remove('modal-open');

}


function confirmLogout() {

    const logoutForm =
        document.getElementById('logoutForm');


    if (!logoutForm) return;


    logoutForm.submit();

}


// =========================================================
// ESC UNTUK TUTUP MODAL
// =========================================================

document.addEventListener(
    'keydown',
    function(event) {

        if (event.key === 'Escape') {

            closeModal();

            closeLogoutModal();

        }

    }
);


// =========================================================
// KLIK LUAR MODAL
// =========================================================

document.addEventListener(
    'click',
    function(event) {

        const logoutModal =
            document.getElementById('logoutModal');


        if (
            logoutModal &&
            event.target === logoutModal
        ) {

            closeLogoutModal();

        }

    }
);

</script>

@endsection