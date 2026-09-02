@extends('layouts.customer')

@section('title', 'Register - Quattro Coffee')

@section('content')

<div class="app-container">

    {{-- TOP NAV --}}
    <div class="top-nav-bar">

        <a
            href="{{ route('customer.login') }}"
            class="btn-back"
        >
            <i class="fa-solid fa-arrow-left"></i>
        </a>

    </div>


    {{-- HEADER --}}
    <div class="auth-header">

        <i class="fa-solid fa-mug-hot"></i>

        <h2>
            Buat Akun Baru
        </h2>

        <p>
            Bergabunglah bersama komunitas Quattro Coffee
        </p>

    </div>


    {{-- ERROR MESSAGE --}}
    @if ($errors->any())

        <div class="auth-alert error">

            {{ $errors->first() }}

        </div>

    @endif


    {{-- REGISTER FORM --}}
    <form
        method="POST"
        action="{{ route('customer.register.store') }}"
    >

        @csrf


        {{-- NAMA --}}
        <div class="form-group">

            <label>
                Nama Lengkap
            </label>

            <div class="input-wrapper">

                <i class="fa-solid fa-user"></i>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nama lengkap kamu"
                    required
                >

            </div>

        </div>


        {{-- EMAIL --}}
        <div class="form-group">

            <label>
                Email
            </label>

            <div class="input-wrapper">

                <i class="fa-solid fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Alamat email kamu"
                    required
                >

            </div>

        </div>


        {{-- PASSWORD --}}
        <div class="form-group">

            <label>
                Kata Sandi
            </label>

            <div class="input-wrapper password-wrapper">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    name="password"
                    id="registerPassword"
                    placeholder="Minimal 8 karakter"
                    required
                >

                <button
                    type="button"
                    class="toggle-password"
                    onclick="togglePassword('registerPassword', 'registerEye')"
                    aria-label="Tampilkan password"
                >

                    {{-- AWAL: PASSWORD TERSEMBUNYI --}}
                    <i
                        class="fa-solid fa-eye-slash"
                        id="registerEye"
                    ></i>

                </button>

            </div>

        </div>


        {{-- KONFIRMASI PASSWORD --}}
        <div class="form-group">

            <label>
                Konfirmasi Kata Sandi
            </label>

            <div class="input-wrapper password-wrapper">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    name="password_confirmation"
                    id="registerPasswordConfirmation"
                    placeholder="Ulangi kata sandi"
                    required
                >

                <button
                    type="button"
                    class="toggle-password"
                    onclick="togglePassword(
                        'registerPasswordConfirmation',
                        'registerConfirmationEye'
                    )"
                    aria-label="Tampilkan password"
                >

                    {{-- AWAL: PASSWORD TERSEMBUNYI --}}
                    <i
                        class="fa-solid fa-eye-slash"
                        id="registerConfirmationEye"
                    ></i>

                </button>

            </div>

        </div>


        {{-- REGISTER BUTTON --}}
        <button
            type="submit"
            class="btn-primary"
        >
            Daftar Akun
        </button>

    </form>


    {{-- LOGIN LINK --}}
    <div class="auth-footer">

        Sudah punya akun?

        <a href="{{ route('customer.login') }}">
            Masuk
        </a>

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('js/customer.js') }}"></script>

@endpush