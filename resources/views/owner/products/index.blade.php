@extends('layouts.owner')

@section('title', 'Produk - Quattro Coffee')

@section('content')

@push('styles')
<style>
.owner-products{display:grid;gap:20px;padding-bottom:30px}.owner-products .page-head{margin:0;align-items:center}.owner-products .panel{padding:22px;border:1px solid #eee3d7;border-radius:20px;box-shadow:0 10px 28px rgba(65,43,21,.045)}.owner-products .product-toolbar{display:grid;grid-template-columns:minmax(0,1fr) 190px;gap:12px;margin:0 0 20px}.owner-products .product-toolbar .search{width:100%;max-width:none}.owner-products .product-toolbar select{min-height:46px;border:1px solid #e9ddcf;border-radius:12px;background:#fff;padding:0 13px;color:#63482f}.owner-products .cards{grid-template-columns:repeat(auto-fill,minmax(235px,1fr));gap:17px}.owner-products .product-card{display:flex;flex-direction:column;min-width:0;padding:12px;border:1px solid #eee4d9;border-radius:18px;background:#fff;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}.owner-products .product-card[hidden]{display:none}.owner-products .product-card:hover{transform:translateY(-3px);border-color:#e4d2bb;box-shadow:0 14px 28px rgba(66,44,23,.09)}.owner-products .product-thumb{position:relative;height:auto;aspect-ratio:1/1;overflow:hidden;margin:0 0 14px;border-radius:18px;background:linear-gradient(145deg,#f8f1e8,#efe2d0);color:#9a6a32}.owner-products .product-thumb img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .35s ease}.owner-products .product-thumb img[hidden]{display:none!important}.owner-products .product-card:hover .product-thumb img{transform:scale(1.035)}.owner-products .product-thumb-fallback{position:absolute;inset:0;display:grid;place-items:center;color:#a77c4c;font-size:34px}.owner-products .product-thumb-fallback[hidden]{display:none}.owner-products .product-card h3{margin:0 0 5px;color:#30251d;font-size:15px;line-height:1.4}.owner-products .product-card>p{margin:0 0 8px;color:#927b65;font-size:11px}.owner-products .product-cost{display:flex;align-items:center;justify-content:space-between;margin:0 0 10px;color:#987f66;font-size:10px}.owner-products .product-cost strong{color:#63482f;font-size:11px}.owner-products .product-bottom{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:auto;padding-top:12px;border-top:1px solid #f2ece4}.owner-products .product-bottom>span:first-child{color:#422b16;font-size:16px;font-weight:800}.owner-products .product-actions{gap:8px!important;margin-top:12px!important}.owner-products .product-actions .btn{min-height:39px;border-radius:10px!important;font-size:11px}.owner-products .product-empty{grid-column:1/-1;padding:38px 16px;text-align:center;color:#90785f}.owner-products .product-empty i{display:grid;place-items:center;width:46px;height:46px;margin:0 auto 11px;border-radius:15px;background:#f7eee3;color:#8a520d}
@media(max-width:700px){.owner-products{gap:15px}.owner-products .panel{padding:16px}.owner-products .product-toolbar{grid-template-columns:1fr}.owner-products .cards{grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:12px}}
@media(max-width:440px){.owner-products .cards{grid-template-columns:1fr}.owner-products .page-head{align-items:flex-start;gap:12px}.owner-products .page-head .btn{white-space:nowrap}}
</style>
@endpush

<div class="grid owner-products">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <header class="page-head">

        <div>

            <span class="eyebrow">
                PRODUCT MANAGEMENT
            </span>

            <h1>
                Produk
            </h1>

            <p>
                Kelola menu, harga, dan ketersediaan produk.
            </p>

        </div>


        {{-- Tombol tambah produk --}}
        <a
            class="btn btn-primary"
            href="{{ route('owner.products.create') }}"
        >

            <i class="fa-solid fa-plus"></i>

            Tambah Produk

        </a>

    </header>



    {{-- =====================================================
         SUCCESS MESSAGE
    ====================================================== --}}
    @if (session('success'))

        <div
            class="alert alert-success"
            style="
                background:#e9f9ee;
                color:#1e8a4c;
                padding:12px 16px;
                border-radius:10px;
                margin-bottom:16px;
            "
        >

            <i class="fa-solid fa-circle-check"></i>

            {{ session('success') }}

        </div>

    @endif



    {{-- =====================================================
         PRODUCT PANEL
    ====================================================== --}}
    <div class="panel">


        {{-- =================================================
             SEARCH & FILTER
        ================================================== --}}
        <form
            method="GET"
            action="{{ route('owner.products.index') }}"
            class="toolbar product-toolbar"
        >


            {{-- Search --}}
            <label class="search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama produk..."
                >

            </label>



            {{-- Filter status --}}
            <select
                name="status"
                onchange="this.form.submit()"
            >

                <option
                    value="active"
                    {{ request('status', 'active') == 'active' ? 'selected' : '' }}
                >
                    Produk Aktif
                </option>


                <option
                    value=""
                    {{ request('status') == '' ? 'selected' : '' }}
                >
                    Semua Produk
                </option>


                <option
                    value="inactive"
                    {{ request('status') == 'inactive' ? 'selected' : '' }}
                >
                    Produk Nonaktif
                </option>

            </select>

        </form>

        {{-- =================================================
             PRODUCT CARDS
        ================================================== --}}
        <div class="cards">


            @forelse($products as $product)


                <article class="product-card" data-product-name="{{ \Illuminate\Support\Str::lower($product->name) }}">


                    {{-- =====================================
                         GAMBAR / ICON PRODUK
                    ====================================== --}}
                    <div class="product-thumb">

                        @if($product->image)
                            @php($productImageUrl = \Illuminate\Support\Str::startsWith(trim((string) $product->image), ['http://', 'https://']) ? $product->image : route('products.image', $product, false) . '?v=' . substr(sha1(trim((string) $product->image)), 0, 12))
                            <img
                                src="{{ $productImageUrl }}"
                                alt="{{ $product->name }}"
                                loading="lazy"
                                onerror="this.hidden=true;this.nextElementSibling.hidden=false"
                            >
                            <span class="product-thumb-fallback" hidden><i class="fa-solid fa-mug-hot"></i></span>
                        @else
                            <span class="product-thumb-fallback"><i class="fa-solid {{ $product->icon ?? 'fa-mug-hot' }}"></i></span>
                        @endif</div>




                    {{-- =====================================
                         NAMA PRODUK
                    ====================================== --}}
                    <h3>
                        {{ $product->name }}
                    </h3>



                    {{-- =====================================
                         KATEGORI + STATUS KETERSEDIAAN
                    ====================================== --}}
                    <p>

                        {{ collect(config('product_categories'))->firstWhere('value', $product->category)['label'] ?? $product->category }}

                        ·

                        {{ $product->is_available
                            ? 'Tersedia'
                            : 'Tidak tersedia'
                        }}

                    </p>

                    <div class="product-cost"><span>Harga Pokok Produksi</span><strong>{{ (float) $product->cost_price > 0 ? 'Rp ' . number_format($product->cost_price, 0, ',', '.') : 'Belum diisi' }}</strong></div>



                    {{-- =====================================
                         HARGA + BADGE STATUS
                    ====================================== --}}
                    <div class="product-bottom">


                        <span>

                            Rp
                            {{ number_format($product->price, 0, ',', '.') }}

                        </span>


                        <span
                            class="badge {{ $product->is_available ? 'green' : '' }}"
                        >

                            {{ $product->is_available
                                ? 'Aktif'
                                : 'Nonaktif'
                            }}

                        </span>

                    </div>



                    {{-- =====================================
                         ACTION BUTTONS
                    ====================================== --}}
                    <div
                        class="product-actions"
                        style="
                            display:flex;
                            gap:8px;
                            margin-top:12px;
                        "
                    >


                        {{-- =================================
                             EDIT
                        ================================== --}}
                        <a
                            href="{{ route('owner.products.edit', $product) }}"
                            class="btn btn-light"
                            style="
                                flex:1;
                                text-align:center;
                            "
                        >

                            <i class="fa-solid fa-pen"></i>

                            Edit

                        </a>



                        {{-- =================================
                             HAPUS
                        ================================== --}}

                        <button
                            type="button"
                            class="btn btn-light"
                            style="
                                flex:1;
                                width:100%;
                                color:#d9534f;
                            "
                            onclick="openDeleteModal(
                                {{ $product->id }},
                                @js($product->name)
                            )"
                        >

                            <i class="fa-solid fa-trash"></i>

                            Hapus

                        </button>


                    </div>


                </article>


            @empty


                {{-- =========================================
                     JIKA BELUM ADA PRODUK
                ========================================== --}}
                <div class="product-empty"><i class="fa-solid fa-mug-saucer"></i><strong>Produk belum tersedia</strong><span>Klik “Tambah Produk” untuk menambahkan menu.</span></div>


            @endforelse


        </div>

    </div>

