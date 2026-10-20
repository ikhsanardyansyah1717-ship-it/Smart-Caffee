@extends('layouts.customer')

@section('title', 'Pesanan - Quattro Coffee')

@section('content')
<main class="orders-checkout-page app-container">
    <header class="orders-checkout-header">
        <a href="{{ route('customer.home') }}" aria-label="Kembali ke menu"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Pesanan</h1>
        <span></span>
    </header>

    <section class="order-pickup-card" aria-label="Tipe pesanan">
        <div class="order-pickup-top"><span>Tipe Pesanan</span><strong>Ambil di Outlet <i class="fa-regular fa-circle-check"></i></strong></div>
        <div class="order-pickup-bottom">
            <strong><i class="fa-solid fa-bell-concierge"></i> Diambil Sekarang</strong>
            <label class="sr-only" for="pickup-time">Waktu pengambilan</label>
            <select id="pickup-time" aria-label="Waktu pengambilan">
                <option value="now">Pilih Waktu</option>
                <option value="15">15 menit lagi</option>
                <option value="30">30 menit lagi</option>
                <option value="45">45 menit lagi</option>
            </select>
        </div>
    </section>

    <section class="order-items-section">
        <div class="order-section-heading">
            <h2>Item yang dipesan <span id="order-item-count">(0)</span></h2>
            <a href="{{ route('customer.home') }}"><i class="fa-solid fa-plus"></i> Tambah Item</a>
        </div>
        <div id="checkout-order-items" class="checkout-order-items"></div>
        <div class="order-empty-state" id="checkout-order-empty" hidden>
            <i class="fa-solid fa-basket-shopping"></i>
            <p>Keranjang masih kosong.</p>
            <a href="{{ route('customer.home') }}">Pilih Menu</a>
        </div>
    </section>

    <label class="order-note-label" for="order-note"><i class="fa-regular fa-pen-to-square"></i> Tambah catatan lainnya</label>
    <textarea id="order-note" class="order-note-input" maxlength="300" placeholder="Contoh: pesanan disiapkan tanpa sedotan"></textarea>

    <section class="order-payment-summary">
        <h2>Rincian Pembayaran</h2>
        <div><span>Subtotal <small id="checkout-order-count-label">(0 menu)</small></span><strong id="checkout-order-subtotal">Rp 0</strong></div>
        <div><span>Biaya layanan</span><strong id="checkout-order-service">Rp 0</strong></div>
        <div><span>Pembulatan</span><strong>Rp 0</strong></div>
        <div class="order-grand-total"><span>Total</span><strong id="checkout-order-total">Rp 0</strong></div>
    </section>

    <div class="checkout-bottom-spacer" aria-hidden="true"></div>
    <footer class="orders-checkout-bottom">
        <div><span>Total Pembayaran</span><strong id="checkout-sticky-total">Rp 0</strong></div>
        <button type="button" id="continue-payment-button" onclick="continueToPayment()">Lanjut Pembayaran <i class="fa-solid fa-arrow-right"></i></button>
    </footer>
</main>

<div id="toast" class="toast"><span id="toast-message"></span></div>
@endsection

