@extends('layouts.customer')

@section('title', 'Masuk - Quattro Coffee')
@section('content')
<main class="customer-wa-login">
    <header class="customer-wa-login__header">
        <a href="{{ route('customer.home') }}" aria-label="Tutup halaman masuk"><i class="fa-solid fa-xmark"></i></a>
        <strong>Masuk Pelanggan</strong>
        <span aria-hidden="true"></span>
    </header>

    <section class="customer-wa-login__hero" aria-label="Masuk dengan Google">
        <div class="customer-wa-login__brand"><span><i class="fa-solid fa-mug-hot"></i></span><strong>QUATTRO <small>COFFEE</small></strong></div>
        <div class="customer-wa-login__art" aria-hidden="true">
            <div class="customer-wa-login__spark customer-wa-login__spark--one">✦</div>
            <div class="customer-wa-login__spark customer-wa-login__spark--two">✦</div>
            <div class="customer-wa-login__chat"><i class="fa-solid fa-heart"></i><span>Secangkir favorit,<br>semudah satu langkah</span></div>
            <div class="customer-wa-login__cup"><i class="fa-solid fa-mug-hot"></i><span></span></div>
            <div class="customer-wa-login__badge"><i class="fa-solid fa-check"></i></div>
        </div>
        <h1>Kopi favoritmu, selalu dekat.</h1>
        <p>Masuk dengan akun Google untuk pesan lebih cepat dan cek pesananmu.</p>
    </section>

    <a class="customer-wa-login__button customer-google-login__button" href="{{ route('customer.login.google') }}" aria-label="Lanjutkan login dengan Google">
        <svg class="customer-google-login__icon" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
            <path fill="#4285F4" d="M43.6 24.5c0-1.4-.1-2.8-.4-4.1H24v7.8h11a9.4 9.4 0 0 1-4.1 6.2v5.1h6.7c3.9-3.6 6-8.8 6-15z"/>
            <path fill="#34A853" d="M24 44c5.5 0 10.1-1.8 13.5-4.8l-6.7-5.1c-1.8 1.2-4 2-6.8 2-5.2 0-9.6-3.5-11.2-8.2H5.9v5.2A20 20 0 0 0 24 44z"/>
            <path fill="#FBBC05" d="M12.8 27.9a12 12 0 0 1 0-7.8v-5.2H5.9a20 20 0 0 0 0 18.2l6.9-5.2z"/>
            <path fill="#EA4335" d="M24 11.9c3 0 5.7 1 7.8 3.1l5.9-5.9C34.1 5.8 29.5 4 24 4A20 20 0 0 0 5.9 14.9l6.9 5.2c1.6-4.7 6-8.2 11.2-8.2z"/>
        </svg>
        <span>Lanjutkan dengan Google</span>
    </a>
    <p class="customer-google-login__note"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Kata sandi Google tidak disimpan di Quattro.</p>
    @if (session('google_login_error'))
        <div class="customer-wa-login__state is-error" role="alert">
            <span class="customer-wa-login__state-icon"><i class="fa-solid fa-circle-exclamation"></i></span>
            <span class="customer-wa-login__state-content"><strong>Login belum berhasil</strong><span>{{ session('google_login_error') }}</span></span>
        </div>
    @endif
    <a class="customer-wa-login__guest" href="{{ route('customer.home') }}">
        <i class="fa-solid fa-arrow-right"></i>
        Lihat menu sebagai tamu
    </a>
    <p class="customer-wa-login__terms">Dengan melanjutkan, kamu menyetujui <a href="{{ route('customer.privacy') }}">Kebijakan Privasi</a> Quattro Coffee.</p>
@push('styles')
<style>
/* =====================================================
   QUATTRO COFFEE - CUSTOMER LOGIN
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


.customer-wa-login { max-width: 500px; padding-inline: 24px; background: radial-gradient(ellipse at 50% 15%, #fffdf9 0, #fbf7f0 62%, #f5ede1 100%); }
.customer-wa-login__hero { margin-top: 18px; }
.customer-wa-login__art { height: 210px; border-radius: 28px; }
.customer-wa-login__button { min-height: 54px; border-radius: 14px; background: linear-gradient(135deg, #8b4e0d, #633607); box-shadow: 0 8px 20px rgba(99,54,7,.18); transition: transform .18s ease, box-shadow .18s ease; }
.customer-wa-login__button:hover { transform: translateY(-1px); box-shadow: 0 11px 24px rgba(99,54,7,.22); }
.customer-wa-login__hint { max-width: 380px; color: #766755; line-height: 1.65; }
.customer-wa-login { max-width: 500px; padding-inline: 24px; background: radial-gradient(ellipse at 50% 15%, #fffdf9 0, #fbf7f0 62%, #f5ede1 100%); }
.customer-wa-login__hero { margin-top: 18px; }
.customer-wa-login__art { height: 210px; border-radius: 28px; }
.customer-wa-login__button { min-height: 54px; border-radius: 14px; background: linear-gradient(135deg, #8b4e0d, #633607); box-shadow: 0 8px 20px rgba(99,54,7,.18); transition: transform .18s ease, box-shadow .18s ease; }
.customer-wa-login__button:hover { transform: translateY(-1px); box-shadow: 0 11px 24px rgba(99,54,7,.22); }
.customer-wa-login__hint { max-width: 380px; color: #766755; line-height: 1.65; }
.customer-wa-login__state { max-width: none; }
.customer-google-login__button {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    min-height: 58px;
    margin-top: 26px;
    padding: 0 20px;
    border: 1px solid #e6ddd2;
    background: #fff;
    color: #3a2b1d;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 8px 22px rgba(66, 45, 23, .09);
}
.customer-google-login__button:hover {
    background: #fffdfa;
    border-color: #cdb99e;
    box-shadow: 0 12px 26px rgba(66, 45, 23, .14);
}
.customer-google-login__button:focus-visible {
    outline: 3px solid rgba(66, 133, 244, .35);
    outline-offset: 3px;
}
.customer-google-login__icon { width: 21px; height: 21px; flex: 0 0 21px; }

.customer-google-login__note { margin: 13px auto 0; color: #8b7b68; text-align: center; font-size: 10px; line-height: 1.5; }
.customer-google-login__note i { margin-right: 5px; color: #8b5727; }

@media (max-width: 480px) { .customer-wa-login { padding-inline: 18px; } .customer-wa-login__art { height: 190px; } }

</style>
@endpush

@endsection
