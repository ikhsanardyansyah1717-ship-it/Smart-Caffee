@extends('layouts.owner')
@section('title', 'Daftar Gaji - Quattro Coffee')
@push('styles')
<style>
.salary-page{display:grid;gap:22px;padding-bottom:28px}.salary-alert{padding:13px 16px;border-radius:12px;background:#eaf7ef;color:#267245;font-size:13px}.salary-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.salary-card{display:flex;align-items:center;gap:14px;padding:19px;background:#fff;border:1px solid #eadfd1;border-radius:18px;box-shadow:0 8px 28px rgba(91,57,25,.05)}.salary-icon{display:grid;place-items:center;width:46px;height:46px;flex:0 0 auto;border-radius:14px;background:#f4e8da;color:#763f06;font-size:18px}.salary-icon.green{background:#e7f5ed;color:#23945e}.salary-icon.gold{background:#fff1dc;color:#c27c16}.salary-label{color:#927b65;font-size:12px}.salary-value{margin-top:5px;color:#30251d;font-size:20px;font-weight:800}.salary-table-wrap{overflow-x:auto;border:1px solid #eee4d8;border-radius:15px}.salary-table{width:100%;min-width:1000px;border-collapse:collapse}.salary-table th,.salary-table td{padding:13px 12px;border-bottom:1px solid #f0e7dd;text-align:left;vertical-align:middle;font-size:12px}.salary-table th{background:#fdfaf6;color:#947b62;font-size:10px;text-transform:uppercase;letter-spacing:.06em}.salary-table tbody tr:last-child td{border-bottom:0}.salary-employee strong{display:block;color:#30251d}.salary-employee small,.salary-table td small{display:block;margin-top:4px;color:#927b65;font-size:10px}.salary-edit{display:grid;grid-template-columns:minmax(150px,.9fr) minmax(170px,1.2fr) auto;gap:8px;align-items:center}.salary-edit input{width:100%;min-height:38px;padding:0 10px;border:1px solid #e7d9c9;border-radius:9px;background:#fff;color:#30251d;font-size:12px}.salary-edit button{min-height:38px;padding:0 12px;border:1px solid #763f06;border-radius:9px;background:#763f06;color:#fff;font-size:11px;font-weight:700;white-space:nowrap}.salary-edit button:hover{background:#572e04}.salary-amount{font-weight:800;color:#713f12;white-space:nowrap}.salary-note{margin:0;color:#8b7562;font-size:12px;line-height:1.6}@media(max-width:900px){.salary-summary{grid-template-columns:1fr 1fr}.salary-edit{grid-template-columns:1fr 1fr}.salary-edit button{grid-column:1/-1}}@media(max-width:560px){.salary-summary{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
@php($payableEmployees = $employees->filter(fn ($employee) => (float) ($employee->salary?->amount ?? 0) > 0))
<div class="grid salary-page">
    <header class="page-head">
        <div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Daftar Gaji</h1><p>Kelola nominal gaji bulanan karyawan Quattro Coffee.</p></div>
    </header>

    @if(session('success'))<div class="salary-alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if($errors->any())<div class="salary-alert" style="background:#fff0ee;color:#ae443b">{{ $errors->first() }}</div>@endif

    <section class="salary-summary">
        <article class="salary-card"><div class="salary-icon"><i class="fa-solid fa-money-bill-wave"></i></div><div><div class="salary-label">Total gaji bulanan</div><div class="salary-value">Rp {{ number_format($totalMonthlyPayroll, 0, ',', '.') }}</div></div></article>
        <article class="salary-card"><div class="salary-icon green"><i class="fa-solid fa-user-check"></i></div><div><div class="salary-label">Gaji sudah diatur</div><div class="salary-value">{{ $employeesWithSalary }} / {{ $employees->count() }} karyawan</div></div></article>
        <article class="salary-card"><div class="salary-icon gold"><i class="fa-solid fa-chart-simple"></i></div><div><div class="salary-label">Rata-rata gaji terdata</div><div class="salary-value">Rp {{ number_format($averageSalary, 0, ',', '.') }}</div></div></article>
    </section>

    <section class="panel">
        <div class="panel-head"><div><h2>Gaji Karyawan</h2><p>Ubah nominal dan catatan lalu simpan per karyawan.</p></div></div>
        <p class="salary-note" style="margin:-5px 0 16px">Nominal disimpan sebagai data internal Owner. Total bulanan menjumlahkan gaji yang sudah diatur.</p>
        <div class="salary-table-wrap">
            <table class="salary-table">
                <thead><tr><th>Karyawan</th><th>Posisi / Shift</th><th>Gaji saat ini</th><th>Perbarui data gaji</th></tr></thead>
                <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td class="salary-employee"><strong>{{ $employee->name }}</strong><small>{{ $employee->user?->username ?? $employee->user?->email ?? 'Akun belum terhubung' }}</small></td>
                        <td>{{ $employee->position }}<small>{{ $employee->shift }} · {{ $employee->status }}</small></td>
                        <td><span class="salary-amount">Rp {{ number_format((float) ($employee->salary?->amount ?? 0), 0, ',', '.') }}</span><small>{{ $employee->salary?->effective_date ? 'Berlaku ' . $employee->salary->effective_date->translatedFormat('d M Y') : 'Belum ditentukan' }}</small></td>
                        <td>
                            <form class="salary-edit" method="POST" action="{{ route('owner.salaries.update', $employee) }}">
                                @csrf @method('PUT')
                                @php($salaryInputAmount = old('amount', (float) ($employee->salary?->amount ?? 0)))
                                <input type="text" inputmode="numeric" data-salary-amount-display required value="{{ number_format((float) $salaryInputAmount, 0, ',', '.') }}" aria-label="Gaji bulanan {{ $employee->name }}" placeholder="Nominal Rp">
                                <input type="hidden" name="amount" data-salary-amount-value value="{{ $salaryInputAmount }}">
                                <input type="text" name="notes" maxlength="2000" value="{{ old('notes', $employee->salary?->notes) }}" aria-label="Catatan gaji {{ $employee->name }}" placeholder="Catatan (opsional)">
                                <button type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:28px;text-align:center;color:#927b65">Belum ada karyawan. Tambahkan karyawan terlebih dahulu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head"><div><h2>Catat Pembayaran Gaji</h2><p>Pembayaran yang disimpan otomatis masuk ke pengeluaran Catatan Keuangan dan laporan.</p></div><span class="salary-icon green"><i class="fa-solid fa-arrow-right-arrow-left"></i></span></div>
        @if($payableEmployees->isNotEmpty())
            <form method="POST" action="{{ route('owner.salaries.payments.store') }}" class="salary-payment-form">
                @csrf
                <label>Karyawan
                    <select name="employee_id" required>
                        <option value="">Pilih karyawan</option>
                        @foreach($payableEmployees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} — Rp {{ number_format((float) $employee->salary->amount, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Periode gaji
                    <input type="month" name="period" required value="{{ old('period', now()->format('Y-m')) }}">
                </label>
                <label>Tanggal dibayar
                    <input type="date" name="paid_on" required value="{{ old('paid_on', now()->toDateString()) }}">
                </label>
                <label>Catatan (opsional)
                    <input type="text" name="notes" maxlength="1000" value="{{ old('notes') }}" placeholder="Contoh: Transfer bank">
                </label>
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-circle-check"></i> Catat sebagai dibayar</button>
            </form>
            <p class="salary-note" style="margin-top:12px">Nominal yang dicatat mengikuti gaji saat ini pada daftar di atas. Satu karyawan hanya dapat dicatat satu kali untuk setiap periode.</p>
        @else
            <p class="salary-note">Atur nominal gaji lebih dari Rp0 pada daftar karyawan sebelum mencatat pembayaran.</p>
        @endif
    </section>

    <section class="panel">
        <div class="panel-head"><div><h2>Riwayat Pembayaran</h2><p>Maksimal 100 pembayaran terbaru. Riwayat ini tersambung ke catatan pengeluaran.</p></div></div>
        <div class="salary-table-wrap">
            <table class="salary-table">
                <thead><tr><th>Karyawan</th><th>Periode</th><th>Tanggal Dibayar</th><th>Nominal</th><th>Catatan</th><th>Status Keuangan</th></tr></thead>
                <tbody>
                @forelse($salaryPayments as $payment)
                    <tr>
                        <td><strong>{{ $payment->employee?->name ?? 'Karyawan dihapus' }}</strong></td>
                        <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $payment->period)->translatedFormat('F Y') }}</td>
                        <td>{{ $payment->paid_on->translatedFormat('d M Y') }}</td>
                        <td class="salary-amount">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                        <td>{{ $payment->notes ?: '—' }}</td>
                        <td><span class="badge {{ $payment->expense_id ? 'green' : 'red' }}">{{ $payment->expense_id ? 'Masuk pengeluaran' : 'Catatan pengeluaran dihapus' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:26px;text-align:center;color:#927b65">Belum ada pembayaran gaji yang dicatat.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
<script>
document.querySelectorAll('.salary-edit').forEach(form => {
    const display = form.querySelector('[data-salary-amount-display]');
    const raw = form.querySelector('[data-salary-amount-value]');
    if (!display || !raw) return;

    const syncAmount = () => {
        const digits = display.value.replace(/\D/g, '');
        raw.value = digits;
        display.value = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : '';
    };

    display.addEventListener('input', syncAmount);
    form.addEventListener('submit', syncAmount);
});
</script>
<style>
.salary-payment-form{display:grid;grid-template-columns:repeat(4,minmax(150px,1fr));gap:12px;align-items:end}.salary-payment-form label{display:grid;gap:7px;color:#765f4d;font-size:12px;font-weight:700}.salary-payment-form input,.salary-payment-form select{width:100%;height:42px;padding:0 11px;border:1px solid #e7d9c9;border-radius:10px;background:#fff;color:#30251d}.salary-payment-form button{min-height:42px;grid-column:1/-1;justify-self:start}@media(max-width:800px){.salary-payment-form{grid-template-columns:1fr 1fr}}@media(max-width:540px){.salary-payment-form{grid-template-columns:1fr}.salary-payment-form button{width:100%}}
</style>
@endsection
