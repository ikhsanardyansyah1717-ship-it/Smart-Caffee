@extends('layouts.owner')
@section('title','Pelanggan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head"><div><span class="eyebrow">CUSTOMER MANAGEMENT</span><h1>Pelanggan</h1><p>Lihat pertumbuhan dan aktivitas pelanggan.</p></div><a class="btn btn-primary" href="{{ route('owner.customers.create') }}"><i class="fa-solid fa-user-plus"></i> Tambah Pelanggan</a></header>

@if (session('success'))
<div class="alert alert-success" style="background:#e9f9ee;color:#1e8a4c;padding:12px 16px;border-radius:10px;margin-bottom:16px;"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<section class="stats">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-user-group"></i></div><div><div class="stat-label">Total Pelanggan</div><div class="stat-value">{{ $totalCustomers }}</div></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-user-plus"></i></div><div><div class="stat-label">Pelanggan Baru</div><div class="stat-value">{{ $newThisMonth }}</div><div class="stat-note">Bulan ini</div></div></article>
</section>

<div class="panel">
<div class="panel-head">
    <div><h2>Daftar Pelanggan</h2><p>Semua akun dengan role customer.</p></div>
    <form method="GET" action="{{ route('owner.customers.index') }}" class="search" style="max-width:280px">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pelanggan..." onchange="this.form.submit()">
    </form>
</div>
<div class="table-wrap"><table><thead><tr><th>Pelanggan</th><th>Email</th><th>Jumlah Pesanan</th><th>Bergabung</th><th>Aksi</th></tr></thead><tbody>
@forelse($customers as $customer)
<tr>
    <td><strong>{{ $customer->name }}</strong></td>
    <td>{{ $customer->email }}</td>
    <td>{{ $customer->orders_count }}</td>
    <td>{{ $customer->created_at->format('d M Y') }}</td>
    <td style="display:flex;gap:8px;">
        <a href="{{ route('owner.customers.edit', $customer) }}" class="btn btn-light" style="padding:6px 12px;"><i class="fa-solid fa-pen"></i></a>
        <form action="{{ route('owner.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan {{ $customer->name }}?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-light" style="padding:6px 12px;color:#d9534f;"><i class="fa-solid fa-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;color:#888;padding:24px;">Belum ada pelanggan terdaftar.</td></tr>
@endforelse
</tbody></table></div>
</div>
</div>
@endsection