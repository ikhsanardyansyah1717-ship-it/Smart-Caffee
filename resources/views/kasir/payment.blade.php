@extends('layouts.kasir')
@section('title','Pembayaran - Quattro Coffee')

@section('content')
<header class="topbar">
    <div><span class="eyebrow">CASHIER CONTROL</span><h1>Pembayaran</h1><p>Proses pembayaran pelanggan dengan cepat dan mudah.</p></div>
</header>

<section class="payment-grid">
    <div class="panel">
        <div class="panel-head"><div><h2>Pilih Pesanan</h2><p>Pilih transaksi yang akan dibayar.</p></div></div>
        <div class="payment-orders">
        @foreach($orders as $order)
            <button class="payment-order" onclick="selectPayment(this,'{{ $order['id'] }}',{{ $order['total'] }})">
                <span class="payment-id">{{ $order['id'] }}</span>
                <strong>{{ $order['customer'] }}</strong>
                <small>{{ $order['items'] }}</small>
                <b>Rp {{ number_format($order['total'],0,',','.') }}</b>
            </button>
        @endforeach
        </div>
    </div>

    <div class="panel payment-card">
        <div class="panel-head"><div><h2>Detail Pembayaran</h2><p id="selectedText">Belum ada pesanan dipilih.</p></div></div>
        <div class="amount-box"><span>Total</span><strong id="paymentTotal">Rp 0</strong></div>
        <label>Metode Pembayaran
            <select id="paymentMethod">
                <option>Cash</option><option>QRIS</option><option>Debit / E-Wallet</option>
            </select>
        </label>
        <label>Uang Diterima<input id="cashInput" type="number" min="0" placeholder="Masukkan nominal"></label>
        <div class="change-row"><span>Kembalian</span><strong id="changeAmount">Rp 0</strong></div>
        <button class="primary-btn full" onclick="processPayment()"><i class="fa-solid fa-check-circle"></i> Selesaikan Pembayaran</button>
    </div>
</section>
@endsection
