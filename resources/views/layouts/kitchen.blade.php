<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quattro Coffee - Kitchen')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/kitchen.css') }}">
    @stack('styles')
</head>
<body>
    <div class="kitchen-app">
        <div class="kitchen-shell">
            <!-- Sidebar Utama -->
            <aside class="sidebar">
                <div class="brand">
                    <div class="brand-icon">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <div>
                        <strong>QUATTRO</strong>
                        <span>KITCHEN</span>
                    </div>
                </div>

                <nav>
                    <a href="{{ route('kitchen.dashboard') }}" class="side-link {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('kitchen.incoming') }}" class="side-link {{ request()->routeIs('kitchen.incoming') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i>
                        <span>Pesanan Baru</span>
                        <b id="new-count">0</b>
                    </a>
                    <a href="{{ route('kitchen.processing') }}" class="side-link {{ request()->routeIs('kitchen.processing') ? 'active' : '' }}">
                        <i class="fa-solid fa-fire-burner"></i>
                        <span>Sedang Diproses</span>
                        <b id="process-count">0</b>
                    </a>
                    <a href="{{ route('kitchen.completed') }}" class="side-link {{ request()->routeIs('kitchen.completed') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Siap Diambil</span>
                        <b id="ready-count">0</b>
                    </a>
                    <a href="{{ route('kitchen.history') }}" class="side-link {{ request()->routeIs('kitchen.history') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Riwayat</span>
                    </a>
                </nav>

                <div class="kitchen-user">
                    <div class="avatar">K</div>
                    <div>
                        <strong>Kitchen Staff</strong>
                        <small>Quattro Coffee</small>
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

            <!-- Main Content Area -->
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/kitchen.js') }}"></script>
    @stack('scripts')
</body>
</html>