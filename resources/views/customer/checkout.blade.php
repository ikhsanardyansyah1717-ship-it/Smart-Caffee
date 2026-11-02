@extends('layouts.customer')

@section('title', 'Pesanan - Quattro Coffee')

@section('content')
<main class="orders-checkout-page app-container">
    <header class="orders-checkout-header">
        <a href="{{ route('customer.home') }}" aria-label="Kembali ke menu"><i class="fa-solid fa-arrow-left"></i></a>
        <div><span class="checkout-kicker">QUATTRO COFFEE · ORDER</span><h1>Keranjangmu</h1></div>
        <span class="checkout-header-mark"><i class="fa-solid fa-mug-hot"></i></span>
    </header>

    <section class="order-pickup-card" aria-label="Tipe pesanan">
        <span class="pickup-icon"><i class="fa-solid fa-bag-shopping"></i></span>
        <div class="pickup-copy"><span>METODE PESANAN</span><strong>Ambil di outlet</strong><small>Pesanan disiapkan setelah pembayaran dikonfirmasi</small></div>
        <span class="pickup-status"><i class="fa-solid fa-check"></i> Sekarang</span>
    </section>

    <section class="order-items-section">
        <div class="order-section-heading">
            <div><span class="section-kicker">PILIHANMU</span><h2>Item pesanan <span id="order-item-count">(0)</span></h2></div>
            <a href="{{ route('customer.home') }}"><i class="fa-solid fa-plus"></i> Tambah menu</a>
        </div>
        <div id="checkout-order-items" class="checkout-order-items"></div>
        <div class="order-empty-state" id="checkout-order-empty" hidden>
            <i class="fa-solid fa-basket-shopping"></i>
            <p>Keranjang masih kosong.</p>
            <a href="{{ route('customer.home') }}">Pilih Menu</a>
        </div>
    </section>

    <section class="order-note-card">
        <label class="order-note-label" for="order-note"><span><i class="fa-regular fa-pen-to-square"></i> Catatan untuk barista</span><small>OPSIONAL</small></label>
        <textarea id="order-note" class="order-note-input" maxlength="300" placeholder="Contoh: tanpa sedotan, gula dipisah"></textarea>
    </section>

    <section class="order-payment-summary">
        <div class="summary-heading"><span class="section-kicker">RINGKASAN</span><h2>Rincian pembayaran</h2></div>
        <div><span>Subtotal <small id="checkout-order-count-label">(0 menu)</small></span><strong id="checkout-order-subtotal">Rp 0</strong></div>
        <div><span>Biaya layanan</span><strong id="checkout-order-service">Rp 0</strong></div>
        <div><span>Pembulatan</span><strong>Rp 0</strong></div>
        <div class="order-grand-total"><span>Total</span><strong id="checkout-order-total">Rp 0</strong></div>
    </section>

    <div class="checkout-bottom-spacer" aria-hidden="true"></div>
    <footer class="orders-checkout-bottom">
        <div><span>Total Pembayaran</span><strong id="checkout-sticky-total">Rp 0</strong></div>
        <button type="button" id="continue-payment-button" onclick="continueToPayment()"><span>Lanjut ke pembayaran</span><i class="fa-solid fa-arrow-right"></i></button>
    </footer>
</main>

<div id="toast" class="toast"><span id="toast-message"></span></div>
@endsection

