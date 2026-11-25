@extends('layouts.owner')
@section('title','Karyawan - Quattro Coffee')
@push('styles')
<style>
.employee-credential-card{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 20px;border:1px solid #d8eadc;border-radius:16px;background:linear-gradient(135deg,#f2faf4,#fff);color:#2d5838;box-shadow:0 8px 24px rgba(49,91,59,.06)}
.employee-credential-main{display:grid;gap:12px;min-width:0}.employee-credential-main strong{font-size:14px}.employee-credential-list{display:flex;gap:10px 22px;flex-wrap:wrap}.employee-credential-list span{display:grid;gap:4px;min-width:130px}.employee-credential-list small{color:#718574;font-size:10px}.employee-credential-list code{font:700 12px ui-monospace,Consolas,monospace;color:#273d2c;user-select:all;overflow-wrap:anywhere}.employee-copy-credentials{flex:0 0 auto;min-height:40px;padding:0 14px;border:1px solid #c5dcc9;border-radius:10px;background:#fff;color:#386044;font-size:11px;font-weight:700;cursor:pointer;transition:background .18s ease,transform .18s ease}.employee-copy-credentials:hover{background:#f0f7f1;transform:translateY(-1px)}
.employee-password-dialog{width:min(440px,calc(100% - 32px));padding:0;border:1px solid #eadfd1;border-radius:18px;background:#fffdfa;color:#30251d;box-shadow:0 24px 80px rgba(45,31,18,.24)}.employee-password-dialog::backdrop{background:rgba(33,24,17,.48);backdrop-filter:blur(3px)}.employee-password-form{display:grid;gap:15px;padding:24px}.employee-password-form h2{margin:0;font-size:20px}.employee-password-form p{margin:-8px 0 2px;color:#927b65;font-size:12px}.employee-password-form label{display:grid;gap:7px;color:#493524;font-size:12px;font-weight:700}.employee-password-form input{min-height:44px;padding:0 12px;border:1px solid #e6d9c9;border-radius:10px;font:500 13px Inter,'Segoe UI',sans-serif}.employee-password-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:3px}.employee-password-error{margin:0 16px;padding:11px 13px;border-radius:10px;background:#fff0ef;color:#a52822;font-size:12px}
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
@if ($errors->has('password'))
<div class="employee-password-error">{{ $errors->first('password') }}</div>
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
        <div><h2>Daftar Karyawan</h2><p>Performa bulanan {{ $performanceMonthLabel }} · kasir menampilkan keuntungan kotor dan kitchen menampilkan rata-rata waktu penyelesaian.</p></div>
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
                    <th>Rata-rata Performa per Bulan</th>
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
                    <td>
                        @if($employee->position === 'Kasir')
                            <strong style="color:#23804a">Keuntungan Rp {{ number_format((float) ($employee->monthly_gross_profit ?? 0), 0, ',', '.') }}</strong>
                        @elseif($employee->position === 'Kitchen')
                            <strong>{{ $employee->average_completion_minutes !== null ? 'Waktu penyelesaian ' . $employee->average_completion_minutes . ' menit' : 'Belum ada data bulan ini' }}</strong>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('owner.employees.edit', $employee) }}" class="btn btn-light">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if($employee->user)
                            <button type="button" class="btn btn-light" title="Ubah kata sandi" aria-label="Ubah kata sandi {{ $employee->name }}" data-change-password data-name="{{ $employee->name }}" data-action="{{ route('owner.employees.change-password', $employee) }}"><i class="fa-solid fa-key"></i></button>
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
<dialog class="employee-password-dialog" id="employeePasswordDialog" aria-labelledby="employeePasswordTitle">
    <form class="employee-password-form" id="employeePasswordForm" method="POST">
        @csrf
        <h2 id="employeePasswordTitle">Ubah kata sandi</h2>
        <p id="employeePasswordDescription">Masukkan kata sandi baru karyawan.</p>
        <label>Kata sandi baru<input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
        <label>Ulangi kata sandi<input type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></label>
        <div class="employee-password-actions">
            <button type="button" class="btn btn-light" data-close-password-dialog>Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-key"></i> Simpan kata sandi</button>
        </div>
    </form>
</dialog>
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
<script>
const employeePasswordDialog = document.getElementById('employeePasswordDialog');
document.querySelectorAll('[data-change-password]').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('employeePasswordForm').action = button.dataset.action;
        document.getElementById('employeePasswordDescription').textContent = `Atur kata sandi baru untuk ${button.dataset.name}. Minimal 8 karakter.`;
        employeePasswordDialog.showModal();
    });
});
document.querySelector('[data-close-password-dialog]')?.addEventListener('click', () => employeePasswordDialog.close());
employeePasswordDialog?.addEventListener('click', event => {
    if (event.target === employeePasswordDialog) employeePasswordDialog.close();
});
</script>
@endsection
