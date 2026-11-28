<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;
use App\Services\UnpaidPaymentExpiryService;
use App\Services\MidtransSnapService;
use App\Services\MidtransPaymentStatusService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Order::query()
        ->where('status', '!=', 'Dibatalkan')
        ->where('payment_status', '!=', 'Dibayar')
        ->whereHas('payment', fn ($query) => $query->whereIn('payment_method', ['QRIS', 'Bank Transfer'])->whereIn('status', ['Pending', 'Gagal']))
        ->where('created_at', '<=', now()->subMinutes(UnpaidPaymentExpiryService::MINUTES))
        ->with('payment.attempts')
        ->chunkById(100, function ($orders) {
            $expiry = app(UnpaidPaymentExpiryService::class);
            $midtrans = app(MidtransSnapService::class);
            $status = app(MidtransPaymentStatusService::class);
            foreach ($orders as $order) $expiry->expire($order, $midtrans, $status);
        });
})->name('expire-unpaid-orders')->everyMinute()->withoutOverlapping();


