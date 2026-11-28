@extends('layouts.customer')

@section('title', 'Quattro Coffee - Home')

@section('content')

<div class="app-container quattro-home-entry-pending">

    {{-- =====================================================
        TOAST
    ====================================================== --}}
    <div id="toast" class="toast">

        <i class="fa-solid fa-circle-check"></i>

        <span id="toast-message">
            Pemberitahuan
        </span>

    </div>


    {{-- =====================================================
    COMPACT COFFEE APP HEADER
===================================================== --}}

<header class="quattro-header">

    {{-- BRAND --}}
    <div class="quattro-brand">

        <div class="quattro-logo">
            <i class="fa-solid fa-mug-hot"></i>
        </div>

        <div class="quattro-brand-text">
            <strong>QUATTRO</strong>
            <small>COFFEE</small>
        </div>

    </div>


    {{-- RIGHT SIDE --}}
    <div class="quattro-header-actions">

        {{-- PROFILE --}}
    <a href="{{ route('customer.profile') }}" class="quattro-profile" aria-label="Buka profil">
        <i class="fa-solid fa-user"></i>
    </a>

</header>

{{-- =====================================================
    GREETING
===================================================== --}}

<div class="quattro-greeting">

    <h2>
        Halo, {{ auth()->user()?->name ?? 'Sahabat Quattro' }}! 👋
    </h2>

    <p>
        Mau ngopi apa di Quattro Coffee hari ini?
    </p>

</div>



    {{-- =====================================================
        PROMO
    ====================================================== --}}

    <div class="quattro-promo-slider">

        {{-- PROMO 1 --}}
        <div class="quattro-promo-slide">

            <div class="promo-card">

                <div class="promo-text">

                    <span class="promo-badge">
                        Rekomendasi
                    </span>

                    <h3>
                        Cafe Latte
                    </h3>

                    <p>
                        Best Seller pekan ini di Quattro Coffee
                    </p>

                </div>

                <img
                    src="https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop"
                    class="promo-img"
                    alt="Cafe Latte"
                >

            </div>

        </div>


        {{-- PROMO 2 --}}
        <div class="quattro-promo-slide">

            <div class="promo-card">

                <div class="promo-text">

                    <span class="promo-badge">
                        Favorit
                    </span>

                    <h3>
                        Cappuccino
                    </h3>

                    <p>
                        Kopi creamy favorit pelanggan Quattro
                    </p>

                </div>

                <img
                    src="https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop"
                    class="promo-img"
                    alt="Cappuccino"
                >

            </div>

        </div>


        {{-- PROMO 3 --}}
        <div class="quattro-promo-slide">

            <div class="promo-card">

                <div class="promo-text">

                    <span class="promo-badge">
                        Special
                    </span>

                    <h3>
                        Caramel Macchiato
                    </h3>

                    <p>
                        Rasa manis dan creamy untuk hari ini
                    </p>

                </div>

                <img
                    src="https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=500&auto=format&fit=crop"
                    class="promo-img"
                    alt="Caramel Macchiato"
                >

            </div>

        </div>


        {{-- PROMO 4 --}}
        <div class="quattro-promo-slide">

            <div class="promo-card">

                <div class="promo-text">

                    <span class="promo-badge">
                        Quattro
                    </span>

                    <h3>
                        Americano
                    </h3>

                    <p>
                        Simple, bold, dan cocok untuk kapan saja
                    </p>

                </div>

                <img
                    src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop"
                    class="promo-img"
                    alt="Americano"
                >

            </div>

        </div>

    </div>


    {{-- DOT CAROUSEL --}}

    <div class="quattro-promo-dots">

        <span class="active"></span>
        <span></span>
        <span></span>
        <span></span>

    </div>

{{-- =====================================================
    SEARCH
    DI BAWAH PROMO
===================================================== --}}

<div class="search-box">

    <i class="fa-solid fa-magnifying-glass"></i>

    <input
        type="text"
        id="search-input"
        onkeyup="filterMenu()"
        placeholder="Cari espresso, latte, croissant..."
    >