</div>



{{-- =========================================================
     MODAL KONFIRMASI HAPUS
========================================================= --}}

<div
    id="deleteModal"
    class="delete-modal-overlay"
    onclick="closeDeleteModal(event)"
>


    {{-- Kotak modal --}}
    <div
        class="delete-modal"
        onclick="event.stopPropagation()"
    >


        {{-- ================================================
             ICON
        ================================================= --}}
        <div class="delete-modal-icon">

            <i class="fa-solid fa-trash"></i>

        </div>



        {{-- ================================================
             JUDUL
        ================================================= --}}
        <h2>
            Hapus Produk?
        </h2>



        {{-- ================================================
             DESKRIPSI
        ================================================= --}}
        <p>

            Apakah kamu yakin ingin menghapus produk

            <span
                id="deleteProductName"
                class="delete-product-name"
            ></span>

            ?

        </p>



        {{-- ================================================
             PERINGATAN
        ================================================= --}}
        <div class="delete-warning">

            <i class="fa-solid fa-triangle-exclamation"></i>

            Data produk yang dihapus tidak dapat dikembalikan.

        </div>



        {{-- ================================================
             BUTTON
        ================================================= --}}
        <div class="delete-modal-actions">


            {{-- BATAL --}}
            <button
                type="button"
                class="delete-cancel-btn"
                onclick="closeDeleteModal()"
            >

                Batal

            </button>



            {{-- FORM DELETE --}}
            <form
                id="deleteProductForm"
                method="POST"
                style="flex:1;"
            >

                @csrf

                @method('DELETE')


                <button
                    type="submit"
                    class="delete-confirm-btn"
                    style="width:100%;"
                >

                    <i class="fa-solid fa-trash"></i>

                    Hapus Produk

                </button>

            </form>


        </div>


    </div>

