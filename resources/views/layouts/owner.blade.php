<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Owner - Quattro Coffee')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/owner.css') }}">
    @stack('styles')
</head>
<body>
<div class="owner-app">
    <aside class="owner-sidebar" id="ownerSidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-mug-hot"></i></div>
            <div><strong>QUATTRO</strong><span>OWNER</span></div>
        </div>

        <nav class="owner-nav">
            <a href="{{ route('owner.dashboard') }}" class="owner-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('owner.sales') }}" class="owner-link {{ request()->routeIs('owner.sales') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i><span>Penjualan</span>
            </a>
            <a href="{{ route('owner.products.index') }}" class="owner-link {{ request()->routeIs('owner.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-mug-saucer"></i><span>Produk</span>
            </a>
            <a href="{{ route('owner.employees') }}" class="owner-link {{ request()->routeIs('owner.employees') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>Karyawan</span>
            </a>
            <a href="{{ route('owner.customers.index') }}" class="owner-link {{ request()->routeIs('owner.customers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-group"></i><span>Pelanggan</span>
            </a>
            <a href="{{ route('owner.reports') }}" class="owner-link {{ request()->routeIs('owner.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i><span>Laporan</span>
            </a>
        </nav>

        <div class="owner-profile">
            <div class="avatar">O</div>
            <div><strong>Owner</strong><small>Quattro Coffee</small></div>
        </div>

        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
    @csrf

    <button type="submit" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
    </button>
</form>

    </aside>

    <main class="owner-main">
        <header class="mobile-topbar">
            <button class="icon-btn" id="ownerMenu"><i class="fa-solid fa-bars"></i></button>
            <div class="mobile-brand"><strong>QUATTRO</strong><span>OWNER</span></div>
            <button class="icon-btn" id="ownerRefresh"><i class="fa-solid fa-rotate"></i></button>
        </header>

        @yield('content')
    </main>
</div>
<script src="{{ asset('js/owner.js') }}"></script>
@stack('scripts')
</body>
</html>