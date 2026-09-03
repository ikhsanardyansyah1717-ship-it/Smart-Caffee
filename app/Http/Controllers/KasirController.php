<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;

class KasirController extends Controller
{
    /**
     * =========================================================
     * DATA PRODUK
     * =========================================================
     */
    private function products()
    {
        return Product::where('is_available', true)->get();
    }


    /**
     * =========================================================
     * DASHBOARD KASIR
     * =========================================================
     */
    public function dashboard()
    {
        $today = now()->toDateString();


        /**
         * =====================================================
         * PESANAN HARI INI
         * =====================================================
         *
         * Menghitung pesanan yang pembayarannya
         * berhasil dilakukan hari ini.
         *
         * Contoh:
         * Order dibuat 2 September
         * Dibayar 3 September
         *
         * Maka tetap dihitung sebagai transaksi
         * pada tanggal 3 September.
         */
        $pesananHariIni = Order::whereHas('payment', function ($query) use ($today) {
            $query->whereDate('paid_at', $today)
                ->where('status', 'Berhasil');
        })->count();


        /**
         * =====================================================
         * MENUNGGU BAYAR
         * =====================================================
         *
         * SEMUA pesanan yang belum dibayar.
         *
         * Tidak dibatasi tanggal.
         *
         * Jadi pesanan dari kemarin yang belum dibayar
         * tetap muncul di dashboard.
         */
        $menungguBayar = Order::where(
            'payment_status',
            'Belum Dibayar'
        )->count();


        /**
         * =====================================================
         * PENJUALAN HARI INI
         * =====================================================
         *
         * Total pembayaran yang berhasil hari ini.
         */
        $penjualanHariIni = Payment::whereDate(
                'paid_at',
                $today
            )
            ->where('status', 'Berhasil')
            ->sum('amount');


        /**
         * =====================================================
         * TRANSAKSI SELESAI
         * =====================================================
         *
         * Jumlah pembayaran berhasil hari ini.
         */
        $transaksiSelesai = Payment::whereDate(
                'paid_at',
                $today
            )
            ->where('status', 'Berhasil')
            ->count();


        /**
         * =====================================================
         * PELANGGAN HARI INI
         * =====================================================
         *
         * Menghitung pelanggan yang melakukan
         * pembayaran berhasil hari ini.
         */
        $pelangganHariIni = Order::whereHas('payment', function ($query) use ($today) {

                $query->whereDate('paid_at', $today)
                    ->where('status', 'Berhasil');

            })
            ->whereNotNull('customer_name')
            ->distinct('customer_name')
            ->count('customer_name');


        /**
         * =====================================================
         * PESANAN PRIORITAS
         * =====================================================
         *
         * SEMUA pesanan yang statusnya masih Menunggu.
         *
         * Tidak dibatasi tanggal.
         *
         * Jadi pesanan yang belum diproses tetap muncul
         * walaupun dibuat kemarin.
         */
        $pesananPrioritas = Order::where(
            'status',
            'Menunggu'
        )->count();


        /**
         * =====================================================
         * PESANAN TERBARU
         * =====================================================
         */
        $orders = Order::with([
                'items',
                'payment'
            ])
            ->latest()
            ->take(5)
            ->get();


        /**
         * =====================================================
         * PRODUK
         * =====================================================
         */
        $products = $this->products();


        /**
         * =====================================================
         * KIRIM DATA KE DASHBOARD
         * =====================================================
         */
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
     * =========================================================
     * DAFTAR PESANAN
     * =========================================================
     */
    public function orders()
    {
        $orders = Order::with([
                'items',
                'payment'
            ])
            ->latest()
            ->get();

        $products = $this->products();

        return view('kasir.orders', compact(
            'orders',
            'products'
        ));
    }


    /**
     * =========================================================
     * HALAMAN PEMBAYARAN
     * =========================================================
     */
    public function payment()
    {
        $orders = Order::with([
                'items'
            ])
            ->where(
                'payment_status',
                'Belum Dibayar'
            )
            ->latest()
            ->get();

        return view('kasir.payment', compact(
            'orders'
        ));
    }


    /**
     * =========================================================
     * SELESAIKAN PEMBAYARAN
     * =========================================================
     */
    public function completePayment(Request $request, $id)
    {
        /**
         * Validasi metode pembayaran
         */
        $request->validate([
            'payment_method' => 'required|string|max:50',
        ]);


        /**
         * Simpan pembayaran dan update order
         * dalam satu transaksi database.
         */
        DB::transaction(function () use ($request, $id) {

            $order = Order::findOrFail($id);


            /**
             * Jangan sampai dibayar dua kali.
             */
            if ($order->payment_status === 'Dibayar') {

                abort(
                    400,
                    'Pesanan ini sudah dibayar.'
                );
            }


            /**
             * Simpan pembayaran
             * ke tabel payments.
             */
            Payment::create([
                'order_id' => $order->id,

                'payment_method' =>
                    $request->payment_method,

                'amount' =>
                    $order->total,

                'status' =>
                    'Berhasil',

                'paid_at' =>
                    now(),
            ]);


            /**
             * Update status order.
             */
            $order->update([
                'payment_status' => 'Dibayar',
                'status' => 'Selesai',
            ]);
        });


        /**
         * Setelah pembayaran berhasil,
         * kembali ke halaman riwayat.
         */
        return redirect()
            ->route('kasir.history')
            ->with(
                'success',
                'Pembayaran berhasil diselesaikan.'
            );
    }


    /**
     * =========================================================
     * RIWAYAT TRANSAKSI
     * =========================================================
     */
    public function history()
    {
        $orders = Order::with([
                'items',
                'payment'
            ])
            ->where('status', 'Selesai')
            ->latest()
            ->get();

        return view('kasir.history', compact(
            'orders'
        ));
    }


    /**
     * =========================================================
     * SIMPAN PESANAN
     * =========================================================
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

            'order_number' =>
                'ORD-' .
                now()->format('YmdHis') .
                '-' .
                rand(100, 999),

            'user_id' =>
                auth()->id(),

            'customer_name' =>
                $request->customer,

            'table_number' =>
                $request->table,

            'subtotal' =>
                $request->total,

            'tax' =>
                0,

            'total' =>
                $request->total,

            'status' =>
                'Menunggu',

            'payment_status' =>
                'Belum Dibayar',
        ]);


        return redirect()
            ->route('kasir.orders')
            ->with(
                'success',
                'Pesanan berhasil dibuat.'
            );
    }
}