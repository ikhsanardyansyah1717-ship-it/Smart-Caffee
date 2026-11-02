@extends('layouts.kasir')

@section('title', 'Pantau Stok - Quattro Coffee')

@section('content')
<header class="topbar">
    <div>
        <span class="eyebrow">CASHIER CONTROL</span>
        <h1>Pantau Stok Produk</h1>
        <p>Stok mengikuti data menu yang dikelola Owner.</p>
    </div>
    <div class="top-actions"><a href="{{ route('kasir.dashboard') }}" class="outline-btn">Kembali ke Dashboard</a></div>
</header>

<section class="stats-grid" style="margin-top:16px;">
    <div class="stat-card"><div class="stat-icon brown"><i class="fa-solid fa-boxes-stacked"></i></div><div><span>Total Produk</span><strong>{{ $totalProducts }}</strong><small>Per {{ now()->format('d/m/Y') }}</small></div></div>
    <div class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-triangle-exclamation"></i></div><div><span>Stok Menipis</span><strong>{{ $lowStockCount }}</strong><small>Stok 1 sampai 5 · Per {{ now()->format('d/m/Y') }}</small></div></div>
    <div class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-circle-xmark"></i></div><div><span>Stok Habis</span><strong>{{ $outOfStockCount }}</strong><small>Stok 0 · Per {{ now()->format('d/m/Y') }}</small></div></div>
</section>

@if(session('success'))
    <div class="notice notice-success" style="margin-top:16px;padding:12px 16px;border-radius:10px;background:#e8f7ed;color:#176534;">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="notice notice-error" style="margin-top:16px;padding:12px 16px;border-radius:10px;background:#fdeaea;color:#9d2020;">{{ $errors->first('stock') }}</div>
@endif

<section class="panel" style="margin-top:20px;">
    <div class="panel-head">
        <div><h2>Daftar Stok</h2><p>Untuk tambah atau ubah menu, gunakan halaman Produk di akun Owner.</p></div>
    </div>
    <div class="table-wrap"><table>
        <thead><tr><th>Menu</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Ketersediaan</th><th>Diubah</th><th>Penyesuaian Stok</th></tr></thead>
        <tbody>
        @forelse($products as $product)
            @php($categoryLabel = collect(config('product_categories'))->firstWhere('value', $product->category)['label'] ?? $product->category)
            <tr>
                <td><strong>{{ $product->name }}</strong></td>
                <td>{{ $categoryLabel }}</td>
                <td>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                <td><strong style="color:{{ $product->stock <= 0 ? '#b42318' : ($product->stock <= 5 ? '#b76e00' : '#236b3b') }}">{{ $product->stock }}</strong></td>
                <td>@if($product->stock <= 0)<span class="badge red">Stok Habis</span>@elseif($product->stock <= 5)<span class="badge" style="background:#fff0d4;color:#8a5100;">Stok Menipis</span>@else<span class="badge green">Tersedia</span>@endif</td>
                <td>{{ $product->updated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>
                    <form method="POST" action="{{ route('kasir.products.stock.update', $product) }}" style="display:flex;align-items:center;gap:7px;min-width:190px;">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="stock-{{ $product->id }}">Jumlah stok {{ $product->name }}</label>
                        <input id="stock-{{ $product->id }}" type="number" name="stock" value="{{ $product->stock }}" min="0" step="1" required style="width:86px;padding:8px;border:1px solid #e5dbd1;border-radius:8px;">
                        <button type="submit" class="outline-btn" style="white-space:nowrap;">Simpan Stok</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;padding:30px;">Belum ada produk.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
@endsection
