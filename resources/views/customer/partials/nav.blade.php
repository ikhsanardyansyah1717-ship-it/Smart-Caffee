<div id="bottom-nav" class="bottom-nav show">
    <a class="nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}" href="{{ route('customer.home') }}">
        <i class="fa-solid fa-house"></i><span>Home</span>
    </a>
    <a class="nav-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}" href="{{ route('customer.orders') }}">
        <i class="fa-solid fa-receipt"></i><span>Pesanan</span>
    </a>
    <a class="nav-item {{ request()->routeIs('customer.favorites') ? 'active' : '' }}" href="{{ route('customer.favorites') }}">
        <i class="fa-solid fa-heart"></i><span>Favorit</span>
    </a>
    <a class="nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
        <i class="fa-solid fa-user"></i><span>Profil</span>
    </a>
</div>
