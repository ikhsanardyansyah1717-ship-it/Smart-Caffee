@extends('layouts.owner')
@section('title','Tambah Karyawan - Quattro Coffee')
@section('content')
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">TEAM MANAGEMENT</span><h1>Tambah Karyawan</h1><p>Tambahkan data staf baru ke Quattro Coffee.</p></div>
    <a class="btn btn-light" href="{{ route('owner.employees.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</header>
<div class="panel" style="max-width:640px;">
    <form method="POST" action="{{ route('owner.employees.store') }}">
        @csrf
        <div style="margin-bottom:16px;">
            <label for="name" style="display:block;margin-bottom:6px;font-weight:600;">Nama Karyawan</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Dina Lestari" style="width:100%;">
            @error('name')
                <small style="color:#d9534f;">{{ $message }}</small>
            @enderror
        </div>
        <div style="margin-bottom:16px;">
            <label for="position" style="display:block;margin-bottom:6px;font-weight:600;">Posisi</label>
            <select id="position" name="position" style="width:100%;">
                <option value="">-- Pilih Posisi --</option>
                <option value="Admin" {{ old('position') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Kasir" {{ old('position') == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="Kitchen" {{ old('position') == 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
            </select>
            @error('position')
                <small style="color:#d9534f;">{{ $message }}</small>
            @enderror
        </div>
        <div style="margin-bottom:16px;">
            <label for="shift" style="display:block;margin-bottom:6px;font-weight:600;">Shift</label>
            <select id="shift" name="shift" style="width:100%;">
                <option value="">-- Pilih Shift --</option>
                <option value="Pagi" {{ old('shift') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                <option value="Siang" {{ old('shift') == 'Siang' ? 'selected' : '' }}>Siang</option>
            </select>
            @error('shift')
                <small style="color:#d9534f;">{{ $message }}</small>
            @enderror
        </div>
        <div style="margin-bottom:16px;">
            <label for="status" style="display:block;margin-bottom:6px;font-weight:600;">Status</label>
            <select id="status" name="status" style="width:100%;">
                <option value="">-- Pilih Status --</option>
                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ old('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
            </select>
            @error('status')
                <small style="color:#d9534f;">{{ $message }}</small>
            @enderror
        </div>
        <div style="margin-bottom:16px;">
            <label for="phone" style="display:block;margin-bottom:6px;font-weight:600;">Nomor Kontak</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 0812xxxxxxx" style="width:100%;">
            @error('phone')
                <small style="color:#d9534f;">{{ $message }}</small>
            @enderror
        </div>
        <div class="pf-actions">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Karyawan</button>
            <a href="{{ route('owner.employees.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
</div>
</div>
@endsection