@extends('layouts.owner')

@section('title', 'Produk - Quattro Coffee')

@section('content')

<div class="grid">

    <header class="page-head">
        <div>
            <span class="eyebrow">PRODUCT MANAGEMENT</span>
            <h1>Produk</h1>
            <p>Kelola menu dan produk Quattro Coffee.</p>
        </div>

        <div class="head-actions">
            <a
                href="{{ route('owner.products.create') }}"
                class="btn btn-primary"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Produk
            </a>
        </div>
    </header>


    {{-- NOTIFIKASI --}}
    @if(session('success'))

        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>

    @endif


    {{-- FILTER --}}
    <section class="panel">

        <div class="panel-head">

            <div>
                <h2>Daftar Produk</h2>
                <p>Kelola semua menu yang tersedia.</p>
            </div>

        </div>


        <form
            method="GET"
            action="{{ route('owner.products.index') }}"
            class="toolbar"
        >

            <label class="search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari produk..."
                >

            </label>


            <select name="category">

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


            <select name="status">

                <option value="">
                    Semua Status
                </option>

                <option
                    value="active"
                    {{ request('status') == 'active' ? 'selected' : '' }}
                >
                    Aktif
                </option>

                <option
                    value="inactive"
                    {{ request('status') == 'inactive' ? 'selected' : '' }}
                >
                    Tidak Aktif
                </option>

            </select>


            <button
                type="submit"
                class="btn btn-light"
            >
                <i class="fa-solid fa-filter"></i>
                Filter
            </button>

        </form>


        {{-- PRODUK --}}
        <div class="cards">

            @forelse($products as $product)

                <article class="product-card">

                    {{-- GAMBAR / ICON --}}
                    <div class="product-thumb">

                        @if($product->image)

                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="product-image"
                            >

                        @else

                            <i
                                class="fa-solid {{ $product->icon ?? 'fa-mug-hot' }}"
                            ></i>

                        @endif

                    </div>


                    {{-- INFORMASI --}}
                    <h3>
                        {{ $product->name }}
                    </h3>

                    <p>
                        {{ $product->category }}
                    </p>


                    @if($product->description)

                        <p class="product-description">
                            {{ Str::limit($product->description, 70) }}
                        </p>

                    @endif


                    <div class="product-bottom">

                        <strong>
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </strong>


                        @if($product->is_available)

                            <span class="badge green">
                                Aktif
                            </span>

                        @else

                            <span class="badge">
                                Tidak Aktif
                            </span>

                        @endif

                    </div>


                    {{-- AKSI --}}
                    <div class="product-actions">

                        <a
                            href="{{ route('owner.products.edit', $product) }}"
                            class="btn btn-light"
                        >
                            <i class="fa-solid fa-pen"></i>
                            Edit
                        </a>


                        <button
                            type="button"
                            class="btn btn-light"
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

                <div class="product-empty">

                    <i class="fa-solid fa-box-open"></i>

                    <p>
                        Belum ada produk.
                    </p>

                    <a
                        href="{{ route('owner.products.create') }}"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-plus"></i>
                        Tambah Produk
                    </a>

                </div>

            @endforelse

        </div>

    </section>

</div>


{{-- MODAL HAPUS --}}
<div
    id="deleteModal"
    class="delete-modal-overlay"
>

    <div class="delete-modal">

        <div class="delete-modal-icon">
            <i class="fa-solid fa-trash"></i>
        </div>


        <h2>Hapus Produk?</h2>


        <p>
            Apakah kamu yakin ingin menghapus
            <span
                id="deleteProductName"
                class="delete-product-name"
            ></span>?
        </p>


        <div class="delete-warning">
            Produk yang sudah dihapus tidak dapat dikembalikan.
        </div>


        <div class="delete-modal-actions">

            <button
                type="button"
                class="delete-cancel-btn"
                onclick="closeDeleteModal()"
            >
                Batal
            </button>


            <form
                id="deleteProductForm"
                method="POST"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="delete-confirm-btn"
                >
                    <i class="fa-solid fa-trash"></i>
                    Ya, Hapus
                </button>

            </form>

        </div>

    </div>

</div>


<script>

    function openDeleteModal(productId, productName) {

        const modal =
            document.getElementById('deleteModal');

        const name =
            document.getElementById('deleteProductName');

        const form =
            document.getElementById('deleteProductForm');


        name.textContent = productName;

        form.action =
            "{{ url('/owner/products') }}/" + productId;

        modal.classList.add('show');

        document.body.classList.add('modal-open');
    }


    function closeDeleteModal() {

        const modal =
            document.getElementById('deleteModal');

        modal.classList.remove('show');

        document.body.classList.remove('modal-open');
    }


    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {
            closeDeleteModal();
        }

    });

</script>

@endsection