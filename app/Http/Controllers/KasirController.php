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
    public function dashboard(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $tanggal = $request->input('date', now()->toDateString());

        $pesananHariIni = Order::whereDate('created_at', $tanggal)
            ->count();

        $menungguBayar = Order::whereDate('created_at', $tanggal)
            ->where('payment_status', 'Belum Dibayar')
            ->count();

        $penjualanHariIni = Payment::whereDate('paid_at', $tanggal)
            ->where('status', 'Berhasil')
            ->sum('amount');

        $transaksiSelesai = Payment::whereDate('paid_at', $tanggal)
            ->where('status', 'Berhasil')
            ->count();

        $pelangganHariIni = Order::whereHas('payment', function ($query) use ($tanggal) {
                $query->whereDate('paid_at', $tanggal)
                    ->where('status', 'Berhasil');
            })
            ->whereNotNull('customer_name')
            ->distinct('customer_name')
            ->count('customer_name');

        $pesananPrioritas = Order::whereDate('created_at', $tanggal)
            ->where('status', 'Menunggu')
            ->where('payment_status', 'Dibayar')
            ->count();

        $orders = Order::with(['items', 'payment'])
            ->whereDate('created_at', $tanggal)
            ->latest()
            ->take(5)
            ->get();

        $products = Product::orderBy('name')->get();
        $productAvailability = $products;
        $stockWarnings = Product::where('stock', '<=', 5)->orderBy('stock')->orderBy('name')->get();
        $stockWarningCount = $stockWarnings->count();

        return view('kasir.dashboard', compact(
            'orders',
            'products',
            'pesananHariIni',
            'menungguBayar',
            'penjualanHariIni',
            'transaksiSelesai',
            'pelangganHariIni',
            'pesananPrioritas',
            'stockWarnings',
            'stockWarningCount',
            'productAvailability',
            'tanggal'
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


        /**
         * MEJA YANG SEDANG DIGUNAKAN
         *
         * Hanya Menunggu dan Diproses yang dianggap aktif.
         * Jika Selesai / Dibatalkan maka meja tersedia kembali.
         */
        $occupiedTables = Order::whereNotNull('table_number')
            ->where('table_number', '!=', 'Take Away')
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai'])
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
     * Selesaikan penggunaan meja dan kembalikan meja menjadi tersedia.
     * Pesanan meja harus sudah dibayar sebelum bisa diselesaikan.
     */
    public function finishTable($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->table_number || $order->table_number === 'Take Away') {
            return redirect()->route('kasir.orders')
                ->with('error', 'Pesanan ini tidak menggunakan meja.');
        }

        if (!in_array($order->status, ['Menunggu', 'Diproses', 'Selesai'], true)) {
            return redirect()->route('kasir.orders')
                ->with('error', 'Meja ini sudah diselesaikan atau tidak sedang digunakan.');
        }

        $table = $order->table_number;

        $unpaidExists = Order::where('table_number', $table)
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai'])
            ->where('payment_status', '!=', 'Dibayar')
            ->exists();

        if ($unpaidExists) {
            return redirect()->route('kasir.orders')
                ->with('error', "Pembayaran meja {$table} belum lunas. Konfirmasi pembayaran sebelum menyelesaikan meja.");
        }

        Order::where('table_number', $table)
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai'])
            ->update(['status' => 'Sudah Diambil']);

        return redirect()->route('kasir.orders')
            ->with('success', "Meja {$table} selesai digunakan dan sekarang tersedia kembali.");
    }


    /**
     * =========================================================
     * KONFIRMASI PESANAN MANUAL
     * =========================================================
     *
     * Hanya pesanan manual kasir yang masuk ke sini karena
     * pesanan Customer sudah otomatis berstatus Dibayar.
     */
    public function confirmManualPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:Cash,QRIS'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
        ]);

        $paymentMethod = $validated['payment_method'];
        $cashReceived = (float) ($validated['cash_received'] ?? 0);

        try {
            DB::transaction(function () use ($id, $paymentMethod, $cashReceived) {
                $order = Order::whereKey($id)->lockForUpdate()->firstOrFail();

                if ($order->payment_status === 'Dibayar') {
                    throw new \RuntimeException('Pesanan ini sudah dibayar.');
                }

                if (!in_array($order->status, ['Menunggu', 'Diproses', 'Selesai'], true)) {
                    throw new \RuntimeException('Pesanan tidak dapat dibayar karena statusnya tidak aktif.');
                }

                if ($paymentMethod === 'Cash' && $cashReceived < (float) $order->total) {
                    throw new \RuntimeException('Uang yang diterima tidak mencukupi.');
                }

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethod,
                    'amount' => $order->total,
                    'status' => 'Berhasil',
                    'paid_at' => now(),
                ]);

                $order->update([
                    'payment_status' => 'Dibayar',
                    'status' => 'Menunggu',
                ]);
            });

            return redirect()
                ->route('kasir.orders')
                ->with('success', 'Pembayaran berhasil dikonfirmasi. Pesanan sudah masuk ke Kitchen.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('kasir.orders')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('kasir.orders')
                ->with('error', 'Pembayaran gagal diproses. Silakan coba kembali.');
        }
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
    ->where(function ($query) {
        $query->where('status', 'Sudah Diambil')
              ->orWhere('status', 'Dibatalkan');
    })
    ->latest()
    ->get();

    return view('kasir.history', compact('orders'));
}

    /**
     * =========================================================
     * SIMPAN PESANAN
     * =========================================================
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_type' => ['required', 'in:meja,take_away'],
            'customer' => ['required', 'string', 'max:100'],
            'table' => ['required', 'string', 'max:50'],
            'items' => ['required', 'json'],
        ]);

        $items = json_decode($request->items, true);
        if (!is_array($items) || count($items) === 0) {
            return back()->withErrors(['items' => 'Silakan pilih minimal satu menu.'])->withInput();
        }

        // Gabungkan produk yang sama agar stok tidak terhitung keliru jika muncul lebih dari sekali.
        $quantities = [];
        foreach ($items as $item) {
            if (!is_array($item) || empty($item['product_id']) || !isset($item['quantity']) || filter_var($item['quantity'], FILTER_VALIDATE_INT) === false || (int) $item['quantity'] < 1) {
                return back()->withErrors(['items' => 'Data menu tidak valid. Silakan pilih menu kembali.'])->withInput();
            }
            $id = (int) $item['product_id'];
            $quantities[$id] = ($quantities[$id] ?? 0) + (int) $item['quantity'];
        }

        $table = $request->input('order_type') === 'take_away' ? 'Take Away' : trim((string) $request->input('table'));
        if (strcasecmp($table, 'Take Away') === 0) {
            $table = 'Take Away';
        } else {
            $table = strtoupper($table);
            if (!preg_match('/^[A-Z](?:0[1-9]|[1-9][0-9]|100)$/', $table)) {
                return back()->withErrors(['table' => 'Nomor meja tidak valid. Pilih meja A01-Z100 atau Take Away.'])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request, $quantities, $table) {
                if ($table !== 'Take Away') {
                    $tableTaken = Order::where('table_number', $table)
                        ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai'])
                        ->lockForUpdate()->exists();
                    if ($tableTaken) {
                        throw new \RuntimeException("Meja {$table} sedang digunakan. Silakan pilih meja lain.");
                    }
                }

                // Lock semua produk dalam urutan ID yang konsisten untuk mengurangi risiko deadlock.
                $products = Product::whereIn('id', array_keys($quantities))
                    ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

                foreach ($quantities as $id => $quantity) {
                    $product = $products->get($id);
                    if (!$product) {
                        throw new \RuntimeException('Salah satu produk tidak ditemukan.');
                    }
                    if ((int) $product->stock < $quantity) {
                        throw new \RuntimeException("Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}, diminta: {$quantity}.");
                    }
                    if ((int) $product->stock <= 0 || !$product->is_available) {
                        throw new \RuntimeException("Produk {$product->name} sedang tidak tersedia.");
                    }
                }

                $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999);
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

                $subtotal = 0;
                foreach ($quantities as $id => $quantity) {
                    $product = $products->get($id);
                    $itemSubtotal = (float) $product->price * $quantity;
                    $subtotal += $itemSubtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'cost_price' => $product->cost_price,
                        'price' => $product->price,
                        'subtotal' => $itemSubtotal,
                    ]);

                    $product->stock = (int) $product->stock - $quantity;
                    $product->is_available = $product->stock > 0;
                    $product->save();
                }

                $order->update(['subtotal' => $subtotal, 'tax' => 0, 'total' => $subtotal]);
            });

            return redirect()->route('kasir.orders')->with('success', 'Pesanan berhasil disimpan dan stok produk telah diperbarui.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['order' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['order' => 'Pesanan gagal disimpan. Periksa log Laravel untuk detail kesalahan.'])->withInput();
        }
    }
}
