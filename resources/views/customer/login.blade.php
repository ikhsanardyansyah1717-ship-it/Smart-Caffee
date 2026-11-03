@extends('layouts.customer')

@section('title', 'Masuk - Quattro Coffee')
@section('content')
<main class="customer-wa-login">
    <header class="customer-wa-login__header">
        <a href="{{ route('customer.home') }}" aria-label="Tutup halaman masuk"><i class="fa-solid fa-xmark"></i></a>
        <strong>Masuk Pelanggan</strong>
        <span aria-hidden="true"></span>
    </header>

    <section class="customer-wa-login__hero" aria-label="Masuk dengan WhatsApp">
        <div class="customer-wa-login__brand"><span><i class="fa-solid fa-mug-hot"></i></span><strong>QUATTRO <small>COFFEE</small></strong></div>
        <div class="customer-wa-login__art" aria-hidden="true">
            <div class="customer-wa-login__spark customer-wa-login__spark--one">✦</div>
            <div class="customer-wa-login__spark customer-wa-login__spark--two">✦</div>
            <div class="customer-wa-login__chat"><i class="fa-solid fa-heart"></i><span>Secangkir favorit,<br>semudah satu langkah</span></div>
            <div class="customer-wa-login__cup"><i class="fa-solid fa-mug-hot"></i><span></span></div>
            <div class="customer-wa-login__badge"><i class="fa-solid fa-check"></i></div>
        </div>
        <h1>Kopi favoritmu, selalu dekat.</h1>
        <p>Masuk ke akun Quattro untuk pesan lebih cepat dan cek pesananmu.</p>
    </section>

    <button class="customer-wa-login__button" id="customer-wa-login-start" type="button">
        <i class="fa-brands fa-whatsapp"></i> Log In via WhatsApp
    </button>
    <p class="customer-wa-login__hint">WhatsApp akan terbuka dengan pesan verifikasi. Kirim pesannya, lalu kembali ke sini.</p>
    <div
    class="customer-wa-login__state"
    id="customer-wa-login-state"
    role="status"
    aria-live="polite"
    aria-atomic="true"
    hidden
>
    <span class="customer-wa-login__state-icon" id="customer-wa-login-state-icon">
        <i class="fa-solid fa-circle-info"></i>
    </span>

    <span class="customer-wa-login__state-content">
        <strong id="customer-wa-login-state-title">Informasi Login</strong>
        <span id="customer-wa-login-state-message"></span>
    </span>

    <span class="customer-wa-login__state-loader" id="customer-wa-login-state-loader" hidden>
        <i class="fa-solid fa-spinner"></i>
    </span>
</div>
    <a class="customer-wa-login__button customer-wa-login__open-wa" id="customer-wa-login-open-wa" href="#" target="_blank" rel="noopener" hidden>Buka WhatsApp</a>
    <a class="customer-wa-login__guest" href="{{ route('customer.home') }}">
    <i class="fa-solid fa-arrow-right"></i>
    Lanjutkan sebagai tamu
</a>
    <p class="customer-wa-login__terms">Dengan melanjutkan, kamu menyetujui <a href="{{ route('customer.privacy') }}">Kebijakan Privasi</a> Quattro Coffee.</p>
</main>

@push('styles')
<style>
/* =====================================================
   QUATTRO COFFEE - WHATSAPP LOGIN
   NOTIFICATION DESIGN
===================================================== */

.customer-wa-login__state {
    box-sizing: border-box;
    width: 100%;
    max-width: 440px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 18px auto 0;
    padding: 15px;
    border: 1px solid #e9dfd2;
    border-radius: 16px;
    background: #fffdf9;
    color: #302318;
    text-align: left;
    box-shadow: 0 6px 20px rgba(48, 35, 24, .06);
    animation: waNotificationIn .25s ease both;
}

.customer-wa-login__state[hidden] {
    display: none !important;
}

/* ICON */

.customer-wa-login__state-icon {
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    background: #f3eadc;
    color: #815020;
    font-size: 15px;
}

/* CONTENT */

.customer-wa-login__state-content {
    flex: 1;
    min-width: 0;
    display: grid;
    gap: 5px;
    padding-top: 2px;
}

.customer-wa-login__state-content strong {
    color: #302318;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.4;
}

