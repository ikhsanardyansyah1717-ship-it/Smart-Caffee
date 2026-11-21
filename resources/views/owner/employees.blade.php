@extends('layouts.owner')
@section('title','Karyawan - Quattro Coffee')
@push('styles')
<style>
.employee-credential-card{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 20px;border:1px solid #d8eadc;border-radius:16px;background:linear-gradient(135deg,#f2faf4,#fff);color:#2d5838;box-shadow:0 8px 24px rgba(49,91,59,.06)}
.employee-credential-main{display:grid;gap:12px;min-width:0}.employee-credential-main strong{font-size:14px}.employee-credential-list{display:flex;gap:10px 22px;flex-wrap:wrap}.employee-credential-list span{display:grid;gap:4px;min-width:130px}.employee-credential-list small{color:#718574;font-size:10px}.employee-credential-list code{font:700 12px ui-monospace,Consolas,monospace;color:#273d2c;user-select:all;overflow-wrap:anywhere}.employee-copy-credentials{flex:0 0 auto;min-height:40px;padding:0 14px;border:1px solid #c5dcc9;border-radius:10px;background:#fff;color:#386044;font-size:11px;font-weight:700;cursor:pointer;transition:background .18s ease,transform .18s ease}.employee-copy-credentials:hover{background:#f0f7f1;transform:translateY(-1px)}
@media(max-width:700px){.employee-credential-card{align-items:stretch;flex-direction:column}.employee-copy-credentials{width:100%}.employee-credential-list span{min-width:calc(50% - 12px)}}
</style>
@endpush
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

@if (session('created_account'))
    @php($createdAccount = session('created_account'))
    <section class="employee-credential-card" style="margin-bottom:18px;">
        <div class="employee-credential-main">
            <strong><i class="fa-solid fa-circle-check"></i> Akun {{ $createdAccount['name'] }} siap digunakan</strong>
            <div class="employee-credential-list">
                <span><small>Username</small><code>{{ $createdAccount['username'] }}</code></span>
                <span><small>Email</small><code>{{ $createdAccount['email'] }}</code></span>
                <span><small>Kata sandi awal</small><code>{{ $createdAccount['password'] }}</code></span>
            </div>
        </div>
        <button type="button" class="employee-copy-credentials" data-copy-credentials="{{ $createdAccount['username'] }} | {{ $createdAccount['password'] }}"><i class="fa-regular fa-copy"></i> Salin kredensial</button>
    </section>
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
                    <th>Email Login</th>
                    <th>Username</th>
                    <th>Nomor HP</th>
                    <th>Rata-rata Penyelesaian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="employeeRows">
            @forelse($employees as $employee)
                <tr>
                    <td><strong>{{ $employee->name }}</strong></td>
                    <td>{{ $employee->position === 'Admin' ? '—' : $employee->position }}</td>
                    <td>{{ $employee->shift }}</td>
                    <td>
                        <span class="badge {{ $employee->display_status == 'Aktif' ? 'green' : '' }}">
                            {{ $employee->display_status }}
                        </span>
                    </td>
                    <td>{{ $employee->user?->email ?? '—' }}</td>
                    <td>{{ $employee->user?->username ?? '—' }}</td>
                    <td>{{ $employee->phone ?? '—' }}</td>
                    <td>{{ $employee->position === 'Kitchen' ? ($employee->average_completion_minutes !== null ? $employee->average_completion_minutes . ' menit' : 'Belum ada data') : '—' }}</td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('owner.employees.edit', $employee) }}" class="btn btn-light">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if($employee->user)
                            <form action="{{ route('owner.employees.reset-password', $employee) }}" method="POST" onsubmit="return confirm('Atur ulang kata sandi {{ $employee->name }} ke kata sandi awal karyawan?');">
                                @csrf
                                <button type="submit" class="btn btn-light" title="Atur ulang kata sandi"><i class="fa-solid fa-key"></i></button>
                            </form>
                            @endif
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
                    <td colspan="9" style="padding:24px;text-align:center;color:#888;">
                        Belum ada data karyawan. Klik "Tambah Karyawan" untuk menambahkan data baru.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@if(session('created_account'))
<script>
document.querySelector('[data-copy-credentials]')?.addEventListener('click', async function () {
    try {
        await navigator.clipboard.writeText(this.dataset.copyCredentials);
        this.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin';
    } catch (error) {
        this.textContent = 'Salin manual dari kartu akun';
    }
});
</script>
@endif
@endsection
