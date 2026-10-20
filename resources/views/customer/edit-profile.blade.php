@extends('layouts.customer')

@section('title', 'Edit Profil - Quattro Coffee')

@section('content')

<div class="app-container">

    <div class="edit-profile-page">

        {{-- =================================================
            HEADER
        ================================================== --}}
        <div class="edit-profile-header">

            <a href="{{ route('customer.profile') }}"
               class="profile-back-button"
               aria-label="Kembali">

                <i class="fa-solid fa-arrow-left"></i>

            </a>

            <h3>Edit Profil</h3>

        </div>


        {{-- =================================================
            FORM CARD
        ================================================== --}}
        <div class="edit-profile-card">

            <div class="edit-profile-card-title">

                <i class="fa-solid fa-user-pen"></i>

                <div>
                    <strong>Informasi Pribadi</strong>

                    <span>
                        Perbarui informasi akun kamu
                    </span>
                </div>

            </div>


            {{-- =================================================
                FORM
            ================================================== --}}
            <form
                action="{{ route('customer.profile.update') }}"
                method="POST"
                class="edit-profile-form"
            >

                @csrf
                @method('PUT')


                {{-- =================================================
                    NAMA
                ================================================== --}}
                <div class="form-group">

                    <label for="name">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    USERNAME
                ================================================== --}}
                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        placeholder="Masukkan username"
                        autocomplete="username"
                    >

                    @error('username')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    EMAIL
                ================================================== --}}
                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="Masukkan email"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    NOMOR HP
                ================================================== --}}
                <div class="form-group">

                    <label for="phone">
                        Nomor HP
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="08xxxxxxxxxx"
                        autocomplete="tel"
                    >

                    @error('phone')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    SAVE BUTTON
                ================================================== --}}
                <button
                    type="submit"
                    class="save-profile-btn"
                >

                    <i class="fa-solid fa-check"></i>

                    <span>Simpan Perubahan</span>

                </button>

            </form>

        </div>

    </div>


    {{-- =====================================================
        BOTTOM NAVIGATION
    ====================================================== --}}
</div>

@endsection
