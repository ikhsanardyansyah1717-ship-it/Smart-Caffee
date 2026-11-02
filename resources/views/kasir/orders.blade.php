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

#orderModal .modal-card {
    box-sizing: border-box;
    width: min(1040px, calc(100vw - 32px));
    max-width: 1040px;
    max-height: min(860px, calc(100dvh - 48px));
    padding: 26px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cdb59d transparent;
}

#orderModal .modal-card input, #orderModal .modal-card select,
#confirmPaymentModal .modal-card input, #confirmPaymentModal .modal-card select { box-sizing: border-box; }
#orderModal .modal-head { margin-bottom: 16px; }
#orderModal .modal-head h2 { font-size: 26px; }
#orderModal .form-grid { grid-template-columns: 1fr; gap: 12px; margin-bottom: 0; }
#orderModal .form-grid label { min-width: 0; font-size: 13px; }
#orderModal .modal-card input, #orderModal .modal-card select { min-height: 44px; font-size: 14px; }
#orderModal .menu-selector { margin-top: 0; padding: 17px; background:#fff; }
#orderModal .menu-input-row { grid-template-columns: minmax(0, 1fr) 105px; gap: 10px; margin-top: 12px; }
#orderModal .menu-input-row label { font-size: 13px; }
#orderModal .add-menu-btn { min-height: 44px; padding: 0 14px; grid-column:1/-1; }
#orderModal .selected-menu-item { padding: 12px 0; }
#orderModal .order-total-box { margin-top: 12px; padding: 14px 16px; }
#orderModal .order-total-box strong { font-size: 23px; }
#orderModal .primary-btn.full { min-height: 50px; margin-top: 10px; }
.order-workspace { display:grid; grid-template-columns:minmax(0,1.2fr) minmax(320px,.88fr); gap:16px; align-items:start; }
.order-builder { display:grid; gap:14px; min-width:0; }
.order-panel { padding:16px; border:1px solid #eee2d7; border-radius:17px; background:#fffdfa; }
.order-panel-title { display:flex; align-items:center; gap:11px; margin:0 0 14px; }
.order-panel-icon { display:grid; place-items:center; width:38px; height:38px; border-radius:12px; background:#f5eadc; color:#925308; }
.order-panel-title h3 { margin:0; font-size:15px; color:#3b2b20; }
.order-panel-title p { margin:3px 0 0; font-size:12px; color:#9a8878; }
.order-customer-grid { display:grid; grid-template-columns:minmax(0,1.2fr) minmax(145px,.8fr); gap:12px; align-items:end; }
.order-table-field { grid-column:1/-1; }
.order-cart { position:sticky; top:0; padding:17px; border:1px solid #ead9c7; border-radius:18px; background:#fbf5ee; }
.order-cart-head { display:flex; justify-content:space-between; align-items:center; gap:10px; padding-bottom:13px; border-bottom:1px solid #eadfd3; }
.order-cart-head h3 { margin:0; font-size:17px; color:#3b2b20; }
.order-cart-head p { margin:4px 0 0; font-size:12px; color:#8b7a6b; }
.order-cart-count { padding:5px 9px; border-radius:999px; background:#efe0ce; color:#85500f; font-size:11px; font-weight:700; white-space:nowrap; }
#orderModal #selectedProducts { max-height:350px; overflow-y:auto; }
#orderModal .empty-menu { min-height:170px; flex-direction:column; gap:8px; text-align:center; }
#orderModal .empty-menu i { width:44px; height:44px; display:grid; place-items:center; border-radius:50%; background:#f5eadc; }
#orderModal .selected-menu-item { align-items:flex-start; gap:8px; }
#orderModal .menu-subtotal { align-items:center; gap:8px; }
#orderModal .menu-subtotal strong { font-size:13px; }
#orderModal .remove-menu-btn { width:30px; height:30px; flex-shrink:0; }
#orderModal .order-total-box { background:#f1e2d0; border-color:#e7d2ba; }
#orderModal .order-total-box span { font-size:13px; }
#orderModal .order-cart .primary-btn.full { margin-top:10px; }
.order-customer-grid { grid-template-columns:minmax(0,1.2fr) minmax(150px,.8fr) minmax(155px,.9fr) minmax(140px,.8fr); }
.order-table-field { grid-column:auto; }
.order-catalog-toolbar { display:grid; grid-template-columns:minmax(0,1fr) 190px; gap:10px; margin-bottom:13px; }
.order-catalog-toolbar input, .order-catalog-toolbar select { min-height:42px!important; }
.pos-product-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; max-height:430px; overflow-y:auto; padding:2px; }
.pos-product-card { display:flex; flex-direction:column; align-items:flex-start; gap:6px; min-width:0; padding:13px; border:1px solid #eee2d7; border-radius:14px; background:#fff; text-align:left; cursor:pointer; transition:transform .15s,border-color .15s,box-shadow .15s; }
.pos-product-card:hover { transform:translateY(-2px); border-color:#c9955c; box-shadow:0 8px 20px #70451314; }
.pos-product-card:disabled { opacity:.5; cursor:not-allowed; transform:none; }
.pos-product-card strong { overflow:hidden; max-width:100%; color:#3b2b20; font-size:14px; text-overflow:ellipsis; white-space:nowrap; }
.pos-product-card small { color:#8b7a6b; font-size:11px; }
.pos-product-card-price { color:#915509; font-weight:800; font-size:14px; }
.pos-product-card-foot { display:flex; justify-content:space-between; align-items:center; width:100%; gap:8px; margin-top:3px; }
.pos-product-category { padding:3px 7px; border-radius:999px; background:#f7eee4; color:#85500f; font-size:10px; font-weight:700; }
.order-cart #selectedProducts { max-height:365px; overflow-y:auto; }
.order-cart .selected-menu-item { gap:6px; }
.order-cart .menu-info strong { font-size:13px; }
.order-cart .menu-info small { font-size:11px; }
.order-cart .quantity-control { margin-top:5px; }
.order-cart-count { min-width:58px; text-align:center; }
.order-catalog-empty { grid-column:1/-1; padding:26px 14px; border:1px dashed #dfcbb4; border-radius:13px; color:#8b7a6b; text-align:center; }
#orderModal #saveOrderBtn:disabled { opacity:.7; cursor:wait; }

@media(max-width:900px) {
    .order-workspace { grid-template-columns:1fr; }
    .order-cart { position:static; }
    .pos-product-grid { max-height:260px; }
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

.menu-search-label { margin-bottom: 14px; }
.menu-search-label input { min-height: 44px; }
.stock-hint { min-height: 18px; margin: 7px 0 0; color: #7b6a5a; font-size: 12px; }
.quantity-control { display: inline-flex; align-items: center; gap: 8px; margin-top: 7px; }
.quantity-control button { width: 28px; height: 28px; border: 1px solid #eadfd3; border-radius: 8px; background: #fffaf5; color: #8c5a24; cursor: pointer; }
.quantity-control button:disabled { opacity: .4; cursor: not-allowed; }
.quantity-control strong { min-width: 20px; text-align: center; }


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

#saveOrderBtn:disabled { opacity: .7; cursor: wait; }


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

    #orderModal .form-grid { grid-template-columns: 1fr; }
    #orderModal .modal-card { width: calc(100vw - 20px); max-height: calc(100dvh - 24px); padding: 19px; }
    .order-customer-grid { grid-template-columns:1fr 1fr; }
    .order-table-field { grid-column:1/-1; }
    .order-catalog-toolbar { grid-template-columns:1fr; }
    .pos-product-grid { grid-template-columns:1fr 1fr; max-height:220px; }
    #orderModal .order-workspace { grid-template-columns:1fr; }
    #orderModal .order-cart { position:static; }
    #orderModal .order-customer-grid { grid-template-columns:1fr 1fr; }
    #orderModal .order-customer-grid label:first-child { grid-column:1/-1; }


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

                    <th>Pembayaran</th>

                    <th>Status</th>

                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>

                @forelse($orders as $order)

                    @php($paymentConfirmed = $order->payment_status === 'Dibayar' || $order->payment?->status === 'Berhasil')
                    @php($latestGatewayAttempt = $order->payment?->attempts->sortByDesc('id')->first())
                    @php($gatewayPayment = $latestGatewayAttempt !== null)
                    @php($gatewayPaymentPending = $latestGatewayAttempt?->status === 'Pending')

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

                        <td>
                            @if($order->payment)
                                <strong>{{ $order->payment->payment_method }}</strong>
                                <small style="display:block;color:{{ $paymentConfirmed ? '#2e8b57' : '#b06b00' }};">{{ $paymentConfirmed ? 'Lunas' : ($gatewayPayment ? ($gatewayPaymentPending ? 'Menunggu Midtrans' : 'QRIS perlu dibuat ulang') : ($order->payment->status === 'Gagal' ? 'Pembayaran gagal' : 'Menunggu konfirmasi')) }}</small>
                            @else
                                <small>Belum ada pembayaran</small>
                            @endif
                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span
                                class="badge {{ str_replace(' ', '-', strtolower($order->status)) }}"
                            >

                                {{ $order->status }}

                            </span>
                        </td>


                        {{-- AKSI --}}
                        <td>
                            @if($order->table_number && $order->table_number !== 'Take Away' && in_array($order->status, ['Menunggu', 'Diproses', 'Selesai']))
                                <button type="button" class="table-action" style="border:0;cursor:pointer;width:auto;padding:8px 12px;display:inline-flex;align-items:center;gap:6px;background:#2e8b57;color:white;" onclick="openFinishTableModal({{ $order->id }}, @js($order->table_number), @js(route('kasir.orders.finishTable', $order->id)))">
                                    <i class="fa-solid fa-check"></i> Selesaikan Meja
                                </button>
                            @endif

                            @if($paymentConfirmed)
                                @if($gatewayPayment && !$order->kitchen_confirmed_at && $order->status === 'Menunggu')
                                    <form method="POST" action="{{ route('kasir.orders.kitchen.confirm', $order->id) }}" style="display:inline-flex;">
                                        @csrf
                                        <button type="submit" class="table-action" title="Kirim pesanan lunas ke antrean Kitchen" style="border:0;cursor:pointer;width:auto;padding:8px 12px;display:inline-flex;align-items:center;gap:6px;background:#e9f7ef;color:#237347;">
                                            <i class="fa-solid fa-kitchen-set"></i> Konfirmasi ke Kitchen
                                        </button>
                                    </form>
                                @else
                                    <span class="table-action" title="Pembayaran lunas{{ $order->kitchen_confirmed_at ? ' dan sudah dikirim ke Kitchen' : '' }}" style="display:inline-flex;align-items:center;gap:6px;background:#e9f7ef;color:#237347;padding:8px 12px;border-radius:8px;font-weight:700;">
                                        <i class="fa-solid fa-check-double"></i> {{ $order->kitchen_confirmed_at ? 'Sudah ke Kitchen' : 'Lunas' }}
                                    </span>
                                @endif
                            @elseif($gatewayPayment && $gatewayPaymentPending)
                                <form method="POST" action="{{ route('kasir.orders.payment.check', $order->id) }}" style="display:inline-flex;">
                                    @csrf
                                    <button type="submit" class="table-action" title="Periksa status transaksi ke Midtrans sebelum menandai lunas" style="border:0;cursor:pointer;width:auto;padding:8px 12px;display:inline-flex;align-items:center;gap:6px;background:#fff5e5;color:#956014;">
                                        <i class="fa-solid fa-rotate"></i> Cek Status QRIS
                                    </button>
                                </form>
                            @elseif($gatewayPayment)
                                <span class="table-action" title="Pembayaran QRIS Midtrans harus diverifikasi otomatis" style="display:inline-flex;align-items:center;gap:6px;background:#fff5e5;color:#956014;padding:8px 12px;border-radius:8px;font-weight:700;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> QRIS gagal
                                </span>
                            @else

                                <button
                                    type="button"
                                    class="table-action"
                                    title="Konfirmasi pembayaran"
                                    style="border:0; cursor:pointer; width:auto; padding:8px 12px; display:inline-flex; align-items:center; gap:6px;"
                                    onclick="openConfirmPayment({{ $order->id }}, @js($order->order_number), {{ (float) $order->total }}, @js($order->payment && $order->payment->status === 'Pending' ? $order->payment->payment_method : null), @js($order->items->map(fn ($item) => ['name' => $item->product_name, 'quantity' => $item->quantity, 'subtotal' => (float) $item->subtotal])->values()), @js($order->created_at->format('d/m/Y H:i')), @js($order->customer_name), @js($order->table_number ?: 'Take Away'))"
                                >
                                    <i class="fa-solid fa-check"></i>
                                    {{ $order->payment?->status === 'Pending' ? 'Lihat & Konfirmasi' : 'Konfirmasi' }}
                                </button>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
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
    <div class="modal-card" style="max-width:580px;">
        <div class="modal-head">
            <div>
                <span class="eyebrow">KONFIRMASI PEMBAYARAN</span>
                <h2>Periksa & Konfirmasi</h2>
                <p id="confirmPaymentOrder" style="margin-top:8px;color:#806f61;"></p>
            </div>
            <button type="button" onclick="closeConfirmPayment()">&times;</button>
        </div>

        <form id="confirmPaymentForm" method="POST">
            @csrf

            <div id="confirmPaymentSummary" style="margin:0 0 16px;padding:15px;border:1px solid #eadfd3;border-radius:14px;background:#fffaf5;">
                <strong style="color:#493524;">Ringkasan pesanan</strong>
                <p id="confirmPaymentMeta" style="margin:7px 0 12px;color:#806f61;font-size:13px;"></p>
                <div id="existingPaymentItems" style="display:grid;gap:8px;"></div>
            </div>

            <label id="paymentMethodWrap" style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;color:#6f5a49;font-size:14px;font-weight:600;">
                Metode Pembayaran
                <select name="payment_method" id="paymentMethod" required onchange="toggleCashInput()"
                        style="width:100%;min-height:50px;padding:0 15px;border:1px solid #eadfd3;border-radius:12px;background:#fff;color:#3b2b20;font-size:15px;">
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Cash">Cash</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </label>

            <div id="confirmedMethodSummary" hidden style="margin-bottom:16px;padding:12px 14px;border-radius:12px;background:#edf7ef;color:#28643d;">
                <strong id="confirmedMethodText"></strong>
                <small style="display:block;margin-top:4px;color:#52745a;">Pastikan pembayaran sudah diterima sebelum menekan konfirmasi.</small>
            </div>
            <small id="paymentInstruction" style="display:block;margin:-7px 0 16px;color:#806f61;">Periksa metode dan jumlah pembayaran sebelum konfirmasi.</small>

            <div id="cashInputWrap" style="display:none;margin-bottom:16px;">
                <label style="display:flex;flex-direction:column;gap:8px;color:#6f5a49;font-size:14px;font-weight:600;">
                    Uang Diterima
                        <input type="number" name="cash_received" id="cashReceived" min="0" step="0.01" inputmode="decimal"
                           placeholder="Masukkan uang yang diterima"
                           style="width:100%;min-height:50px;padding:0 15px;border:1px solid #eadfd3;border-radius:12px;background:#fff;color:#3b2b20;font-size:15px;">
                </label>
                <small id="changePreview" aria-live="polite" style="display:block;margin-top:7px;color:#806f61;font-weight:700;"></small>
            </div>

            <div class="order-total-box" style="margin-top:0;">
                <span>Total Pembayaran</span>
                <strong id="confirmPaymentTotal">Rp 0</strong>
            </div>

            <input type="hidden" name="payment_method" id="confirmedPaymentMethod" disabled>

            <button id="confirmPaymentSubmit" type="submit" class="primary-btn full" style="border:0;cursor:pointer;">
                <i class="fa-solid fa-check-double"></i>
                <span>Konfirmasi Pembayaran</span>
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


            <section class="order-panel" style="margin-bottom:14px;">
                <div class="order-panel-title">
                    <span class="order-panel-icon"><i class="fa-solid fa-clipboard-list"></i></span>
                    <div><h3>Info Pesanan</h3><p>Isi nama pelanggan dan pilih tipe pesanan.</p></div>
                </div>
                <div class="order-customer-grid">
                    <label>Nama Pelanggan<input name="customer" required maxlength="100" placeholder="Contoh: Andi"></label>
                    <label>Tipe Pesanan
                        <select id="orderType" name="order_type" required onchange="updateTableOptions()">
                            <option value="meja">Makan di tempat</option>
                            <option value="take_away">Take Away</option>
                        </select>
                    </label>
                    <label id="tableField" class="order-table-field">Nomor Meja
                        <select id="tableSelect" name="table" required><option value="">Pilih meja</option></select>
                    </label>
                    <label>Metode Pembayaran
                        <select name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </label>
                </div>
            </section>

            <div class="order-workspace">
                <section class="order-panel order-catalog">
                    <div class="order-panel-title">
                        <span class="order-panel-icon"><i class="fa-solid fa-mug-hot"></i></span>
                        <div><h3>Katalog Menu</h3><p>Harga dan stok tersedia · {{ now()->format('d/m/Y') }}</p></div>
                    </div>

                    <div class="order-catalog-toolbar">
                        <input id="productSearch" type="search" placeholder="Cari menu..." autocomplete="off" aria-label="Cari menu">
                        <select id="productCategoryFilter" aria-label="Filter kategori menu">
                            <option value="">Semua kategori</option>
                            @foreach(config('product_categories') as $category)
                                <option value="{{ strtolower(str_replace(' ', '-', $category['value'])) }}">{{ $category['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pos-product-grid" id="productCatalog">
                        @forelse($products as $product)
                            @php($productCategory = collect(config('product_categories'))->firstWhere('value', $product->category))
                            <button type="button" class="pos-product-card"
                                data-product-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-stock="{{ $product->available_stock }}"
                                data-category="{{ strtolower(str_replace(' ', '-', $product->category)) }}"
                                onclick="addProductFromCard(this)">
                                <span class="pos-product-category">{{ $productCategory['label'] ?? $product->category }}</span>
                                <strong>{{ $product->name }}</strong>
                                <span class="pos-product-card-foot">
                                    <span class="pos-product-card-price">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
                                    <small class="pos-product-stock">Stok {{ $product->available_stock }}</small>
                                </span>
                            </button>
                        @empty
                            <div class="order-catalog-empty">Belum ada menu yang tersedia.</div>
                        @endforelse
                        <div id="catalogNoResults" class="order-catalog-empty" hidden>Menu tidak ditemukan.</div>
                    </div>
                </section>

                <aside class="order-cart">
                    <div class="order-cart-head">
                        <div><h3>Keranjang</h3><p>Periksa jumlah sebelum simpan.</p></div>
                        <span id="orderCartCount" class="order-cart-count">0 item</span>
                    </div>
                    <div id="selectedProducts">
                        <div class="empty-menu"><i class="fa-solid fa-basket-shopping"></i><span>Belum ada menu dipilih</span></div>
                    </div>
                    <div class="order-total-box"><span>Total pesanan</span><strong id="totalDisplay">Rp 0</strong></div>
                    <input type="hidden" name="items" id="itemsInput">
                    <input type="hidden" name="total" id="totalInput" value="0">
                    <button class="primary-btn full" type="submit" id="saveOrderBtn"><i class="fa-solid fa-check"></i> Simpan Pesanan</button>
                </aside>
            </div>

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
        document.getElementById('productSearch')?.addEventListener('input', filterProductCatalog);
        document.getElementById('productCategoryFilter')?.addEventListener('change', filterProductCatalog);
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

        for (let number = 1; number <= 20; number++) {
            const tableNumber = 'A' + String(number).padStart(2, '0');
            const isOccupied = occupiedTables.includes(tableNumber);
            const option = document.createElement('option');
            option.value = tableNumber;
            option.textContent = isOccupied ? `${tableNumber} — Sedang Dipakai` : `${tableNumber} — Tersedia`;
            option.disabled = isOccupied;
            tableSelect.appendChild(option);
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

    function openConfirmPayment(orderId, orderNumber, total, existingMethod = null, items = [], orderDate = '', customerName = '', tableNumber = '') {
        const modal = document.getElementById('confirmPaymentModal');
        const form = document.getElementById('confirmPaymentForm');
        const method = document.getElementById('paymentMethod');
        const cash = document.getElementById('cashReceived');
        const totalEl = document.getElementById('confirmPaymentTotal');
        const orderEl = document.getElementById('confirmPaymentOrder');
        const methodWrap = document.getElementById('paymentMethodWrap');
        const methodSummary = document.getElementById('confirmedMethodSummary');
        const methodText = document.getElementById('confirmedMethodText');
        const detailMeta = document.getElementById('confirmPaymentMeta');
        const detailItems = document.getElementById('existingPaymentItems');
        const hiddenMethod = document.getElementById('confirmedPaymentMethod');
        const submitButton = document.getElementById('confirmPaymentSubmit');

        confirmPaymentTotalValue = Number(total) || 0;
        form.action = `{{ url('/kasir/orders') }}/${orderId}/confirm`;
        orderEl.textContent = `Pesanan ${orderNumber}`;
        totalEl.textContent = formatRupiah(confirmPaymentTotalValue);
        method.value = existingMethod || '';
        method.disabled = Boolean(existingMethod);
        methodWrap.hidden = Boolean(existingMethod);
        methodSummary.hidden = !existingMethod;
        methodText.textContent = existingMethod ? `Metode pembayaran: ${existingMethod}` : '';
        hiddenMethod.value = existingMethod || '';
        hiddenMethod.disabled = !existingMethod;
        cash.value = '';
        cash.max = '';
        submitButton.disabled = false;
        submitButton.querySelector('span').textContent = existingMethod ? 'Konfirmasi Pembayaran' : 'Konfirmasi & Bayar';
        document.getElementById('changePreview').textContent = '';
        document.getElementById('changePreview').style.color = '#806f61';
        toggleCashInput();
        detailMeta.textContent = `${orderDate || 'Tanggal tidak tersedia'} · ${escapeOrderText(customerName || 'Pelanggan')} · ${escapeOrderText(tableNumber || 'Take Away')} · ${formatRupiah(confirmPaymentTotalValue)}`;
        detailItems.innerHTML = items.length
            ? items.map(item => `<div style="display:flex;justify-content:space-between;gap:12px;padding-bottom:7px;border-bottom:1px solid #efe5dc;"><span>${escapeOrderText(item.name)} × ${Number(item.quantity)}</span><strong>${formatRupiah(item.subtotal)}</strong></div>`).join('')
            : '<span style="color:#806f61;">Rincian menu tidak tersedia.</span>';

        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function escapeOrderText(value) {
        return String(value).replace(/[&<>"']/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character]));
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
        const instruction = document.getElementById('paymentInstruction');

        if (method === 'Cash') {
            wrap.style.display = 'block';
            cash.required = true;
            instruction.textContent = 'Hitung uang yang diterima. Pastikan jumlahnya sama dengan atau lebih besar dari total.';
        } else {
            wrap.style.display = 'none';
            cash.required = false;
            cash.value = '';
            cash.removeAttribute('max');
            preview.textContent = '';
            instruction.textContent = method === 'QRIS'
                ? 'Pastikan pembayaran QRIS berhasil diterima sebelum mengonfirmasi.'
                : 'Pilih metode dan periksa total pembayaran sebelum konfirmasi.';
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
        document.getElementById('changePreview').style.color = change >= 0 ? '#28643d' : '#b42318';
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
            return;
        }

        const submitButton = document.getElementById('confirmPaymentSubmit');
        submitButton.disabled = true;
        submitButton.querySelector('span').textContent = 'Memproses Pembayaran...';
    });


    function openModal() {

        document.getElementById('orderForm').reset();
        document.getElementById('productSearch').value = '';
        document.getElementById('productCategoryFilter').value = '';
        selectedProducts = [];
        filterProductCatalog();
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



    function filterProductCatalog() {
        const query = (document.getElementById('productSearch')?.value || '').trim().toLowerCase();
        const category = document.getElementById('productCategoryFilter')?.value || '';
        const cards = Array.from(document.querySelectorAll('.pos-product-card'));
        let visible = 0;

        cards.forEach(card => {
            const matchesName = card.dataset.name.toLowerCase().includes(query);
            const matchesCategory = !category || card.dataset.category === category;
            card.hidden = !(matchesName && matchesCategory);
            if (!card.hidden) visible++;
        });

        const empty = document.getElementById('catalogNoResults');
        if (empty) empty.hidden = cards.length === 0 || visible > 0;
    }

    function addProductFromCard(card) {
        if (!card || card.disabled) return;

        const productId = Number(card.dataset.productId);
        const productName = card.dataset.name;
        const price = Number(card.dataset.price);
        const availableStock = Number(card.dataset.stock || 0);
        const existing = selectedProducts.find(item => item.product_id === productId);

        if (existing) {
            if (existing.quantity >= availableStock) {
                showMessage(`Stok ${productName} yang tersedia hanya ${availableStock}.`);
                return;
            }
            existing.quantity++;
        } else {
            selectedProducts.push({
                product_id: productId,
                product_name: productName,
                price,
                available_stock: availableStock,
                quantity: 1
            });
        }

        renderProducts();
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
            syncProductCardStocks();

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
                                ${item.quantity} · stok tersedia ${item.available_stock}
                            </small>

                            <div class="quantity-control" aria-label="Ubah jumlah ${escapeHtml(item.product_name)}">
                                <button type="button" onclick="updateSelectedQuantity(${index}, -1)" aria-label="Kurangi jumlah">−</button>
                                <strong>${item.quantity}</strong>
                                <button type="button" onclick="updateSelectedQuantity(${index}, 1)" aria-label="Tambah jumlah" ${item.quantity >= item.available_stock ? 'disabled' : ''}>+</button>
                            </div>

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
        syncProductCardStocks();

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

    function updateSelectedQuantity(index, difference) {
        const item = selectedProducts[index];
        if (!item) return;
        const nextQuantity = item.quantity + difference;
        if (nextQuantity < 1) return;
        if (nextQuantity > item.available_stock) {
            showMessage(`Stok ${item.product_name} yang tersedia hanya ${item.available_stock}.`);
            return;
        }
        item.quantity = nextQuantity;
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

        const itemCount = selectedProducts.reduce((count, item) => count + item.quantity, 0);
        document.getElementById('orderCartCount').textContent = `${itemCount} item`;


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

    function syncProductCardStocks() {
        document.querySelectorAll('.pos-product-card').forEach(card => {
            const baseStock = Number(card.dataset.stock || 0);
            const selected = selectedProducts.find(item => item.product_id === Number(card.dataset.productId));
            const remaining = Math.max(0, baseStock - (selected?.quantity || 0));
            const stockLabel = card.querySelector('.pos-product-stock');
            if (stockLabel) stockLabel.textContent = `Sisa ${remaining}`;
            card.disabled = remaining < 1;
        });
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
            const submitButton = document.getElementById('saveOrderBtn');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan Pesanan...';

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
