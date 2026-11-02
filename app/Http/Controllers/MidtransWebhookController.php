<?php

namespace App\Http\Controllers;

use App\Models\PaymentAttempt;
use App\Services\MidtransPaymentStatusService;
use App\Services\MidtransSnapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransSnapService $midtrans, MidtransPaymentStatusService $statusService)
    {
        $notice = $request->all();
        if (!$midtrans->verifyNotification($notice)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $attempt = PaymentAttempt::where('gateway_order_id', $notice['order_id'] ?? '')
            ->with('payment.order.items')
            ->first();
        if (!$attempt || !$attempt->payment || !$attempt->payment->order) {
            return response()->json(['message' => 'Unknown order'], 404);
        }
        try {
            $statusService->apply($attempt, $notice);
        } catch (\Throwable $exception) {
            Log::error('Midtrans webhook could not be finalized', ['attempt_id' => $attempt->id, 'error' => $exception->getMessage()]);
            return response()->json(['message' => 'Temporary processing failure'], 500);
        }

        return response()->json(['message' => 'OK']);
    }
}
