<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir - Quattro Coffee')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-mug-hot"></i></div>
            <div><strong>QUATTRO</strong><span>COFFEE</span></div>
        </div>

        <div class="menu-label">KASIR</div>
        <nav>
            <a href="{{ route('kasir.dashboard') }}" class="side-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('kasir.orders') }}" class="side-link {{ request()->routeIs('kasir.orders') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i><span>Pesanan</span>
            </a>
            <a href="{{ route('kasir.payment') }}" class="side-link {{ request()->routeIs('kasir.payment') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register"></i><span>Pembayaran</span>
            </a>
            <a href="{{ route('kasir.history') }}" class="side-link {{ request()->routeIs('kasir.history') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="cashier-user">
                <div class="avatar">K</div>
                <div><strong>Kasir Staff</strong><small>Quattro Coffee</small></div>
            </div>
        </div>

        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
    @csrf

    <button type="submit" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
    </button>
</form>

    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="toast success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<script src="{{ asset('js/kasir.js') }}"></script>
@stack('scripts')
</body>
</html>