@push('styles')
<style>
.orders-checkout-page.app-container{width:100%;max-width:760px;min-height:100vh;margin:0 auto;padding:0 16px 24px;background:#fff;color:var(--text-dark)}
.orders-checkout-header{height:58px;display:grid;grid-template-columns:44px 1fr 44px;align-items:center;margin:0 -16px 12px;padding:0 16px;background:#fff;box-shadow:0 2px 10px rgba(40,30,20,.08)}
.orders-checkout-header a{display:flex;align-items:center;justify-content:flex-start;height:44px;color:var(--text-dark);font-size:18px;text-decoration:none}
.orders-checkout-header h1{margin:0;text-align:center;color:var(--primary-dark);font-size:16px}
.order-pickup-card{overflow:hidden;margin:0 0 22px;border:1px solid #eee8e0;border-radius:14px;background:#fff;box-shadow:0 3px 12px rgba(40,30,20,.06)}
.order-pickup-top,.order-pickup-bottom{min-height:48px;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:0 13px}
.order-pickup-top{border-bottom:1px solid #f0ebe5;color:var(--primary-dark);font-size:12px}
.order-pickup-top strong{padding:6px 9px;border:1px solid rgba(118,63,6,.2);border-radius:8px;font-size:11px}
.order-pickup-bottom strong{display:flex;align-items:center;gap:9px;font-size:12px}
.order-pickup-bottom strong i{color:var(--primary)}
.order-pickup-bottom select{max-width:130px;padding:7px 9px;border:1px solid rgba(118,63,6,.28);border-radius:8px;background:#fff;color:var(--primary-dark);font-size:11px}
.order-section-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:0 0 12px;border-bottom:1px solid #f0ebe5}
.order-section-heading h2{margin:0;color:var(--primary-dark);font-size:14px}
.order-section-heading h2 span{color:var(--text-muted);font-weight:500}
.order-section-heading>a{padding:7px 10px;border:1px solid rgba(118,63,6,.25);border-radius:8px;color:var(--primary);font-size:11px;text-decoration:none;white-space:nowrap}
.checkout-order-items{display:grid}
.checkout-order-item{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px 14px;padding:15px 0;border-bottom:1px solid #f0ebe5}
.checkout-order-item-main{min-width:0}
.checkout-order-item-main h3{margin:0 0 5px;color:var(--primary-dark);font-size:13px}
.checkout-order-item-main p{margin:2px 0;color:#75695e;font-size:11px;line-height:1.5}
.checkout-order-item-price{align-self:start;color:var(--primary-dark);font-size:12px;font-weight:800;white-space:nowrap}
.checkout-order-item-controls{grid-column:1/-1;display:flex;align-items:center;justify-content:space-between}
.checkout-order-item-controls>strong{color:var(--primary-dark);font-size:12px}
.order-qty-controls{display:flex;align-items:center;gap:14px}
.order-qty-controls button{width:27px;height:27px;border:1px solid #d9cec2;border-radius:50%;background:#fff;color:var(--primary-dark);font-size:15px;cursor:pointer}
.order-qty-controls span{min-width:14px;text-align:center;font-size:12px;font-weight:700}
.order-note-label{display:flex;align-items:center;gap:8px;margin:15px 0 8px;color:var(--primary);font-size:12px}
.order-note-input{width:100%;min-height:58px;resize:vertical;padding:10px 12px;border:1px solid #e7ded4;border-radius:10px;font:inherit;font-size:12px}
.order-payment-summary{margin-top:20px;padding:14px;border:1px solid #e4ddd4;border-radius:13px;background:#fff}
.order-payment-summary h2{margin:0 0 10px;text-align:center;color:var(--primary-dark);font-size:13px}
.order-payment-summary>div{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed #eee7de;font-size:11px}
.order-payment-summary>div:last-child{border:0}
.order-payment-summary small{color:var(--text-muted)}
.order-grand-total{color:var(--primary-dark);font-weight:800}
.order-grand-total strong{color:var(--primary)}
.order-empty-state{padding:38px 12px;text-align:center;color:var(--text-muted)}
.order-empty-state i{font-size:26px;color:rgba(118,63,6,.35)}
.order-empty-state p{font-size:13px}
.order-empty-state a{color:var(--primary);font-size:12px}
.checkout-bottom-spacer{height:86px}
.orders-checkout-bottom{position:fixed;z-index:100;right:0;bottom:0;left:0;max-width:760px;min-height:70px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 auto;padding:10px 14px calc(10px + env(safe-area-inset-bottom));border:1px solid #eee8e0;border-radius:14px 14px 0 0;background:#fff;box-shadow:0 -5px 22px rgba(40,30,20,.1)}
.orders-checkout-bottom>div{display:grid;gap:3px;font-size:10px;color:var(--text-muted)}
.orders-checkout-bottom>div strong{color:var(--primary-dark);font-size:15px}
.orders-checkout-bottom button{min-height:42px;padding:0 15px;border:0;border-radius:9px;background:var(--primary);color:#fff;font-size:12px;font-weight:800;cursor:pointer}
.orders-checkout-bottom button:disabled{opacity:.5;cursor:not-allowed}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media(min-width:700px){.orders-checkout-page.app-container{padding:0 28px 30px}.orders-checkout-header{margin-right:-28px;margin-left:-28px;padding-right:28px;padding-left:28px}.orders-checkout-bottom{border-radius:14px 14px 0 0}.checkout-order-item{grid-template-columns:minmax(0,1fr) auto;padding:18px 4px}.order-payment-summary{max-width:620px;margin:24px auto 0}}
@media(max-width:380px){.orders-checkout-page.app-container{padding-right:12px;padding-left:12px}.orders-checkout-header{margin-right:-12px;margin-left:-12px;padding-right:12px;padding-left:12px}.order-section-heading h2{font-size:12px}.order-section-heading>a{padding:7px;font-size:10px}.orders-checkout-bottom button{padding:0 10px;font-size:11px}}
</style>
@endpush

@push('scripts')
<script>
window.quattroUser = @json(['id' => auth()->id(), 'name' => auth()->user()?->name ?? 'Sahabat Quattro', 'email' => auth()->user()?->email]);
window.quattroFavoriteIds = [];
window.quattroProducts = @json($products);
</script>
<script src="{{ asset('js/customer.js') }}?v=11"></script>
<script>
window.renderCustomerOrderPage = function () {
    const container = document.getElementById('checkout-order-items');
    if (!container || !Array.isArray(appState.cart)) return;
    const count = appState.cart.reduce((sum, item) => sum + Number(item.qty || 0), 0);
    const subtotal = appState.cart.reduce((sum, item) => sum + Number(item.price || 0) * Number(item.qty || 0), 0);
    const service = subtotal * 0.10;
    const total = subtotal + service;
    document.getElementById('order-item-count').textContent = `(${count})`;
    document.getElementById('checkout-order-count-label').textContent = `(${count} menu)`;
    document.getElementById('checkout-order-subtotal').textContent = rupiah(subtotal);
    document.getElementById('checkout-order-service').textContent = rupiah(service);
    document.getElementById('checkout-order-total').textContent = rupiah(total);
    document.getElementById('checkout-sticky-total').textContent = rupiah(total);
    document.getElementById('checkout-order-empty').hidden = count > 0;
    document.getElementById('continue-payment-button').disabled = count === 0;
    container.innerHTML = appState.cart.map(item => `
        <article class="checkout-order-item">
            <div class="checkout-order-item-main">
                <h3>${escapeHtml(item.name)}</h3>
                ${item.customization?.note ? `<p>${escapeHtml(item.customization.note)}</p>` : ''}
            </div>
            <span class="checkout-order-item-price">${rupiah(Number(item.price || 0) * Number(item.qty || 0))}</span>
            <div class="checkout-order-item-controls">
                <strong>${rupiah(item.price)}</strong>
                <div class="order-qty-controls">
                    <button type="button" aria-label="Kurangi ${escapeHtml(item.name)}" onclick="updateProductCartQty(${Number(item.id)}, -1)">−</button>
                    <span>${Number(item.qty || 0)}</span>
                    <button type="button" aria-label="Tambah ${escapeHtml(item.name)}" onclick="updateProductCartQty(${Number(item.id)}, 1)">+</button>
                </div>
            </div>
        </article>
    `).join('');
};

function continueToPayment() {
    if (!appState.cart.length) return;
    sessionStorage.setItem('quattro_order_note', document.getElementById('order-note')?.value.trim() || '');
    sessionStorage.setItem('quattro_pickup_time', document.getElementById('pickup-time')?.value || 'now');
    window.location.href = @json(route('customer.payment'));
}
</script>
@endpush
