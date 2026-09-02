@extends('layouts.owner')

@section('title', 'Tambah Produk - Quattro Coffee')

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
                Tambah Produk
            </h1>

            <p>
                Tambahkan menu baru ke katalog Quattro Coffee.
            </p>

        </div>


        {{-- Tombol kembali --}}
        <a
            class="btn btn-light"
            href="{{ route('owner.products.index') }}"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Kembali

        </a>

    </header>



    {{-- =====================================================
         FORM
    ====================================================== --}}
    <div class="panel product-form-panel">

        <form
            method="POST"
            action="{{ route('owner.products.store') }}"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- Form product --}}
            @include('owner.products._form')


            {{-- =================================================
                 ACTION BUTTONS
            ================================================== --}}
            <div class="pf-actions">

                {{-- Simpan --}}
                <button
                    type="submit"
                    class="btn btn-primary product-form-btn"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Simpan Produk

                </button>


                {{-- Batal --}}
                <a
                    href="{{ route('owner.products.index') }}"
                    class="btn btn-light product-form-btn"
                >

                    Batal

                </a>

            </div>

        </form>

    </div>

</div>

@endsection