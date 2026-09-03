@extends('layouts.owner')

@section('title', 'Edit Produk - Quattro Coffee')

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
                Edit Produk
            </h1>

            <p>
                Perbarui detail menu "{{ $product->name }}".
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
         FORM EDIT
    ====================================================== --}}
    <div class="panel product-form-panel">

        <form
            method="POST"
            action="{{ route('owner.products.update', $product) }}"
            enctype="multipart/form-data"
        >

            @csrf

            @method('PUT')


            {{-- Form product --}}
            @include('owner.products._form')


            {{-- =================================================
                 ACTION BUTTONS
            ================================================== --}}
            <div class="pf-actions">

                {{-- Simpan perubahan --}}
                <button
                    type="submit"
                    class="btn btn-primary product-form-btn"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Simpan Perubahan

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