<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Dashboard Kitchen
     */
    public function dashboard(Request $request)
    {
        [$periodStart, $periodEnd] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('start'),
            $request->query('end')
        );
        $periodLabel = FinancialReportService::periodLabel($periodStart, $periodEnd);
        $orders = Order::with([
                'items.product',
                'payment'
            ])
            ->where('payment_status', 'Dibayar')
            ->whereNotNull('kitchen_confirmed_at')
            ->whereIn('status', [
                'Menunggu',
                'Diproses',
                'Selesai'
            ])
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->latest()
            ->get();

        $menuAverages = collect();
        Order::with('items')->whereNotNull('kitchen_started_at')
            ->whereNotNull('kitchen_completed_at')
            ->whereBetween('kitchen_completed_at', [$periodStart, $periodEnd])
            ->whereIn('status', ['Selesai', 'Sudah Diambil'])
            ->get()->each(function ($order) use ($menuAverages) {
                $minutes = abs($order->kitchen_completed_at->getTimestamp() - $order->kitchen_started_at->getTimestamp()) / 60;
                foreach ($order->items as $item) {
                    $key = $item->product_name;
                    $entry = $menuAverages->get($key, ['total' => 0, 'orders' => 0]);
                    $entry['total'] += $minutes;
                    $entry['orders']++;
                    $menuAverages->put($key, $entry);
                }
            });
        $menuAverages = $menuAverages->map(fn ($row) => round($row['total'] / $row['orders'], 1))->sortKeys();
        $pendingReservations = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->whereColumn('order_items.product_id', 'products.id')
            ->where('payments.status', 'Pending')
            ->where('orders.payment_status', 'Belum Dibayar')
            ->where('orders.status', '!=', 'Dibatalkan')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');
        $activeMenuCount = Product::query()
            ->where('products.is_available', true)
            ->whereRaw('products.stock > (' . $pendingReservations->toSql() . ')', $pendingReservations->getBindings())
            ->count();

        return view(
            'kitchen.dashboard',
            compact('orders', 'periodStart', 'periodEnd', 'periodLabel', 'menuAverages', 'activeMenuCount')
        );
    }


    /**
     * Pesanan Baru
     */
    public function incoming()
    {
        $orders = Order::with([
                'items.product',
                'payment'
            ])
            ->where('payment_status', 'Dibayar')
            ->whereNotNull('kitchen_confirmed_at')
            ->whereIn('status', ['Menunggu', 'Diproses'])
            ->latest()
            ->get();

        return view(
            'kitchen.incoming',
            compact('orders')
        );
    }


    /**
     * Sedang Diproses
     */
    public function processing()
    {
        return redirect()->route('kitchen.incoming');
    }


    /**
     * Siap Diambil
     */
    public function completed()
    {
        $orders = Order::with([
                'items.product',
                'payment'
            ])
            ->where('payment_status', 'Dibayar')
            ->whereNotNull('kitchen_confirmed_at')
            ->where('status', 'Selesai')
            ->latest()
            ->get();

        return view(
            'kitchen.completed',
            compact('orders')
        );
    }


    /**
     * Riwayat
     * Hanya pesanan yang benar-benar sudah diambil / dibatalkan.
     */
    public function history()
    {
        $orders = Order::with([
                'items.product',
                'payment'
            ])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_status', 'Dibayar')
                      ->whereNotNull('kitchen_confirmed_at')
                      ->where('status', 'Sudah Diambil');
                })->orWhere('status', 'Dibatalkan');
            })
            ->latest()
            ->get();

        return view(
            'kitchen.history',
            compact('orders')
        );
    }


    /**
     * Proses Pesanan
     */
    public function process($id)
    {
        $order = Order::where('payment_status', 'Dibayar')
            ->whereNotNull('kitchen_confirmed_at')
            ->where('status', 'Menunggu')
            ->findOrFail($id);

        $order->update([
            'status' => 'Diproses',
            'kitchen_started_at' => now(),
            'kitchen_started_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pesanan sedang diproses.');
    }


    /**
     * Siap Diambil
     */
    public function complete($id)
    {
        $order = Order::where('payment_status', 'Dibayar')
            ->whereNotNull('kitchen_confirmed_at')
            ->where('status', 'Diproses')
            ->with('items.product')
            ->findOrFail($id);

        $order->update([
            'status' => 'Selesai',
            'kitchen_completed_at' => now(),
            'kitchen_completed_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Pesanan siap diambil.'
            );
    }


    /**
     * Konfirmasi Pesanan Sudah Diambil
     */
    public function confirmPickup($id)
    {
        $order = Order::where('payment_status', 'Dibayar')
            ->whereNotNull('kitchen_confirmed_at')
            ->where('status', 'Selesai')
            ->findOrFail($id);

        $order->update([
            'status' => 'Sudah Diambil',
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Pesanan berhasil dikonfirmasi sudah diambil.'
            );
    }
}
