@extends('layouts.customer')

@section('title', 'Login - Quattro Coffee')

@section('content')
<div class="app-container">
    <div class="top-nav-bar">
        <a href="{{ route('welcome') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <div class="auth-header">
        <i class="fa-solid fa-mug-hot"></i>
        <h2>Selamat Datang Kembali</h2>
        <p>Masuk ke Quattro Coffee untuk menikmati racikan favoritmu</p>
    </div>

    @if (session('success'))
        <div class="auth-alert success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="auth-alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="form-group">
            <label>Email</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email kamu" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label>Kata Sandi</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
        </div>

        <div class="forgot-pass">
            <span>Lupa kata sandi? Hubungi admin.</span>
        </div>

        <button type="submit" class="btn-primary">Masuk Sekarang</button>
    </form>

    <div class="auth-footer">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar Akun</a>
    </div>
</div>
@endsection
