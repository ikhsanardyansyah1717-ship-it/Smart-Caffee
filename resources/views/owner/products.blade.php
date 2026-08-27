@extends('layouts.owner')
@section('title','Produk - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head"><div><span class="eyebrow">PRODUCT MANAGEMENT</span><h1>Produk</h1><p>Kelola menu, harga, dan ketersediaan produk.</p></div><button class="btn btn-primary" data-demo="Form tambah produk dibuka"><i class="fa-solid fa-plus"></i> Tambah Produk</button></header>
<div class="panel">
<div class="toolbar"><label class="search"><i class="fa-solid fa-magnifying-glass"></i><input data-search=".product-card" placeholder="Cari nama produk..."></label><select><option>Semua Kategori</option><option>Coffee</option><option>Non Coffee</option><option>Food</option></select><select><option>Produk Aktif</option><option>Semua Produk</option></select></div>
<div class="cards">
@php($products=[['Caramel Macchiato','Coffee','Rp 28.000','fa-mug-hot'],['Hazelnut Latte','Coffee','Rp 26.000','fa-mug-saucer'],['Matcha Cream Latte','Non Coffee','Rp 29.000','fa-glass-water'],['Butter Croissant','Food','Rp 22.000','fa-bread-slice'],['Chocolate Cake','Food','Rp 30.000','fa-cake-candles'],['Americano','Coffee','Rp 22.000','fa-mug-hot']])
@foreach($products as $p)
<article class="product-card"><div class="product-thumb"><i class="fa-solid {{ $p[3] }}"></i></div><h3>{{ $p[0] }}</h3><p>{{ $p[1] }} · Stok tersedia</p><div class="product-bottom"><span>{{ $p[2] }}</span><span class="badge green">Aktif</span></div></article>
@endforeach
</div></div></div>
@endsection
