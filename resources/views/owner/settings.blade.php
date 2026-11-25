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
                <div class="bank-account-list" id="bank-account-list">
                    @foreach(old('bank_accounts', $bankAccounts ?: [['id' => '', 'bank_name' => '', 'bank_account_number' => '', 'bank_account_holder' => '']]) as $index => $account)
                        <article class="bank-account-row">
                            <input type="hidden" name="bank_accounts[{{ $index }}][id]" value="{{ $account['id'] ?? '' }}">
                            <div class="bank-account-row-heading"><strong>Rekening <span class="bank-account-number">{{ $loop->iteration }}</span></strong><button type="button" class="bank-account-remove" aria-label="Hapus rekening ini"><i class="fa-solid fa-trash-can"></i></button></div>
                            <div class="bank-account-fields">
                                <label>Nama bank<input name="bank_accounts[{{ $index }}][bank_name]" value="{{ $account['bank_name'] ?? '' }}" placeholder="Contoh: BCA" required maxlength="80"></label>
                                <label>Nomor rekening<input name="bank_accounts[{{ $index }}][bank_account_number]" value="{{ $account['bank_account_number'] ?? '' }}" placeholder="Masukkan nomor rekening" required maxlength="40" inputmode="numeric" pattern="[0-9]+"></label>
                                <label>Nama pemilik rekening<input name="bank_accounts[{{ $index }}][bank_account_holder]" value="{{ $account['bank_account_holder'] ?? '' }}" placeholder="Sesuai nama di rekening" required maxlength="120"></label>
                            </div>
                        </article>
                    @endforeach
                </div>
                <button class="bank-account-add" id="add-bank-account" type="button"><i class="fa-solid fa-plus"></i> Tambah rekening bank</button>
                @error('bank_accounts')<small class="field-error">{{ $message }}</small>@enderror
                @foreach($errors->get('bank_accounts.*') as $messages)@foreach($messages as $message)<small class="field-error">{{ $message }}</small>@endforeach @endforeach
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
                <textarea id="privacy-policy" name="privacy_policy" rows="9" required maxlength="20000" placeholder="Tuliskan kebijakan privasi Quattro Coffee">{{ old('privacy_policy', $privacyPolicy) }}</textarea>
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
<style>
.settings-cards{grid-template-columns:minmax(0,1fr);align-items:start;gap:20px}
.settings-card{padding:clamp(22px,2.4vw,34px);border-radius:22px}
.settings-card-heading{align-items:center;min-height:0;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid #f1e7dc}
.settings-card-icon{width:50px;height:50px;flex-basis:50px;border-radius:16px}
.bank-settings-panel .bank-settings-form{grid-template-columns:repeat(3,minmax(0,1fr));align-items:end;gap:16px}
.bank-settings-panel .settings-save-button{grid-column:1/-1;justify-self:start;min-width:220px}
.privacy-settings-panel .bank-settings-form{gap:12px}
.privacy-settings-panel .bank-settings-form>label{margin-bottom:2px}
.privacy-settings-panel .bank-settings-form textarea{height:260px;min-height:220px;border-radius:14px}
.privacy-settings-panel .settings-save-button{width:100%;margin-top:2px}
.privacy-editor-footer{padding:0 2px 2px}
@media(max-width:900px){.bank-settings-panel .bank-settings-form{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.bank-settings-panel .bank-settings-form{grid-template-columns:1fr}.bank-settings-panel .settings-save-button{grid-column:auto;width:100%;min-width:0}.privacy-settings-panel .bank-settings-form textarea{height:230px}}
</style>
<style>
.bank-account-list{display:grid;gap:12px}.bank-account-row{padding:15px;border:1px solid #eadfD2;border-radius:14px;background:#fffdf9}.bank-account-row-heading{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;color:#3a291a;font-size:12px}.bank-account-fields{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.bank-account-remove{width:32px;height:32px;border:1px solid #f0d8d2;border-radius:10px;background:#fff8f6;color:#a54836;cursor:pointer}.bank-account-add{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:42px;padding:0 15px;border:1px dashed #c6a47f;border-radius:11px;background:#fffaf4;color:#78430c;font-size:11px;font-weight:800;cursor:pointer}.bank-account-add:hover{background:#f8eddf}.bank-settings-panel .bank-settings-form{grid-template-columns:1fr!important;align-items:stretch!important}.bank-settings-panel .bank-settings-form>.bank-account-list,.bank-settings-panel .bank-settings-form>.bank-account-add{grid-column:1/-1}.bank-settings-panel .bank-settings-form>.settings-save-button{grid-column:auto;justify-self:stretch;width:100%}@media(max-width:620px){.bank-account-fields{grid-template-columns:1fr}.bank-settings-panel .bank-settings-form>.bank-account-add{width:100%}}
</style>
<script>
(() => {
    const list = document.getElementById('bank-account-list');
    const addButton = document.getElementById('add-bank-account');
    if (list && addButton) {
        const refreshRows = () => {
            [...list.querySelectorAll('.bank-account-row')].forEach((row, index) => {
                row.querySelector('.bank-account-number').textContent = index + 1;
                row.querySelectorAll('input[name]').forEach(input => {
                    input.name = input.name.replace(/bank_accounts\[\d+\]/, `bank_accounts[${index}]`);
                });
                row.querySelector('.bank-account-remove').disabled = list.children.length === 1;
            });
        };
        addButton.addEventListener('click', () => {
            const index = list.children.length;
            const row = document.createElement('article');
            row.className = 'bank-account-row';
            row.innerHTML = `<input type="hidden" name="bank_accounts[${index}][id]" value=""><div class="bank-account-row-heading"><strong>Rekening <span class="bank-account-number">${index + 1}</span></strong><button type="button" class="bank-account-remove" aria-label="Hapus rekening ini"><i class="fa-solid fa-trash-can"></i></button></div><div class="bank-account-fields"><label>Nama bank<input name="bank_accounts[${index}][bank_name]" placeholder="Contoh: BRI" required maxlength="80"></label><label>Nomor rekening<input name="bank_accounts[${index}][bank_account_number]" placeholder="Masukkan nomor rekening" required maxlength="40" inputmode="numeric" pattern="[0-9]+"></label><label>Nama pemilik rekening<input name="bank_accounts[${index}][bank_account_holder]" placeholder="Sesuai nama di rekening" required maxlength="120"></label></div>`;
            list.appendChild(row);
            refreshRows();
            row.querySelector('input[name$="[bank_name]"]').focus();
        });
        list.addEventListener('click', event => {
            const remove = event.target.closest('.bank-account-remove');
            if (remove && list.children.length > 1) {
                remove.closest('.bank-account-row').remove();
                refreshRows();
            }
        });
        refreshRows();
    }
    const editor = document.getElementById('privacy-policy');
    const counter = document.getElementById('privacy-char-count');
    if (!editor || !counter) return;
    const updateCount = () => counter.textContent = editor.value.length.toLocaleString('id-ID');
    editor.addEventListener('input', updateCount);
    updateCount();
})();
</script>
@endsection
