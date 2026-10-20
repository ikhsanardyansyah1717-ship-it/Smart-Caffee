@extends('layouts.customer')

@section('title', 'Quattro Coffee - Home')

@section('content')

<div class="app-container">

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

        {{-- LOCATION --}}
        <div class="quattro-location">
            <i class="fa-solid fa-location-dot"></i>
            <span>Dago</span>
        </div>


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
            <p>Pilih sesuai seleramu</p>
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


        {{-- COFFEE --}}
        <button type="button"
            class="category-item"
            aria-pressed="false"
            onclick="selectCategory(this, 'coffee')"
        >
            <i class="fa-solid fa-mug-hot" aria-hidden="true"></i>
            <span>Coffee</span>
        </button>


        {{-- NON COFFEE --}}
        <button type="button"
            class="category-item"
            aria-pressed="false"
            onclick="selectCategory(this, 'non-coffee')"
        >
            <i class="fa-solid fa-glass-water" aria-hidden="true"></i>
            <span>Non-Coffee</span>
        </button>


        {{-- FOOD --}}
        <button type="button"
            class="category-item"
            aria-pressed="false"
            onclick="selectCategory(this, 'food')"
        >
            <i class="fa-solid fa-utensils" aria-hidden="true"></i>
            <span>Food</span>
        </button>

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


<script src="{{ asset('js/customer.js') }}?v=13"></script>


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
