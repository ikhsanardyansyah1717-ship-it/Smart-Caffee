@extends('layouts.customer')

@section('title', 'Quattro Coffee - Get Started')

@section('content')
<div class="app-container">
    <div class="get-started-container">
        <div class="hero-section">
            <div class="logo-wrapper">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <div class="brand-name">QUATTRO COFFEE</div>
            <div class="hero-text">
                <h1>Nikmati Kopi Terbaik <span>Kapan Saja</span></h1>
                <p>Temukan keseimbangan sempurna dari 4 elemen rasa biji kopi terbaik yang diseduh khusus untukmu.</p>
            </div>
        </div>

        <div class="action-section">
            @auth
                @if (auth()->user()->role === 'customer')
                    <a class="btn-primary" href="{{ route('customer.home') }}">Masuk ke Aplikasi <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
                @else
                    <a class="btn-primary" href="{{ route('role.dashboard') }}">Buka Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
                @endif
            @else
                <a class="btn-primary" href="{{ route('login') }}">Get Started <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
            @endauth
        </div>
    </div>
</div>
@endsection