</div>

    {{-- =====================================================
        KATEGORI
    ====================================================== --}}

    <div class="section-header category-section-heading">
        <div>
            <h4>Kategori</h4>
            <p>Pilih menu favoritmu</p>
        </div>
    </div>


    <div class="categories-grid" role="group" aria-label="Filter kategori menu">

        {{-- SEMUA --}}
        <button type="button"
            class="category-item active"
            aria-pressed="true"
            onclick="selectCategory(this, 'all')"
        >
            <i class="fa-solid fa-border-all" aria-hidden="true"></i>
            <span>Semua</span>
        </button>


        @foreach($categories as $category)
            <button type="button" class="category-item" aria-pressed="false"
                onclick="selectCategory(this, @js(strtolower(str_replace(' ', '-', $category['value']))))">
                <i class="{{ $category['icon'] }}" aria-hidden="true"></i>
                <span>{{ $category['label'] }}</span>
            </button>
        @endforeach

    </div>


    {{-- =====================================================
        MENU
    ====================================================== --}}

    <div class="section-header menu-section-heading">
        <div>
            <h4>Menu pilihan</h4>
            <p id="menu-result-count">Pilihan favorit dari Quattro</p>
        </div>
    </div>


    <div
        class="menu-list"
        id="main-menu-list"
    >

        <div class="empty-state">

            <i class="fa-solid fa-spinner fa-spin"></i>

            <p>
                Memuat menu...
            </p>

        </div>

    </div>

    <a id="quick-cart-bar" class="quick-cart-bar" hidden aria-live="polite"
       href="{{ route('customer.orders') }}" aria-label="Checkout pesanan">
        <div class="quick-cart-summary">
            <span class="quick-cart-icon">
                <i class="fa-solid fa-basket-shopping" aria-hidden="true"></i>
                <span id="quick-cart-count">0</span>
            </span>
            <span class="quick-cart-total-label">
                Total
                <strong id="quick-cart-total">Rp 0</strong>
            </span>
        </div>
        <span class="quick-cart-checkout">
            Checkout
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </span>
    </a>

    <div id="checkout-navigation-loading" class="checkout-navigation-loading" hidden role="status" aria-live="polite">
        <div class="checkout-navigation-card">
            <div class="checkout-loader-brand">
                <span class="checkout-loader-logo"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></span>
                <span><strong>QUATTRO</strong><small>COFFEE</small></span>
            </div>
            <div class="checkout-loader-cup" aria-hidden="true">
                <span class="checkout-loader-orbit"></span>
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <strong class="checkout-loader-title">Menyiapkan keranjang</strong>
            <small class="checkout-loader-caption">Sebentar ya, pesananmu sedang disiapkan.</small>
            <div class="checkout-loader-progress" aria-hidden="true"><span></span></div>
            <span class="checkout-loader-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        </div>
    </div>

    <div id="product-detail-modal" class="product-detail-modal" aria-hidden="true">
        <div class="product-detail-panel" role="dialog" aria-modal="true" aria-labelledby="detail-product-name">
            <button type="button" class="detail-close" onclick="closeProductDetail()" aria-label="Tutup detail menu">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div class="detail-product-image-wrap">
                <img id="detail-product-image" src="" alt="">
            </div>
            <div class="detail-product-summary">
                <div>
                    <h3 id="detail-product-name"></h3>
                    <p id="detail-product-description"></p>
                </div>
                <strong id="detail-product-price"></strong>
            </div>
            <div id="product-options" class="product-options"></div>
            <div class="product-detail-footer">
                <div class="detail-quantity-control" aria-label="Jumlah pesanan">
                    <button type="button" onclick="changeDetailQty(-1)" aria-label="Kurangi jumlah">−</button>
                    <span id="detail-quantity">1</span>
                    <button type="button" onclick="changeDetailQty(1)" aria-label="Tambah jumlah">+</button>
                </div>
                <button type="button" class="detail-add-button" onclick="addConfiguredProduct()">
                    <i class="fa-solid fa-plus"></i>
                    <span id="detail-add-label">Tambahkan Pesanan</span>
                    <strong id="detail-line-total"></strong>
                </button>
            </div>
        </div>
    </div>


</div>

@endsection

