@extends('layouts.owner')

@section('title', 'Produk - Quattro Coffee')

@section('content')

<div class="grid">

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
            class="toolbar"
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



            {{-- Filter kategori --}}
            <select
                name="category"
                onchange="this.form.submit()"
            >

                <option value="">
                    Semua Kategori
                </option>


                <option
                    value="Coffee"
                    {{ request('category') == 'Coffee' ? 'selected' : '' }}
                >
                    Coffee
                </option>


                <option
                    value="Non Coffee"
                    {{ request('category') == 'Non Coffee' ? 'selected' : '' }}
                >
                    Non Coffee
                </option>


                <option
                    value="Food"
                    {{ request('category') == 'Food' ? 'selected' : '' }}
                >
                    Food
                </option>

            </select>



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


                <article class="product-card">


                    {{-- =====================================
                         GAMBAR / ICON PRODUK
                    ====================================== --}}
                    <div class="product-thumb">

                        @if($product->image)

                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                style="
                                    width:100%;
                                    height:100%;
                                    object-fit:cover;
                                    border-radius:inherit;
                                "
                            >

                        @else

                            <i
                                class="fa-solid {{ $product->icon ?? 'fa-mug-hot' }}"
                            ></i>

                        @endif

                    </div>



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

                        {{ $product->category }}

                        ·

                        {{ $product->is_available
                            ? 'Tersedia'
                            : 'Tidak tersedia'
                        }}

                    </p>



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
                <p
                    style="
                        padding:24px;
                        text-align:center;
                        color:#888;
                    "
                >

                    Belum ada produk.

                    Klik
                    <strong>"Tambah Produk"</strong>
                    untuk menambahkan menu baru.

                </p>


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