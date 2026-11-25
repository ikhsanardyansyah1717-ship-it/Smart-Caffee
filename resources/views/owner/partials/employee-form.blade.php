@once
@push('styles')
<style>
.employee-form-panel{width:100%;max-width:none;padding:28px}.employee-form{display:grid;gap:19px}.employee-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px 20px}.employee-field{display:grid;align-content:start;gap:7px;min-width:0}.employee-field label{color:#493524;font-size:12px;font-weight:700}.employee-field input,.employee-field select{width:100%;min-height:47px;box-sizing:border-box;padding:0 13px;border:1px solid #e6d9c9!important;border-radius:11px!important;background:#fff!important;color:#2c2118!important;font:500 13px Inter,'Segoe UI',sans-serif!important;box-shadow:none!important;appearance:auto}.employee-field input::placeholder{color:#aa9987}.employee-field input:focus,.employee-field select:focus{outline:0!important;border-color:#b7834e!important;background:#fffefa!important;box-shadow:0 0 0 4px rgba(183,131,78,.14)!important}.employee-field small{color:#9b8068;font-size:10px;line-height:1.45}.employee-field .field-error{color:#b44434;font-size:11px}.employee-account-note{display:flex;align-items:flex-start;gap:11px;padding:13px 14px;border:1px solid #eadcc8;border-radius:13px;background:#fffaf3;color:#80521f;font-size:11px;line-height:1.55}.employee-account-note i{margin-top:2px}.employee-form-actions{display:flex;gap:10px;flex-wrap:wrap;padding-top:3px}.employee-form-actions .btn{min-width:165px}.employee-credential-card{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:16px 18px;border:1px solid #d8eadc;border-radius:15px;background:linear-gradient(135deg,#f5fbf5,#fff);color:#2d5838}.employee-credential-main{display:grid;gap:8px}.employee-credential-main strong{font-size:13px}.employee-credential-list{display:flex;gap:8px 18px;flex-wrap:wrap;font-size:12px}.employee-credential-list span{display:grid;gap:3px}.employee-credential-list small{color:#718574;font-size:10px}.employee-credential-list code{font:700 12px ui-monospace,Consolas,monospace;color:#273d2c;user-select:all}.employee-copy-credentials{flex:0 0 auto;min-height:39px;padding:0 12px;border:1px solid #c5dcc9;border-radius:10px;background:#fff;color:#386044;font-size:11px;font-weight:700;cursor:pointer}
.employee-phone-input{display:flex;align-items:center;min-height:47px;border:1px solid #e6d9c9;border-radius:11px;background:#fff;overflow:hidden}.employee-phone-input:focus-within{border-color:#b7834e;box-shadow:0 0 0 4px rgba(183,131,78,.14);background:#fffefa}.employee-phone-input .country-code{padding:0 0 0 13px;color:#75440b;font:700 13px Inter,'Segoe UI',sans-serif}.employee-phone-input input{min-width:0;border:0!important;box-shadow:none!important;background:transparent!important}.employee-phone-input input:focus{outline:0!important}
@media(max-width:620px){.employee-form-panel{padding:19px}.employee-form-grid{grid-template-columns:1fr;gap:14px}.employee-credential-card{align-items:stretch;flex-direction:column}.employee-copy-credentials{width:100%}.employee-form-actions .btn{flex:1}}
</style>
@endpush
@endonce

@php
    $editing = isset($employee);
    $phoneDigits = preg_replace('/\D+/', '', (string) old('phone', $editing ? $employee->phone : ''));
    $phoneDigits = preg_replace('/^62/', '', $phoneDigits);
    $phoneDigits = substr(ltrim($phoneDigits, '0'), 0, 10);
    $phoneValue = '+62' . $phoneDigits;
@endphp
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
            <div class="employee-phone-input"><span class="country-code" aria-hidden="true">+62</span><input id="employee-phone-local" type="tel" value="{{ $phoneDigits }}" required maxlength="10" inputmode="numeric" pattern="[0-9]{1,10}" title="Maksimal 12 angka termasuk kode negara +62" autocomplete="tel"><input id="employee-phone" type="hidden" name="phone" value="{{ $phoneValue }}"></div>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phone = document.getElementById('employee-phone-local');
    const phoneFull = document.getElementById('employee-phone');
    if (!phone || !phoneFull) return;
    const syncPhone = function () {
        let digits = phone.value.replace(/\D/g, '').replace(/^62/, '').replace(/^0+/, '').slice(0, 10);
        phone.value = digits;
        phoneFull.value = '+62' + digits;
    };
    phone.addEventListener('input', syncPhone);
    phone.closest('form')?.addEventListener('submit', syncPhone);
    phone.addEventListener('focus', function () {
        const end = this.value.length;
        this.setSelectionRange(end, end);
    });
});
</script>
