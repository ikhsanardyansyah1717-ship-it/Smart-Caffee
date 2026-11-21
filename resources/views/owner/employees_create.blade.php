@extends('layouts.owner')

@section('title', 'Tambah Karyawan - Quattro Coffee')

@section('content')
<div class="grid">
    <header class="page-head">
        <div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Tambah Karyawan</h1><p>Buat data staf sekaligus akun login sesuai posisi.</p></div>
        <a class="btn btn-light" href="{{ route('owner.employees.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </header>
    <section class="panel employee-form-panel">
        @include('owner.partials.employee-form')
    </section>
</div>
@endsection
