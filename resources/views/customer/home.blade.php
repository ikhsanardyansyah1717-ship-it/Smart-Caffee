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
        <button type="button" class="quattro-location" id="quattro-open-branch">
            <i class="fa-solid fa-location-dot"></i>
            <span>{{ $selectedBranch?->name ?? 'Pilih Lokasi' }}</span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px"></i>
        </button>


        {{-- PROFILE --}}
    <a href="{{ route('customer.profile') }}" class="quattro-profile" aria-label="Buka profil">
        <i class="fa-solid fa-user"></i>
    </a>

</header>

{{-- PEMILIHAN CABANG --}}
<div id="quattro-branch-modal" class="quattro-branch-overlay" aria-hidden="true">
    <section class="quattro-branch-panel" role="dialog" aria-modal="true" aria-labelledby="branch-modal-title">
        <button type="button" class="quattro-branch-close" id="quattro-close-branch" aria-label="Tutup">&times;</button>
        <div class="quattro-branch-heading">
            <span class="quattro-branch-icon"><i class="fa-solid fa-location-dot"></i></span>
            <h3 id="branch-modal-title">Pilih Lokasi Quattro</h3>
            <p>Tentukan cabang tempat kamu ingin memesan.</p>
        </div>
        <button type="button" class="quattro-gps-button" id="quattro-use-gps">
            <i class="fa-solid fa-location-crosshairs"></i> Gunakan lokasi GPS saya
        </button>
        <p id="quattro-gps-message" class="quattro-gps-message" role="status"></p>
        <div class="quattro-branch-list">
            @foreach($branches as $branch)
                <form method="POST" action="{{ route('customer.branches.select') }}">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    <button type="submit" class="quattro-branch-option">
                        <span class="quattro-branch-pin"><i class="fa-solid fa-store"></i></span>
                        <span class="quattro-branch-info">
                            <strong>Quattro Coffee {{ $branch->name }}</strong>
                            <small>{{ $branch->address }}</small>
                        </span>
                        @if($selectedBranch && $selectedBranch->id === $branch->id)
                            <i class="fa-solid fa-circle-check quattro-branch-checked"></i>
                        @else
                            <i class="fa-solid fa-chevron-right quattro-branch-arrow"></i>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
        <small class="quattro-branch-note">Alamat dan titik GPS masih data dummy sementara.</small>
    </section>
</div>

<style>
.quattro-location{border:0;cursor:pointer;font:inherit}
.quattro-branch-overlay{position:fixed;inset:0;background:rgba(35,24,15,.52);z-index:9999;display:none;align-items:flex-end;justify-content:center;padding:14px}
.quattro-branch-overlay.is-open{display:flex}
.quattro-branch-panel{position:relative;background:#fffaf4;width:min(100%,480px);max-height:85vh;overflow:auto;border-radius:24px;padding:24px 18px 20px;box-shadow:0 15px 50px #0003;color:#2d2424}
.quattro-branch-close{position:absolute;right:16px;top:12px;border:0;background:#f1e7dc;border-radius:50%;width:34px;height:34px;font-size:24px;color:#572e04}
.quattro-branch-heading{text-align:center;padding:8px 24px 18px}
.quattro-branch-icon{width:48px;height:48px;margin:auto auto 10px;border-radius:50%;background:#f2e5d7;color:#763f06;display:grid;place-items:center;font-size:20px}
.quattro-branch-heading h3{margin:0 0 5px;font-size:19px}
.quattro-branch-heading p{margin:0;color:#8c7a6b;font-size:12px}
.quattro-gps-button{width:100%;border:0;border-radius:13px;padding:13px;background:#763f06;color:white;font-weight:700;cursor:pointer}
.quattro-gps-button:disabled{opacity:.65}
.quattro-gps-message{font-size:12px;text-align:center;color:#76502d;min-height:16px;margin:8px 0}
.quattro-branch-list{display:grid;gap:9px}
.quattro-branch-option{width:100%;display:flex;align-items:center;gap:11px;text-align:left;padding:13px;border:1px solid #eadfd3;border-radius:14px;background:#fff;color:#2d2424;cursor:pointer}
.quattro-branch-pin{width:38px;height:38px;flex:0 0 38px;border-radius:11px;background:#f7eee5;color:#763f06;display:grid;place-items:center}
.quattro-branch-info{display:grid;gap:4px;flex:1;min-width:0}
.quattro-branch-info strong{font-size:13px}
.quattro-branch-info small{font-size:10px;line-height:1.4;color:#8c7a6b}
.quattro-branch-checked{color:#398451}.quattro-branch-arrow{color:#a28f7e;font-size:11px}
.quattro-branch-note{display:block;text-align:center;margin-top:14px;color:#9b8a7b;font-size:10px}
@media(min-width:600px){.quattro-branch-overlay{align-items:center}.quattro-branch-panel{border-radius:24px}}
</style>

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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('quattro-branch-modal');
    const openBtn = document.getElementById('quattro-open-branch');
    const closeBtn = document.getElementById('quattro-close-branch');
    const gpsBtn = document.getElementById('quattro-use-gps');
    const gpsMessage = document.getElementById('quattro-gps-message');

    function openModal() {
        modal?.classList.add('is-open');
        modal?.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal?.classList.remove('is-open');
        modal?.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    modal?.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    gpsBtn?.addEventListener('click', function () {
        if (!navigator.geolocation) {
            gpsMessage.textContent = 'Browser tidak mendukung GPS. Silakan pilih cabang manual.';
            return;
        }
        gpsBtn.disabled = true;
        gpsMessage.textContent = 'Meminta izin lokasi...';
        navigator.geolocation.getCurrentPosition(async position => {
            try {
                const response = await fetch("{{ route('customer.branches.nearest') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal menentukan cabang.');
                gpsMessage.textContent = 'Lokasi terdekat: ' + result.branch.name + '. Memuat ulang...';
                window.location.reload();
            } catch (error) {
                gpsMessage.textContent = error.message;
                gpsBtn.disabled = false;
            }
        }, error => {
            const messages = {
                1: 'Izin lokasi ditolak. Aktifkan izin lokasi atau pilih cabang manual.',
                2: 'Lokasi tidak dapat ditemukan. Coba lagi atau pilih cabang manual.',
                3: 'Permintaan GPS terlalu lama. Coba lagi.'
            };
            gpsMessage.textContent = messages[error.code] || 'GPS gagal. Pilih cabang manual.';
            gpsBtn.disabled = false;
        }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 });
    });
});
</script>


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
