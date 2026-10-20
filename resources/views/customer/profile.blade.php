@extends('layouts.customer')

@section('title', 'Profile - Quattro Coffee')
@section('content')
<main class="app-container profile-page">
    <header class="profile-page-header">
        <a class="profile-back" href="{{ route('customer.home') }}" aria-label="Kembali ke Beranda"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Profile</h1>
        <span></span>
    </header>

    <section class="profile-page-content">
        <div class="profile-identity">
            <div class="profile-guest-avatar"><i class="fa-regular fa-circle-user"></i></div>
            <div>
                <div class="profile-identity-name">
                    @if($user && $user->role === 'customer')
                        {{ $user->name }}
                    @else
                        Log In as Guest
                    @endif
                </div>
                @if(!$user || $user->role !== 'customer')
                    <a class="profile-signin" href="{{ route('customer.login') }}">Sign In</a>
                @endif
            </div>
        </div>

        <nav class="profile-menu" aria-label="Menu profil">
            <a class="profile-menu-item" href="{{ route('customer.history') }}">
                <i class="fa-regular fa-rectangle-list"></i><span>Order History</span>
            </a>
            <a class="profile-menu-item" href="{{ route('customer.favorites') }}">
                <i class="fa-regular fa-heart"></i><span>Favorit</span>
            </a>
            <button class="profile-menu-item" type="button" onclick="document.getElementById('language-dialog').showModal()">
                <i class="fa-solid fa-globe"></i><span>Language</span><i class="fa-solid fa-chevron-right profile-chevron"></i>
            </button>
            <a class="profile-menu-item" href="{{ route('customer.privacy') }}">
                <i class="fa-solid fa-shield-halved"></i><span>Privacy Policy</span>
            </a>
        </nav>

        @if($user && $user->role === 'customer')
            <form class="profile-logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="profile-menu-item" type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Keluar</span></button>
            </form>
        @endif
    </section>

    
</main>

<dialog class="profile-dialog" id="language-dialog">
    <h2>Language</h2>
    <p id="language-status" data-no-translate>Bahasa aplikasi saat ini: Indonesia.</p>
    <div class="profile-language-options">
        <button type="button" class="profile-language-option" data-language-option="id" onclick="setCustomerLanguage('id')">Bahasa Indonesia</button>
        <button type="button" class="profile-language-option" data-language-option="en" onclick="setCustomerLanguage('en')">English</button>
    </div>
    <button type="button" class="profile-dialog-close" onclick="document.getElementById('language-dialog').close()">Tutup</button>
</dialog>
@endsection
