@extends('layouts.customer')

@section('title', 'Pembayaran - Quattro Coffee')

@section('content')
<main class="customer-payment-page app-container">
    <header class="customer-payment-header">
        <a href="{{ route('customer.orders') }}" aria-label="Kembali ke pesanan"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Pembayaran</h1>
        <span></span>
    </header>

    <div class="payment-pickup-type"><span>Tipe Pesanan</span><strong>Ambil di Outlet <i class="fa-regular fa-circle-check"></i></strong></div>

    <section class="payment-customer-info">
        <h2>Informasi Pemesan</h2>
        <label for="customer-name">Nama Lengkap<span>*</span></label>
        <div class="payment-page-input"><i class="fa-regular fa-user"></i><input id="customer-name" type="text" maxlength="255" value="{{ auth()->user()?->name }}" placeholder="Nama Lengkap" required></div>
        <label for="customer-phone">Nomor Ponsel <small>(untuk info promo)</small></label>
        <div class="payment-page-input"><i class="fa-solid fa-phone"></i><input id="customer-phone" type="tel" maxlength="30" value="{{ auth()->user()?->phone }}" placeholder="Nomor Ponsel"></div>
        <label for="customer-email">Kirim struk ke email</label>
        <div class="payment-page-input"><i class="fa-regular fa-envelope"></i><input id="customer-email" type="email" maxlength="255" value="{{ auth()->user()?->email }}" placeholder="Email"></div>
    </section>

    <section class="payment-order-source">
        <h2>Kamu pesan dari</h2>
        <p>Quattro Coffee</p>
        <div><i class="fa-solid fa-bell-concierge"></i><strong id="payment-pickup-time">Diambil Sekarang</strong></div>
    </section>

    <section class="payment-method-page">
        <h2>Selesaikan Pembayaran</h2>
        <label class="payment-method payment-qris-only" for="payment-qris">
            <input type="radio" name="payment_method" id="payment-qris" value="QRIS" checked>
            <span class="payment-method-icon qris-icon"><i class="fa-solid fa-qrcode"></i></span>
            <span class="payment-method-info"><strong>QRIS</strong><span>Scan QR dari aplikasi pembayaran kamu</span></span>
            <span class="payment-check"><i class="fa-solid fa-check"></i></span>
        </label>
    </section>

    <button type="button" class="payment-voucher-row" onclick="document.getElementById('payment-voucher-input').hidden = false; document.getElementById('payment-voucher-input').focus()">
        <span><i class="fa-solid fa-ticket"></i> Tambah Promo atau Voucher</span><i class="fa-solid fa-chevron-right"></i>
    </button>
    <input id="payment-voucher-input" class="payment-voucher-input" type="text" maxlength="40" placeholder="Masukkan kode promo" hidden>

    <div class="payment-page-spacer"></div>
    <footer class="payment-page-bottom">
        <div><span>Total Pembayaran</span><strong id="payment-page-total">Rp 0</strong></div>
        <button type="button" id="pay-qris-button" onclick="submitQrisPayment()">Bayar</button>
    </footer>
</main>
<div id="toast" class="toast"><span id="toast-message"></span></div>
@endsection

