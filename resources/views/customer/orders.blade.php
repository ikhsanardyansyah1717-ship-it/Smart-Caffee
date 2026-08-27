@extends('layouts.customer')
@section('title', 'Pesanan - Quattro Coffee')
@section('content')
<div class="app-container">
    <div class="top-nav-bar"><h3>Pesanan Saya</h3></div>
    <div class="order-tabs">
        <button class="tab-btn active" onclick="switchOrderTab('cart')">Keranjang (<span id="cart-count">0</span>)</button>
        <button class="tab-btn" onclick="switchOrderTab('history')">Riwayat</button>
    </div>
    <div id="tab-cart">
        <div id="cart-items-container"></div>
        <div id="cart-summary-box" class="order-summary" style="display:none;">
            <div class="summary-row"><span>Subtotal</span><span id="subtotal-val">Rp 0</span></div>
            <div class="summary-row"><span>Pajak & Layanan (10%)</span><span id="tax-val">Rp 0</span></div>
            <div class="summary-row total"><span>Total Pembayaran</span><span id="total-val">Rp 0</span></div>
            <button class="btn-primary" style="margin-top:15px;" onclick="checkout()">Bayar Sekarang</button>
        </div>
    </div>
    <div id="tab-history" style="display:none"><div id="history-container"></div></div>
    @include('customer.partials.nav')
</div>
@endsection
@push('scripts')
<script>window.quattroUser = @json(['id'=>auth()->id(),'name'=>auth()->user()->name,'email'=>auth()->user()->email]);</script>
<script src="{{ asset('js/customer.js') }}"></script>
@endpush
