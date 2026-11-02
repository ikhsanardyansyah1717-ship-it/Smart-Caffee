<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentAttempt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransSnapService
{
    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key'));
    }

    public function createSession(Payment $payment): PaymentAttempt
    {
        $serverKey = config('services.midtrans.server_key');
        if (!$serverKey) {
            throw new RuntimeException('Midtrans Sandbox belum dikonfigurasi.');
        }

        // QRIS payment window is fixed to the requested 15 minutes.
        $minutes = 15;
        $expiresAt = now()->addMinutes($minutes);
        $attempt = $payment->attempts()->create([
            'gateway_order_id' => 'QTR-' . $payment->order_id . '-' . Str::lower(Str::random(10)),
            'status' => 'Pending',
            'expires_at' => $expiresAt,
        ]);

        $order = $payment->order()->with('items')->firstOrFail();
        $baseUrl = config('services.midtrans.production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $attempt->gateway_order_id,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'customer_details' => [
                'first_name' => mb_substr($order->customer_name, 0, 50),
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
            'custom_expiry' => [
                'order_time' => now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'expiry_duration' => $minutes,
            ],
            'qris' => ['acquirer' => 'gopay'],
        ];

        $response = Http::acceptJson()
            ->asJson()
            ->withBasicAuth($serverKey, '')
            ->timeout(20)
            ->post($baseUrl . '/v2/charge', $payload);

        $transactionId = $response->json('transaction_id');
        $qrActionUrl = collect($response->json('actions', []))
            ->firstWhere('name', 'generate-qr-code')['url'] ?? null;
        $qrPath = $qrActionUrl ? parse_url($qrActionUrl, PHP_URL_PATH) : null;
        // Midtrans examples may return a production host even when the charge
        // was created against Sandbox. Keep its QR endpoint on the same host as
        // the charge/status APIs to avoid a broken image or cross-environment call.
        $qrCodeUrl = is_string($qrPath) && preg_match('#^/v[24]/qris/[^/]+/qr-code$#', $qrPath)
            ? $baseUrl . $qrPath
            : null;
        if (!$response->successful() || !$transactionId || !$qrCodeUrl) {
            $attempt->update(['status' => 'Gagal']);
            report(new RuntimeException('Midtrans gagal membuat sesi pembayaran: ' . $response->body()));
            throw new RuntimeException('QRIS belum dapat dibuat. Cek konfigurasi Midtrans Sandbox.');
        }

        $attempt->update([
            'gateway_transaction_id' => $transactionId,
            'qr_code_url' => $qrCodeUrl,
            'payment_url' => route('customer.orders.payment.open', $order),
            // Keep the deadline in the same clock used by Laravel and the browser.
            // Midtrans expiry_time may be returned without a timezone suffix.
            'expires_at' => $expiresAt,
        ]);

        return $attempt->refresh();
    }

    public function transactionStatus(PaymentAttempt $attempt): array
    {
        $serverKey = config('services.midtrans.server_key');
        if (!$serverKey) {
            throw new RuntimeException('Midtrans Sandbox belum dikonfigurasi.');
        }
        $baseUrl = config('services.midtrans.production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
        $response = Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->timeout(15)
            ->get($baseUrl . '/v2/' . rawurlencode($attempt->gateway_order_id) . '/status');
        if (!$response->successful()) {
            throw new RuntimeException('Midtrans belum dapat memeriksa status pembayaran.');
        }
        return $response->json();
    }

    public function cancelTransaction(PaymentAttempt $attempt): array
    {
        return $this->updateTransactionState($attempt, 'cancel', 'Midtrans belum dapat membatalkan transaksi QRIS.');
    }

    public function expireTransaction(PaymentAttempt $attempt): array
    {
        return $this->updateTransactionState($attempt, 'expire', 'Midtrans belum dapat mengakhiri transaksi QRIS.');
    }

    private function updateTransactionState(PaymentAttempt $attempt, string $action, string $fallbackMessage): array
    {
        $serverKey = config('services.midtrans.server_key');
        if (!$serverKey) {
            throw new RuntimeException('Midtrans Sandbox belum dikonfigurasi.');
        }
        $baseUrl = config('services.midtrans.production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
        $transactionIds = array_values(array_unique(array_filter([
            $attempt->gateway_transaction_id,
            $attempt->gateway_order_id,
        ])));
        $lastResponse = null;
        foreach ($transactionIds as $transactionId) {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(15)
                ->post($baseUrl . '/v2/' . rawurlencode($transactionId) . '/' . $action);
            if ($response->successful()) {
                return $response->json();
            }
            $lastResponse = $response;
        }

        $message = $lastResponse?->json('status_message') ?: $fallbackMessage;
        throw new RuntimeException($message);
    }

    public function verifyNotification(array $notification): bool
    {
        $serverKey = config('services.midtrans.server_key');
        $signature = $notification['signature_key'] ?? '';
        if (!$serverKey || !$signature) {
            return false;
        }

        $expected = hash('sha512', implode('', [
            (string) ($notification['order_id'] ?? ''),
            (string) ($notification['status_code'] ?? ''),
            (string) ($notification['gross_amount'] ?? ''),
            $serverKey,
        ]));

        return hash_equals($expected, (string) $signature);
    }
}
