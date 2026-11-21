@extends('layouts.owner')

@section('title', 'Pengaturan - Quattro Coffee')

@section('content')
<div class="grid">
    <header class="page-head">
        <div><span class="eyebrow">PREFERENSI WEBSITE</span><h1>Pengaturan</h1><p>Kelola rekening transfer dan kebijakan privasi customer.</p></div>
    </header>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="settings-cards">
        <section class="panel settings-card bank-settings-panel">
            <div class="settings-card-heading">
                <span class="settings-card-icon bank-icon"><i class="fa-solid fa-building-columns"></i></span>
                <div><span class="settings-card-kicker">PEMBAYARAN</span><h2>Rekening transfer</h2><p>Rekening ini akan ditampilkan kepada customer saat checkout.</p></div>
            </div>
            <form method="POST" action="{{ route('owner.settings.update') }}" class="bank-settings-form">
                @csrf
                @method('PUT')
                <label>Nama bank<input name="bank_name" value="{{ old('bank_name', $bank['bank_name']) }}" placeholder="Contoh: BCA" required maxlength="80">@error('bank_name')<small>{{ $message }}</small>@enderror</label>
                <label>Nomor rekening<input name="bank_account_number" value="{{ old('bank_account_number', $bank['bank_account_number']) }}" placeholder="Masukkan nomor rekening" required maxlength="40" inputmode="numeric">@error('bank_account_number')<small>{{ $message }}</small>@enderror</label>
                <label>Nama pemilik rekening<input name="bank_account_holder" value="{{ old('bank_account_holder', $bank['bank_account_holder']) }}" placeholder="Sesuai nama di rekening" required maxlength="120">@error('bank_account_holder')<small>{{ $message }}</small>@enderror</label>
                <button class="settings-save-button" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan rekening</button>
            </form>
        </section>

        <section class="panel settings-card privacy-settings-panel">
            <div class="settings-card-heading">
                <span class="settings-card-icon privacy-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <div><span class="settings-card-kicker">PRIVASI CUSTOMER</span><h2>Privacy Policy</h2><p>Atur informasi privasi yang dibaca customer di website.</p></div>
            </div>
            <form method="POST" action="{{ route('owner.settings.privacy-policy.update') }}" class="bank-settings-form privacy-form">
                @csrf
                @method('PUT')
                <label for="privacy-policy">Isi kebijakan privasi</label>
                <textarea id="privacy-policy" name="privacy_policy" rows="15" required maxlength="20000" placeholder="Tuliskan kebijakan privasi Quattro Coffee">{{ old('privacy_policy', $privacyPolicy) }}</textarea>
                @error('privacy_policy')<small class="field-error">{{ $message }}</small>@enderror
                <div class="privacy-editor-footer"><small>Pisahkan paragraf dengan baris kosong agar mudah dibaca.</small><span><b id="privacy-char-count">0</b> / 20.000</span></div>
                <button class="settings-save-button" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Privacy Policy</button>
            </form>
        </section>
    </div>
</div>
<style>
.settings-cards{display:grid;grid-template-columns:minmax(320px,.85fr) minmax(420px,1.15fr);align-items:stretch;gap:22px}.settings-card{min-width:0;padding:clamp(20px,2.5vw,32px);border:1px solid #ecdfd1;border-radius:20px;background:linear-gradient(145deg,#fff 0%,#fffdf9 100%);box-shadow:0 12px 32px rgba(74,45,17,.055)}
.settings-card-heading{display:flex;align-items:flex-start;gap:14px;min-height:72px;margin-bottom:24px}.settings-card-icon{display:grid;place-items:center;flex:0 0 48px;height:48px;border-radius:15px;font-size:17px}.bank-icon{background:#f5eadb;color:#81500f}.privacy-icon{background:#f7e8e3;color:#b85c43}.settings-card-kicker{display:block;margin:1px 0 5px;color:#b85c43;font-size:9px;font-weight:800;letter-spacing:1.25px}.settings-card-heading h2{margin:0;color:#211a14;font-size:20px;letter-spacing:-.4px}.settings-card-heading p{margin:6px 0 0;color:#927b65;font-size:12px;line-height:1.55}.bank-settings-form{display:grid;gap:17px}.bank-settings-form label{display:grid;gap:8px;color:#493524;font-size:12px;font-weight:700}.bank-settings-form input,.bank-settings-form textarea{width:100%;box-sizing:border-box;border:1px solid #e6d9c9;border-radius:12px;background:#fff;color:#2c2118;font:500 13px Inter,Arial,sans-serif;transition:border-color .18s,box-shadow .18s,background .18s}.bank-settings-form input{min-height:48px;padding:0 14px}.bank-settings-form textarea{min-height:310px;padding:15px;line-height:1.7;resize:vertical}.bank-settings-form input::placeholder,.bank-settings-form textarea::placeholder{color:#aa9987;font-weight:400}.bank-settings-form input:focus,.bank-settings-form textarea:focus{outline:0;border-color:#b7834e;background:#fffefa;box-shadow:0 0 0 4px rgba(183,131,78,.14)}.field-error{color:#b44434;font-size:11px}.settings-save-button{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:46px;padding:0 20px;border:0;border-radius:12px;background:linear-gradient(135deg,#824608,#6d3700);color:#fff;font-size:12px;font-weight:800;cursor:pointer;box-shadow:0 5px 12px rgba(109,55,0,.15);transition:transform .18s,box-shadow .18s}.settings-save-button:hover{transform:translateY(-1px);box-shadow:0 8px 16px rgba(109,55,0,.2)}.privacy-editor-footer{display:flex;justify-content:space-between;gap:12px;color:#927b65;font-size:10px}.privacy-editor-footer span{flex:0 0 auto;font-variant-numeric:tabular-nums}.privacy-editor-footer b{color:#71420e}
@media(max-width:1050px){.settings-cards{grid-template-columns:1fr 1fr}}
@media(max-width:760px){.settings-cards{grid-template-columns:1fr}.settings-card-heading{min-height:0}.bank-settings-form textarea{min-height:250px}}
@media(max-width:420px){.settings-card{padding:18px}.settings-card-heading{gap:11px}.settings-card-icon{flex-basis:42px;height:42px;border-radius:13px}.settings-card-heading h2{font-size:17px}.privacy-editor-footer{align-items:flex-start;flex-direction:column}}
</style>
<script>
(() => {
    const editor = document.getElementById('privacy-policy');
    const counter = document.getElementById('privacy-char-count');
    if (!editor || !counter) return;
    const updateCount = () => counter.textContent = editor.value.length.toLocaleString('id-ID');
    editor.addEventListener('input', updateCount);
    updateCount();
})();
</script>
@endsection
