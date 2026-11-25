@extends('layouts.customer')

@section('title', 'Transfer Bank - Quattro Coffee')

@section('content')
<main class="bank-transfer-page app-container">
    <header class="transfer-header"><a href="{{ route('customer.history') }}" aria-label="Kembali ke riwayat"><i class="fa-solid fa-arrow-left"></i></a><div><span>PEMBAYARAN</span><h1>Transfer bank</h1></div></header>
    <section class="bank-transfer-card">
        @php
            $transferStatus = $order->status === 'Dibatalkan'
                ? 'Pesanan dibatalkan'
                : ($order->payment_status === 'Dibayar'
                    ? 'Pembayaran terverifikasi'
                    : ($order->payment?->transfer_proof_path ? 'Bukti sedang diperiksa' : 'Menunggu pembayaran'));
        @endphp
        <div class="transfer-card-heading"><div><span class="transfer-order-label">PESANAN {{ $order->order_number }}</span><h2>Rekening tujuan</h2></div><span class="transfer-status {{ $order->status === 'Dibatalkan' ? 'is-cancelled' : ($order->payment_status === 'Dibayar' ? 'is-paid' : '') }}"><i class="fa-solid {{ $order->status === 'Dibatalkan' ? 'fa-circle-xmark' : ($order->payment_status === 'Dibayar' ? 'fa-circle-check' : 'fa-clock') }}"></i>{{ $transferStatus }}</span></div>
        <div class="transfer-account">
            <div><small>Bank</small><strong>{{ $bank['bank_name'] }}</strong></div>
            <div class="account-number-row"><div><small>Nomor rekening</small><strong id="transfer-account-number">{{ $bank['bank_account_number'] }}</strong></div><button type="button" class="copy-account" id="copy-account" aria-label="Salin nomor rekening"><i class="fa-regular fa-copy"></i><span>Salin</span></button></div>
            <div><small>Atas nama</small><strong>{{ $bank['bank_account_holder'] }}</strong></div>
        </div>
        <div class="transfer-amount"><span>Total yang ditransfer</span><strong>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</strong></div>
        <p class="transfer-note">Transfer sesuai nominal di atas, lalu kirim foto bukti pembayaran. Kasir akan memeriksanya.</p>
        @if(session('success'))<p class="transfer-feedback success">{{ session('success') }}</p>@endif
        @if(session('error'))<p class="transfer-feedback error">{{ session('error') }}</p>@endif
        <section class="transfer-proof-box">
            <div class="proof-heading"><span class="proof-icon"><i class="fa-solid fa-receipt"></i></span><div><h2>Bukti pembayaran</h2><p>JPG, PNG, atau WEBP · Maks. 5 MB</p></div></div>
            @if($order->payment?->transfer_proof_path)
                <div class="transfer-proof-image-wrap"><img id="transfer-proof-preview" src="{{ route('customer.orders.bank-transfer.proof-image', $order) }}" alt="Bukti transfer pesanan {{ $order->order_number }}"></div>
                @if($order->payment_status !== 'Dibayar' && $order->status !== 'Dibatalkan')<p class="proof-caption"><i class="fa-solid fa-circle-check"></i> Bukti diterima · Menunggu verifikasi Kasir</p>@endif
            @endif
            @if($order->payment_status !== 'Dibayar' && $order->status !== 'Dibatalkan')
                <form method="POST" action="{{ route('customer.orders.bank-transfer.proof', $order) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="transfer-proof-picker"><input id="transfer-proof" name="transfer_proof" type="file" accept="image/jpeg,image/png,image/webp" required><label for="transfer-proof"><i class="fa-solid fa-arrow-up-from-bracket"></i> {{ $order->payment?->transfer_proof_path ? 'Ganti foto' : 'Pilih foto bukti' }}</label><span id="transfer-proof-name">{{ $order->payment?->transfer_proof_path ? 'Pilih foto baru untuk mengganti bukti' : 'Belum ada foto dipilih' }}</span></div>
                    @error('transfer_proof')<small class="transfer-field-error">{{ $message }}</small>@enderror
                    <button class="submit-proof" type="submit"><i class="fa-solid fa-cloud-arrow-up"></i> {{ $order->payment?->transfer_proof_path ? 'Simpan foto baru' : 'Kirim bukti' }}</button>
                </form>
            @endif
        </section>
        <a class="transfer-history" href="{{ route('customer.history') }}">Lihat riwayat pesanan</a>
        <p id="copy-account-status" role="status" aria-live="polite"></p>
    </section>
