<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;

class KasirController extends Controller
{
    /**
     * =========================================================
     * PRODUK TERSEDIA
     * =========================================================
     */
    private function products()
    {
        return Product::where('is_available', true)
            ->orderBy('name')
            ->get();
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
         * PESANAN HARI INI
         *
         * Semua order yang dibuat hari ini.
         */
        $pesananHariIni = Order::whereDate('created_at', $today)
            ->count();


        /**
         * MENUNGGU BAYAR
         *
         * Semua order yang belum dibayar.
         */
        $menungguBayar = Order::where('payment_status', 'Belum Dibayar')
            ->count();


        /**
         * PENJUALAN HARI INI
         *
         * Berdasarkan pembayaran yang berhasil hari ini.
         */
        $penjualanHariIni = Payment::whereDate('paid_at', $today)
            ->where('status', 'Berhasil')
            ->sum('amount');


        /**
         * TRANSAKSI SELESAI
         *
         * Jumlah pembayaran berhasil hari ini.
         */
        $transaksiSelesai = Payment::whereDate('paid_at', $today)
            ->where('status', 'Berhasil')
            ->count();


        /**
         * PELANGGAN HARI INI
         *
         * Pelanggan yang melakukan pembayaran berhasil hari ini.
         */
        $pelangganHariIni = Order::whereHas('payment', function ($query) use ($today) {
                $query->whereDate('paid_at', $today)
                    ->where('status', 'Berhasil');
            })
            ->whereNotNull('customer_name')
            ->distinct('customer_name')
            ->count('customer_name');


        /**
         * PESANAN PRIORITAS
         *
         * Semua pesanan yang masih berstatus Menunggu.
         */
        $pesananPrioritas = Order::where('status', 'Menunggu')
            ->count();


        /**
         * PESANAN TERBARU
         */
        $orders = Order::with([
                'items',
                'payment'
            ])
            ->latest()
            ->take(5)
            ->get();


        /**
         * PRODUK
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

        // Meja yang sedang digunakan oleh pesanan aktif.
        // Meja Selesai / Dibatalkan otomatis tersedia kembali.
        $occupiedTables = Order::whereNotNull('table_number')
            ->where('table_number', '!=', 'Take Away')
            ->whereIn('status', ['Menunggu', 'Diproses'])
            ->pluck('table_number')
            ->map(fn ($table) => strtoupper(trim($table)))
            ->unique()
            ->values()
            ->toArray();

        return view('kasir.orders', compact(
            'orders',
            'products',
            'occupiedTables'
        ));
    }


    /**
     * =========================================================
     * HALAMAN PEMBAYARAN
     * =========================================================
     */
    public function payment(Request $request)
    {
        $orders = Order::with([
                'items'
            ])
            ->where('payment_status', 'Belum Dibayar')
            ->latest()
            ->get();

        $receiptOrder = null;
        $receiptCashReceived = 0;
        $receiptChange = 0;

        if ($request->filled('receipt')) {
            $receiptOrder = Order::with(['items', 'payment'])
                ->where('id', $request->receipt)
                ->where('payment_status', 'Dibayar')
                ->first();

            $receiptCashReceived = (float) session('receipt_cash_received', 0);
            $receiptChange = (float) session('receipt_change', 0);
        }

        return view('kasir.payment', compact(
            'orders',
            'receiptOrder',
            'receiptCashReceived',
            'receiptChange'
        ));
    }


    /**
     * =========================================================
     * SELESAIKAN PEMBAYARAN
     * =========================================================
     */
    public function completePayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string|max:50',
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        $paidOrderId = null;
        $cashReceived = (float) $request->input('cash_received', 0);

        DB::transaction(function () use ($request, $id, &$paidOrderId, $cashReceived) {
            $order = Order::findOrFail($id);

            if ($order->payment_status === 'Dibayar') {
                abort(400, 'Pesanan ini sudah dibayar.');
            }

            if ($request->payment_method === 'Cash' && $cashReceived < (float) $order->total) {
                abort(422, 'Uang yang diterima tidak mencukupi.');
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $order->total,
                'status' => 'Berhasil',
                'paid_at' => now(),
            ]);

            $order->update([
                'payment_status' => 'Dibayar',
                'status' => 'Selesai',
            ]);

            $paidOrderId = $order->id;

            $change = $request->payment_method === 'Cash'
                ? max(0, $cashReceived - (float) $order->total)
                : 0;

            session([
                'receipt_cash_received' => $request->payment_method === 'Cash' ? $cashReceived : 0,
                'receipt_change' => $change,
            ]);
        });

