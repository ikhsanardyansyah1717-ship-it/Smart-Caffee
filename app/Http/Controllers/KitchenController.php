<?php

namespace App\Http\Controllers;

use App\Models\Order;

class KitchenController extends Controller
{
    /**
     * Dashboard Kitchen
     */
    public function dashboard()
    {
        $orders = Order::with([
                'items.product',
                'payment'
            ])
            ->where('payment_status', 'Dibayar')
            ->whereIn('status', [
                'Menunggu',
                'Diproses',
                'Selesai'
            ])
            ->latest()
            ->get();

        return view(
            'kitchen.dashboard',
            compact('orders')
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
            ->where('status', 'Menunggu')
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
        $orders = Order::with([
                'items.product',
                'payment'
            ])
            ->where('payment_status', 'Dibayar')
            ->where('status', 'Diproses')
            ->latest()
            ->get();

        return view(
            'kitchen.processing',
            compact('orders')
        );
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
        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'Diproses',
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Pesanan sedang diproses.'
            );
    }


    /**
     * Siap Diambil
     */
    public function complete($id)
    {
        $order = Order::where('payment_status', 'Dibayar')
            ->where('status', 'Diproses')
            ->findOrFail($id);

        $order->update([
            'status' => 'Selesai',
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