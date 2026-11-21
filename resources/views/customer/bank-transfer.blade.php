@extends('layouts.customer')

@section('title', 'Transfer Bank - Quattro Coffee')

@section('content')
<main class="bank-transfer-page app-container">
    <header><a href="{{ route('customer.history') }}" aria-label="Kembali ke riwayat"><i class="fa-solid fa-arrow-left"></i></a><div><span>QUATTRO COFFEE · PEMBAYARAN</span><h1>Transfer Bank</h1></div></header>
    <section class="bank-transfer-card">
        <div class="transfer-status"><i class="fa-regular fa-clock"></i><span>Menunggu pembayaran</span></div>
        <p>Silakan transfer sesuai total pesanan ke rekening berikut.</p>
        <div class="transfer-account"><small>Bank tujuan</small><strong>{{ $bank['bank_name'] }}</strong><small>Nomor rekening</small><strong id="transfer-account-number">{{ $bank['bank_account_number'] }}</strong><small>Nama pemilik rekening</small><strong>{{ $bank['bank_account_holder'] }}</strong></div>
        <div class="transfer-amount"><span>Total transfer</span><strong>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</strong></div>
        <button type="button" class="copy-account" id="copy-account"><i class="fa-regular fa-copy"></i> Salin nomor rekening</button>
        <ol><li>Lakukan transfer sesuai nominal di atas.</li><li>Simpan bukti transfer.</li><li>Kasir akan memeriksa dan mengonfirmasi pembayaran.</li></ol>
        <a class="transfer-history" href="{{ route('customer.history') }}">Lihat status di Riwayat Pesanan</a>
        <p id="copy-account-status" role="status" aria-live="polite"></p>
    </section>
</main>
<style>
.bank-transfer-page{box-sizing:border-box;max-width:690px;min-height:100vh;margin:auto;padding:20px 18px 40px;background:#fbf8f2;color:#211a14}.bank-transfer-page header{display:flex;align-items:center;gap:13px;margin:0 -18px 20px;padding:12px 18px 18px;border-bottom:1px solid #eee6dc}.bank-transfer-page header>a{display:grid;place-items:center;width:40px;height:40px;border:1px solid #eadfd1;border-radius:13px;background:#fff;color:#754000;text-decoration:none}.bank-transfer-page header span{font-size:9px;font-weight:800;letter-spacing:1.3px;color:#b85c43}.bank-transfer-page h1{margin:4px 0 0;font:700 22px Georgia,serif}.bank-transfer-card{padding:23px;border:1px solid #ebdfd1;border-radius:19px;background:white;box-shadow:0 10px 30px #3c24100c}.transfer-status{display:flex;align-items:center;gap:8px;color:#8a5e20;font-size:12px;font-weight:700}.bank-transfer-card>p{color:#786b5b;font-size:13px}.transfer-account{display:grid;gap:6px;margin:18px 0;padding:16px;border-radius:14px;background:#faf6f0}.transfer-account small{margin-top:5px;color:#887866;font-size:10px}.transfer-account strong{color:#302216;font-size:16px}.transfer-amount{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-top:1px solid #f0e8df}.transfer-amount span{font-size:12px;color:#887866}.transfer-amount strong{font-size:20px;color:#754000}.copy-account,.transfer-history{display:flex;justify-content:center;align-items:center;gap:8px;width:100%;box-sizing:border-box;min-height:45px;margin-top:9px;padding:10px;border:1px solid #d7b995;border-radius:12px;background:white;color:#754000;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}.transfer-history{background:#754000;color:white;border-color:#754000}.bank-transfer-card ol{padding-left:20px;color:#746657;font-size:12px;line-height:1.8}.bank-transfer-card #copy-account-status{min-height:16px;margin-bottom:0;text-align:center;font-size:11px;color:#457348}@media(max-width:420px){.bank-transfer-card{padding:17px}.transfer-amount strong{font-size:17px}}
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
</script>
@endsection
