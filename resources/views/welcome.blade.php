@extends('layouts.customer')

@section('title', 'Quattro Coffee')

@push('styles')
<style>
    .quattro-loader-screen {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        background: #f7f1e8;
        color: #4b2e20;
        text-align: center;
    }
    .quattro-loader-screen[hidden] { display: none; }
    .quattro-loader-content { padding: 24px; }
    .quattro-loader-cup {
        display: grid;
        place-items: center;
        width: 82px;
        height: 82px;
        margin: 0 auto 18px;
        border: 1px solid #dbc7ad;
        border-radius: 28px;
        background: #fffaf3;
        color: #7a4b2a;
        font-size: 32px;
        animation: quattro-cup-pulse 1.1s ease-in-out infinite;
    }
    .quattro-loader-brand { font-weight: 800; letter-spacing: .2em; }
    .quattro-loader-caption { margin-top: 8px; color: #8a7461; font-size: 14px; }
    .quattro-loader-dots::after { content: ''; animation: quattro-dots 1.2s steps(4, end) infinite; }
    .quattro-loader-progress {
        width: 150px;
        height: 3px;
        margin: 22px auto 0;
        overflow: hidden;
        border-radius: 999px;
        background: #e8dccd;
    }
    .quattro-loader-progress::after {
        display: block;
        width: 45%;
        height: 100%;
        border-radius: inherit;
        background: #9b623b;
        content: '';
        animation: quattro-progress 1s ease-in-out infinite;
    }
    .quattro-staff-screen { min-height: 100vh; }
    .quattro-staff-screen .get-started-container { min-height: 100vh; }
    @keyframes quattro-cup-pulse { 50% { transform: translateY(-5px) scale(1.04); } }
    @keyframes quattro-dots { 0% { content: ''; } 25% { content: '.'; } 50% { content: '..'; } 75%, 100% { content: '...'; } }
    @keyframes quattro-progress { from { transform: translateX(-110%); } to { transform: translateX(240%); } }
</style>
@endpush

@section('content')
@if (auth()->guest() || auth()->user()->role === 'customer')
    <main class="quattro-loader-screen" aria-live="polite" aria-label="Memuat Quattro Coffee">
        <div class="quattro-loader-content">
            <div class="quattro-loader-cup"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></div>
            <div class="quattro-loader-brand">QUATTRO COFFEE</div>
            <div class="quattro-loader-caption">Menyiapkan menu favoritmu<span class="quattro-loader-dots"></span></div>
            <div class="quattro-loader-progress" aria-hidden="true"></div>
        </div>
    </main>
    <script>
        window.setTimeout(() => window.location.replace(@json(route('customer.home'))), 900);
    </script>
@else
    <div class="app-container quattro-staff-screen">
        <div class="get-started-container">
            <div class="hero-section">
                <div class="logo-wrapper"><i class="fa-solid fa-mug-hot"></i></div>
                <div class="brand-name">QUATTRO COFFEE</div>
                <div class="hero-text">
                    <h1>Selamat Datang</h1>
                    <p>Dashboard {{ ucfirst(auth()->user()->role) }} Quattro Coffee siap dibuka.</p>
                </div>
            </div>
            <div class="action-section">
                <a class="btn-primary" href="{{ route('role.dashboard') }}" id="staff-dashboard-link">
                    Buka Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="quattro-loader-screen" id="staff-dashboard-loader" hidden aria-live="polite" aria-label="Membuka dashboard">
        <div class="quattro-loader-content">
            <div class="quattro-loader-cup"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></div>
            <div class="quattro-loader-brand">QUATTRO COFFEE</div>
            <div class="quattro-loader-caption">Membuka dashboard<span class="quattro-loader-dots"></span></div>
            <div class="quattro-loader-progress" aria-hidden="true"></div>
        </div>
    </div>
    <script>
        document.getElementById('staff-dashboard-link').addEventListener('click', () => {
            document.getElementById('staff-dashboard-loader').hidden = false;
        });
    </script>
@endif
@endsection
