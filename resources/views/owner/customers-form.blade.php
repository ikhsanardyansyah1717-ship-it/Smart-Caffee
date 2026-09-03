<style>
.pf-group{margin-bottom:18px;}
.pf-group label{display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;}
.pf-group input{width:100%;padding:10px 12px;border:1px solid #e2e2e2;border-radius:10px;font-size:14px;background:#fafafa;}
.pf-group input:focus{outline:none;border-color:#b08a5a;background:#fff;}
.pf-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.pf-error{color:#d9534f;font-size:12px;margin-top:4px;}
</style>
<div class="pf-group">
    <label for="name">Nama Lengkap</label>
    <input type="text" id="name" name="name" value="{{ old('name', $customer->name ?? '') }}" placeholder="Contoh: Andi Pratama" required>
    @error('name') <div class="pf-error">{{ $message }}</div> @enderror
</div>
<div class="pf-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $customer->email ?? '') }}" placeholder="nama@email.com" required>
    @error('email') <div class="pf-error">{{ $message }}</div> @enderror
</div>
<div class="pf-row">
    <div class="pf-group">
        <label for="password">{{ isset($customer) ? 'Password Baru (opsional)' : 'Password' }}</label>
        <input type="password" id="password" name="password" placeholder="{{ isset($customer) ? 'Kosongkan jika tidak ganti' : 'Minimal 8 karakter' }}" {{ isset($customer) ? '' : 'required' }}>
        @error('password') <div class="pf-error">{{ $message }}</div> @enderror
    </div>
    <div class="pf-group">
        <label for="password_confirmation">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password">
    </div>
</div>