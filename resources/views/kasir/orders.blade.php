@extends('layouts.kasir')

@section('title', 'Pesanan - Quattro Coffee')

@push('styles')
<style>
#ordersTable{width:100%;min-width:1530px;table-layout:fixed}
#ordersTable th,#ordersTable td{padding:12px 10px;vertical-align:middle}
#ordersTable th:nth-child(1),#ordersTable td:nth-child(1){width:180px}
#ordersTable th:nth-child(2),#ordersTable td:nth-child(2){width:125px}
#ordersTable th:nth-child(3),#ordersTable td:nth-child(3){width:137px}
#ordersTable th:nth-child(4),#ordersTable td:nth-child(4){width:90px}
#ordersTable th:nth-child(5),#ordersTable td:nth-child(5){width:165px;white-space:normal}
#ordersTable th:nth-child(6),#ordersTable td:nth-child(6){width:105px}
#ordersTable th:nth-child(7),#ordersTable td:nth-child(7){width:155px;white-space:normal}
#ordersTable th:nth-child(8),#ordersTable td:nth-child(8){width:130px}
#ordersTable th:nth-child(9),#ordersTable td:nth-child(9){width:168px}
#ordersTable th:nth-child(10),#ordersTable td:nth-child(10){width:275px}
#ordersTable tbody tr{height:72px}
#ordersTable tbody td{font-size:11px}
#ordersTable tbody tr{height:auto}
#ordersTable th:nth-child(8),#ordersTable th:nth-child(9),#ordersTable th:nth-child(10){text-align:center}
#ordersTable td:nth-child(8),#ordersTable td:nth-child(9),#ordersTable td:nth-child(10){text-align:center}
#ordersTable .badge{box-sizing:border-box;width:108px;min-width:108px;justify-content:center}
#ordersTable .cashier-order-timer,#ordersTable .cashier-order-time-muted{box-sizing:border-box;width:146px;min-height:34px;justify-content:center}
#ordersTable .order-actions{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;width:100%}
#ordersTable .order-action-form{width:100%;justify-content:center}
#ordersTable .order-actions .order-action-button,#ordersTable .order-actions .order-action-state{box-sizing:border-box;width:180px!important;min-width:0;max-width:100%!important;min-height:36px;margin:0 auto;justify-self:auto;text-align:center}
.orders-panel{padding:18px;border:1px solid #eadfd3;border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(82,55,31,.035)}
.orders-panel .panel-head{align-items:center;gap:18px;margin-bottom:16px}
.orders-panel .panel-head h2{margin:0 0 4px;font-size:16px;letter-spacing:-.2px}
.orders-panel .panel-head p{font-size:11px;line-height:1.45}
.orders-panel .table-wrap{overflow:auto;border:1px solid #f0e8df;border-radius:12px;background:#fff;scrollbar-color:#d9c8b5 #f8f5f1;scrollbar-width:thin}
#ordersTable{min-width:1530px;border-collapse:separate;border-spacing:0}
#ordersTable th{position:sticky;top:0;z-index:1;padding:12px 11px;border-bottom:1px solid #e9dfd5;background:#fbf8f4;color:#8f7d6b;font-size:9px;font-weight:800;letter-spacing:.045em;text-transform:uppercase}
#ordersTable td{padding:13px 11px;border-bottom:1px solid #f0eae4;line-height:1.4}
#ordersTable tbody tr:last-child td{border-bottom:0}
#ordersTable tbody tr:hover td{background:#fffdf9}
#ordersTable .badge{min-width:82px;min-height:26px;padding:5px 10px;border:1px solid transparent;border-radius:999px;font-size:10px;font-weight:750;line-height:1.1}
#ordersTable .badge.menunggu{background:#f7ecdf;color:#85500f;border-color:#efdfcc}
#ordersTable .badge.diproses{background:#fff1df;color:#9a5b0b;border-color:#f3dfc4}
#ordersTable .badge.selesai,#ordersTable .badge.sudah-diambil{background:#eaf4ec;color:#35734b;border-color:#d9e9dc}
#ordersTable .badge.dibatalkan{background:#fae8e5;color:#b84238;border-color:#f2d2cd}
#ordersTable td:nth-child(1) strong{font-size:10px;letter-spacing:.01em}
#ordersTable td:nth-child(1) small,#ordersTable td:nth-child(2) small{margin-top:4px;line-height:1.3}
#ordersTable .order-actions{gap:7px;align-content:center}
#ordersTable .order-action-state{min-height:34px;padding:7px 9px;border:1px solid #d9ebdf;border-radius:10px;font-size:10px;line-height:1.25}
#ordersTable .order-action-state.is-cancelled{background:#fae8e5;color:#b84238;border-color:#f2d2cd}
#ordersTable .order-action-state.is-failed{background:#fae8e5;color:#b84238;border-color:#f2d2cd}
#ordersTable .order-action-state.is-cancelled,#ordersTable .order-action-state.is-failed{justify-self:center;width:max-content;min-width:150px;max-width:100%;margin:0 auto}
#ordersTable .order-action-button{min-height:35px;border-radius:10px!important;font-size:10px;line-height:1.25}
@media(max-width:900px){.orders-panel{padding:14px}.orders-panel .panel-head{align-items:flex-start;flex-wrap:wrap}.orders-panel .table-wrap{border-radius:10px}}
#ordersTable .badge{display:inline-flex;align-items:center;justify-content:center;min-width:72px;min-height:25px;padding:5px 10px;border-radius:999px;font-size:10px;font-weight:700;line-height:1.15;white-space:nowrap}
#ordersTable .badge.dibatalkan{background:#fae8e5;color:#b84238;border:1px solid #f2d2cd}
#ordersTable .badge.menunggu{background:#f5e8da;color:#8B4F08;border:1px solid #efddc8}
#ordersTable td:nth-child(1) strong,#ordersTable td:nth-child(3){overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#ordersTable .order-items-summary{display:grid;gap:3px;max-height:38px;overflow:hidden;line-height:16px}
#ordersTable .order-items-summary div{display:flex;align-items:baseline;gap:4px;min-width:0;white-space:nowrap}
#ordersTable .order-items-summary strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#ordersTable .order-items-summary span,#ordersTable .order-items-summary small{flex:none;font-size:10px}
#ordersTable td:last-child form{display:flex!important;align-items:center;justify-content:center;width:100%;margin:3px 0}
#ordersTable .table-action{box-sizing:border-box;display:flex!important;align-items:center;justify-content:center;min-height:36px;width:min(100%,180px)!important;max-width:100%;margin:3px auto;padding:8px 10px!important;border:1px solid transparent!important;border-radius:9px;font-size:11px;line-height:1.25;white-space:normal;text-align:center}
.order-place-text{font-size:inherit;font-weight:400;line-height:inherit;white-space:nowrap}
.order-place-text.is-takeaway{color:#2863a5}
.order-place-text.is-dinein{color:#28754a}
#confirmPaymentModal .modal-card{width:min(760px,calc(100vw - 32px));overflow-y:auto}
#confirmPaymentModal [hidden]{display:none!important}
.transfer-proof-preview{margin:-2px 0 17px;padding:16px;border:1px solid #eadfd3;border-radius:16px;background:linear-gradient(145deg,#fffaf4,#fff)}
.transfer-proof-heading{display:flex;align-items:center;gap:11px;margin-bottom:13px}
.transfer-proof-icon{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:#f4e7d6;color:#8b4d08;font-size:17px}
.transfer-proof-heading strong,.transfer-proof-heading small{display:block}
.transfer-proof-heading strong{color:#493524;font-size:14px}
.transfer-proof-heading small{margin-top:3px;color:#8c7968;font-size:11px;font-weight:400}
.transfer-proof-frame{display:grid;place-items:center;min-height:180px;max-height:390px;overflow:auto;padding:10px;border:1px solid #eee4da;border-radius:12px;background:#f6f2ed}
.transfer-proof-frame img{display:block;width:100%;max-height:360px;object-fit:contain;border-radius:8px}
.transfer-proof-empty{display:grid;justify-items:center;gap:8px;padding:28px 18px;color:#806f61;text-align:center}
.transfer-proof-empty i{font-size:26px;color:#b49470}
.transfer-proof-empty strong{color:#493524;font-size:14px}
.transfer-proof-empty span{max-width:380px;font-size:12px;line-height:1.5}
@media(max-width:700px){#confirmPaymentModal .modal-card{width:calc(100vw - 24px);max-height:calc(100dvh - 24px);padding:19px}.transfer-proof-frame{min-height:140px}}
@media(max-width:900px){#ordersTable{min-width:1530px}}
.order-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:center;gap:6px;width:100%}
.order-action-form{display:flex;min-width:0;margin:0}
.order-actions .is-wide{grid-column:1/-1}
.order-actions .order-action-button{width:100%!important;max-width:none;min-height:36px;margin:0;padding:7px 8px!important;font-size:10px}
.order-action-state{box-sizing:border-box;display:flex;align-items:center;justify-content:center;gap:5px;min-width:0;min-height:36px;padding:6px 8px;border-radius:9px;background:#e9f7ef;color:#237347;font-size:10px;font-weight:700;text-align:center}
.order-action-state.is-cancelled{background:#fae8e5;color:#b84238;border:1px solid #f2d2cd}
.finish-table-action{background:#2e8b57!important;color:#fff!important}
.pickup-action{background:#fff2dc!important;color:#8a550a!important;border-color:#f0dcbd!important}
.kitchen-action{background:#e9f7ef!important;color:#237347!important}
.payment-action{background:#fff5e5!important;color:#956014!important}
.order-items-details summary{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:2px 5px;align-items:center;cursor:pointer;list-style:none}
.order-items-details summary::-webkit-details-marker{display:none}
.order-items-details summary strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.order-items-details summary small{grid-column:1/-1;color:#9a8068;font-size:10px}
.order-items-details[open] summary{margin-bottom:6px}
.order-items-expanded{display:grid;gap:5px;max-height:150px;overflow:auto;padding:8px;border:1px solid #eee2d7;border-radius:8px;background:#fffdfa}
.order-items-expanded div{display:flex;justify-content:space-between;gap:8px;font-size:10px;white-space:nowrap}
.order-items-expanded strong{overflow:hidden;text-overflow:ellipsis}</style>

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
.pos-product-card[hidden],#productCatalog .order-catalog-empty[hidden]{display:none!important;}
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
.order-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:center;gap:6px;width:100%}
.order-action-form{display:flex;min-width:0;margin:0}
.order-actions .is-wide{grid-column:1/-1}
.order-actions .order-action-button{width:100%!important;max-width:none;min-height:36px;margin:0;padding:7px 8px!important;font-size:10px}
.order-action-state{box-sizing:border-box;display:flex;align-items:center;justify-content:center;gap:5px;min-width:0;min-height:36px;padding:6px 8px;border-radius:9px;background:#e9f7ef;color:#237347;font-size:10px;font-weight:700;text-align:center}
.order-action-state.is-cancelled{background:#fae8e5;color:#b84238;border:1px solid #f2d2cd}
.finish-table-action{background:#2e8b57!important;color:#fff!important}
.pickup-action{background:#fff2dc!important;color:#8a550a!important;border-color:#f0dcbd!important}
.kitchen-action{background:#e9f7ef!important;color:#237347!important}
.payment-action{background:#fff5e5!important;color:#956014!important}
.order-items-details summary{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:2px 5px;align-items:center;cursor:pointer;list-style:none}
.order-items-details summary::-webkit-details-marker{display:none}
.order-items-details summary strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.order-items-details summary small{grid-column:1/-1;color:#9a8068;font-size:10px}
.order-items-details[open] summary{margin-bottom:6px}
.order-items-expanded{display:grid;gap:5px;max-height:150px;overflow:auto;padding:8px;border:1px solid #eee2d7;border-radius:8px;background:#fffdfa}
.order-items-expanded div{display:flex;justify-content:space-between;gap:8px;font-size:10px;white-space:nowrap}
.order-items-expanded strong{overflow:hidden;text-overflow:ellipsis}</style>
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

<div class="panel orders-panel">

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

                    <th>Waktu Proses</th>

                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>

                @forelse($orders as $order)

                    @php($paymentConfirmed = $order->payment_status === 'Dibayar' || $order->payment?->status === 'Berhasil')
                    @php($latestGatewayAttempt = $order->payment?->attempts->sortByDesc('id')->first())
                    @php($gatewayPayment = $latestGatewayAttempt !== null)
                    @php($gatewayPaymentPending = $latestGatewayAttempt?->status === 'Pending')
                    @php($processingStartedAt = $order->kitchen_started_at ?? ($order->status === 'Diproses' ? $order->updated_at : null))

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
                                <span class="order-place-text is-takeaway">Take Away</span>
                            @else
                                <span class="order-place-text is-dinein">{{ $order->table_number }}</span>
                            @endif
                            @if($order->table_number && $order->table_number !== 'Take Away' && in_array($order->status, ['Menunggu', 'Diproses', 'Selesai', 'Sudah Diambil']) && !$order->table_finished_at)
                                <small style="display:block;margin-top:5px;color:#b06b00;font-weight:700;">Sedang Dipakai</small>
                            @elseif($order->table_number && $order->table_number !== 'Take Away' && $order->table_finished_at)
                                <small style="display:block;margin-top:5px;color:#2e8b57;font-weight:700;">Tersedia</small>
                            @endif
                        </td>


                        {{-- PESANAN --}}
                        <td class="order-items-cell">
                            @if($order->items->isNotEmpty())
                                <details class="order-items-details">
                                    <summary>
                                        <strong>{{ $order->items->first()->product_name }}</strong>
                                        <span>× {{ $order->items->first()->quantity }}</span>
                                        @if($order->items->count() > 1)<small>+ {{ $order->items->count() - 1 }} menu lainnya</small>@endif
                                    </summary>
                                    <div class="order-items-expanded">
                                        @foreach($order->items as $item)
                                            <div><strong>{{ $item->product_name }}</strong><span>× {{ $item->quantity }}</span></div>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <span>Tidak ada item</span>
                            @endif
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

                                {{ $order->status === 'Diproses' ? 'Sedang Diproses' : $order->status }}

                            </span>
                        </td>


                        {{-- DURASI PROSES DARI KITCHEN --}}
                        <td>
                            @if($order->status === 'Diproses' && $processingStartedAt)
                                <span class="cashier-order-timer" data-started-at="{{ $processingStartedAt->toIso8601String() }}"><i class="fa-regular fa-clock"></i> <span class="timer-label">Menghitung...</span></span>
                            @elseif(in_array($order->status, ['Selesai', 'Sudah Diambil']) && $order->kitchen_started_at && $order->kitchen_completed_at)
                                @php($elapsedSeconds = abs($order->kitchen_completed_at->getTimestamp() - $order->kitchen_started_at->getTimestamp()))
                                <span class="cashier-order-timer is-complete"><i class="fa-regular fa-circle-check"></i> {{ intdiv($elapsedSeconds, 60) }} mnt {{ $elapsedSeconds % 60 }} dtk</span>
                            @else
                                <span class="cashier-order-time-muted {{ $order->status === 'Dibatalkan' ? 'is-cancelled' : '' }}">{{ $order->status === 'Menunggu' ? 'Menunggu diproses' : ($order->status === 'Dibatalkan' ? 'Tidak diproses' : 'Durasi tidak tercatat') }}</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td>
                            <div class="order-actions">
                                @if($order->status === 'Dibatalkan')
                                    @if($gatewayPayment && !$gatewayPaymentPending && !$paymentConfirmed && $order->payment?->payment_method === 'QRIS')
                                    <span class="order-action-state is-failed is-wide"><i class="fa-solid fa-triangle-exclamation"></i> QRIS gagal</span>
                                @else
                                    <span class="order-action-state is-cancelled is-wide"><i class="fa-solid fa-ban"></i> Pesanan dibatalkan</span>
                                @endif
                                @else
                                @if($paymentConfirmed && $order->status === 'Selesai')
                                    <form class="order-action-form" method="POST" action="{{ route('kasir.orders.pickup', $order->id) }}">
                                        @csrf
                                        <button type="submit" class="table-action order-action-button pickup-action">
                                            <i class="fa-solid fa-bell-concierge"></i> Sudah Diambil
                                        </button>
                                    </form>
                                @elseif($order->table_finished_at)
                                    <span class="order-action-state pickup-confirmed">
                                        <i class="fa-solid fa-circle-check"></i> Pesanan selesai
                                    </span>
                                @endif

                                @if($order->table_number && $order->table_number !== 'Take Away'
                                    && $order->status === 'Sudah Diambil' && !$order->table_finished_at)
                                    <button type="button" class="table-action order-action-button finish-table-action"
                                        onclick="openFinishTableModal({{ $order->id }}, @js($order->table_number), @js(route('kasir.orders.finishTable', $order->id)))">
                                        <i class="fa-solid fa-check"></i> Selesaikan Meja
                                    </button>
                                @endif

                                @if($order->status === 'Sudah Diambil' && (!$order->table_number || $order->table_number === 'Take Away') && !$order->table_finished_at)
                                    <span class="order-action-state is-complete is-wide"><i class="fa-solid fa-circle-check"></i> Pesanan selesai</span>
                                @endif

                                @if($paymentConfirmed)
                                    @if(!$order->kitchen_confirmed_at && $order->status === 'Menunggu')
                                        <form class="order-action-form is-wide" method="POST" action="{{ route('kasir.orders.kitchen.confirm', $order->id) }}">
                                            @csrf
                                            <button type="submit" class="table-action order-action-button kitchen-action">
                                                <i class="fa-solid fa-kitchen-set"></i> Kirim ke Kitchen
                                            </button>
                                        </form>
                                    @elseif(!in_array($order->status, ['Selesai', 'Sudah Diambil']))
                                        <span class="order-action-state kitchen-state is-wide">
                                            <i class="fa-solid fa-check-double"></i> {{ $order->kitchen_confirmed_at ? 'Sudah ke Kitchen' : 'Lunas' }}
                                        </span>
                                    @endif
                                @elseif($gatewayPayment && $gatewayPaymentPending)
                                    <form class="order-action-form is-wide" method="POST" action="{{ route('kasir.orders.payment.check', $order->id) }}">
                                        @csrf
                                        <button type="submit" class="table-action order-action-button payment-action">
                                            <i class="fa-solid fa-rotate"></i> Cek Status QRIS
                                        </button>
                                    </form>
                                @elseif($gatewayPayment)
                                    <span class="order-action-state payment-action is-wide">
                                        <i class="fa-solid fa-triangle-exclamation"></i> QRIS gagal
                                    </span>
                                @else
                                    <button type="button" class="table-action order-action-button payment-action is-wide"
                                        onclick="openConfirmPayment({{ $order->id }}, @js($order->order_number), {{ (float) $order->total }}, @js($order->payment && $order->payment->status === 'Pending' ? $order->payment->payment_method : null), @js($order->items->map(fn ($item) => ['name' => $item->product_name, 'quantity' => $item->quantity, 'subtotal' => (float) $item->subtotal])->values()), @js($order->created_at->format('d/m/Y H:i')), @js($order->customer_name), @js($order->table_number ?: 'Take Away'), @js($order->payment?->payment_method === 'Bank Transfer' && $order->payment?->transfer_proof_path ? route('kasir.orders.transfer-proof', $order->id, false) : null))">
                                        <i class="fa-solid fa-check"></i>
                                        {{ $order->payment?->status === 'Pending' ? 'Lihat & Konfirmasi' : 'Konfirmasi' }}
                                    </button>
                                @endif
                                @endif
                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="10"
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
    <div class="modal-card" style="max-width:760px;max-height:calc(100dvh - 40px);padding:26px;">
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

            <section id="transferProofPreview" class="transfer-proof-preview" hidden>
                <div class="transfer-proof-heading">
                    <span class="transfer-proof-icon"><i class="fa-regular fa-image"></i></span>
                    <div><strong>Bukti transfer</strong><small>Periksa nominal dan tujuan pembayaran sebelum konfirmasi.</small></div>
                </div>
                <div class="transfer-proof-frame">
                    <img id="transferProofImage" alt="Bukti pembayaran transfer bank">
                    <div id="transferProofMissing" class="transfer-proof-empty" hidden>
                        <i class="fa-regular fa-image"></i>
                        <strong>Bukti belum diunggah</strong>
                        <span>Minta pelanggan mengirim bukti transfer sebelum pembayaran dikonfirmasi.</span>
                    </div>
                </div>
            </section>

            <label id="paymentMethodWrap" style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;color:#6f5a49;font-size:14px;font-weight:600;">
                Metode Pembayaran
                <select name="payment_method" id="paymentMethod" required onchange="toggleCashInput()"
                        style="width:100%;min-height:50px;padding:0 15px;border:1px solid #eadfd3;border-radius:12px;background:#fff;color:#3b2b20;font-size:15px;">
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Cash">Cash</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Bank Transfer">Transfer Bank</option>
                </select>
            </label>

            <div id="confirmedMethodSummary" hidden style="margin-bottom:16px;padding:12px 14px;border-radius:12px;background:#edf7ef;color:#28643d;">
                <strong id="confirmedMethodText"></strong>
                <small style="display:block;margin-top:4px;color:#52745a;">Pastikan pembayaran sudah diterima sebelum menekan konfirmasi.</small>
            </div>
            <small id="paymentInstruction" style="display:block;margin:-7px 0 16px;color:#806f61;">Periksa metode dan jumlah pembayaran sebelum konfirmasi.</small>

            <section id="cashierQrisPanel" hidden aria-live="polite" style="margin:0 0 16px;padding:18px;border:1px solid #eadfd3;border-radius:16px;background:#fffaf5;text-align:center;">
                <strong style="display:block;color:#493524;font-size:15px;">Pembayaran QRIS</strong>
                <p id="cashierQrisStatus" style="margin:6px 0 14px;color:#806f61;font-size:13px;">Menyiapkan QRIS Midtrans…</p>
                <div id="cashierQrisImageWrap" hidden style="display:inline-flex;padding:10px;border:1px solid #eadfd3;border-radius:14px;background:#fff;">
                    <img id="cashierQrisImage" alt="Kode QRIS Midtrans untuk dipindai pelanggan" width="220" height="220" style="display:block;width:min(220px,60vw);height:auto;image-rendering:auto;">
                </div>
                <p id="cashierQrisHint" hidden style="margin:12px 0 0;color:#806f61;font-size:12px;">Pelanggan dapat memindai QR ini melalui aplikasi pembayaran. Status akan diperiksa otomatis.</p>
                <div id="cashierQrisUrlActions" hidden style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-top:12px;">
                    <a id="cashierQrisOpenUrl" href="#" target="_blank" rel="noopener" style="padding:9px 13px;border:1px solid #d7bda3;border-radius:10px;background:#fff;color:#75420f;font-size:12px;font-weight:700;text-decoration:none;">Buka QRIS</a>
                    <button type="button" onclick="copyCashierQrisUrl()" style="padding:9px 13px;border:1px solid #d7bda3;border-radius:10px;background:#fff;color:#75420f;font-size:12px;font-weight:700;cursor:pointer;">Salin URL QRIS</button>
                </div>
                <button id="cashierQrisRetry" type="button" hidden onclick="beginCashierQris(true)" style="margin-top:12px;padding:9px 15px;border:1px solid #d7bda3;border-radius:10px;background:#fff;color:#75420f;font-weight:700;cursor:pointer;">Buat QR baru</button>
            </section>

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

async function refreshCashierTables() {
    try {
        const response = await fetch(@json(route('kasir.tables.availability', [], false)), { headers: { Accept: 'application/json' } });
        if (!response.ok) return;
        const data = await response.json();
        window.cashierOccupiedTables = data.occupied || [];
        if (document.getElementById('orderType')?.value === 'meja') generateTableOptions();
    } catch (error) {
        // Keep the last server-rendered availability if a refresh is temporarily unavailable.
    }
}

document.addEventListener('DOMContentLoaded', function () {
    refreshCashierTables();
    window.setInterval(refreshCashierTables, 15000);
});
    function generateTableOptions() {
        const tableSelect = document.getElementById('tableSelect');
        if (!tableSelect) return;

        const previousValue = tableSelect.value;
        tableSelect.innerHTML = '<option value="">-- Pilih Nomor Meja --</option>';
        const occupiedTables = window.cashierOccupiedTables || @json($occupiedTables ?? []);

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
    let cashierQrisOrderId = null;
    let cashierQrisPollTimer = null;
    let cashierQrisBusy = false;
    let cashierQrisChecking = false;

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

    function openConfirmPayment(orderId, orderNumber, total, existingMethod = null, items = [], orderDate = '', customerName = '', tableNumber = '', transferProofUrl = '') {
        stopCashierQrisPolling();
        cashierQrisOrderId = orderId;
        resetCashierQrisPanel();
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
        const proofPreview = document.getElementById('transferProofPreview');
        const proofImage = document.getElementById('transferProofImage');
        const proofMissing = document.getElementById('transferProofMissing');

        const hasTransferProof = Boolean(transferProofUrl);
        const showTransferProof = existingMethod === 'Bank Transfer' || hasTransferProof;
        proofPreview.hidden = !showTransferProof;
        proofImage.hidden = !hasTransferProof;
        proofMissing.hidden = hasTransferProof;
        proofMissing.querySelector('strong').textContent = hasTransferProof ? 'Foto bukti gagal dimuat' : 'Bukti belum diunggah';
        proofMissing.querySelector('span').textContent = hasTransferProof
            ? 'Coba muat ulang halaman atau periksa file bukti transfer.'
            : 'Minta pelanggan mengirim bukti transfer sebelum pembayaran dikonfirmasi.';
        proofImage.onerror = function () {
            this.hidden = true;
            proofMissing.hidden = false;
        };        if (hasTransferProof) proofImage.src = transferProofUrl;
        else proofImage.removeAttribute('src');

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
        submitButton.hidden = false;
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
        stopCashierQrisPolling();
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
        const submitButton = document.getElementById('confirmPaymentSubmit');
        const qrisPanel = document.getElementById('cashierQrisPanel');

        if (method === 'Cash') {
            stopCashierQrisPolling();
            qrisPanel.hidden = true;
            submitButton.hidden = false;
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
                ? 'Pelanggan memindai QR Midtrans. Pembayaran akan diverifikasi otomatis.'
                : 'Pilih metode dan periksa total pembayaran sebelum konfirmasi.';
            if (method === 'QRIS') {
                submitButton.hidden = true;
                qrisPanel.hidden = false;
                if (!document.getElementById('cashierQrisImage').src || qrisPanel.dataset.orderId !== String(cashierQrisOrderId)) {
                    beginCashierQris();
                } else if (!cashierQrisPollTimer) {
                    startCashierQrisPolling();
                }
            } else {
                stopCashierQrisPolling();
                qrisPanel.hidden = true;
                submitButton.hidden = false;
            }
        }
    }

    function resetCashierQrisPanel() {
        const panel = document.getElementById('cashierQrisPanel');
        const image = document.getElementById('cashierQrisImage');
        stopCashierQrisPolling();
        panel.dataset.orderId = '';
        image.removeAttribute('src');
        document.getElementById('cashierQrisImageWrap').hidden = true;
        document.getElementById('cashierQrisHint').hidden = true;
        document.getElementById('cashierQrisUrlActions').hidden = true;
        document.getElementById('cashierQrisRetry').hidden = true;
        document.getElementById('cashierQrisStatus').textContent = 'Menyiapkan QRIS Midtrans…';
        document.getElementById('cashierQrisStatus').style.color = '#806f61';
    }

    function stopCashierQrisPolling() {
        if (cashierQrisPollTimer) window.clearInterval(cashierQrisPollTimer);
        cashierQrisPollTimer = null;
        cashierQrisChecking = false;
    }

    function cashierQrisHeaders() {
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('#confirmPaymentForm input[name="_token"]')?.value || ''
        };
    }

    async function beginCashierQris(forceNew = false) {
        const panel = document.getElementById('cashierQrisPanel');
        const imageWrap = document.getElementById('cashierQrisImageWrap');
        const retry = document.getElementById('cashierQrisRetry');
        const status = document.getElementById('cashierQrisStatus');
        const orderId = cashierQrisOrderId;
        if (!orderId || cashierQrisBusy) return;
        cashierQrisBusy = true;
        stopCashierQrisPolling();
        panel.hidden = false;
        panel.dataset.orderId = String(orderId);
        imageWrap.hidden = true;
        retry.hidden = true;
        status.textContent = forceNew ? 'Memuat ulang QRIS…' : 'Menyiapkan QRIS Midtrans…';
        try {
            const response = await fetch(`{{ url('/kasir/orders') }}/${orderId}/qris/create`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: cashierQrisHeaders(),
                body: '{}'
            });
            const data = await response.json();
            if (cashierQrisOrderId !== orderId
                || !document.getElementById('confirmPaymentModal').classList.contains('show')
                || document.getElementById('paymentMethod').value !== 'QRIS') return;
            if (!response.ok) throw new Error(data.message || 'QRIS belum dapat dibuat.');
            if (data.status === 'Dibayar') {
                handleCashierQrisPaid(data.message);
                return;
            }
            if (!data.qr_url) throw new Error(data.message || 'Kode QR belum tersedia.');
            const qrLink = document.getElementById('cashierQrisOpenUrl');
            qrLink.href = data.qr_url;
            document.getElementById('cashierQrisUrlActions').hidden = false;
            const image = document.getElementById('cashierQrisImage');
            image.onload = () => {
                if (cashierQrisOrderId !== orderId || document.getElementById('paymentMethod').value !== 'QRIS') return;
                imageWrap.hidden = false;
                document.getElementById('cashierQrisHint').hidden = false;
                status.textContent = 'Menunggu pelanggan memindai dan membayar';
                startCashierQrisPolling();
            };
            image.onerror = () => {
                if (cashierQrisOrderId !== orderId || document.getElementById('paymentMethod').value !== 'QRIS') return;
                status.textContent = 'QRIS gagal dimuat. Silakan coba buat QR baru.';
                retry.hidden = false;
            };
            image.src = data.qr_url;
        } catch (error) {
            if (cashierQrisOrderId === orderId) {
                status.textContent = error.message || 'QRIS belum dapat dibuat. Periksa koneksi dan konfigurasi Midtrans.';
                retry.textContent = 'Muat ulang QR';
                retry.hidden = false;
            }
        } finally {
            cashierQrisBusy = false;
        }
    }

    function startCashierQrisPolling() {
        if (cashierQrisPollTimer) return;
        checkCashierQrisStatus();
        cashierQrisPollTimer = window.setInterval(checkCashierQrisStatus, 5000);
    }

    async function checkCashierQrisStatus() {
        const orderId = cashierQrisOrderId;
        if (!orderId || cashierQrisChecking
            || !document.getElementById('confirmPaymentModal').classList.contains('show')
            || document.getElementById('paymentMethod').value !== 'QRIS') return;
        cashierQrisChecking = true;
        try {
            const response = await fetch(`{{ url('/kasir/orders') }}/${orderId}/payment/check`, {
                method: 'POST', credentials: 'same-origin', headers: cashierQrisHeaders(), body: '{}'
            });
            const data = await response.json();
            if (cashierQrisOrderId !== orderId) return;
            if (data.status === 'Dibayar') {
                stopCashierQrisPolling();
                handleCashierQrisPaid(data.message);
            } else if (data.status === 'Kedaluwarsa' || data.status === 'TidakAda') {
                stopCashierQrisPolling();
                document.getElementById('cashierQrisStatus').textContent = 'QRIS kedaluwarsa atau pembayaran gagal. Buat QR baru untuk mencoba kembali.';
                document.getElementById('cashierQrisImageWrap').hidden = true;
                document.getElementById('cashierQrisHint').hidden = true;
                document.getElementById('cashierQrisUrlActions').hidden = true;
                document.getElementById('cashierQrisRetry').textContent = 'Buat QR baru';
                document.getElementById('cashierQrisRetry').hidden = false;
            }
        } catch (error) {
            // Network hiccups are retried on the next interval without interrupting the cashier.
        } finally {
            cashierQrisChecking = false;
        }
    }

    function handleCashierQrisPaid(message) {
        stopCashierQrisPolling();
        document.getElementById('cashierQrisImageWrap').hidden = true;
        document.getElementById('cashierQrisHint').hidden = true;
        document.getElementById('cashierQrisUrlActions').hidden = true;
        document.getElementById('cashierQrisRetry').hidden = true;
        document.getElementById('cashierQrisStatus').textContent = message || 'Pembayaran terverifikasi. Pesanan akan diperbarui.';
        document.getElementById('cashierQrisStatus').style.color = '#28643d';
        window.setTimeout(() => window.location.reload(), 1200);
    }

    async function copyCashierQrisUrl() {
        const url = document.getElementById('cashierQrisOpenUrl')?.href;
        if (!url || url.endsWith('#')) return;
        try {
            await navigator.clipboard.writeText(url);
            showMessage('URL QRIS berhasil disalin.');
        } catch (error) {
            window.prompt('Salin URL QRIS ini:', url);
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



@push('styles')
<style>
.cashier-order-timer{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border:1px solid #f0d6b8;border-radius:999px;background:#fff6e9;color:#82500f;font-size:12px;font-weight:750;white-space:nowrap}
    .cashier-order-timer.is-complete{border-color:#d9e7dc;background:#f1f7f2;color:#55725e}
    .cashier-order-time-muted{display:inline-flex;align-items:center;min-height:26px;padding:5px 9px;border:1px solid #eee7df;border-radius:999px;background:#f8f6f2;color:#8b7a69;font-size:10px;font-weight:650;white-space:nowrap}
.cashier-order-time-muted.is-cancelled{border-color:#f2d2cd;background:#fae8e5;color:#b84238}
.order-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:center;gap:6px;width:100%}
.order-action-form{display:flex;min-width:0;margin:0}
.order-actions .is-wide{grid-column:1/-1}
.order-actions .order-action-button{width:100%!important;max-width:none;min-height:36px;margin:0;padding:7px 8px!important;font-size:10px}
.order-action-state{box-sizing:border-box;display:flex;align-items:center;justify-content:center;gap:5px;min-width:0;min-height:36px;padding:6px 8px;border-radius:9px;background:#e9f7ef;color:#237347;font-size:10px;font-weight:700;text-align:center}
.order-action-state.is-cancelled{background:#fae8e5;color:#b84238;border:1px solid #f2d2cd}
.finish-table-action{background:#2e8b57!important;color:#fff!important}
.pickup-action{background:#fff2dc!important;color:#8a550a!important;border-color:#f0dcbd!important}
.kitchen-action{background:#e9f7ef!important;color:#237347!important}
.payment-action{background:#fff5e5!important;color:#956014!important}
.order-items-details summary{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:2px 5px;align-items:center;cursor:pointer;list-style:none}
.order-items-details summary::-webkit-details-marker{display:none}
.order-items-details summary strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.order-items-details summary small{grid-column:1/-1;color:#9a8068;font-size:10px}
.order-items-details[open] summary{margin-bottom:6px}
.order-items-expanded{display:grid;gap:5px;max-height:150px;overflow:auto;padding:8px;border:1px solid #eee2d7;border-radius:8px;background:#fffdfa}
.order-items-expanded div{display:flex;justify-content:space-between;gap:8px;font-size:10px;white-space:nowrap}
.order-items-expanded strong{overflow:hidden;text-overflow:ellipsis}</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cashier-order-timer[data-started-at]').forEach(function (timer) {
        const startedAt = new Date(timer.dataset.startedAt).getTime();
        const label = timer.querySelector('.timer-label');
        if (!label || Number.isNaN(startedAt)) return;
        const update = function () {
            const seconds = Math.max(0, Math.floor((Date.now() - startedAt) / 1000));
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remainder = seconds % 60;
            label.textContent = hours > 0
                ? `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(remainder).padStart(2, '0')}`
                : `${String(minutes).padStart(2, '0')}:${String(remainder).padStart(2, '0')}`;
        };
        update();
        window.setInterval(update, 1000);
    });
});
</script>
@endpush

@endsection