@push('styles')
<style>
.customer-payment-page.app-container{width:100%;max-width:760px;min-height:100vh;margin:0 auto;padding:0 16px 24px;background:#fff;color:var(--text-dark)}
.customer-payment-header{height:58px;display:grid;grid-template-columns:44px 1fr 44px;align-items:center;margin:0 -16px 14px;padding:0 16px;background:#fff;box-shadow:0 2px 10px rgba(40,30,20,.08)}
.customer-payment-header a{display:flex;align-items:center;height:44px;color:var(--text-dark);font-size:18px;text-decoration:none}
.customer-payment-header h1{margin:0;text-align:center;color:var(--primary-dark);font-size:16px}
.payment-pickup-type{min-height:34px;display:flex;align-items:center;justify-content:space-between;margin:0 0 20px;padding:0 11px;border:1px solid rgba(118,63,6,.22);border-radius:8px;background:#fff9f2;color:var(--primary-dark);font-size:11px}
.payment-pickup-type strong{font-size:10px}
.payment-pickup-type i{margin-left:4px;color:var(--primary)}
.payment-customer-info{padding:0 10px 20px}
.payment-customer-info h2,.payment-order-source h2{margin:0 0 10px;color:var(--primary-dark);font-size:13px}
.payment-customer-info label{display:block;margin:13px 0 5px;color:var(--primary-dark);font-size:11px}
.payment-customer-info label span{color:#c44834}
.payment-customer-info label small{color:var(--text-muted)}
.payment-page-input{height:42px;display:flex;align-items:center;gap:10px;padding:0 11px;border:1px solid #e9e3dc;border-radius:9px;background:#fff;color:#77808b}
.payment-page-input i{width:16px;text-align:center;font-size:13px}
.payment-page-input input{width:100%;height:100%;border:0;outline:0;background:transparent;color:var(--text-dark);font:inherit;font-size:12px}
.payment-order-source{margin:0 -16px;padding:16px 26px;border-top:7px solid #f5f4f2;border-bottom:7px solid #f5f4f2}
.payment-order-source h2{margin-bottom:4px}
.payment-order-source p{margin:0 0 12px;color:var(--text-muted);font-size:11px}
.payment-order-source>div{min-height:45px;display:flex;align-items:center;gap:10px;padding:0 12px;border:1px solid #eee8e0;border-radius:10px;box-shadow:0 2px 8px rgba(30,20,10,.05);font-size:11px}
.payment-order-source>div i{color:var(--primary)}
.payment-method-page{padding:18px 10px 20px}
.payment-method-page h2{margin:0 0 14px;color:var(--primary-dark);font-size:14px;font-weight:500}
.payment-qris-only{margin:0}
.payment-voucher-row{width:100%;min-height:46px;display:flex;align-items:center;justify-content:space-between;margin-top:12px;padding:0 12px;border:1px solid #e9e3dc;border-radius:10px;background:#fff;color:var(--primary);font:inherit;font-size:12px;cursor:pointer}
.payment-voucher-row span{display:flex;align-items:center;gap:9px}
.payment-voucher-row>i{color:var(--text-muted);font-size:11px}
.payment-voucher-input{width:100%;height:40px;margin-top:8px;padding:0 11px;border:1px solid #e9e3dc;border-radius:8px;font:inherit;font-size:12px}
.payment-page-spacer{height:90px}
.payment-page-bottom{position:fixed;z-index:100;right:0;bottom:0;left:0;max-width:760px;min-height:74px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 auto;padding:10px 14px calc(10px + env(safe-area-inset-bottom));border:1px solid #eee8e0;border-radius:14px 14px 0 0;background:#fff;box-shadow:0 -5px 22px rgba(40,30,20,.1)}
.payment-page-bottom>div{display:grid;gap:3px;color:var(--text-muted);font-size:10px}
.payment-page-bottom>div strong{color:var(--primary-dark);font-size:15px}
.payment-page-bottom button{min-width:120px;min-height:42px;border:0;border-radius:9px;background:var(--primary);color:#fff;font-size:12px;font-weight:800;cursor:pointer}
.payment-page-bottom button:disabled{opacity:.55;cursor:not-allowed}
@media(min-width:700px){.customer-payment-page.app-container{padding:0 28px 30px}.customer-payment-header{margin-right:-28px;margin-left:-28px;padding-right:28px;padding-left:28px}.payment-order-source{margin-right:-28px;margin-left:-28px;padding-right:38px;padding-left:38px}.payment-customer-info,.payment-method-page{max-width:620px;margin-right:auto;margin-left:auto}.payment-page-bottom{border-radius:14px 14px 0 0}}
@media(max-width:380px){.customer-payment-page.app-container{padding-right:12px;padding-left:12px}.customer-payment-header{margin-right:-12px;margin-left:-12px;padding-right:12px;padding-left:12px}.payment-order-source{margin-right:-12px;margin-left:-12px;padding-right:22px;padding-left:22px}.payment-page-bottom button{min-width:96px}}
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
document.addEventListener('DOMContentLoaded', () => {
    const minutes = sessionStorage.getItem('quattro_pickup_time') || 'now';
    document.getElementById('payment-pickup-time').textContent = minutes === 'now' ? 'Diambil Sekarang' : `Diambil dalam ${minutes} menit`;
    document.getElementById('payment-page-total').textContent = rupiah(getCheckoutTotal());
    document.getElementById('pay-qris-button').disabled = !appState.cart.length;
});

async function submitQrisPayment() {
    const name = document.getElementById('customer-name').value.trim();
    const phone = document.getElementById('customer-phone').value.trim();
    const email = document.getElementById('customer-email').value.trim();
    if (!name) {
        document.getElementById('customer-name').focus();
        showToast('Nama lengkap harus diisi');
        return;
    }
    if (!document.getElementById('payment-qris').checked) {
        showToast('Pilih pembayaran QRIS');
        return;
    }
    await checkout({ payment_method: 'QRIS', customer_name: name, customer_phone: phone, customer_email: email });
}
</script>
@endpush