@push('styles')
<style>
.orders-checkout-page.app-container{box-sizing:border-box;width:100%;max-width:760px;min-height:100vh;margin:0 auto;padding:0 18px 28px;background:#fbf8f2;color:#211a14}
.orders-checkout-header{min-height:104px;display:grid;grid-template-columns:42px 1fr 42px;align-items:center;gap:12px;margin:0 -18px 18px;padding:14px 20px;background:#fbf8f2;color:#211a14;border-bottom:1px solid #eee6dc;border-radius:0 0 20px 20px;box-shadow:0 5px 16px #30231808}
.orders-checkout-header a{display:grid;place-items:center;width:38px;height:38px;border:1px solid #eadfd1;border-radius:13px;background:#fff;color:#754000;font-size:15px;text-decoration:none}
.orders-checkout-header h1{margin:4px 0 0;color:#211a14;font-family:Georgia,serif;font-size:25px;letter-spacing:-.4px}
.checkout-kicker,.section-kicker{color:#b85c43;font-size:9px;font-weight:800;letter-spacing:1.5px}
.checkout-header-mark{display:grid;place-items:center;width:38px;height:38px;border-radius:13px;background:#fff;color:#8b4f22;font-size:16px;box-shadow:0 3px 10px #3023180a}
.order-pickup-card{display:flex;align-items:center;gap:12px;margin:0 0 24px;padding:14px;border:1px solid #e8ded0;border-radius:16px;background:#fff;color:#211a14;box-shadow:0 5px 16px #3023180c}
.pickup-icon{display:grid;flex:0 0 42px;place-items:center;width:42px;height:42px;border-radius:13px;background:#f5eee4;color:#713b05}
.pickup-copy{display:grid;flex:1;gap:3px;min-width:0}.pickup-copy>span{color:#956127;font-size:8px;font-weight:800;letter-spacing:1.1px}.pickup-copy strong{color:#24180f;font-family:Georgia,serif;font-size:14px}.pickup-copy small{color:#897b6d;font-size:10px;line-height:1.35}
.pickup-status{display:flex;align-items:center;gap:5px;padding:7px 9px;border-radius:99px;background:#edf4ea;color:#4d744c;font-size:9px;font-weight:800;white-space:nowrap}.pickup-status i{font-size:8px}
.order-section-heading{display:flex;align-items:end;justify-content:space-between;gap:10px;padding:0 0 12px;border-bottom:1px solid #e8e0d6}
.order-section-heading>div{display:grid;gap:4px}.order-section-heading h2{margin:0;color:#211a14;font-family:Georgia,serif;font-size:18px}
.order-section-heading h2 span{color:var(--text-muted);font-weight:500}
.order-section-heading>a{padding:8px 10px;border:1px solid #e4d8c8;border-radius:10px;color:#713b05;font-size:10px;font-weight:700;text-decoration:none;white-space:nowrap}
.checkout-order-items{display:grid}
.checkout-order-item{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px 14px;margin-top:10px;padding:15px;border:1px solid #e9e2d8;border-radius:14px;background:#fff}
.checkout-order-item-main{min-width:0}.checkout-order-item-main h3{margin:0 0 5px;color:#211a14;font-family:Georgia,serif;font-size:14px}
.checkout-order-item-main p{margin:2px 0;color:#75695e;font-size:11px;line-height:1.5}
.checkout-order-item-price{align-self:start;color:var(--primary-dark);font-size:12px;font-weight:800;white-space:nowrap}
.checkout-order-item-controls{grid-column:1/-1;display:flex;align-items:center;justify-content:space-between}
.checkout-order-item-controls>strong{color:#713b05;font-size:12px}
.order-qty-controls{display:flex;align-items:center;gap:14px}
.order-qty-controls button{width:30px;height:30px;border:1px solid #e5d9c9;border-radius:10px;background:#fff;color:#713b05;font-size:15px;cursor:pointer}
.order-qty-controls span{min-width:14px;text-align:center;font-size:12px;font-weight:700}
.order-note-card{margin-top:18px;padding:14px;border:1px solid #e8e0d6;border-radius:15px;background:#fff}.order-note-label{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:0 0 9px;color:#392719;font-size:11px;font-weight:700}.order-note-label span{display:flex;align-items:center;gap:7px}.order-note-label small{color:#9a8065;font-size:8px;letter-spacing:1px}
.order-note-input{box-sizing:border-box;width:100%;min-height:62px;resize:vertical;padding:11px 12px;border:1px solid #e6dfd6;border-radius:11px;background:#fff;color:#211a14;font:inherit;font-size:11px}
.order-payment-summary{margin-top:16px;padding:16px;border:1px solid #eee4d8;border-radius:17px;background:#fffdf9;box-shadow:0 5px 16px #30231808}
.summary-heading{display:grid;gap:4px;margin-bottom:7px}.summary-heading .section-kicker{color:#b85c43}.order-payment-summary h2{margin:0;color:#211a14;font-family:Georgia,serif;font-size:16px}
.order-payment-summary>div{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed #eee7de;font-size:11px}
.order-payment-summary>div:last-child{border:0}
.order-payment-summary small{color:var(--text-muted)}
.order-grand-total{color:#211a14;font-weight:800}.order-grand-total strong{color:#211a14;font-size:13px}
.order-empty-state{padding:38px 12px;text-align:center;color:var(--text-muted)}
.order-empty-state i{font-size:26px;color:rgba(118,63,6,.35)}
.order-empty-state p{font-size:13px}
.order-empty-state a{color:var(--primary);font-size:12px}
.checkout-bottom-spacer{height:86px}
.orders-checkout-bottom{position:fixed;z-index:100;right:0;bottom:0;left:0;max-width:760px;min-height:70px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 auto;padding:10px 18px calc(10px + env(safe-area-inset-bottom));border-top:1px solid #e8e0d6;background:#fff;box-shadow:0 -8px 25px #30231816}
.orders-checkout-bottom>div{display:grid;gap:3px;font-size:10px;color:var(--text-muted)}
.orders-checkout-bottom>div strong{color:var(--primary-dark);font-size:15px}
.orders-checkout-bottom button{display:flex;align-items:center;gap:14px;min-height:46px;padding:0 16px;border:0;border-radius:13px;background:#713b05;color:#fffaf3;font-size:11px;font-weight:800;cursor:pointer;box-shadow:0 5px 12px #713b0524}
.orders-checkout-bottom button:disabled{opacity:.5;cursor:not-allowed}
@media(min-width:700px){.orders-checkout-page.app-container{padding:0 28px 34px}.orders-checkout-header{margin-right:-28px;margin-left:-28px;padding-right:32px;padding-left:32px}.orders-checkout-bottom{border-radius:18px 18px 0 0}.checkout-order-item{padding:18px}.order-payment-summary{max-width:620px;margin:20px 0 0 auto}.order-items-section,.order-note-card{max-width:680px}.order-pickup-card{max-width:680px}}
@media(max-width:380px){.orders-checkout-page.app-container{padding-right:12px;padding-left:12px}.orders-checkout-header{margin-right:-12px;margin-left:-12px;padding-right:16px;padding-left:16px}.order-section-heading h2{font-size:16px}.order-section-heading>a{padding:7px;font-size:9px}.orders-checkout-bottom{padding-right:12px;padding-left:12px}.orders-checkout-bottom button{gap:8px;padding:0 10px;font-size:10px}}
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
    window.location.href = @json(route('customer.payment'));
}
</script>
@endpush
