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
        <p>Pilih QRIS atau transfer bank untuk menyelesaikan pesanan.</p>
    </section>

    <section class="payment-customer-info">
        <div class="payment-section-title"><span>01</span><div><h2>Data pemesan</h2><p>Untuk menghubungi dan mengirim struk</p></div></div>
        @if(auth()->user()?->role === 'customer')
            @php
                $accountPhone = preg_replace('/\D+/', '', (string) auth()->user()->phone);
                if (str_starts_with($accountPhone, '62')) $accountPhone = '0' . substr($accountPhone, 2);
                elseif (str_starts_with($accountPhone, '8')) $accountPhone = '0' . $accountPhone;
            @endphp
            @php $needsReceiptEmail = str_ends_with(strtolower((string) auth()->user()->email), '@whatsapp.quattro.local'); @endphp
            <div class="payment-account-note"><i class="fa-solid fa-circle-check"></i><span>{{ $needsReceiptEmail ? 'Akun WhatsApp siap digunakan. Isi email aktif untuk menerima struk.' : 'Data dari profil akunmu akan digunakan untuk pesanan ini.' }}</span>@unless($needsReceiptEmail)<a href="{{ route('customer.profile.edit') }}">Edit profil</a>@endunless</div>
            <label for="customer-name">Nama lengkap</label>
            <div class="payment-page-input payment-readonly-field"><i class="fa-regular fa-user"></i><input id="customer-name" type="text" value="{{ auth()->user()->name }}" readonly aria-readonly="true"></div>
            <label for="customer-phone">Nomor ponsel @if(!$accountPhone)<span>*</span>@endif</label>
            @if($accountPhone)
                <div class="payment-page-input payment-readonly-field"><i class="fa-solid fa-phone"></i><input id="customer-phone" type="tel" inputmode="numeric" value="{{ $accountPhone }}" readonly aria-readonly="true"></div>
            @else
                <div class="payment-page-input"><i class="fa-solid fa-phone"></i><input id="customer-phone" type="tel" inputmode="numeric" pattern="[0-9]*" autocomplete="tel-national" maxlength="13" placeholder="08xxxxxxxxxx" required aria-describedby="customer-phone-help"></div>
                <small id="customer-phone-help" class="payment-field-help">Nomor ini dipakai outlet untuk menghubungi Anda soal pesanan.</small>
            @endif
            @if($needsReceiptEmail)
                <label for="customer-email">Email untuk struk digital<span>*</span></label>
                <div class="payment-page-input"><i class="fa-regular fa-envelope"></i><input id="customer-email" type="email" inputmode="email" autocomplete="email" maxlength="255" placeholder="nama@email.com" required aria-describedby="customer-email-help"></div>
                <small id="customer-email-help" class="payment-field-help">Email ini disimpan ke profil akunmu untuk struk berikutnya.</small>
            @else
            <label for="customer-email">Email struk digital</label>
            <div class="payment-page-input payment-readonly-field"><i class="fa-regular fa-envelope"></i><input id="customer-email" type="email" value="{{ auth()->user()->email }}" readonly aria-readonly="true"></div>
            @endif
            @if(!$accountPhone || (!$needsReceiptEmail && !auth()->user()->email))
                <small class="payment-field-help payment-profile-incomplete">Masukkan nomor HP aktif untuk melanjutkan pesanan.</small>
            @endif
        @else
            <label for="customer-name">Nama Lengkap<span>*</span></label>
            <div class="payment-page-input"><i class="fa-regular fa-user"></i><input id="customer-name" type="text" maxlength="255" placeholder="Nama Lengkap" required></div>
            <label for="customer-phone">Nomor Ponsel<span>*</span></label>
            <div class="payment-page-input"><i class="fa-solid fa-phone"></i><input id="customer-phone" type="tel" inputmode="numeric" pattern="[0-9]*" autocomplete="tel-national" maxlength="13" placeholder="08xxxxxxxxxx" required aria-describedby="customer-phone-help"></div>
            <small id="customer-phone-help" class="payment-field-help">Dipakai kru untuk menghubungi Anda jika pesanan siap.</small>
            <label for="customer-email">Email untuk struk digital<span>*</span></label>
            <div class="payment-page-input"><i class="fa-regular fa-envelope"></i><input id="customer-email" type="email" inputmode="email" autocomplete="email" maxlength="255" placeholder="nama@email.com" required aria-describedby="customer-email-help"></div>
            <small id="customer-email-help" class="payment-field-help">Struk dikirim setelah pembayaran berhasil.</small>
        @endif
    </section>

    <section class="payment-method-page customer-table-choice">
        <div class="payment-section-title"><span>02</span><div><h2>Pilihan tempat</h2><p>Pilih nomor meja atau bawa pulang</p></div></div>
        <label for="table-number" class="table-number-label">Pilih nomor meja</label>
        <div class="payment-page-input table-select-wrap"><i class="fa-solid fa-chair"></i><select id="table-number" aria-label="Pilih nomor meja"><option value="Take Away">Take Away</option></select></div>
        <small id="table-availability-note" class="payment-field-help">Memeriksa ketersediaan meja...</small>
    </section>
    <section class="payment-method-page">
        <div class="payment-section-title"><span>03</span><div><h2>Metode pembayaran</h2><p>Pilih cara pembayaran pesananmu</p></div></div>
        <label class="payment-method payment-qris-only" for="payment-qris">
            <input type="radio" name="payment_method" id="payment-qris" value="QRIS" checked>
            <span class="payment-method-icon qris-icon"><i class="fa-solid fa-qrcode"></i></span>
            <span class="payment-method-info"><strong>QRIS</strong><span>Scan QR dari aplikasi pembayaran kamu</span></span>
            <span class="payment-check"><i class="fa-solid fa-check"></i></span>
        </label>
        <label class="payment-method payment-bank-method {{ !$bankConfigured ? 'is-disabled' : '' }}" for="payment-bank-transfer">
            <input type="radio" name="payment_method" id="payment-bank-transfer" value="Bank Transfer" {{ !$bankConfigured ? 'disabled' : '' }}>
            <span class="payment-method-icon qris-icon"><i class="fa-solid fa-building-columns"></i></span>
            <span class="payment-method-info"><strong>Transfer Bank</strong><span>{{ $bankConfigured ? 'Bayar melalui rekening bank Quattro Coffee' : 'Rekening belum diatur oleh Owner' }}</span></span>
            <span class="payment-check"><i class="fa-solid fa-check"></i></span>
        </label>
        @if($bankConfigured)
            <div class="bank-account-options" id="bank-account-options" hidden>
                <div class="bank-account-options-title">Pilih rekening tujuan</div>
                @foreach($bankAccounts as $index => $account)
                    <label class="bank-account-option" for="bank-account-{{ $index }}">
                        <input type="radio" name="bank_account_id" id="bank-account-{{ $index }}" value="{{ $account['id'] }}" {{ $index === 0 ? 'checked' : '' }}>
                        <span class="bank-account-option-mark"><i class="fa-solid fa-building-columns"></i></span>
                        <span class="bank-account-option-info"><strong>{{ $account['bank_name'] }}</strong><span class="bank-account-option-number">{{ $account['bank_account_number'] }}</span><small>Pemilik: {{ $account['bank_account_holder'] }}</small></span>
                        <span class="bank-account-option-check"><i class="fa-solid fa-check"></i></span>
                    </label>
                @endforeach
            </div>
        @endif
    </section>

    <div class="payment-secure-note"><i class="fa-solid fa-lock"></i><span><strong>Pembayaran aman</strong><small>Pastikan nama dan total pesanan sudah benar.</small></span></div>

    <div class="payment-page-spacer"></div>
    <footer class="payment-page-bottom">
        <div><span>Total Pembayaran</span><strong id="payment-page-total">Rp 0</strong></div>
        <button type="button" id="pay-payment-button" onclick="submitSelectedPayment()">Bayar</button>
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
.receipt-brand{display:flex;align-items:center;gap:11px}.receipt-logo{display:grid;place-items:center;width:38px;height:38px;border-radius:13px;background:#f5eee4;color:#8b4f22}.receipt-brand div{display:grid;gap:3px}.receipt-brand small{color:#b85c43;font-size:8px;font-weight:800;letter-spacing:1.5px}.receipt-brand strong{font-family:Georgia,serif;font-size:15px}.receipt-divider{display:flex;align-items:center;gap:8px;margin:15px -23px 13px;color:#a99782;font-size:6px}.receipt-divider span{height:1px;flex:1;border-top:1px dashed #e8dfd4}.receipt-pickup{display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:10px}.receipt-pickup>span{color:#674a2d}.receipt-pickup>span i{margin-right:5px;color:#b85c43}.receipt-pickup strong{display:inline-flex;align-items:center;gap:4px;padding:6px 9px;border-radius:999px;background:#eaf4e9;color:#477146;font-size:9px;font-weight:700;white-space:nowrap}.receipt-pickup strong i{margin:0}.payment-receipt-card>p{margin:10px 0 0;color:#81766a;font-size:9px;line-height:1.4}
.payment-customer-info{padding:16px;margin-bottom:16px;border:1px solid #eee4d8;border-radius:17px;background:#fffdf9;box-shadow:0 6px 18px #30231808}.payment-section-title{display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:11px;border-bottom:1px solid #eee6dc}.payment-section-title>span{display:grid;place-items:center;width:30px;height:30px;border:1px solid #e0d5c5;border-radius:10px;color:#713b05;font-family:Georgia,serif;font-size:11px;font-weight:700}.payment-section-title h2{margin:0;color:#211a14;font-family:Georgia,serif;font-size:15px}.payment-section-title p{margin:3px 0 0;color:#81766a;font-size:9px}
.payment-customer-info label{display:block;margin:12px 0 5px;color:#674a2d;font-size:10px;font-weight:700}
.payment-customer-info label span{color:#c44834}
.payment-customer-info label small{color:var(--text-muted)}
.payment-account-note{display:flex;align-items:center;gap:8px;margin:-2px 0 13px;padding:10px 11px;border:1px solid #e9dfd2;border-radius:11px;background:#faf6f0;color:#755735;font-size:9px;line-height:1.45}.payment-account-note>i{color:#6d8a55}.payment-account-note span{flex:1}.payment-account-note a{color:#78400d;font-weight:700;text-decoration:none;white-space:nowrap}.payment-readonly-field{background:#f8f5f0;color:#846d55}.payment-readonly-field input{color:#5f4a36}.payment-profile-incomplete{margin-top:10px;color:#9a5039}
.payment-field-help{display:block;margin:5px 2px 0;color:#81766a;font-size:9px}
.payment-page-input{height:44px;display:flex;align-items:center;gap:10px;padding:0 12px;border:1px solid #e8e0d6;border-radius:12px;background:#fff;color:#8d7b68}
.table-select-wrap{position:relative;height:48px;padding:0 40px 0 42px;border-color:#e6d9c9;border-radius:13px;background:#fff;transition:border-color .18s ease,box-shadow .18s ease}.table-select-wrap:focus-within{border-color:#a66b32;box-shadow:0 0 0 3px rgba(138,81,0,.1)}.table-select-wrap>i{position:absolute;left:14px;width:18px;color:#8a5100}.table-select-wrap::after{position:absolute;top:50%;right:16px;width:8px;height:8px;border-right:1.7px solid #80684e;border-bottom:1.7px solid #80684e;content:"";pointer-events:none;transform:translateY(-70%) rotate(45deg)}.table-select-wrap select{width:100%;height:100%;border:0;outline:0;appearance:none;-webkit-appearance:none;background:transparent;color:#34271c;font:inherit;font-size:12px;font-weight:600;cursor:pointer}
.table-number-label{display:block!important;align-items:center;margin:0 0 8px!important;padding:0;background:transparent;color:#634323!important;font-size:11px!important;font-weight:700!important;letter-spacing:.15px}
.payment-page-input i{width:16px;text-align:center;font-size:13px}
.payment-page-input input{width:100%;height:100%;border:0;outline:0;background:transparent;color:#211a14;font:inherit;font-size:11px}
.payment-method-page{margin:0 -2px;padding:17px 14px 16px;border:1px solid #eee4d8;border-radius:17px;background:#fffdf9}.payment-method-page .payment-section-title{margin-bottom:13px}.payment-method{position:relative;display:flex;align-items:center;gap:12px;margin:0 0 9px;padding:12px 14px;border:1px solid #e4ddd2;border-radius:14px;background:#fff;cursor:pointer}.payment-method:last-of-type{margin-bottom:0}.payment-method input{position:absolute;opacity:0}.payment-method-icon{display:grid;place-items:center;flex:0 0 40px;height:40px;border-radius:13px;background:#f0eafa;color:#7250a3}.payment-method-info{display:grid;gap:4px;flex:1}.payment-method-info strong{font-size:12px;color:#211a14}.payment-method-info>span{font-size:10px;color:#81766a}.payment-check{display:grid;place-items:center;width:22px;height:22px;border:1px solid #d8caba;border-radius:50%;color:transparent}.payment-method:has(input:checked){border-color:#9a6740;box-shadow:0 0 0 2px #9a67401a}.payment-method:has(input:checked) .payment-check{background:#75452d;border-color:#75452d;color:#fff}.payment-method.is-disabled{opacity:.55;cursor:not-allowed}.bank-transfer-preview{display:grid;gap:5px;margin-top:10px;padding:13px;border-radius:12px;background:#f8f2e9;color:#71502e}.bank-transfer-preview[hidden]{display:none}.bank-transfer-preview span,.bank-transfer-preview small{font-size:10px}.bank-transfer-preview strong{font-size:13px;color:#302216}
.bank-account-options{display:grid;gap:8px;margin-top:11px;padding:11px;border:1px solid #eee4d8;border-radius:14px;background:#faf7f2}.bank-account-options[hidden]{display:none}.bank-account-options-title{margin:0 2px 2px;color:#80684e;font-size:10px;font-weight:700}.bank-account-option{display:flex;align-items:center;gap:10px;margin:0;padding:10px;border:1px solid #e8e0d6;border-radius:12px;background:#fff;cursor:pointer}.bank-account-option input{position:absolute;opacity:0}.bank-account-option-mark{display:grid;place-items:center;flex:0 0 34px;width:34px;height:34px;border-radius:11px;background:#f4ede3;color:#79420a}.bank-account-option-info{display:grid;flex:1;min-width:0;gap:3px}.bank-account-option-info strong{color:#34271c;font-size:11px}.bank-account-option-info .bank-account-option-number{color:#59432e;font-size:10px;font-weight:700;letter-spacing:.02em}.bank-account-option-info small{overflow:hidden;color:#9a8773;font-size:9px;text-overflow:ellipsis;white-space:nowrap}.bank-account-option-check{display:grid;place-items:center;width:19px;height:19px;border:1px solid #d8caba;border-radius:50%;color:transparent;font-size:9px}.bank-account-option:has(input:checked){border-color:#a66b32;background:#fffaf4;box-shadow:0 0 0 2px rgba(138,81,0,.08)}.bank-account-option:has(input:checked) .bank-account-option-check{border-color:#75452d;background:#75452d;color:#fff}
.bank-account-options{gap:6px;margin-top:10px;padding:0;border:0;border-radius:0;background:transparent}.bank-account-options-title{margin:0 2px 4px;color:#76583c;font-size:10px;font-weight:700}.bank-account-option{min-height:62px;gap:11px;padding:10px 12px;border-color:#eee5da;border-radius:13px;background:#fff}.bank-account-option-mark{flex-basis:36px;width:36px;height:36px;border-radius:12px}.bank-account-option-info{gap:4px}.bank-account-option-info strong{font-size:11px;line-height:1.2}.bank-account-option-info .bank-account-option-number{font-size:11px;letter-spacing:.04em;line-height:1.2}.bank-account-option-info small{font-size:9px;line-height:1.25}.bank-account-option:has(input:checked){border-color:#c69b70;background:#fffaf4;box-shadow:0 0 0 1px rgba(138,81,0,.08)}
.customer-table-choice{margin-bottom:12px}.payment-method-page+.payment-method-page{margin-top:12px}
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
<script src="{{ asset('js/customer.js') }}?v=20261128-session-cart-reset"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const contactStorageKey = 'quattro_checkout_contact_v1';
    const fields = ['customer-name', 'customer-phone', 'customer-email'];
    const loggedIn = @json(auth()->user()?->role === 'customer');
    if (!loggedIn) {
        try {
            const savedContact = JSON.parse(sessionStorage.getItem(contactStorageKey) || '{}');
            fields.forEach(id => {
                if (savedContact[id]) document.getElementById(id).value = savedContact[id];
            });
        } catch (error) {
            sessionStorage.removeItem(contactStorageKey);
        }
    }
    const phoneInput = document.getElementById('customer-phone');
    if (phoneInput) {
        let digitsOnly = phoneInput.value.replace(/\D/g, '').slice(0, 13);
        if (digitsOnly.startsWith('62')) digitsOnly = '0' + digitsOnly.slice(2);
        phoneInput.value = digitsOnly;
    }
    const saveContact = () => {
        if (loggedIn) return;
        const values = {};
        fields.forEach(id => values[id] = document.getElementById(id)?.value || '');
        sessionStorage.setItem(contactStorageKey, JSON.stringify(values));
    };
    phoneInput?.addEventListener('input', () => {
        const digitsOnly = phoneInput.value.replace(/\D/g, '').slice(0, 13);
        if (phoneInput.value !== digitsOnly) phoneInput.value = digitsOnly;
        saveContact();
    });
    fields.forEach(id => document.getElementById(id)?.addEventListener('input', saveContact));
    document.getElementById('payment-page-total').textContent = rupiah(getCheckoutTotal());
    document.getElementById('pay-payment-button').disabled = !appState.cart.length;
    document.querySelectorAll('input[name="payment_method"]').forEach(input => input.addEventListener('change', updatePaymentSelection));
    updatePaymentSelection();
});

async function loadCustomerTableAvailability() {
    const select = document.getElementById('table-number');
    const note = document.getElementById('table-availability-note');
    if (!select) return;
    try {
        const response = await fetch(@json(route('customer.tables.availability', [], false)), { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Meja belum dapat diperiksa.');
        const data = await response.json();
        select.innerHTML = '<option value="Take Away">Take Away</option>';
        let availableCount = 0;
        (data.tables || []).forEach(table => {
            const option = document.createElement('option');
            option.value = table.number;
            option.textContent = table.available ? `${table.number} · Tersedia` : `${table.number} · Sedang dipakai`;
            option.disabled = !table.available;
            if (table.available) availableCount++;
            select.appendChild(option);
        });
        if (note) note.textContent = `${availableCount} meja tersedia. Meja yang sedang dipakai tidak bisa dipilih.`;
    } catch (error) {
        if (note) note.textContent = 'Daftar meja belum dapat dimuat. Silakan coba muat ulang halaman.';
    }
    select.addEventListener('change', () => {
        const label = document.querySelector('.receipt-pickup strong');
        if (label) label.innerHTML = '<i class="fa-solid fa-check"></i> ' + (select.value === 'Take Away' ? 'Take Away' : 'Meja ' + select.value);
    });
}

document.addEventListener('DOMContentLoaded', loadCustomerTableAvailability);
function updatePaymentSelection() {
    const transfer = document.getElementById('payment-bank-transfer')?.checked;
    const accounts = document.getElementById('bank-account-options');
    const button = document.getElementById('pay-payment-button');
    if (accounts) accounts.hidden = !transfer;
    if (button) button.textContent = transfer ? 'Buat Pesanan' : 'Bayar';
}

async function submitSelectedPayment() {
    const method = document.querySelector('input[name="payment_method"]:checked')?.value;
    const name = document.getElementById('customer-name').value.trim();
    const phone = document.getElementById('customer-phone').value.trim();
    const email = document.getElementById('customer-email').value.trim();
    if (!name) {
        document.getElementById('customer-name').focus();
        showToast('Nama lengkap harus diisi');
        return;
    }
    const normalizedPhone = phone;
    if (!/^08\d{8,11}$/.test(normalizedPhone)) {
        document.getElementById('customer-phone').focus();
        showToast('Nomor ponsel harus 10–13 angka dan diawali 08');
        return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('customer-email').focus();
        showToast('Masukkan email yang valid untuk menerima struk');
        return;
    }
    if (!['QRIS', 'Bank Transfer'].includes(method)) {
        showToast('Pilih metode pembayaran');
        return;
    }
    const bankAccountId = document.querySelector('input[name="bank_account_id"]:checked')?.value;
    if (method === 'Bank Transfer' && !bankAccountId) {
        showToast('Pilih rekening tujuan transfer');
        return;
    }
    await checkout({ payment_method: method, bank_account_id: bankAccountId, customer_name: name, customer_phone: normalizedPhone, customer_email: email });
}
</script>
@endpush


