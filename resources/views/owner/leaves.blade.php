@extends('layouts.owner')
@section('title', 'Pengajuan Cuti - Quattro Coffee')
@push('styles')
<style>
.owner-leaves{display:grid;gap:22px;padding-bottom:28px}.owner-leaves .leave-table-wrap{overflow-x:auto;border:1px solid #eee4d8;border-radius:15px}.owner-leaves table{width:100%;min-width:850px;border-collapse:collapse}.owner-leaves th,.owner-leaves td{padding:14px 13px;border-bottom:1px solid #f0e7dd;text-align:left;font-size:12px;vertical-align:top}.owner-leaves th{background:#fdfaf6;color:#947b62;font-size:10px;text-transform:uppercase;letter-spacing:.06em}.owner-leaves tbody tr:last-child td{border-bottom:0}.owner-leaves .leave-state{display:inline-flex;padding:6px 10px;border-radius:999px;background:#fff4df;color:#a46a16;font-size:10px;font-weight:800}.owner-leaves .leave-state.approved{background:#eaf7f0;color:#168653}.owner-leaves .leave-state.rejected{background:#fff0ee;color:#b34b3e}.owner-leaves .leave-review-form{display:grid;gap:7px;min-width:175px}.owner-leaves .leave-review-form textarea{min-height:54px;padding:8px;border:1px solid #e6d9c9;border-radius:9px;resize:vertical}.owner-leaves .leave-review-actions{display:flex;gap:6px}.owner-leaves .leave-review-actions button{padding:7px 9px;font-size:10px}.owner-leaves .leave-alert{padding:12px 15px;border-radius:11px;background:#eaf7ef;color:#267245;font-size:12px}.owner-leaves .leave-alert.error{background:#fff0ee;color:#ae443b}
</style>
@endpush
@section('content')
<div class="grid owner-leaves">
    <header class="page-head"><div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Pengajuan Cuti</h1><p>Tinjau permintaan cuti dan catatan karyawan.</p></div></header>
    @if(session('success'))<div class="leave-alert">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="leave-alert error">{{ session('error') }}</div>@endif
    <section class="panel">
        <div class="panel-head"><div><h2>Daftar pengajuan</h2><p>Pengajuan yang disetujui otomatis membatasi akses selama tanggal cuti.</p></div></div>
        <div class="leave-table-wrap"><table><thead><tr><th>Karyawan</th><th>Posisi / Shift</th><th>Periode</th><th>Alasan</th><th>Status</th><th>Catatan / Tindakan</th></tr></thead><tbody>
        @forelse($leaveRequests as $leave)
            @php($displayStatus = $leave->status === 'Disetujui' && $leave->end_date->lt(today()) ? 'Selesai' : $leave->status)
            <tr>
                <td><strong>{{ $leave->employee?->name ?? 'Karyawan dihapus' }}</strong><br><small>{{ $leave->employee?->user?->username ?? '—' }}</small></td>
                <td>{{ $leave->employee?->position ?? '—' }}<br><small>{{ $leave->employee?->shift ?? '—' }}</small></td>
                <td>{{ $leave->start_date->translatedFormat('d M Y') }}<br>s/d {{ $leave->end_date->translatedFormat('d M Y') }}</td>
                <td>{{ $leave->reason }}</td>
                <td><span class="leave-state {{ $displayStatus === 'Disetujui' ? 'approved' : ($displayStatus === 'Ditolak' ? 'rejected' : '') }}">{{ $displayStatus }}</span></td>
                <td>
                    @if($leave->status === 'Pending')
                    <form method="POST" action="{{ route('owner.leaves.review', $leave) }}" class="leave-review-form">@csrf @method('PATCH')<textarea name="owner_note" maxlength="1000" placeholder="Catatan Owner (opsional)"></textarea><div class="leave-review-actions"><button class="btn btn-primary" name="decision" value="Disetujui">Setujui</button><button class="btn btn-light" name="decision" value="Ditolak">Tolak</button></div></form>
                    @else
                    {{ $leave->owner_note ?: '—' }}<br><small>{{ $leave->reviewer?->name ? 'Diproses oleh ' . $leave->reviewer->name : '' }}</small>
                    @endif
                </td>
            </tr>
        @empty<tr><td colspan="6" style="text-align:center;color:#9a8068;padding:25px">Belum ada pengajuan cuti.</td></tr>@endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