</div>



{{-- =========================================================
     JAVASCRIPT MODAL
========================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('.owner-products input[name="search"]');
    const cards = [...document.querySelectorAll('.owner-products .product-card')];
    if (!input) return;
    input.addEventListener('input', function () {
        const term = this.value.trim().toLocaleLowerCase('id-ID');
        cards.forEach(card => {
            card.hidden = !card.dataset.productName.toLocaleLowerCase('id-ID').includes(term);
        });
    });
});

    /*
     * Membuka modal hapus
     */
    function openDeleteModal(productId, productName)
    {

        const modal =
            document.getElementById('deleteModal');


        const productNameElement =
            document.getElementById('deleteProductName');


        const deleteForm =
            document.getElementById('deleteProductForm');



        /*
         * Masukkan nama produk
         */
        productNameElement.textContent =
            productName;



        /*
         * Tentukan URL DELETE
         *
         * Contoh:
         * /owner/products/5
         */
        deleteForm.action =
            "{{ url('/owner/products') }}/" + productId;



        /*
         * Tampilkan modal
         */
        modal.classList.add('show');



        /*
         * Nonaktifkan scroll halaman
         */
        document.body.classList.add('modal-open');

    }



    /*
     * Menutup modal
     */
    function closeDeleteModal(event)
    {

        /*
         * Kalau user klik bagian dalam modal,
         * jangan tutup modal.
         */
        if (
            event &&
            event.target !== event.currentTarget
        ) {

            return;

        }



        const modal =
            document.getElementById('deleteModal');



        /*
         * Sembunyikan modal
         */
        modal.classList.remove('show');



        /*
         * Aktifkan kembali scroll
         */
        document.body.classList.remove('modal-open');

    }



    /*
     * Tombol ESC untuk menutup modal
     */
    document.addEventListener(
        'keydown',
        function(event)
        {

            if (event.key === 'Escape')
            {

                closeDeleteModal();

            }

        }
    );

</script>

@endsection
