@extends('layouts.owner')

@section('title', 'Edit Karyawan - Quattro Coffee')

@section('content')
<div class="grid">
    <header class="page-head">
        <div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Edit Karyawan</h1><p>Perbarui data dan hak akses {{ $employee->name }}.</p></div>
        <a class="btn btn-light" href="{{ route('owner.employees.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </header>
    <section class="panel employee-form-panel">
        @include('owner.partials.employee-form', ['employee' => $employee])
    </section>
</div>
@endsection
