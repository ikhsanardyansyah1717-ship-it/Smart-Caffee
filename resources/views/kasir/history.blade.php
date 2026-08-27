@extends('layouts.kasir')
@section('title','Riwayat - Quattro Coffee')

@section('content')
<header class="topbar">
    <div><span class="eyebrow">CASHIER CONTROL</span><h1>Riwayat Transaksi</h1><p>Lihat transaksi yang sudah selesai.</p></div>
</header>

<div class="panel">
    <div class="panel-head">
        <div><h2>Transaksi Selesai</h2><p>Riwayat transaksi kasir.</p></div>
        <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input id="historySearch" type="text" placeholder="Cari transaksi..." onkeyup="filterTable('historySearch','historyTable')"></div>
    </div>
    <div class="table-wrap">
        <table id="historyTable">
            <thead><tr><th>ID</th><th>Pelanggan</th><th>Pesanan</th><th>Total</th><th>Status</th><th>Waktu</th></tr></thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td><strong>{{ $order['id'] }}</strong></td>
                    <td>{{ $order['customer'] }}</td>
                    <td>{{ $order['items'] }}</td>
                    <td>Rp {{ number_format($order['total'],0,',','.') }}</td>
                    <td><span class="badge {{ strtolower($order['status']) }}">{{ $order['status'] }}</span></td>
                    <td>{{ $order['time'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
