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

{{-- =====================================================
     SCRIPT AUTO FORMAT RUPIAH
====================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mencari input dengan ID "price" atau name="price"
    const priceInput = document.getElementById('price') || document.querySelector('input[name="price"]');

    if (priceInput) {
        // Ubah tipe ke text agar bisa menampilkan format titik
        priceInput.type = 'text';

        // Fungsi format angka dengan pemisah titik
        function formatRupiah(value) {
            const cleanValue = value.replace(/\D/g, '');
            if (!cleanValue) return '';
            return new Intl.NumberFormat('id-ID').format(cleanValue);
        }

        // Format angka jika sudah ada isinya (misal saat kembalian error validasi)
        if (priceInput.value) {
            priceInput.value = formatRupiah(priceInput.value);
        }

        // Format otomatis setiap kali mengetik
        priceInput.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
        });

        // Bersihkan titik sebelum form dikirim ke controller/database
        const form = priceInput.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                priceInput.value = priceInput.value.replace(/\D/g, '');
            });
        }
    }
});
</script>

@endsection