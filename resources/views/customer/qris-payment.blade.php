@extends('layouts.customer')

@section('title', 'Pembayaran QRIS - Quattro Coffee')

@section('content')
<main class="quattro-qris-page">
    <header class="quattro-qris-topbar">
        <a href="{{ route('customer.history', [], false) }}" aria-label="Kembali ke riwayat"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="quattro-qris-brand"><span><i class="fa-solid fa-mug-hot"></i></span><div><strong>QUATTRO</strong><small>COFFEE</small></div></div>
        <span class="quattro-qris-secure"><i class="fa-solid fa-lock"></i> Aman</span>
    </header>

    <section class="quattro-qris-card">
        <div class="quattro-qris-heading">
            <span class="quattro-qris-eyebrow">PEMBAYARAN PESANAN</span>
            <h1>Scan QRIS untuk membayar</h1>
            <p>Gunakan aplikasi pembayaran yang mendukung QRIS.</p>
        </div>

        <div class="quattro-qris-orderline">
            <span>Pesanan <strong>#{{ $order->order_number }}</strong></span>
            <span class="quattro-qris-live"><i></i> Menunggu pembayaran</span>
        </div>

        <div class="quattro-qris-total"><span>Total pembayaran</span><strong>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</strong></div>

        <div class="quattro-qris-timer" data-deadline="{{ $attempt->expires_at?->toIso8601String() }}">
            <span><i class="fa-regular fa-clock"></i> Selesaikan sebelum QR kedaluwarsa</span>
            <strong id="quattro-qris-countdown">--:--</strong>
        </div>

        <figure class="quattro-qris-code">
            <img id="quattro-qris-image" src="{{ $qrImageData ?: $qrImageUrl }}" alt="QRIS pembayaran pesanan {{ $order->order_number }}">
            <p id="quattro-qris-image-error" hidden>QR gagal dimuat. Muat ulang halaman atau buat QRIS baru.</p>
            <figcaption>QR unik untuk pesanan ini</figcaption>
        </figure>

        <div class="quattro-qris-download-actions">
            <a class="quattro-qris-download" href="{{ $qrDownloadUrl }}" download="quattro-{{ $order->order_number }}-qris.png">
                <i class="fa-solid fa-download"></i> Unduh QR PNG
            </a>
            @if ($sandboxQrUrl)
                <button type="button" id="quattro-qris-copy-url" class="quattro-qris-copy-url">Salin URL QR</button>
            @endif
        </div>
        @if ($sandboxQrUrl)
            <textarea id="quattro-qris-sandbox-url" class="quattro-qris-sandbox-url" rows="1" readonly tabindex="-1" aria-label="URL QR Sandbox">{{ $sandboxQrUrl }}</textarea>
            <span id="quattro-qris-copy-feedback" role="status" aria-live="polite"></span>
        @endif

        <ol class="quattro-qris-steps">
            <li><span>1</span><p>Buka aplikasi bank atau dompet digital, lalu pilih Scan QRIS.</p></li>
            <li><span>2</span><p>Pindai QR dan pastikan nominalnya sesuai sebelum membayar.</p></li>
            <li><span>3</span><p>Kembali ke halaman ini dan tekan Cek Status Pembayaran.</p></li>
        </ol>

        <div class="quattro-qris-actions">
            <button type="button" id="quattro-qris-check"><i class="fa-solid fa-rotate"></i> Cek Status Pembayaran</button>
            <a href="{{ route('customer.history', [], false) }}">Ke Riwayat Pesanan</a>
        </div>
        <p class="quattro-qris-feedback" id="quattro-qris-feedback" role="status" aria-live="polite"></p>
        <a class="quattro-qris-new" id="quattro-qris-new" href="{{ route('customer.orders.payment.open', $order, false) }}" hidden>Buat QRIS baru</a>
    </section>
    <p class="quattro-qris-footnote"><i class="fa-solid fa-shield-halved"></i> Pembayaran diverifikasi otomatis oleh Midtrans.</p>
</main>

