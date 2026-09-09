<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $periodLabel = now()->translatedFormat('F Y');

        $totalPendapatan = Order::where('payment_status', 'Dibayar')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'Dibayar')
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as total_qty')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(4)
            ->get();

        $maxQty = $topProducts->max('total_qty') ?: 1;

        return view('owner.reports', compact('totalPendapatan', 'topProducts', 'maxQty', 'periodLabel'));
    }
}