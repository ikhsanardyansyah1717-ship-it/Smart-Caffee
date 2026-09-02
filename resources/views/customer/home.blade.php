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
        LOCATION HEADER
    ====================================================== --}}
    <div class="location-header">

        <div class="loc-tag">

            <i class="fa-solid fa-location-dot"></i>

            <span>
                Quattro Coffee, Dago Central
            </span>

        </div>


        <a href="{{ route('customer.profile') }}">

            <img
                src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop"
                class="profile-btn-header"
                alt="Profil"
            >

        </a>

    </div>


    {{-- =====================================================
        GREETING
    ====================================================== --}}
    <div class="user-greeting">

        <h2>
            Selamat Datang, {{ auth()->user()->name }}! 👋
        </h2>

        <p>
            Mau ngopi apa di Quattro Coffee hari ini?
        </p>

    </div>


    {{-- =====================================================
        SEARCH
    ====================================================== --}}
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
        PROMO
    ====================================================== --}}
    <div class="promo-card">

        <div class="promo-text">

            <span class="promo-badge">
                Rekomendasi
            </span>

            <h3>
                Cafe Latte
            </h3>

            <p>
                Best Seller pekan ini di Bandung
            </p>

        </div>


        <img
            src="https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop"
            class="promo-img"
            alt="Cafe Latte"
        >

    </div>


    {{-- KATEGORI --}}
<div class="section-header">
    <h4>Kategori</h4>
</div>

<div class="categories-grid">

    {{-- SEMUA --}}
    <div
        class="category-item active"
        onclick="selectCategory(this, 'all')"
    >
        <div class="category-icon">
            <i class="fa-solid fa-border-all"></i>
        </div>

        <span>Semua</span>
    </div>


    {{-- COFFEE --}}
    <div
        class="category-item"
        onclick="selectCategory(this, 'coffee')"
    >
        <div class="category-icon">
            <i class="fa-solid fa-mug-hot"></i>
        </div>

        <span>Coffee</span>
    </div>


    {{-- NON COFFEE --}}
    <div
        class="category-item"
        onclick="selectCategory(this, 'non-coffee')"
    >
        <div class="category-icon">
            <i class="fa-solid fa-glass-water"></i>
        </div>

        <span>Non-Coffee</span>
    </div>


    {{-- FOOD --}}
    <div
        class="category-item"
        onclick="selectCategory(this, 'food')"
    >
        <div class="category-icon">
            <i class="fa-solid fa-utensils"></i>
        </div>

        <span>Food</span>
    </div>

</div>


    {{-- =====================================================
        MENU
    ====================================================== --}}
    <div class="section-header">

        <h4>
            Menu Pilihan
        </h4>

    </div>


    <div
        class="menu-list"
        id="main-menu-list"
    >

        {{-- Menu akan di-render oleh customer.js --}}

        <div class="empty-state">

            <i class="fa-solid fa-spinner fa-spin"></i>

            <p>
                Memuat menu...
            </p>

        </div>

    </div>


    {{-- =====================================================
        BOTTOM NAVIGATION
    ====================================================== --}}
    @include('customer.partials.nav')


</div>

@endsection


{{-- =========================================================
    JAVASCRIPT
========================================================= --}}
@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | DATA CUSTOMER
    |--------------------------------------------------------------------------
    */

    window.quattroUser = @json([
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'email' => auth()->user()->email
    ]);


    /*
    |--------------------------------------------------------------------------
    | DATA PRODUCT DARI DATABASE
    |--------------------------------------------------------------------------
    |
    | $products dikirim oleh CustomerController:
    |
    | $products = Product::where('is_available', true)->get();
    |
    */

    window.quattroProducts = @json($products);


    /*
    |--------------------------------------------------------------------------
    | DEBUG
    |--------------------------------------------------------------------------
    |
    | Buka F12 > Console untuk melihat apakah produk masuk.
    |
    */

    console.log(
        'Quattro Products:',
        window.quattroProducts
    );

</script>


{{-- CUSTOMER JS --}}
<script src="{{ asset('js/customer.js') }}"></script>

@endpush