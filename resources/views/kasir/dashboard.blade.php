@extends('layouts.kasir')

@section('title','Kasir Dashboard - Quattro Coffee')

@section('content')

<header class="topbar">
    <div>
        <span class="eyebrow">CASHIER CONTROL</span>
        <h1>Dashboard</h1>
        <p>Kelola transaksi dan pantau penjualan cafe berdasarkan tanggal.</p>
    </div>

    <div class="top-actions">
        {{-- PERIODE TANGGAL --}}
        <form method="GET" action="{{ route('kasir.dashboard') }}" style="display:flex; align-items:center; gap:8px;">
            <div style="display:flex; align-items:center; gap:7px; padding:9px 12px; border:1px solid #eadbce; border-radius:10px; background:#fff;">
                <i class="fa-regular fa-calendar" style="color:#9b5b16;"></i>
                <input type="date" name="date" value="{{ $tanggal }}" style="border:0; outline:0; background:transparent; color:#5b3518; font-size:13px; cursor:pointer;">
            </div>
            <button type="submit" class="outline-btn" style="height:42px; cursor:pointer;">Tampilkan</button>
        </form>

        {{-- LIVE --}}
        <span class="live"><i class="fa-solid fa-circle"></i> Live</span>

        {{-- REFRESH --}}
        <button class="icon-btn" onclick="location.reload()">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>
</header>

{{-- STATISTIK --}}
<section class="stats-grid">
    {{-- Pesanan Tanggal Terpilih --}}
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <span>Pesanan Tanggal Terpilih</span>
            <strong>{{ $pesananHariIni }}</strong>
            <small>Total pesanan pada tanggal terpilih</small>
        </div>
    </div>

    {{-- Menunggu Bayar --}}
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-hourglass-half"></i></div>
        <div>
            <span>Menunggu Bayar</span>
            <strong>{{ $menungguBayar }}</strong>
            <small>Perlu diproses</small>
        </div>
    </div>

    {{-- Penjualan Tanggal Terpilih --}}
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <span>Penjualan Tanggal Terpilih</span>
            <strong>Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</strong>
            <small>Total transaksi lunas</small>
        </div>
    </div>

    {{-- Peringatan Stok --}}
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <span>Peringatan Stok</span>
            <strong>{{ $stockWarningCount ?? 0 }}</strong>
            <small>Produk dengan stok 0 sampai 5</small>
        </div>
    </div>
</section>

{{-- DASHBOARD --}}
<section class="dashboard-grid">
    {{-- PESANAN TERBARU --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <h2>Pesanan Terbaru</h2>
                <p>Transaksi pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}.</p>
            </div>
            <a href="{{ route('kasir.orders') }}" class="outline-btn">Lihat Semua</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_number ?? $order->id }}</strong>
                                <small>{{ $order->created_at ? $order->created_at->format('H:i') : '-' }}</small>
                            </td>
                            <td>{{ $order->customer_name ?? '-' }}</td>
                            <td>{{ $order->table_number ?? 'Take Away' }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><span class="badge {{ strtolower($order->status) }}">{{ $order->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px;">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RINGKASAN PENJUALAN --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <h2>Ringkasan Penjualan</h2>
                <p>Performa transaksi pada tanggal terpilih.</p>
            </div>
        </div>

        <div class="sales-highlight">
            <span>Total pendapatan pada {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</span>
            <strong>Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</strong>
            <small><i class="fa-solid fa-arrow-trend-up"></i> Penjualan tanggal terpilih</small>
        </div>

        <div class="mini-list">
            {{-- TRANSAKSI SELESAI --}}
            <div>
                <i class="fa-solid fa-receipt"></i>
                <span>Transaksi selesai</span>
                <strong>{{ $transaksiSelesai }}</strong>
            </div>

            {{-- PELANGGAN --}}
            <div>
                <i class="fa-solid fa-users"></i>
                <span>Pelanggan pada tanggal terpilih</span>
                <strong>{{ $pelangganHariIni }}</strong>
            </div>
        </div>

        <a href="{{ route('kasir.payment') }}" class="primary-btn full">
            <i class="fa-solid fa-cash-register"></i>
            Proses Pembayaran
        </a>
    </div>
</section>

{{-- KETERSEDIAAN PRODUK --}}
<section class="panel" style="margin-top:22px;">
    <div class="panel-head">
        <div>
            <h2>Ketersediaan Produk</h2>
            <p>Ringkasan jumlah stok produk saat ini.</p>
        </div>
        <a href="{{ route('kasir.products.availability') }}" class="outline-btn">Kelola Produk</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Ketersediaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($productAvailability ?? collect()) as $product)
                    <tr>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                        <td><strong>{{ $product->stock }}</strong></td>
                        <td>
                            @if((int) $product->stock <= 0)
                                <span class="badge red">Stok Habis</span>
                            @elseif((int) $product->stock <= 5)
                                <span class="badge" style="background:#fff0d4;color:#8a5100;">Stok Menipis</span>
                            @else
                                <span class="badge green">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:30px;">Belum ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
