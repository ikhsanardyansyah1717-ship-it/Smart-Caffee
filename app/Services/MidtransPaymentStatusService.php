<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentAttempt;
use App\Models\Product;
use App\Mail\CustomerOrderReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MidtransPaymentStatusService
{
    public function apply(PaymentAttempt $attempt, array $notice): string
    {
        $result = DB::transaction(function () use ($attempt, $notice) {
            $lockedAttempt = PaymentAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            $payment = $lockedAttempt->payment()->lockForUpdate()->firstOrFail();
            $order = Order::with('items')->whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            if (abs((float) ($notice['gross_amount'] ?? 0) - (float) $payment->amount) >= 0.01) {
                throw new \RuntimeException('Nominal status Midtrans tidak sesuai dengan transaksi.');
            }

            $status = strtolower((string) ($notice['transaction_status'] ?? ''));
            $fraud = strtolower((string) ($notice['fraud_status'] ?? ''));
            if ($status === 'settlement' || ($status === 'capture' && $fraud === 'accept')) {
                $sendReceipt = $payment->status !== 'Berhasil';
                if ($payment->status !== 'Berhasil') {
                    $quantities = $order->items->groupBy('product_id')->map(fn ($items) => $items->sum('quantity'));
                    $products = Product::whereIn('id', $quantities->keys())->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                    foreach ($quantities as $productId => $quantity) {
                        $product = $products->get($productId);
                        if (!$product || (int) $product->stock < (int) $quantity) {
                            throw new \RuntimeException('Stok tidak cukup untuk menyelesaikan pesanan ' . $order->id);
                        }
                        $product->stock = (int) $product->stock - (int) $quantity;
                        $product->is_available = $product->stock > 0;
                        $product->save();
                    }
                    $payment->update(['status' => 'Berhasil', 'paid_at' => now()]);
                    $order->update(['payment_status' => 'Dibayar']);
                }
                $lockedAttempt->update([
                    'status' => 'Berhasil',
                    'gateway_transaction_id' => $notice['transaction_id'] ?? $lockedAttempt->gateway_transaction_id,
                    'paid_at' => $lockedAttempt->paid_at ?: now(),
                ]);
                return ['status' => 'Dibayar', 'receipt_order_id' => $sendReceipt ? $order->id : null];
            }

            if (in_array($status, ['expire', 'deny', 'cancel'], true)) {
                $lockedAttempt->update([
                    'status' => 'Gagal',
                    'gateway_transaction_id' => $notice['transaction_id'] ?? $lockedAttempt->gateway_transaction_id,
                ]);
                if ((int) $payment->attempts()->max('id') === (int) $lockedAttempt->id && $payment->status !== 'Berhasil') {
                    $payment->update(['status' => 'Gagal']);
                }
                return ['status' => 'Kedaluwarsa', 'receipt_order_id' => null];
            }

            return ['status' => 'Menunggu', 'receipt_order_id' => null];
        });

        if ($result['receipt_order_id']) {
            try {
                $order = Order::with(['items', 'payment'])->find($result['receipt_order_id']);
                if ($order && filled($order->customer_email)) {
                    Mail::to($order->customer_email)->send(new CustomerOrderReceipt($order));
                }
            } catch (\Throwable $exception) {
                Log::error('Gagal mengirim struk pesanan Quattro Coffee.', [
                    'order_id' => $result['receipt_order_id'],
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $result['status'];
    }
}