.customer-wa-login__state-content > span {
    color: #827568;
    font-size: 11px;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

/* LOADING */

.customer-wa-login__state-loader {
    flex: 0 0 auto;
    padding-top: 4px;
    color: #8b5727;
    font-size: 13px;
}

.customer-wa-login__state-loader i {
    animation: waSpin 1s linear infinite;
}

/* LOADING STATUS */

.customer-wa-login__state.is-loading {
    border-color: #e8dccb;
    background: #fffdf9;
}

.customer-wa-login__state.is-loading
.customer-wa-login__state-icon {
    background: #f4eadb;
    color: #8b5727;
}

/* SUCCESS STATUS */

.customer-wa-login__state.is-success {
    border-color: #d7e8d5;
    background: #f8fcf6;
}

.customer-wa-login__state.is-success
.customer-wa-login__state-icon {
    background: #e6f2e2;
    color: #4d814c;
}

/* ERROR STATUS */

.customer-wa-login__state.is-error {
    border-color: #f0d8d2;
    background: #fff9f7;
}

.customer-wa-login__state.is-error
.customer-wa-login__state-icon {
    background: #fbe9e5;
    color: #c05240;
}

/* INFO STATUS */

.customer-wa-login__state.is-info {
    border-color: #e8dfd1;
    background: #fffdf9;
}

.customer-wa-login__state.is-info
.customer-wa-login__state-icon {
    background: #f3eadc;
    color: #815020;
}

/* ANIMATION */

@keyframes waNotificationIn {
    from {
        opacity: 0;
        transform: translateY(7px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes waSpin {
    to {
        transform: rotate(360deg);
    }
}

/* MOBILE */

@media (max-width: 480px) {

    .customer-wa-login__state {
        gap: 10px;
        margin-top: 16px;
        padding: 13px;
        border-radius: 14px;
    }

    .customer-wa-login__state-icon {
        flex-basis: 35px;
        width: 35px;
        height: 35px;
        border-radius: 11px;
    }

    .customer-wa-login__state-content strong {
        font-size: 11px;
    }

    .customer-wa-login__state-content > span {
        font-size: 10px;
        line-height: 1.6;
    }
}

@media (prefers-reduced-motion: reduce) {
    .customer-wa-login__state,
    .customer-wa-login__state-loader i {
        animation: none;
    }
}

/* TOMBOL LANJUT SEBAGAI TAMU */
.customer-wa-login__guest {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    max-width: 440px;
    min-height: 48px;
    box-sizing: border-box;
    margin: 16px auto 0;
    padding: 12px 18px;

    background: #fff;
    border: 1px solid #e6ddd1;
    border-radius: 14px;

    color: #6f5138;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;

    transition: all .2s ease;
    cursor: pointer;
}

.customer-wa-login__guest:hover {
    background: #f8f3ec;
    border-color: #b99570;
    color: #4b321f;
    transform: translateY(-1px);
}

.customer-wa-login__guest:active {
    transform: scale(.98);
}

.customer-wa-login__guest i {
    font-size: 14px;
}

/* TOMBOL LANJUT SEBAGAI TAMU */
.customer-wa-login__guest {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    width: auto;
    min-height: 38px;
    margin: 16px auto 0;
    padding: 9px 18px;

    background: #fff;
    border: 1px solid #e6ddd1;
    border-radius: 11px;

    color: #6f5138;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;

    transition: .2s ease;
}

.customer-wa-login__guest:hover {
    background: #f8f3ec;
    border-color: #b99570;
}

.customer-wa-login__guest i {
    font-size: 11px;
}

/* KEBIJAKAN PRIVASI PALING BAWAH */
.customer-wa-login__terms {
    display: block;
    width: 100%;
    max-width: 340px;
    margin: 38px auto 0;
    padding: 0 12px 24px;
    box-sizing: border-box;

    text-align: center;
    color: #918477;
    font-size: 10px;
    line-height: 1.8;
}

.customer-wa-login__terms a {
    color: #8b5727;
    font-weight: 700;
    text-decoration: none;
}

/* HALAMAN LOGIN MEMENUHI TINGGI LAYAR */
.customer-wa-login {
    min-height: 100vh;
    min-height: 100svh;
    box-sizing: border-box;

    display: flex;
    flex-direction: column;
}

/* TOMBOL LANJUT SEBAGAI TAMU */
.customer-wa-login__guest {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    width: fit-content;
    min-height: 34px;
    margin: 16px auto 0;
    padding: 7px 14px;

    font-size: 10px;
    border-radius: 9px;
}

/* KEBIJAKAN PRIVASI SELALU DI PALING BAWAH */
.customer-wa-login__terms {
    width: 100%;
    max-width: 340px;
    margin: auto auto 0;
    padding: 20px 12px 24px;
    box-sizing: border-box;

    text-align: center;
    font-size: 10px;
    line-height: 1.7;
}

</style>
@endpush

@push('scripts')
<script>
(() => {
    const startButton = document.getElementById('customer-wa-login-start');
   const status = document.getElementById('customer-wa-login-state');
const statusIcon = document.getElementById('customer-wa-login-state-icon');
const statusTitle = document.getElementById('customer-wa-login-state-title');
const statusMessage = document.getElementById('customer-wa-login-state-message');
const statusLoader = document.getElementById('customer-wa-login-state-loader');
    const openWhatsApp = document.getElementById('customer-wa-login-open-wa');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    let polling = false;

   // =====================================================
// LOGIN NOTIFICATION
// =====================================================

const showStatus = (
    message,
    type = 'info',
    title = 'Informasi Login'
) => {

    const icons = {
        loading: 'fa-solid fa-clock',
        success: 'fa-solid fa-circle-check',
        error: 'fa-solid fa-circle-exclamation',
        info: 'fa-solid fa-circle-info'
    };

    status.className = `customer-wa-login__state is-${type}`;

    statusIcon.innerHTML = `<i class="${icons[type] || icons.info}"></i>`;

    statusTitle.textContent = title;

    statusMessage.textContent = message;

    statusLoader.hidden = type !== 'loading';

    status.hidden = false;
};
    async function poll(code) {
        if (polling) return;
        polling = true;

        while (polling) {
            try {
                const response = await fetch(@json(route('customer.login.whatsapp.poll')), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ code })
                });
                const result = await response.json();

                if (response.status === 202 && result.status === 'pending') {
                    await new Promise(resolve => setTimeout(resolve, 1800));
                    continue;
                }

                if (response.ok && result.status === 'authenticated') {
                    showStatus('Berhasil masuk. Mengalihkan…');
                    window.location.assign(result.redirect_url);
                    return;
                }

                if (result.status === 'unlinked') {
                    showStatus('Nomor WhatsApp ini belum terhubung ke akun Quattro Coffee. Tambahkan nomor di profil atau lanjut sebagai tamu.');
                } else {
                    showStatus('Verifikasi tidak ditemukan atau sudah kedaluwarsa. Silakan mulai lagi.');
                }
                polling = false;
                startButton.hidden = false;
                return;
            } catch (error) {
                showStatus('Koneksi terputus. Periksa koneksi internet lalu coba lagi.');
                polling = false;
                startButton.hidden = false;
            }
        }
    }

    async function beginLogin() {
        startButton.disabled = true;
        showStatus('Menyiapkan pesan login…');
        const chatTab = window.open('about:blank', '_blank');

        try {
            const response = await fetch(@json(route('customer.login.whatsapp.start')), {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
            });
            if (!response.ok) throw new Error('start_failed');

            const result = await response.json();
            showStatus('Kirim pesan yang sudah disiapkan di WhatsApp. Kami akan melanjutkan login setelah pesan diterima.');
            if (chatTab) {
                chatTab.location = result.whatsapp_url;
            } else {
                openWhatsApp.href = result.whatsapp_url;
                openWhatsApp.hidden = false;
            }
            poll(result.code);
        } catch (error) {
            if (chatTab) chatTab.close();
            showStatus('Login WhatsApp belum siap. Periksa konfigurasi WhatsApp lalu coba lagi.');
            startButton.disabled = false;
        }
    }

    startButton.addEventListener('click', beginLogin);

    const returnedCode = new URLSearchParams(window.location.search).get('wa_code');
    if (returnedCode) {
        startButton.hidden = true;
        showStatus('Memeriksa pesan WhatsApp kamu…');
        poll(returnedCode);
    }
})();
</script>
@endpush
@endsection