        return redirect()
            ->route('kasir.payment', ['receipt' => $paidOrderId])
            ->with('success', 'Pembayaran berhasil diselesaikan.');
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
            'customer' => ['required', 'string', 'max:100'],
            'table' => ['required', 'string', 'max:50'],
            'items' => ['required', 'json'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $items = json_decode($request->items, true);

        if (!is_array($items) || count($items) === 0) {
            return back()
                ->withErrors(['items' => 'Silakan pilih minimal satu menu.'])
                ->withInput();
        }

        // Validasi isi item sebelum masuk transaksi.
        foreach ($items as $item) {
            if (
                !is_array($item) ||
                empty($item['product_id']) ||
                !isset($item['quantity']) ||
                (int) $item['quantity'] < 1
            ) {
                return back()
                    ->withErrors(['items' => 'Data menu tidak valid. Silakan pilih menu kembali.'])
                    ->withInput();
            }
        }

        $table = trim($request->table);

        if (strcasecmp($table, 'Take Away') === 0) {
            $table = 'Take Away';
        } else {
            $table = strtoupper($table);

            if (!preg_match('/^[A-Z](?:0[1-9]|[1-9][0-9]|100)$/', $table)) {
                return back()
                    ->withErrors(['table' => 'Nomor meja tidak valid. Pilih meja A01-Z100 atau Take Away.'])
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request, $items, $table) {

                // Cegah dua kasir membuat pesanan aktif pada meja yang sama.
                if ($table !== 'Take Away') {
                    $tableTaken = Order::where('table_number', $table)
                        ->whereIn('status', ['Menunggu', 'Diproses'])
                        ->lockForUpdate()
                        ->exists();

                    if ($tableTaken) {
                        throw new \RuntimeException(
                            "Meja {$table} sedang digunakan. Silakan pilih meja lain."
                        );
                    }
                }

                $subtotal = 0;

                $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999);

                // Pastikan nomor order unik.
                while (Order::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                }

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => auth()->id(),
                    'customer_name' => trim($request->customer),
                    'table_number' => $table,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'status' => 'Menunggu',
                    'payment_status' => 'Belum Dibayar',
                ]);

                foreach ($items as $item) {
                    $product = Product::where('id', (int) $item['product_id'])
                        ->where('is_available', true)
                        ->first();

                    if (!$product) {
                        throw new \RuntimeException(
                            'Produk tidak ditemukan atau sudah tidak tersedia.'
                        );
                    }

                    $quantity = (int) $item['quantity'];

                    if ($quantity < 1) {
                        throw new \RuntimeException(
                            "Jumlah {$product->name} tidak valid."
                        );
                    }

                    $itemSubtotal = (float) $product->price * $quantity;
                    $subtotal += $itemSubtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'subtotal' => $itemSubtotal,
                    ]);
                }

                $tax = 0;
                $total = $subtotal + $tax;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                ]);
            });

            return redirect()
                ->route('kasir.orders')
                ->with('success', 'Pesanan berhasil disimpan ke database.');

        } catch (\RuntimeException $e) {
            return back()
                ->withErrors(['order' => $e->getMessage()])
                ->withInput();

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors([
                    'order' => 'Pesanan gagal disimpan ke database. Periksa struktur tabel orders/order_items dan log Laravel.'
                ])
                ->withInput();
        }
    }

}