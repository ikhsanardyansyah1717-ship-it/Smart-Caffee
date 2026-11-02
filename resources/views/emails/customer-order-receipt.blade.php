<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Struk Quattro Coffee</title></head>
<body style="margin:0;padding:24px;background:#f7f3ec;font-family:Arial,sans-serif;color:#2b2118">
    <main style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #eadfce;border-radius:16px;overflow:hidden">
        <header style="padding:24px;background:#fffaf2;border-bottom:1px solid #eee4d8">
            <p style="margin:0 0 8px;color:#8b4f22;font-size:12px;font-weight:bold;letter-spacing:2px">QUATTRO COFFEE</p>
            <h1 style="margin:0;font-size:22px">Pembayaran berhasil</h1>
            <p style="margin:8px 0 0;color:#766b60">Terima kasih, {{ $order->customer_name }}. Ini struk digital pesanan Anda.</p>
        </header>
        <section style="padding:22px 24px">
            <p style="margin:0 0 4px;color:#766b60;font-size:13px">Nomor pesanan</p>
            <strong>{{ $order->order_number }}</strong>
            <p style="margin:5px 0 18px;color:#766b60;font-size:13px">{{ optional($order->payment?->paid_at)->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }} WIB</p>
            <table style="width:100%;border-collapse:collapse">
                @foreach ($order->items as $item)
                    <tr>
                        <td style="padding:12px 0;border-top:1px solid #eee8df">
                            <strong>{{ $item->product_name ?: 'Menu' }}</strong><br>
                            <span style="color:#766b60;font-size:13px">{{ $item->quantity }} × Rp {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                        </td>
                        <td style="padding:12px 0;border-top:1px solid #eee8df;text-align:right;white-space:nowrap">Rp {{ number_format((float) $item->price * (int) $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>
            <div style="padding-top:14px;border-top:1px solid #eadfce">
                <p style="margin:0 0 8px">Subtotal <span style="float:right">Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</span></p>
                <p style="margin:0 0 12px">Pajak <span style="float:right">Rp {{ number_format((float) $order->tax, 0, ',', '.') }}</span></p>
                <p style="margin:0;font-size:18px;font-weight:bold;color:#713b05">Total dibayar <span style="float:right">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span></p>
            </div>
            <p style="margin:20px 0 0;color:#766b60;font-size:13px">Metode pembayaran: {{ $order->payment?->payment_method ?? 'QRIS' }}<br>Nama pemesan: {{ $order->customer_name }}<br>Nomor ponsel: {{ $order->customer_phone }}</p>
        </section>
        <footer style="padding:16px 24px;background:#fffaf2;color:#766b60;font-size:12px">Simpan email ini sebagai bukti pembayaran. Sampai jumpa di Quattro Coffee.</footer>
    </main>
</body>
</html>
