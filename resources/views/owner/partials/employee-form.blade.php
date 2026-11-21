@once
@push('styles')
<style>
.employee-form-panel{max-width:850px;padding:28px}.employee-form{display:grid;gap:19px}.employee-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px 20px}.employee-field{display:grid;align-content:start;gap:7px;min-width:0}.employee-field label{color:#493524;font-size:12px;font-weight:700}.employee-field input,.employee-field select{width:100%;min-height:47px;box-sizing:border-box;padding:0 13px;border:1px solid #e6d9c9!important;border-radius:11px!important;background:#fff!important;color:#2c2118!important;font:500 13px Inter,'Segoe UI',sans-serif!important;box-shadow:none!important;appearance:auto}.employee-field input::placeholder{color:#aa9987}.employee-field input:focus,.employee-field select:focus{outline:0!important;border-color:#b7834e!important;background:#fffefa!important;box-shadow:0 0 0 4px rgba(183,131,78,.14)!important}.employee-field small{color:#9b8068;font-size:10px;line-height:1.45}.employee-field .field-error{color:#b44434;font-size:11px}.employee-account-note{display:flex;align-items:flex-start;gap:11px;padding:13px 14px;border:1px solid #eadcc8;border-radius:13px;background:#fffaf3;color:#80521f;font-size:11px;line-height:1.55}.employee-account-note i{margin-top:2px}.employee-form-actions{display:flex;gap:10px;flex-wrap:wrap;padding-top:3px}.employee-form-actions .btn{min-width:165px}.employee-credential-card{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:16px 18px;border:1px solid #d8eadc;border-radius:15px;background:linear-gradient(135deg,#f5fbf5,#fff);color:#2d5838}.employee-credential-main{display:grid;gap:8px}.employee-credential-main strong{font-size:13px}.employee-credential-list{display:flex;gap:8px 18px;flex-wrap:wrap;font-size:12px}.employee-credential-list span{display:grid;gap:3px}.employee-credential-list small{color:#718574;font-size:10px}.employee-credential-list code{font:700 12px ui-monospace,Consolas,monospace;color:#273d2c;user-select:all}.employee-copy-credentials{flex:0 0 auto;min-height:39px;padding:0 12px;border:1px solid #c5dcc9;border-radius:10px;background:#fff;color:#386044;font-size:11px;font-weight:700;cursor:pointer}
@media(max-width:620px){.employee-form-panel{padding:19px}.employee-form-grid{grid-template-columns:1fr;gap:14px}.employee-credential-card{align-items:stretch;flex-direction:column}.employee-copy-credentials{width:100%}.employee-form-actions .btn{flex:1}}
</style>
@endpush
@endonce

@php($editing = isset($employee))
<form method="POST" action="{{ $editing ? route('owner.employees.update', $employee) : route('owner.employees.store') }}" class="employee-form">
    @csrf
    @if($editing) @method('PUT') @endif
    <div class="employee-form-grid">
        <div class="employee-field">
            <label for="employee-name">Nama karyawan</label>
            <input id="employee-name" type="text" name="name" value="{{ old('name', $editing ? $employee->name : '') }}" placeholder="Contoh: Dina Lestari" required maxlength="255" autocomplete="name">
            @error('name')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="employee-field">
            <label for="employee-email">Email untuk login</label>
            <input id="employee-email" type="email" name="email" value="{{ old('email', $editing ? ($employee->user?->email ?? '') : '') }}" placeholder="nama@email.com" required maxlength="255" autocomplete="email">
            @error('email')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="employee-field">
            <label for="employee-phone">Nomor HP</label>
            <input id="employee-phone" type="tel" name="phone" value="{{ old('phone', $editing ? $employee->phone : '') }}" placeholder="08xxxxxxxxxx" required maxlength="20" autocomplete="tel">
            @error('phone')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="employee-field">
            <label for="employee-position">Posisi</label>
            <select id="employee-position" name="position" required>
                <option value="">Pilih posisi</option>
                <option value="Kasir" {{ old('position', $editing ? ($employee->position === 'Admin' ? '' : $employee->position) : '') === 'Kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="Kitchen" {{ old('position', $editing ? ($employee->position === 'Admin' ? '' : $employee->position) : '') === 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
            </select>
            @error('position')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="employee-field">
            <label for="employee-shift">Shift</label>
            <select id="employee-shift" name="shift" required>
                <option value="">Pilih shift</option>
                <option value="Pagi" {{ old('shift', $editing ? $employee->shift : '') === 'Pagi' ? 'selected' : '' }}>Pagi</option>
                <option value="Siang" {{ old('shift', $editing ? $employee->shift : '') === 'Siang' ? 'selected' : '' }}>Siang</option>
                <option value="Malam" {{ old('shift', $editing ? $employee->shift : '') === 'Malam' ? 'selected' : '' }}>Malam</option>
            </select>
            @error('shift')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="employee-field">
            <label for="employee-status">Status kerja</label>
            <select id="employee-status" name="status" required>
                <option value="Aktif" {{ old('status', $editing ? $employee->status : 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ old('status', $editing ? $employee->status : '') === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="Nonaktif" {{ old('status', $editing ? $employee->status : '') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')<small class="field-error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="employee-account-note"><i class="fa-solid fa-key"></i><span>Kata sandi awal mengikuti posisi: Kasir memakai kasir123 dan Kitchen memakai kitchen123. Username dibuat otomatis.</span></div>

    <div class="employee-form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editing ? 'Simpan Perubahan' : 'Buat Akun & Simpan' }}</button>
        <a href="{{ route('owner.employees.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
