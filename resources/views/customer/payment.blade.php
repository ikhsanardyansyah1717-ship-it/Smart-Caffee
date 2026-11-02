@extends('layouts.customer')

@section('title', 'Pembayaran - Quattro Coffee')

@section('content')
<main class="customer-payment-page app-container">
    <header class="customer-payment-header">
        <a href="{{ route('customer.orders') }}" aria-label="Kembali ke pesanan"><i class="fa-solid fa-arrow-left"></i></a>
        <div><span class="payment-kicker">LANGKAH TERAKHIR</span><h1>Konfirmasi pesanan</h1></div>
        <span class="secure-mark"><i class="fa-solid fa-shield-halved"></i></span>
    </header>

    <section class="payment-receipt-card">
        <div class="receipt-brand"><span class="receipt-logo"><i class="fa-solid fa-mug-hot"></i></span><div><small>QUATTRO COFFEE</small><strong>Pesanan pilihanmu</strong></div></div>
        <div class="receipt-divider"><span></span><i class="fa-solid fa-circle"></i><span></span></div>
        <div class="receipt-pickup"><span><i class="fa-solid fa-bag-shopping"></i> Ambil di outlet</span><strong><i class="fa-solid fa-check"></i> Diambil sekarang</strong></div>
        <p>Pembayaran QRIS diproses aman. Setelah berhasil, status pesanan diperbarui otomatis.</p>
    </section>

    <section class="payment-customer-info">
        <div class="payment-section-title"><span>01</span><div><h2>Data pemesan</h2><p>Untuk menghubungi dan mengirim struk</p></div></div>
        <label for="customer-name">Nama Lengkap<span>*</span></label>
        <div class="payment-page-input"><i class="fa-regular fa-user"></i><input id="customer-name" type="text" maxlength="255" value="{{ auth()->user()?->name }}" placeholder="Nama Lengkap" required></div>
        <label for="customer-phone">Nomor Ponsel<span>*</span></label>
        <div class="payment-page-input"><i class="fa-solid fa-phone"></i><input id="customer-phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="30" value="{{ auth()->user()?->phone }}" placeholder="08xxxxxxxxxx" required aria-describedby="customer-phone-help"></div>
        <small id="customer-phone-help" class="payment-field-help">Dipakai kru untuk menghubungi Anda jika pesanan siap.</small>
        <label for="customer-email">Email untuk struk digital<span>*</span></label>
        <div class="payment-page-input"><i class="fa-regular fa-envelope"></i><input id="customer-email" type="email" inputmode="email" autocomplete="email" maxlength="255" value="{{ auth()->user()?->email }}" placeholder="nama@email.com" required aria-describedby="customer-email-help"></div>
        <small id="customer-email-help" class="payment-field-help">Struk dikirim setelah pembayaran berhasil.</small>
    </section>

    <section class="payment-method-page">
        <div class="payment-section-title"><span>02</span><div><h2>Metode pembayaran</h2><p>Pilih QRIS untuk menyelesaikan transaksi</p></div></div>
        <label class="payment-method payment-qris-only" for="payment-qris">
            <input type="radio" name="payment_method" id="payment-qris" value="QRIS" checked>
            <span class="payment-method-icon qris-icon"><i class="fa-solid fa-qrcode"></i></span>
            <span class="payment-method-info"><strong>QRIS</strong><span>Scan QR dari aplikasi pembayaran kamu</span></span>
            <span class="payment-check"><i class="fa-solid fa-check"></i></span>
        </label>
    </section>

    <div class="payment-secure-note"><i class="fa-solid fa-lock"></i><span><strong>Pembayaran aman</strong><small>Pastikan nama dan total pesanan sudah benar.</small></span></div>

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
.customer-payment-page.app-container{box-sizing:border-box;width:100%;max-width:760px;min-height:100vh;margin:0 auto;padding:0 18px 28px;background:#fbf8f2;color:#211a14}
.customer-payment-header{min-height:80px;display:grid;grid-template-columns:40px 1fr 40px;align-items:center;gap:12px;margin:0 -18px 18px;padding:12px 20px;background:#fbf8f2;border-bottom:1px solid #eee6dc}
.customer-payment-header a{display:grid;place-items:center;width:38px;height:38px;border-radius:13px;background:#fff;color:#713b05;font-size:15px;text-decoration:none}
.customer-payment-header h1{margin:4px 0 0;color:#211a14;font-family:Georgia,serif;font-size:20px}
.payment-kicker{color:#87500f;font-size:8px;font-weight:800;letter-spacing:1.4px}.secure-mark{display:grid;place-items:center;width:38px;height:38px;border-radius:13px;background:#ece8de;color:#713b05}
.payment-receipt-card{position:relative;overflow:hidden;margin-bottom:22px;padding:17px;border:1px solid #eee4d8;border-radius:18px;background:#fffdf9;color:#211a14;box-shadow:0 6px 18px #30231808}
.receipt-brand{display:flex;align-items:center;gap:11px}.receipt-logo{display:grid;place-items:center;width:38px;height:38px;border-radius:13px;background:#f5eee4;color:#8b4f22}.receipt-brand div{display:grid;gap:3px}.receipt-brand small{color:#b85c43;font-size:8px;font-weight:800;letter-spacing:1.5px}.receipt-brand strong{font-family:Georgia,serif;font-size:15px}.receipt-divider{display:flex;align-items:center;gap:8px;margin:15px -23px 13px;color:#a99782;font-size:6px}.receipt-divider span{height:1px;flex:1;border-top:1px dashed #e8dfd4}.receipt-pickup{display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:10px}.receipt-pickup>span{color:#674a2d}.receipt-pickup>span i{margin-right:5px;color:#b85c43}.receipt-pickup strong{color:#547451;font-size:9px}.receipt-pickup strong i{margin-right:3px}.payment-receipt-card>p{margin:10px 0 0;color:#81766a;font-size:9px;line-height:1.4}
.payment-customer-info{padding:0 2px 20px}.payment-section-title{display:flex;align-items:center;gap:10px;margin-bottom:12px}.payment-section-title>span{display:grid;place-items:center;width:30px;height:30px;border:1px solid #e0d5c5;border-radius:10px;color:#713b05;font-family:Georgia,serif;font-size:11px;font-weight:700}.payment-section-title h2{margin:0;color:#211a14;font-family:Georgia,serif;font-size:15px}.payment-section-title p{margin:3px 0 0;color:#81766a;font-size:9px}
.payment-customer-info label{display:block;margin:12px 0 5px;color:#674a2d;font-size:10px;font-weight:700}
.payment-customer-info label span{color:#c44834}
.payment-customer-info label small{color:var(--text-muted)}
.payment-field-help{display:block;margin:5px 2px 0;color:#81766a;font-size:9px}
.payment-page-input{height:44px;display:flex;align-items:center;gap:10px;padding:0 12px;border:1px solid #e8e0d6;border-radius:12px;background:#fff;color:#8d7b68}
.payment-page-input i{width:16px;text-align:center;font-size:13px}
.payment-page-input input{width:100%;height:100%;border:0;outline:0;background:transparent;color:#211a14;font:inherit;font-size:11px}
.payment-method-page{margin:0 -2px;padding:17px 14px 16px;border:1px solid #eee4d8;border-radius:17px;background:#fffdf9}.payment-method-page .payment-section-title{margin-bottom:13px}.payment-qris-only{margin:0!important;border:1px solid #e4ddd2!important;border-radius:14px!important;background:#fff!important}
.payment-secure-note{display:flex;align-items:center;gap:10px;margin-top:14px;padding:12px 13px;border:1px solid #e4ddd2;border-radius:13px;background:#fff;color:#713b05}.payment-secure-note>i{font-size:14px}.payment-secure-note span{display:grid;gap:3px}.payment-secure-note strong{font-size:10px}.payment-secure-note small{color:#81766a;font-size:9px}
.payment-page-spacer{height:90px}
.payment-page-bottom{position:fixed;z-index:100;right:0;bottom:0;left:0;max-width:760px;min-height:74px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 auto;padding:10px 18px calc(10px + env(safe-area-inset-bottom));border-top:1px solid #e8e0d6;background:#fff;box-shadow:0 -8px 24px #30231815}
.payment-page-bottom>div{display:grid;gap:3px;color:var(--text-muted);font-size:10px}
.payment-page-bottom>div strong{color:#211a14;font-size:15px}
.payment-page-bottom button{min-width:130px;min-height:46px;border:0;border-radius:13px;background:#713b05;color:#fffaf3;font-size:11px;font-weight:800;cursor:pointer}
.payment-page-bottom button:disabled{opacity:.55;cursor:not-allowed}
@media(min-width:700px){.customer-payment-page.app-container{padding:0 28px 34px}.customer-payment-header{margin-right:-28px;margin-left:-28px;padding-right:32px;padding-left:32px}.payment-receipt-card{max-width:620px;margin:4px auto 22px}.payment-customer-info,.payment-method-page,.payment-secure-note{max-width:620px;margin-right:auto;margin-left:auto}.payment-page-bottom{border-radius:18px 18px 0 0}}
@media(max-width:380px){.customer-payment-page.app-container{padding-right:12px;padding-left:12px}.customer-payment-header{margin-right:-12px;margin-left:-12px;padding-right:14px;padding-left:14px}.payment-page-bottom{padding-right:12px;padding-left:12px}.payment-page-bottom button{min-width:110px}}
</style>
@endpush

@push('scripts')
<script>
window.quattroUser = @json(['id' => auth()->id(), 'name' => auth()->user()?->name ?? 'Sahabat Quattro', 'email' => auth()->user()?->email]);
window.quattroFavoriteIds = [];
window.quattroProducts = @json($products);
</script>
<script src="{{ asset('js/customer.js') }}?v=19"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
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
    const normalizedPhone = phone.replace(/[\s().-]/g, '');
    if (!/^(?:0|\+?62)8\d{8,11}$/.test(normalizedPhone)) {
        document.getElementById('customer-phone').focus();
        showToast('Masukkan nomor ponsel Indonesia yang valid, contoh 081234567890');
        return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('customer-email').focus();
        showToast('Masukkan email yang valid untuk menerima struk');
        return;
    }
    if (!document.getElementById('payment-qris').checked) {
        showToast('Pilih pembayaran QRIS');
        return;
    }
    await checkout({ payment_method: 'QRIS', customer_name: name, customer_phone: normalizedPhone, customer_email: email });
}
</script>
@endpush
