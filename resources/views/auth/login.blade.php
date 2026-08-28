@extends('layouts.customer')

@section('title', 'Login - Quattro Coffee')

@section('content')

<div class="app-container">

    {{-- TOP NAV --}}
    <div class="top-nav-bar">

        <a
            href="{{ route('welcome') }}"
            class="btn-back"
        >
            <i class="fa-solid fa-arrow-left"></i>
        </a>

    </div>


    {{-- HEADER --}}
    <div class="auth-header">

        <i class="fa-solid fa-mug-hot"></i>

        <h2>
            Selamat Datang Kembali
        </h2>

        <p>
            Masuk ke Quattro Coffee untuk menikmati racikan favoritmu
        </p>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))

        <div class="auth-alert success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if ($errors->any())

        <div class="auth-alert error">
            {{ $errors->first() }}
        </div>

    @endif


    {{-- LOGIN FORM --}}
    <form
        method="POST"
        action="{{ route('customer.login.store') }}"
    >

        @csrf


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
                    placeholder="Masukkan email kamu"
                    required
                    autofocus
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
                    id="loginPassword"
                    placeholder="••••••••"
                    required
                >

                <button
                    type="button"
                    class="toggle-password"
                    onclick="togglePassword('loginPassword', 'loginEye')"
                    aria-label="Tampilkan password"
                >

                    {{-- AWAL: PASSWORD TERSEMBUNYI --}}
                    <i
                        class="fa-solid fa-eye-slash"
                        id="loginEye"
                    ></i>

                </button>

            </div>

        </div>


        {{-- FORGOT PASSWORD --}}
        <div class="forgot-pass">
    <a href="#" onclick="return false;">
        Lupa kata sandi? Hubungi admin.
    </a>
</div>


        {{-- LOGIN BUTTON --}}
        <button
            type="submit"
            class="btn-primary"
        >
            Masuk Sekarang
        </button>

    </form>


    {{-- REGISTER LINK --}}
    <div class="auth-footer">

        Belum punya akun?

        <a href="{{ route('customer.register') }}">
            Daftar Akun
        </a>

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('js/customer.js') }}"></script>

@endpush