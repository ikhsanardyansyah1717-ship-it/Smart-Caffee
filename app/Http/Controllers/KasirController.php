<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

class KasirController extends Controller
{
    /**
     * Data produk yang tersedia
     */
    private function products()
    {
        return Product::where('is_available', true)->get();
    }

    /**
     * Dashboard Kasir
     */
    public function dashboard()
    {
        // Tanggal hari ini
        $today = now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Pesanan terbaru
        |--------------------------------------------------------------------------
        */
        $orders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pesanan hari ini
        |--------------------------------------------------------------------------
        */
        $pesananHariIni = Order::whereDate('created_at', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pesanan yang belum dibayar
        |--------------------------------------------------------------------------
        */
        $menungguBayar = Order::whereDate('created_at', $today)
            ->where('payment_status', 'Belum Dibayar')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Penjualan hari ini
        |--------------------------------------------------------------------------
        */
        $penjualanHariIni = Order::whereDate('created_at', $today)
            ->where('payment_status', 'Lunas')
            ->sum('total');

        /*
        |--------------------------------------------------------------------------
        | Transaksi selesai hari ini
        |--------------------------------------------------------------------------
        */
        $transaksiSelesai = Order::whereDate('created_at', $today)
            ->where('status', 'Selesai')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Jumlah pelanggan hari ini
        |--------------------------------------------------------------------------
        */
        $pelangganHariIni = Order::whereDate('created_at', $today)
            ->whereNotNull('customer_name')
            ->distinct('customer_name')
            ->count('customer_name');

        /*
        |--------------------------------------------------------------------------
        | Pesanan prioritas
        |--------------------------------------------------------------------------
        |
        | Untuk sementara menggunakan pesanan yang masih menunggu.
        | Kalau database kamu memiliki kolom priority, bagian ini
        | bisa dibuat lebih spesifik.
        |
        */
        $pesananPrioritas = Order::whereDate('created_at', $today)
            ->where('status', 'Menunggu')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Produk
        |--------------------------------------------------------------------------
        */
        $products = $this->products();

        return view('kasir.dashboard', compact(
            'orders',
            'products',
            'pesananHariIni',
            'menungguBayar',
            'penjualanHariIni',
            'transaksiSelesai',
            'pelangganHariIni',
            'pesananPrioritas'
        ));
    }

    /**
     * Halaman Pesanan Kasir
     */
    public function orders()
    {
        $orders = Order::with('items')
            ->latest()
            ->get();

        $products = $this->products();

        return view('kasir.orders', compact(
            'orders',
            'products'
        ));
    }

    /**
     * Halaman Pembayaran
     */
    public function payment()
    {
        $orders = Order::with('items')
            ->where('payment_status', 'Belum Dibayar')
            ->latest()
            ->get();

        return view('kasir.payment', compact(
            'orders'
        ));
    }

    /**
     * Halaman Riwayat
     */
    public function history()
    {
        $orders = Order::with('items')
            ->where('status', 'Selesai')
            ->latest()
            ->get();

        return view('kasir.history', compact(
            'orders'
        ));
    }

    /**
     * Simpan pesanan baru dari Kasir
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'customer' => 'required|string|max:100',
            'table' => 'required|string|max:50',
            'items' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        Order::create([
            'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . rand(100, 999),

            'user_id' => auth()->id(),

            'customer_name' => $request->customer,

            'table_number' => $request->table,

            'subtotal' => $request->total,

            'tax' => 0,

            'total' => $request->total,

            'status' => 'Menunggu',

            'payment_status' => 'Belum Dibayar',
        ]);

        return redirect()
            ->route('kasir.orders')
            ->with(
                'success',
                'Pesanan berhasil dibuat.'
            );
    }
}