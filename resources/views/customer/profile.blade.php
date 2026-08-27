@extends('layouts.customer')
@section('title', 'Profil - Quattro Coffee')
@section('content')
<div class="app-container">
    <div class="top-nav-bar"><h3>Profil Saya</h3></div>
    <div class="profile-card">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop" class="profile-avatar" alt="User Avatar">
        <div class="profile-name">{{ auth()->user()->name }}</div>
        <div class="profile-email">{{ auth()->user()->email }}</div>
        <div class="badge-loyalty"><i class="fa-solid fa-user-tag"></i> CUSTOMER</div>
    </div>
    <div class="menu-group">
        <div class="menu-item-profile"><div class="menu-item-left"><i class="fa-solid fa-user-pen"></i><span>Edit Profil</span></div><i class="fa-solid fa-chevron-right menu-item-right"></i></div>
        <div class="menu-item-profile"><div class="menu-item-left"><i class="fa-solid fa-map-location-dot"></i><span>Alamat Pengiriman</span></div><i class="fa-solid fa-chevron-right menu-item-right"></i></div>
        <div class="menu-item-profile"><div class="menu-item-left"><i class="fa-solid fa-credit-card"></i><span>Metode Pembayaran</span></div><i class="fa-solid fa-chevron-right menu-item-right"></i></div>
    </div>
    <div class="menu-group">
        <div class="menu-item-profile"><div class="menu-item-left"><i class="fa-solid fa-headset"></i><span>Pusat Bantuan</span></div><i class="fa-solid fa-chevron-right menu-item-right"></i></div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="menu-item-profile" style="width:100%;background:none;border:0;text-align:left;">
                <div class="menu-item-left" style="color:var(--secondary);"><i class="fa-solid fa-right-from-bracket" style="color:var(--secondary);"></i><span>Keluar Akun</span></div>
                <i class="fa-solid fa-chevron-right menu-item-right"></i>
            </button>
        </form>
    </div>
    @include('customer.partials.nav')
</div>
@endsection
