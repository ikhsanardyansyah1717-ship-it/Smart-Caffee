@extends('layouts.owner')
@section('title','Tambah Pelanggan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">CUSTOMER MANAGEMENT</span><h1>Tambah Pelanggan</h1><p>Buat akun pelanggan baru secara manual.</p></div>
    <a class="btn btn-light" href="{{ route('owner.customers.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</header>
<div class="panel" style="max-width:640px;">
    <form method="POST" action="{{ route('owner.customers.store') }}">
        @csrf
        @include('owner.customers-form')
        <div style="display:flex;gap:10px;margin-top:24px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Pelanggan</button>
            <a href="{{ route('owner.customers.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
</div>
</div>
@endsection