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
    <link rel="stylesheet" href="{{ asset('css/owner.css') }}?v=20261125-owner-notification-shake">
    <link rel="stylesheet" href="{{ asset('css/owner-charts.css') }}">
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
            <a href="{{ route('owner.menu-notifications.read', 'products') }}" class="owner-link {{ request()->routeIs('owner.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-mug-saucer"></i><span>Produk</span>
                @if(($ownerMenuNotifications['products'] ?? 0) > 0)<small class="owner-nav-badge" tabindex="0" title="Lihat rincian perubahan" data-tooltip="Produk berubah: {{ $ownerMenuNotificationDetails['products'] }}" aria-label="Produk berubah: {{ $ownerMenuNotificationDetails['products'] }}">{{ $ownerMenuNotifications['products'] }}</small>@endif
            </a>
            <a href="{{ route('owner.menu-notifications.read', 'employees') }}" class="owner-link {{ request()->routeIs('owner.employees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>Karyawan</span>
                @if(($ownerMenuNotifications['employees'] ?? 0) > 0)<small class="owner-nav-badge" tabindex="0" title="Lihat rincian perubahan" data-tooltip="Karyawan berubah: {{ $ownerMenuNotificationDetails['employees'] }}" aria-label="Karyawan berubah: {{ $ownerMenuNotificationDetails['employees'] }}">{{ $ownerMenuNotifications['employees'] }}</small>@endif
            </a>
            <a href="{{ route('owner.menu-notifications.read', 'leaves') }}" class="owner-link {{ request()->routeIs('owner.leaves.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i><span>Pengajuan Cuti</span>
                @if(($ownerMenuNotifications['leaves'] ?? 0) > 0)<small class="owner-nav-badge" tabindex="0" title="Lihat rincian perubahan" data-tooltip="Cuti menunggu: {{ $ownerMenuNotificationDetails['leaves'] }}" aria-label="Cuti menunggu: {{ $ownerMenuNotificationDetails['leaves'] }}">{{ $ownerMenuNotifications['leaves'] }}</small>@endif
            </a>
            <a href="{{ route('owner.salaries.index') }}" class="owner-link {{ request()->routeIs('owner.salaries.*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-check-dollar"></i><span>Daftar Gaji</span>
            </a>
            <a href="{{ route('owner.menu-notifications.read', 'customers') }}" class="owner-link {{ request()->routeIs('owner.customers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-group"></i><span>Pelanggan</span>
                @if(($ownerMenuNotifications['customers'] ?? 0) > 0)<small class="owner-nav-badge" tabindex="0" title="Lihat rincian perubahan" data-tooltip="Pelanggan berubah: {{ $ownerMenuNotificationDetails['customers'] }}" aria-label="Pelanggan berubah: {{ $ownerMenuNotificationDetails['customers'] }}">{{ $ownerMenuNotifications['customers'] }}</small>@endif
            </a>
            <a href="{{ route('owner.banners.edit') }}" class="owner-link {{ request()->routeIs('owner.banners.*') ? 'active' : '' }}">
                <i class="fa-regular fa-images"></i><span>Banner Customer</span>
            </a>
            <a href="{{ route('owner.reports') }}" class="owner-link {{ request()->routeIs('owner.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i><span>Laporan</span>
            </a>
            <a href="{{ route('owner.settings') }}" class="owner-link {{ request()->routeIs('owner.settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i><span>Pengaturan</span>
            </a>
            <a href="{{ route('owner.menu-notifications.read', 'finance') }}" class="owner-link {{ request()->routeIs('owner.expenses*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-bill-transfer"></i><span>Catatan Keuangan</span>
                @if(($ownerMenuNotifications['finance'] ?? 0) > 0)<small class="owner-nav-badge" tabindex="0" title="Lihat rincian perubahan" data-tooltip="Keuangan berubah: {{ $ownerMenuNotificationDetails['finance'] }}" aria-label="Keuangan berubah: {{ $ownerMenuNotificationDetails['finance'] }}">{{ $ownerMenuNotifications['finance'] }}</small>@endif
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
<script src="{{ asset('js/owner.js') }}?v=20261125-live-customer-search"></script>
@stack('scripts')
<script src="{{ asset('js/notifications.js') }}?v=20261124-notifications"></script>
<script>
(() => {
    const messages = [
        { text: @json(session('success')), type: 'success' },
        { text: @json(session('error')), type: 'error' },
        { text: @json($errors->first()), type: 'error' },
    ];

    document.addEventListener('DOMContentLoaded', () => {
        const pageText = document.querySelector('.owner-main')?.innerText ?? '';
        messages.forEach(({ text, type }) => {
            if (text && !pageText.includes(text) && typeof window.showAppNotification === 'function') {
                window.showAppNotification(text, type);
            }
        });
    });
})();
</script>
    @vite('resources/js/app.js')
</body>
</html>


