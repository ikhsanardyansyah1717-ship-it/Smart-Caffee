@extends('layouts.owner')
@section('title','Karyawan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Karyawan</h1><p>Kelola staf dan akses operasional Quattro Coffee.</p></div>
    <a class="btn btn-primary" href="{{ route('owner.employees.create') }}"><i class="fa-solid fa-user-plus"></i> Tambah Karyawan</a>
</header>

@if (session('success'))
<div class="alert alert-success" style="background:#e9f9ee;color:#1e8a4c;padding:12px 16px;border-radius:10px;margin-bottom:16px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="stats">
    <article class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div><div class="stat-label">Total Karyawan</div><div class="stat-value">{{ $totalEmployees }}</div></div>
    </article>
    <article class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-user-check"></i></div>
        <div><div class="stat-label">Sedang Aktif</div><div class="stat-value">{{ $activeEmployees }}</div></div>
    </article>
    <article class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-user-clock"></i></div>
        <div><div class="stat-label">Shift Hari Ini</div><div class="stat-value">{{ $shiftToday }}</div></div>
    </article>
    <article class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-calendar-xmark"></i></div>
        <div><div class="stat-label">Izin / Cuti</div><div class="stat-value">{{ $onLeave }}</div></div>
    </article>
</div>

<div class="panel">
    <div class="panel-head">
        <div><h2>Daftar Karyawan</h2><p>Data staf dan posisi.</p></div>
        <label class="search" style="max-width:280px">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input data-search="#employeeRows tr" placeholder="Cari karyawan...">
        </label>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Posisi</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="employeeRows">
            @forelse($employees as $employee)
                <tr>
                    <td><strong>{{ $employee->name }}</strong></td>
                    <td>{{ $employee->position }}</td>
                    <td>{{ $employee->shift }}</td>
                    <td>
                        <span class="badge {{ $employee->status == 'Aktif' ? 'green' : '' }}">
                            {{ $employee->status }}
                        </span>
                    </td>
                    <td>{{ $employee->phone ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('owner.employees.edit', $employee) }}" class="btn btn-light">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('owner.employees.destroy', $employee) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus karyawan {{ $employee->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light" style="color:#d9534f;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:24px;text-align:center;color:#888;">
                        Belum ada data karyawan. Klik "Tambah Karyawan" untuk menambahkan data baru.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection