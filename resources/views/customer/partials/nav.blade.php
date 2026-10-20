<nav class="bottom-nav">

    <a
        href="{{ route('customer.home') }}"
        class="nav-item"
    >
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </a>


    <a
        href="{{ route('customer.history') }}"
        class="nav-item active"
    >
        <i class="fa-solid fa-receipt"></i>
        <span>Pesanan</span>
    </a>


    <a
        href="{{ route('customer.favorites') }}"
        class="nav-item"
    >
        <i class="fa-solid fa-heart"></i>
        <span>Favorit</span>
    </a>


</nav>