</main>
<style>
.bank-transfer-page{box-sizing:border-box;max-width:690px;min-height:100vh;margin:auto;padding:20px 18px 40px;background:#fbf8f2;color:#211a14}.bank-transfer-page header{display:flex;align-items:center;gap:13px;margin:0 -18px 20px;padding:12px 18px 18px;border-bottom:1px solid #eee6dc}.bank-transfer-page header>a{display:grid;place-items:center;width:40px;height:40px;border:1px solid #eadfd1;border-radius:13px;background:#fff;color:#754000;text-decoration:none}.bank-transfer-page header span{font-size:9px;font-weight:800;letter-spacing:1.3px;color:#b85c43}.bank-transfer-page h1{margin:4px 0 0;font:700 22px Georgia,serif}.bank-transfer-card{padding:23px;border:1px solid #ebdfd1;border-radius:19px;background:white;box-shadow:0 10px 30px #3c24100c}.transfer-status{display:flex;align-items:center;gap:8px;color:#8a5e20;font-size:12px;font-weight:700}.bank-transfer-card>p{color:#786b5b;font-size:13px}.transfer-account{display:grid;gap:6px;margin:18px 0;padding:16px;border-radius:14px;background:#faf6f0}.transfer-account small{margin-top:5px;color:#887866;font-size:10px}.transfer-account strong{color:#302216;font-size:16px}.transfer-amount{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-top:1px solid #f0e8df}.transfer-amount span{font-size:12px;color:#887866}.transfer-amount strong{font-size:20px;color:#754000}.copy-account,.transfer-history{display:flex;justify-content:center;align-items:center;gap:8px;width:100%;box-sizing:border-box;min-height:45px;margin-top:9px;padding:10px;border:1px solid #d7b995;border-radius:12px;background:white;color:#754000;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}.transfer-history{background:#754000;color:white;border-color:#754000}.transfer-proof-box{margin-top:18px;padding:16px;border:1px solid #eadfd1;border-radius:14px;background:#fffaf4}.transfer-proof-box h2{margin:0 0 8px;font-size:15px;color:#302216}.transfer-proof-box p{color:#786b5b;font-size:12px;line-height:1.5}.transfer-proof-box form{display:grid;gap:9px;margin-top:12px}.transfer-proof-box label{font-size:11px;font-weight:700;color:#674a2d}.transfer-proof-box input{box-sizing:border-box;width:100%;padding:10px;border:1px solid #e4ddd2;border-radius:10px;background:#fff;font-size:12px}.transfer-proof-box button{min-height:44px;border:0;border-radius:11px;background:#754000;color:#fff;font-weight:700;cursor:pointer}.proof-submitted{color:#278454!important}.transfer-feedback{padding:10px 12px;border-radius:10px}.transfer-feedback.success{background:#edf8f0;color:#25774b}.transfer-feedback.error,.transfer-field-error{background:#fff1ef;color:#b7352e}.bank-transfer-card ol{padding-left:20px;color:#746657;font-size:12px;line-height:1.8}.bank-transfer-card #copy-account-status{min-height:16px;margin-bottom:0;text-align:center;font-size:11px;color:#457348}@media(max-width:420px){.bank-transfer-card{padding:17px}.transfer-amount strong{font-size:17px}}
.transfer-proof-image-wrap{display:grid;place-items:center;min-height:140px;max-height:440px;overflow:hidden;padding:10px;border:1px solid #eadfd1;border-radius:13px;background:#f5f1eb}.transfer-proof-image-wrap img{display:block;width:100%;max-height:420px;object-fit:contain;border-radius:8px}.transfer-proof-picker{display:flex;align-items:center;gap:11px;flex-wrap:wrap}.transfer-proof-picker input{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);clip-path:inset(50%)}.transfer-proof-picker label{display:inline-flex;min-height:40px;align-items:center;gap:8px;padding:0 13px;border:1px solid #e3d1bd;border-radius:10px;background:#fff;color:#754000;cursor:pointer}.transfer-proof-picker span,.transfer-image-hint{color:#887866;font-size:11px}.transfer-proof-picker input:focus-visible+label{outline:3px solid #b8895966;outline-offset:2px}
.bank-transfer-page{max-width:520px;padding:18px 16px 36px}.transfer-header{gap:11px;margin-bottom:14px;padding:8px 0 14px}.transfer-header h1{font-size:21px}.transfer-header>a{flex:0 0 38px;width:38px;height:38px;border-radius:12px}.transfer-header span{font-size:8px;letter-spacing:1.2px}.bank-transfer-card{padding:17px;border-radius:17px;box-shadow:0 5px 20px #3c241008}.transfer-card-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:13px}.transfer-order-label{color:#9a7150;font-size:8px;font-weight:800;letter-spacing:1px}.transfer-card-heading h2{margin:4px 0 0;color:#29211a;font:700 17px Georgia,serif}.transfer-status{flex:0 0 auto;padding:6px 9px;border-radius:999px;background:#fff5e7;color:#946318;font-size:9px}.transfer-status.is-paid{background:#edf7ef;color:#398253}.transfer-status.is-cancelled{background:#fff0ee;color:#b24a3f}.transfer-account{gap:12px;margin:0;padding:14px;border:1px solid #f0e8df;border-radius:13px;background:#fcfaf7}.transfer-account>div{display:grid;gap:4px}.transfer-account small{margin:0;color:#8a7e71;font-size:9px}.transfer-account strong{color:#30261e;font-size:13px;overflow-wrap:anywhere}.account-number-row{display:flex!important;align-items:center;justify-content:space-between;gap:10px}.account-number-row>div{display:grid;gap:4px}.copy-account{width:auto;min-height:33px;margin:0;padding:0 10px;border-color:#eadfd1;border-radius:9px;font-size:10px}.copy-account span{display:inline}.transfer-amount{margin-top:13px;padding:13px 0;border-top:1px solid #f0e8df}.transfer-amount span{font-size:10px}.transfer-amount strong{font-size:18px}.transfer-note{margin:10px 0 14px;color:#81766a;font-size:10px;line-height:1.5}.transfer-proof-box{margin-top:14px;padding:14px;border:1px solid #eee6dd;border-radius:14px;background:#fff}.proof-heading{display:flex;align-items:center;gap:10px;margin-bottom:12px}.proof-icon{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:#f6efe6;color:#88500f}.proof-heading h2{margin:0;color:#302216;font-size:13px}.proof-heading p{margin:3px 0 0;color:#8a7e71;font-size:9px}.transfer-proof-image-wrap{min-height:110px;max-height:300px;padding:6px;border-color:#eee6dd;border-radius:11px;background:#faf8f5}.transfer-proof-image-wrap img{max-height:286px;border-radius:7px}.proof-caption{display:flex;align-items:center;gap:6px;margin:8px 0 0;color:#438052;font-size:10px}.transfer-proof-box form{gap:10px;margin-top:12px}.transfer-proof-picker{min-height:40px;align-items:center}.transfer-proof-picker label{min-height:37px;padding:0 11px;border-color:#e8ded2;border-radius:9px;font-size:10px}.transfer-proof-picker span{flex:1;min-width:100px;font-size:9px}.submit-proof{min-height:42px!important;border-radius:10px!important;font-size:11px}.transfer-field-error{display:block;padding:8px;border-radius:8px;font-size:10px}.transfer-history{min-height:40px;margin-top:12px;border-color:#eee4d8;background:#fff;color:#72502e;font-size:10px}.bank-transfer-card #copy-account-status{min-height:0;margin-top:6px;font-size:9px}.bank-transfer-card #copy-account-status:empty{display:none}@media(max-width:420px){.bank-transfer-page{padding:14px 12px 28px}.bank-transfer-card{padding:14px}.transfer-card-heading{gap:7px}.transfer-status{max-width:142px;white-space:normal;line-height:1.25}.transfer-amount strong{font-size:17px}}
</style>
<script>
document.getElementById('copy-account')?.addEventListener('click', async () => {
    const number = document.getElementById('transfer-account-number')?.textContent?.trim() || '';
    const status = document.getElementById('copy-account-status');
    try {
        await navigator.clipboard.writeText(number);
        status.textContent = 'Nomor rekening berhasil disalin.';
    } catch (error) {
        status.textContent = `Nomor rekening: ${number}`;
    }
});
document.getElementById('transfer-proof')?.addEventListener('change', function () {
    const file = this.files?.[0];
    if (!file) return;
    const preview = document.getElementById('transfer-proof-preview');
    const previewUrl = URL.createObjectURL(file);
    if (preview) preview.src = previewUrl;
    else {
        const wrap = document.createElement('div');
        wrap.className = 'transfer-proof-image-wrap';
        const image = document.createElement('img');
        image.id = 'transfer-proof-preview';
        image.alt = 'Pratinjau foto bukti transfer';
        image.src = previewUrl;
        wrap.append(image);
        this.closest('.transfer-proof-box').insertBefore(wrap, this.closest('form'));
    }
    document.getElementById('transfer-proof-name').textContent = file.name;
});
</script>
@endsection