@push('styles')
<style>
    .quattro-home-entry-pending .quattro-header,
    .quattro-home-entry-pending .quattro-greeting,
    .quattro-home-entry-pending .quattro-promo-slider,
    .quattro-home-entry-pending .quattro-promo-dots,
    .quattro-home-entry-pending .search-box,
    .quattro-home-entry-pending .category-section-heading,
    .quattro-home-entry-pending .categories-grid,
    .quattro-home-entry-pending .menu-section-heading,
    .quattro-home-entry-pending #main-menu-list {
        opacity: 0;
        animation: quattroCustomerHomeEnter .42s ease-out forwards;
    }
    .quattro-home-entry-pending .quattro-greeting { animation-delay: .04s; }
    .quattro-home-entry-pending .quattro-promo-slider { animation-delay: .08s; }
    .quattro-home-entry-pending .quattro-promo-dots { animation-delay: .1s; }
    .quattro-home-entry-pending .search-box { animation-delay: .12s; }
    .quattro-home-entry-pending .category-section-heading,
    .quattro-home-entry-pending .categories-grid { animation-delay: .16s; }
    .quattro-home-entry-pending .menu-section-heading,
    .quattro-home-entry-pending #main-menu-list { animation-delay: .2s; }
    .quattro-home-entry-pending #quick-cart-bar:not([hidden]) {
        animation: quattroCartBarReveal .3s ease-out .56s both;
    }
    @keyframes quattroCustomerHomeEnter {
        from { opacity: 0; transform: translateY(9px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes quattroCartBarReveal {
        from { opacity: 0; visibility: hidden; transform: translateY(18px); }
        to { opacity: 1; visibility: visible; transform: translateY(0); }
    }
    @media (prefers-reduced-motion: reduce) {
        .quattro-home-entry-pending .quattro-header,
        .quattro-home-entry-pending .quattro-greeting,
        .quattro-home-entry-pending .quattro-promo-slider,
        .quattro-home-entry-pending .quattro-promo-dots,
        .quattro-home-entry-pending .search-box,
        .quattro-home-entry-pending .category-section-heading,
        .quattro-home-entry-pending .categories-grid,
        .quattro-home-entry-pending .menu-section-heading,
        .quattro-home-entry-pending #main-menu-list,
        .quattro-home-entry-pending .quick-cart-bar:not([hidden]) { animation: none; opacity: 1; visibility: visible; transform: none; }
    }
    .checkout-navigation-loading[hidden] { display: none !important; }
    .checkout-navigation-loading {
        position: fixed;
        inset: 0;
        z-index: 10050;
        display: grid;
        place-items: center;
        padding: 20px;
        background: radial-gradient(circle at 50% 44%, rgba(252, 244, 233, .68), rgba(39, 25, 14, .42));
        backdrop-filter: blur(7px);
        -webkit-backdrop-filter: blur(7px);
        animation: checkoutNavFadeIn .22s ease-out both;
    }
    .checkout-navigation-card {
        display: grid;
        justify-items: center;
        gap: 8px;
        width: min(290px, calc(100vw - 48px));
        padding: 20px 22px 17px;
        border: 1px solid rgba(255,255,255,.85);
        border-radius: 22px;
        background: linear-gradient(155deg, #fffefa, #f8efe4);
        color: #352417;
        box-shadow: 0 22px 60px rgba(37, 25, 15, .25), 0 4px 14px rgba(100, 61, 24, .08);
        animation: checkoutNavCardIn .32s cubic-bezier(.2,.8,.2,1) both;
    }
    .checkout-loader-brand { display:flex; align-items:center; gap:8px; justify-self:start; width:100%; padding-bottom:12px; border-bottom:1px solid #eee2d4; }
    .checkout-loader-logo { display:grid; place-items:center; width:30px; height:30px; border-radius:10px; background:#75400d; color:#fff9ef; font-size:13px; box-shadow:0 4px 9px rgba(89,49,12,.18); }
    .checkout-loader-brand>span:last-child { display:grid; gap:2px; }
    .checkout-loader-brand strong { color:#392719; font-size:10px; letter-spacing:1.3px; }
    .checkout-loader-brand small { color:#a17d5b; font-size:7px; letter-spacing:2px; }
    .checkout-loader-cup { position:relative; display:grid; place-items:center; width:66px; height:66px; margin:5px 0 2px; border-radius:50%; background:radial-gradient(circle, #fff 0 47%, #f3e5d4 48% 100%); color:#874b13; font-size:24px; box-shadow:inset 0 0 0 1px rgba(136,84,33,.08), 0 7px 18px rgba(89,49,12,.1); }
    .checkout-loader-orbit { position:absolute; inset:-5px; border:2px solid transparent; border-top-color:#a55b1e; border-right-color:#dfbd91; border-radius:50%; animation:checkoutNavSpin 1.05s cubic-bezier(.55,.1,.45,.9) infinite; }
    .checkout-loader-cup>i { animation:checkoutCupBob 1.2s ease-in-out infinite; }
    .checkout-loader-title { margin-top:3px; color:#392719; font-size:14px; letter-spacing:.1px; }
    .checkout-loader-caption { color:#88735f; font-size:10px; text-align:center; }
    .checkout-loader-progress { position:relative; width:100%; height:3px; margin-top:8px; overflow:hidden; border-radius:99px; background:#ecdfd0; }
    .checkout-loader-progress>span { position:absolute; inset:0 auto 0 -42%; width:42%; border-radius:inherit; background:linear-gradient(90deg,#a45a1c,#d5a368); animation:checkoutProgressSweep 1.15s ease-in-out infinite; }
    .checkout-loader-dots { display:flex; gap:4px; height:5px; margin-top:1px; }
    .checkout-loader-dots i { width:4px; height:4px; border-radius:50%; background:#98602f; opacity:.35; animation:checkoutDotPulse .9s ease-in-out infinite; }
    .checkout-loader-dots i:nth-child(2) { animation-delay:.12s; }
    .checkout-loader-dots i:nth-child(3) { animation-delay:.24s; }
    @keyframes checkoutNavSpin { to { transform: rotate(360deg); } }
    @keyframes checkoutCupBob { 0%,100% { transform:translateY(1px) rotate(-3deg); } 50% { transform:translateY(-2px) rotate(3deg); } }
    @keyframes checkoutProgressSweep { 0% { transform:translateX(0); } 100% { transform:translateX(340%); } }
    @keyframes checkoutDotPulse { 0%,80%,100% { opacity:.28; transform:scale(.8); } 40% { opacity:1; transform:scale(1); } }
    @keyframes checkoutNavFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes checkoutNavCardIn { from { opacity: 0; transform: translateY(10px) scale(.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @media (prefers-reduced-motion: reduce) {
        .checkout-navigation-loading, .checkout-navigation-card { animation: none; }
        .checkout-loader-orbit, .checkout-loader-cup>i, .checkout-loader-progress>span, .checkout-loader-dots i { animation: none; }
    }
</style>
@endpush


{{-- =========================================================
    JAVASCRIPT
========================================================= --}}

@push('scripts')

<script>

    window.quattroUser = @json([
        'id' => auth()->id(),
        'name' => auth()->user()?->name ?? 'Sahabat Quattro',
        'email' => auth()->user()?->email
    ]);


    window.quattroFavoriteIds = @json($favoriteIds);

    window.quattroProducts = @json($products);


    console.log(
        'Quattro Products:',
        window.quattroProducts
    );

</script>


<script src="{{ asset('js/customer.js') }}?v=20261128-session-cart-reset"></script>
{{-- =====================================================
    PROMO AUTO SLIDER
===================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const slides = document.querySelectorAll(
        '.quattro-promo-slide'
    );

    const dots = document.querySelectorAll(
        '.quattro-promo-dots span'
    );

    if (!slides.length) {
        return;
    }

    let currentSlide = 0;


    function showPromo(index) {

        slides.forEach((slide, i) => {

            slide.style.display =
                i === index ? 'block' : 'none';

        });


        dots.forEach((dot, i) => {

            dot.classList.toggle(
                'active',
                i === index
            );

        });

    }


    dots.forEach((dot, index) => {

        dot.addEventListener('click', function () {

            currentSlide = index;

            showPromo(currentSlide);

        });

    });


    showPromo(0);


    setInterval(function () {

        currentSlide++;

        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }

        showPromo(currentSlide);

    }, 4000);

});

</script>

@endpush
