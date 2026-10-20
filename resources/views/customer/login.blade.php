@extends('layouts.customer')

@section('title', 'Sign In - Quattro Coffee')
@section('content')
<main class="app-container customer-login-page">
    <header class="customer-login-header">
        <a href="{{ route('customer.home') }}" aria-label="Kembali ke beranda"><i class="fa-solid fa-arrow-left"></i></a>
        <strong>Sign In</strong><span></span>
    </header>
    <div class="customer-login-brand">
        <i class="fa-solid fa-mug-hot"></i>
        <h1>QUATTRO COFFEE</h1>
        <p>Masuk ke akun pelanggan kamu</p>
    </div>
    <form class="customer-login-form" method="POST" action="{{ route('customer.login.store') }}">
        @csrf
        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')<div class="customer-login-error">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="password">Kata sandi</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password')<div class="customer-login-error">{{ $message }}</div>@enderror
        </div>
        <button class="customer-login-submit" type="submit">Sign In</button>
    </form>
</main>
@endsection