<style>
.quattro-qris-page{--q-brown:#754000;--q-cream:#f7f3ed;--q-ink:#292018;max-width:560px;min-height:calc(100vh - 40px);margin:0 auto;padding:16px 18px 36px;background:var(--q-cream);color:var(--q-ink);box-sizing:border-box}
.quattro-qris-code img{pointer-events:none;user-select:none}
.quattro-qris-download-actions{display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:18px}.quattro-qris-download-actions .quattro-qris-download{margin:0}.quattro-qris-copy-url{display:flex;align-items:center;justify-content:center;min-height:40px;padding:10px 16px;border:1px solid #d7b995;border-radius:30px;background:#fff;color:var(--q-brown);font:inherit;font-size:13px;font-weight:700;cursor:pointer}.quattro-qris-sandbox-url{position:fixed;left:-10000px;top:0;width:1px;height:1px;opacity:0}#quattro-qris-copy-feedback{display:block;min-height:16px;margin:0 0 12px;text-align:center;color:#47734f;font-size:11px}
.quattro-qris-topbar{display:flex;align-items:center;justify-content:space-between;margin:0 0 16px;padding:8px 2px}.quattro-qris-topbar>a{color:var(--q-brown);font-size:20px;text-decoration:none;padding:8px}.quattro-qris-brand{display:flex;align-items:center;gap:9px;margin-right:auto;margin-left:12px}.quattro-qris-brand>span{width:38px;height:38px;border-radius:50%;background:#fff;color:var(--q-brown);display:grid;place-items:center;box-shadow:0 4px 14px #4b2a1112}.quattro-qris-brand strong,.quattro-qris-brand small{display:block;line-height:1.1;letter-spacing:.12em}.quattro-qris-brand strong{font-size:14px}.quattro-qris-brand small{font-size:8px;color:#987b61;margin-top:3px}.quattro-qris-secure{font-size:11px;color:#78624c;background:#fff;padding:8px 11px;border-radius:30px}
.quattro-qris-card{background:#fff;border:1px solid #ebdfd1;border-radius:24px;padding:22px;box-shadow:0 12px 35px #3c24100e}.quattro-qris-heading{text-align:center}.quattro-qris-eyebrow{font-size:10px;font-weight:800;letter-spacing:.18em;color:#b85c43}.quattro-qris-heading h1{font-size:22px;margin:9px 0 5px;color:var(--q-ink)}.quattro-qris-heading p{font-size:13px;color:#887866;margin:0}.quattro-qris-orderline{display:flex;justify-content:space-between;align-items:center;gap:8px;margin:22px 0 14px;padding:12px 13px;border-radius:13px;background:#faf6f0;font-size:12px}.quattro-qris-orderline strong{color:var(--q-brown)}.quattro-qris-live{color:#876322;font-size:11px;white-space:nowrap}.quattro-qris-live i{display:inline-block;width:7px;height:7px;border-radius:50%;background:#d69a32;margin-right:5px}.quattro-qris-total{display:flex;align-items:center;justify-content:space-between;padding:7px 2px 16px;border-bottom:1px solid #f0e8df}.quattro-qris-total span{font-size:13px;color:#897866}.quattro-qris-total strong{font-size:21px;color:var(--q-brown)}.quattro-qris-timer{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:12px 13px;margin:15px 0;background:#fff7e9;border:1px solid #f2e0c4;border-radius:13px;color:#78562c}.quattro-qris-timer span{font-size:11px}.quattro-qris-timer strong{font-size:19px;font-variant-numeric:tabular-nums}.quattro-qris-timer.is-expired{background:#fff1ef;color:#a23e32;border-color:#f0d2cd}.quattro-qris-code{margin:16px auto 12px;width:min(100%,280px);aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;border:1px solid #eee7df;border-radius:18px;padding:14px;box-sizing:border-box}.quattro-qris-code img{width:100%;height:calc(100% - 22px);min-height:0;object-fit:contain}.quattro-qris-code figcaption{flex:0 0 14px;font-size:10px;color:#9a8978}.quattro-qris-download{display:flex;width:max-content;margin:0 auto 18px;align-items:center;gap:8px;color:var(--q-brown);font-weight:700;font-size:13px;text-decoration:none;padding:10px 16px;border:1px solid #d7b995;border-radius:30px}.quattro-qris-steps{list-style:none;padding:0;margin:0 0 18px}.quattro-qris-steps li{display:flex;align-items:flex-start;gap:10px;padding:8px 0}.quattro-qris-steps li>span{flex:0 0 23px;height:23px;border-radius:50%;display:grid;place-items:center;background:#f5ebde;color:var(--q-brown);font-size:11px;font-weight:800}.quattro-qris-steps p{margin:2px 0 0;color:#6f6255;font-size:11px;line-height:1.5}.quattro-qris-actions{display:grid;gap:9px}.quattro-qris-actions button{min-height:48px;border:0;border-radius:14px;background:var(--q-brown);color:white;font:700 14px inherit;cursor:pointer}.quattro-qris-actions button:disabled{opacity:.65}.quattro-qris-actions>a{text-align:center;text-decoration:none;color:var(--q-brown);font-size:13px;font-weight:700;padding:10px}.quattro-qris-feedback{text-align:center;min-height:18px;margin:6px 0 0;font-size:12px;color:#806b58}.quattro-qris-new{display:block;text-align:center;margin:8px;color:var(--q-brown);font-weight:800}.quattro-qris-footnote{text-align:center;font-size:11px;color:#958573;margin:15px 0}.quattro-qris-footnote i{color:#63936d;margin-right:5px}
.quattro-qris-sandbox{margin:0 0 18px;padding:14px;border:1px solid #eadbc7;border-radius:14px;background:#fffaf3}.quattro-qris-sandbox>strong{display:block;font-size:13px;color:var(--q-brown)}.quattro-qris-sandbox p{margin:5px 0 10px;color:#786b5b;font-size:11px;line-height:1.5}.quattro-qris-sandbox-url{display:block;width:100%;box-sizing:border-box;margin:0 0 9px;padding:9px;border:1px solid #e8dfd3;border-radius:8px;background:#fff;color:#62564b;font:11px/1.4 ui-monospace,monospace}.quattro-qris-sandbox-actions{display:flex;gap:8px;flex-wrap:wrap}.quattro-qris-sandbox-actions button,.quattro-qris-sandbox-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:8px 12px;border:1px solid #d7b995;border-radius:10px;background:#fff;color:var(--q-brown);font:700 11px inherit;text-decoration:none;cursor:pointer}.quattro-qris-sandbox-actions button{background:var(--q-brown);color:#fff;border-color:var(--q-brown)}#quattro-qris-copy-feedback{display:block;min-height:15px;margin-top:7px;color:#47734f;font-size:11px}
@media(max-width:480px){.quattro-qris-page{padding:9px 12px 24px}.quattro-qris-card{padding:17px;border-radius:20px}.quattro-qris-heading h1{font-size:20px}.quattro-qris-orderline{font-size:10px}.quattro-qris-live{font-size:10px}.quattro-qris-total strong{font-size:19px}}
</style>

<script>
(() => {
    const timer = document.querySelector('.quattro-qris-timer');
    const qrImage = document.getElementById('quattro-qris-image');
    const qrImageError = document.getElementById('quattro-qris-image-error');
    const countdown = document.getElementById('quattro-qris-countdown');
    const checkButton = document.getElementById('quattro-qris-check');
    const feedback = document.getElementById('quattro-qris-feedback');
    const newQr = document.getElementById('quattro-qris-new');
    const sandboxQrUrl = document.getElementById('quattro-qris-sandbox-url');
    const copySandboxQrUrl = document.getElementById('quattro-qris-copy-url');
    const copyFeedback = document.getElementById('quattro-qris-copy-feedback');
    const deadline = new Date(timer?.dataset.deadline || '').getTime();
    let checking = false;
    let qrRetries = 0;
    qrImage.addEventListener('error', () => {
        if (qrRetries < 2) {
            qrRetries += 1;
            const retryUrl = new URL(qrImage.src, window.location.href);
            retryUrl.searchParams.set('retry', String(Date.now()));
            qrImage.src = retryUrl.toString();
            return;
        }
        qrImage.hidden = true;
        qrImageError.hidden = false;
    });
    copySandboxQrUrl?.addEventListener('click', async () => {
        if (!/^https:\/\/api\.sandbox\.midtrans\.com\/v[24]\/qris\/[^/]+\/qr-code$/.test(sandboxQrUrl.value.trim())) {
            copyFeedback.textContent = 'URL simulator tidak valid. Muat ulang halaman pembayaran.';
            return;
        }
        sandboxQrUrl.focus();
        sandboxQrUrl.select();
        sandboxQrUrl.setSelectionRange(0, sandboxQrUrl.value.length);
        try {
            await navigator.clipboard.writeText(sandboxQrUrl.value);
            copyFeedback.textContent = 'URL QR berhasil disalin. Tempelkan di simulator Sandbox.';
        } catch (error) {
            const copied = document.execCommand('copy');
            copyFeedback.textContent = copied
                ? 'URL QR berhasil disalin. Tempelkan di simulator Sandbox.'
                : 'URL QR sudah dipilih. Tekan lama lalu pilih Salin.';
        }
    });
    const tick = () => {
        const seconds = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
        countdown.textContent = `${String(Math.floor(seconds / 60)).padStart(2,'0')}:${String(seconds % 60).padStart(2,'0')}`;
        if (!seconds) {
            timer.classList.add('is-expired');
            checkButton.disabled = true;
            feedback.textContent = 'Waktu QR berakhir. Buat QR baru untuk melanjutkan pembayaran.';
            newQr.hidden = false;
        }
    };
    tick();
    const ticker = setInterval(tick, 1000);
    checkButton.addEventListener('click', async () => {
        if (checking) return;
        checking = true;
        checkButton.disabled = true;
        feedback.textContent = 'Memeriksa pembayaran...';
        try {
            const response = await fetch(@json(route('customer.orders.payment.check', $order, false)), {
                method: 'POST',
                headers: {'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || '', 'X-Guest-Token':localStorage.getItem('quattro_guest_token') || ''},
                credentials: 'same-origin'
            });
            const data = await response.json();
            feedback.textContent = data.message || 'Status pembayaran belum tersedia.';
            if (data.status === 'Dibayar') {
                clearInterval(ticker);
                feedback.textContent = 'Pembayaran berhasil. Mengarahkan ke riwayat pesanan...';
                setTimeout(() => window.location.href = @json(route('customer.history')), 1100);
            } else if (data.status === 'Kedaluwarsa') {
                timer.classList.add('is-expired');
                newQr.hidden = false;
            }
        } catch (error) {
            feedback.textContent = 'Status belum dapat diperiksa. Coba kembali sebentar lagi.';
        } finally {
            checking = false;
            checkButton.disabled = Date.now() >= deadline;
        }
    });
})();
</script>
@endsection
