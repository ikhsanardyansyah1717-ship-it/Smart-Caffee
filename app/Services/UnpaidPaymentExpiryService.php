<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use Illuminate\Support\Facades\DB;

class UnpaidPaymentExpiryService
{
    public const MINUTES = 15;
    public const REASON = 'Waktu pembayaran habis.';

    public function expire(Order $order, MidtransSnapService $midtrans, MidtransPaymentStatusService $statusService): string
    {
        $order->loadMissing('payment.attempts');
        $payment = $order->payment;
        if (!$payment || $order->status === 'Dibatalkan' || $order->payment_status === 'Dibayar' || $payment->status === 'Berhasil') {
            return $order->payment_status === 'Dibayar' || $payment?->status === 'Berhasil' ? 'Dibayar' : 'TidakBerakhir';
        }

        $method = strtoupper((string) $payment->payment_method);
        if ($method === 'BANK TRANSFER') {
            if ($payment->transfer_proof_path || !$order->created_at?->copy()->addMinutes(self::MINUTES)->isPast()) return 'Menunggu';
            return $this->cancel($order, null);
        }
        if ($method !== 'QRIS') return 'Menunggu';

        $attempt = $payment->attempts->sortByDesc('id')->first();
        if (!$attempt || !$attempt->created_at?->copy()->addMinutes(self::MINUTES)->isPast()) return 'Menunggu';

        try {
            $gateway = $midtrans->transactionStatus($attempt);
            $state = strtolower((string) ($gateway['transaction_status'] ?? ''));
            if ($this->isPaid($gateway)) return $statusService->apply($attempt, $gateway);
            if ($state === 'pending') {
                try { $gateway = $midtrans->expireTransaction($attempt); }
                catch (\Throwable) { $gateway = $midtrans->transactionStatus($attempt); }
                if ($this->isPaid($gateway)) return $statusService->apply($attempt, $gateway);
                $state = strtolower((string) ($gateway['transaction_status'] ?? ''));
            }
            if (!in_array($state, ['expire', 'cancel', 'deny'], true)) return 'Menunggu';
        } catch (\Throwable $exception) {
            report($exception);
            return 'Menunggu';
        }
        return $this->cancel($order, $attempt);
    }

    private function isPaid(array $gateway): bool
    {
        $state = strtolower((string) ($gateway['transaction_status'] ?? ''));
        return $state === 'settlement' || ($state === 'capture' && strtolower((string) ($gateway['fraud_status'] ?? '')) === 'accept');
    }

    private function cancel(Order $order, ?PaymentAttempt $attempt): string
    {
        return DB::transaction(function () use ($order, $attempt) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (!$locked) return 'TidakBerakhir';
            $payment = Payment::where('order_id', $locked->id)->lockForUpdate()->first();
            if ($locked->status === 'Dibatalkan') return 'Kedaluwarsa';
            if ($locked->payment_status === 'Dibayar' || $payment?->status === 'Berhasil') return 'Dibayar';

            if ($attempt) {
                $lockedAttempt = PaymentAttempt::whereKey($attempt->id)->lockForUpdate()->first();
                if ($lockedAttempt && $lockedAttempt->status === 'Pending') $lockedAttempt->update(['status' => 'Gagal']);
            }
            $payment?->update(['status' => 'Gagal']);
            $locked->update([
                'status' => 'Dibatalkan',
                'cancellation_reason' => self::REASON,
                'cancelled_by' => 'system',
                'cancelled_at' => now(),
            ]);
            return 'Kedaluwarsa';
        });
    }
}

