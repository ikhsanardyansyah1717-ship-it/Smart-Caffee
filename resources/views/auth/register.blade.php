@extends('layouts.customer')

@section('title', 'Register - Quattro Coffee')

@section('content')
<div class="app-container">
    <div class="top-nav-bar">
        <a href="{{ route('login') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <div class="auth-header">
        <i class="fa-solid fa-mug-hot"></i>
        <h2>Buat Akun Baru</h2>
        <p>Bergabunglah bersama komunitas Quattro Coffee</p>
    </div>

    @if ($errors->any())
        <div class="auth-alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="form-group">
            <label>Nama Lengkap</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap kamu" required>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat email kamu" required>
            </div>
        </div>

        <div class="form-group">
            <label>Kata Sandi</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Minimal 8 karakter" required>
            </div>
        </div>

        <div class="form-group">
            <label>Konfirmasi Kata Sandi</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required>
            </div>
        </div>

        <button type="submit" class="btn-primary">Daftar Akun</button>
    </form>

    <div class="auth-footer">
        Sudah punya akun?
        <a href="{{ route('login') }}">Masuk</a>
    </div>
</div>
@endsection
