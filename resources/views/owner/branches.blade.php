@extends('layouts.owner')

@section('title', 'Cabang Quattro Coffee')

@section('content')
<div class="page-header">
    <div>
        <span class="eyebrow">QUATTRO COFFEE</span>
        <h1>Daftar Cabang</h1>
        <p>Pemantauan data cabang yang terdaftar.</p>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div>
            <h2>Semua Cabang</h2>
            <p>{{ $branches->count() }} cabang terdaftar</p>
        </div>
    </div>

    <div class="branch-list">
        @forelse($branches as $branch)
            <article class="branch-card">
                <div class="branch-icon"><i class="fa-solid fa-store"></i></div>
                <div class="branch-detail">
                    <strong>Quattro Coffee {{ $branch->name }}</strong>
                    <small>{{ $branch->code }}</small>
                    <p>{{ $branch->address }}</p>
                    @if($branch->latitude && $branch->longitude)
                        <small>GPS: {{ $branch->latitude }}, {{ $branch->longitude }}</small>
                    @endif
                </div>
                <span class="branch-status">{{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </article>
        @empty
            <p>Belum ada cabang.</p>
        @endforelse
    </div>
</div>

<style>
.branch-list{display:grid;gap:12px}.branch-card{display:flex;align-items:flex-start;gap:14px;padding:16px;border:1px solid #eadfd3;border-radius:14px;background:#fff}.branch-icon{width:42px;height:42px;border-radius:12px;background:#f4e9dc;color:#763f06;display:grid;place-items:center}.branch-detail{flex:1}.branch-detail strong,.branch-detail small{display:block}.branch-detail small{color:#8c7a6b;font-size:12px}.branch-detail p{margin:7px 0;font-size:13px}.branch-status{font-size:11px;color:#287849;background:#e8f5ec;padding:5px 9px;border-radius:20px}
</style>
@endsection
