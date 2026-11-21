@extends('layouts.customer')

@section('title', 'Privacy Policy - Quattro Coffee')

@section('content')
<main class="app-container favorites-page privacy-page">
    <header class="favorites-page-header">
        <a class="favorites-back" href="{{ route('customer.profile') }}" aria-label="Kembali ke Profil">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        </a>
        <h1>Privacy Policy</h1>
        <span aria-hidden="true"></span>
    </header>

    <article class="privacy-content">
        <div class="privacy-policy-text">{!! nl2br(e($privacyPolicy)) !!}</div>
    </article>
</main>
@endsection
