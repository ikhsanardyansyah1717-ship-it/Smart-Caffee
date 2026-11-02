<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KitchenController extends Controller
{
    /**
     * Dashboard Kitchen
     */
    public function dashboard(Request $request)
    {
        $request->validate(['date' => ['nullable', 'date_format:Y-m-d']]);
        $date = $request->input('date', now()->toDateString());
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
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $menuAverages = collect();
        Order::with('items')->whereNotNull('kitchen_started_at')
            ->whereNotNull('kitchen_completed_at')
            ->where('kitchen_completed_at', '<=', Carbon::parse($date)->endOfDay())
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

        return view(
            'kitchen.dashboard',
            compact('orders', 'date', 'menuAverages')
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
