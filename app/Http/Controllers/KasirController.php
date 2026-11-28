<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\MidtransSnapService;
use App\Services\MidtransPaymentStatusService;
use App\Services\FinancialReportService;
use Illuminate\Support\Facades\URL;

class KasirController extends Controller
{
    private function pendingCustomerReservation(int $productId): int
    {
        return (int) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $productId)
            ->where('payments.status', 'Pending')
            ->where('orders.payment_status', 'Belum Dibayar')
            ->where('orders.status', '!=', 'Dibatalkan')
            ->sum('order_items.quantity');
    }

    /**
     * =========================================================
     * PRODUK TERSEDIA
     * =========================================================
     */
    private function products()
    {
        $pendingReservations = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->whereColumn('order_items.product_id', 'products.id')
            ->where('payments.status', 'Pending')
            ->where('orders.payment_status', 'Belum Dibayar')
            ->where('orders.status', '!=', 'Dibatalkan')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');

        return Product::query()
            ->select('products.*')
            ->selectSub($pendingReservations, 'reserved_stock')
            ->where('is_available', true)
            ->orderBy('name')
            ->get()
            ->each(function ($product) {
                $product->available_stock = max(0, (int) $product->stock - (int) $product->reserved_stock);
            })
            ->filter(fn ($product) => $product->available_stock > 0)
            ->values();
    }


    /**
     * =========================================================
     * DASHBOARD KASIR
     * =========================================================
     */
    public function dashboard(Request $request)
    {
        [$periodStart, $periodEnd] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('start'),
            $request->query('end')
        );
        $periodLabel = FinancialReportService::periodLabel($periodStart, $periodEnd);

        $pesananHariIni = Order::whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        $menungguBayar = Order::whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('payment_status', 'Belum Dibayar')
            ->count();

        $penjualanHariIni = Payment::whereBetween('paid_at', [$periodStart, $periodEnd])
            ->where('status', 'Berhasil')
            ->sum('amount');

        $transaksiSelesai = Payment::whereBetween('paid_at', [$periodStart, $periodEnd])
            ->where('status', 'Berhasil')
            ->count();

        $pelangganHariIni = Order::whereHas('payment', function ($query) use ($periodStart, $periodEnd) {
                $query->whereBetween('paid_at', [$periodStart, $periodEnd])
                    ->where('status', 'Berhasil');
            })
            ->whereNotNull('customer_name')
            ->distinct('customer_name')
            ->count('customer_name');

        $pesananPrioritas = Order::whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('status', 'Menunggu')
            ->where('payment_status', 'Dibayar')
            ->count();

        $orders = Order::with(['items', 'payment'])
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->latest()
            ->take(5)
            ->get();

        $pendingReservations = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->whereColumn('order_items.product_id', 'products.id')
            ->where('payments.status', 'Pending')
            ->where('orders.payment_status', 'Belum Dibayar')
            ->where('orders.status', '!=', 'Dibatalkan')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');
        $products = Product::query()
            ->select('products.*')
            ->selectSub($pendingReservations, 'reserved_stock')
            ->orderBy('name')->get();
        $products->each(function ($product) {
            $product->available_stock = max(0, (int) $product->stock - (int) $product->reserved_stock);
        });
        $productAvailability = $products;
        $stockWarnings = $products->filter(fn ($product) => $product->available_stock <= 5)
            ->sortBy('available_stock')->values();
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
            'periodStart',
            'periodEnd',
            'periodLabel'
        ));
    }


    /**
     * =========================================================
     * DAFTAR PESANAN
     * =========================================================
     */
    public function tableAvailability(): \Illuminate\Http\JsonResponse
    {
        $occupied = Order::whereNotNull('table_number')
            ->where('table_number', '!=', 'Take Away')
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai', 'Sudah Diambil'])
            ->whereNull('table_finished_at')
            ->pluck('table_number')
            ->map(fn ($table) => strtoupper(trim($table)))
            ->unique()
            ->values();

        return response()->json(['occupied' => $occupied]);
    }
    public function orders()
    {
        $orders = Order::with([
                'items',
                'payment.attempts'
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
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai', 'Sudah Diambil'])
            ->whereNull('table_finished_at')
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
    public function transferProof($id)
    {
        $payment = Payment::where('order_id', $id)->firstOrFail();
        abort_unless($payment->payment_method === 'Bank Transfer' && $payment->transfer_proof_path, 404);
        abort_unless(Storage::disk('local')->exists($payment->transfer_proof_path), 404);

        $disk = Storage::disk('local');
        $filePath = $disk->path($payment->transfer_proof_path);
        $mimeType = $disk->mimeType($payment->transfer_proof_path) ?: 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
    public function confirmPickup($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status !== 'Dibayar' || !$order->kitchen_confirmed_at) {
            return redirect()->route('kasir.orders')->with('error', 'Pesanan harus lunas dan sudah dikirim ke Kitchen.');
        }

        if ($order->status === 'Sudah Diambil') {
            return redirect()->route('kasir.orders')->with('success', 'Pesanan ini sudah dikonfirmasi diambil.');
        }

        if ($order->status !== 'Selesai') {
            return redirect()->route('kasir.orders')->with('error', 'Pesanan belum siap diambil dari Kitchen.');
        }

        $order->update(['status' => 'Sudah Diambil']);

        return redirect()->route('kasir.orders')->with('success', 'Pesanan berhasil dikonfirmasi sudah diambil.');
    }

    public function finishTable($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->table_number || $order->table_number === 'Take Away') {
            return redirect()->route('kasir.orders')->with('error', 'Pesanan ini tidak menggunakan meja.');
        }

        if ($order->table_finished_at) {
            return redirect()->route('kasir.orders')->with('success', "Meja {$order->table_number} sudah diselesaikan.");
        }

        if ($order->status !== 'Sudah Diambil') {
            return redirect()->route('kasir.orders')->with('error', 'Konfirmasi pesanan sudah diambil sebelum menyelesaikan meja.');
        }

        $table = $order->table_number;
        $activeOrders = Order::where('table_number', $table)
            ->where('branch_id', $order->branch_id)
            ->whereNull('table_finished_at')
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai', 'Sudah Diambil'])
            ->get(['status', 'payment_status']);

        $unpaidExists = $activeOrders->contains(fn ($activeOrder) => $activeOrder->payment_status !== 'Dibayar');
        $notPickedUpExists = $activeOrders->contains(fn ($activeOrder) => $activeOrder->status !== 'Sudah Diambil');

        if ($unpaidExists) {
            return redirect()->route('kasir.orders')->with('error', "Pembayaran meja {$table} belum lunas.");
        }

        if ($notPickedUpExists) {
            return redirect()->route('kasir.orders')->with('error', "Pastikan semua pesanan di meja {$table} sudah diambil terlebih dahulu.");
        }

        Order::where('table_number', $table)
            ->where('branch_id', $order->branch_id)
            ->whereNull('table_finished_at')
            ->whereIn('status', ['Sudah Diambil'])
            ->update(['table_finished_at' => now()]);

        return redirect()->route('kasir.orders')->with('success', "Meja {$table} selesai digunakan dan sekarang tersedia kembali.");
    }
    /**
     * =========================================================
     * KONFIRMASI PESANAN MANUAL
     * =========================================================
     *
     * Mengonfirmasi pembayaran customer yang masih Pending atau
     * pembayaran manual kasir yang belum memiliki payment record.
     */
    public function confirmManualPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_method' => ['nullable', 'string', 'in:Cash,QRIS,Bank Transfer'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
        ]);

        $paymentMethod = $validated['payment_method'] ?? null;
        $cashReceived = (float) ($validated['cash_received'] ?? 0);
        $cashier = $request->user();

        try {
            DB::transaction(function () use ($id, $paymentMethod, $cashReceived, $cashier) {
                $order = Order::with('items')->whereKey($id)->lockForUpdate()->firstOrFail();
                $payment = $order->payment()->lockForUpdate()->first();

                if ($order->payment_status === 'Dibayar') {
                    throw new \RuntimeException('Pesanan ini sudah dibayar.');
                }

                if (!in_array($order->status, ['Menunggu', 'Diproses', 'Selesai'], true)) {
                    throw new \RuntimeException('Pesanan tidak dapat dibayar karena statusnya tidak aktif.');
                }

                if ($payment && $payment->status === 'Berhasil') {
                    throw new \RuntimeException('Pembayaran ini sudah berhasil dikonfirmasi.');
                }

                if (($paymentMethod ?? $payment?->payment_method) === 'Bank Transfer' && !$payment?->transfer_proof_path) {
                    throw new \RuntimeException('Bukti transfer belum dikirim. Minta pelanggan mengunggah bukti sebelum konfirmasi.');
                }
                if ($payment && $payment->attempts()->exists()) {
                    throw new \RuntimeException('Pembayaran QRIS Midtrans harus dikonfirmasi dari status Midtrans, bukan secara manual oleh kasir.');
                }

                $confirmedMethod = $paymentMethod ?: $payment?->payment_method;
                if (!$confirmedMethod) {
                    throw new \RuntimeException('Pilih metode pembayaran sebelum mengonfirmasi transaksi kasir.');
                }
                if ($confirmedMethod === 'QRIS') {
                    throw new \RuntimeException('Pembayaran QRIS harus dibuat dan diverifikasi melalui kode QR Midtrans.');
                }
                if ($payment && abs((float) $payment->amount - (float) $order->total) >= 0.01) {
                    throw new \RuntimeException('Nominal pembayaran tidak sesuai dengan total pesanan. Periksa detail pembayaran.');
                }

                if ($payment && $order->payment_status !== 'Dibayar') {
                    $quantities = $order->items->groupBy('product_id')->map(fn ($items) => $items->sum('quantity'));
                    $products = Product::whereIn('id', $quantities->keys())->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                    foreach ($quantities as $productId => $quantity) {
                        $product = $products->get($productId);
                        if (!$product || (int) $product->stock < $quantity) {
                            throw new \RuntimeException('Stok berubah dan tidak cukup untuk menyelesaikan pesanan ini. Periksa stok produk.');
                        }
                        $product->stock = (int) $product->stock - (int) $quantity;
                        $product->is_available = $product->stock > 0;
                        $product->save();
                    }
                }

                if ($confirmedMethod === 'Cash' && $cashReceived < (float) $order->total) {
                    throw new \RuntimeException('Uang yang diterima tidak mencukupi.');
                }

                if ($payment) {
                    $payment->update([
                        'payment_method' => $confirmedMethod,
                        'status' => 'Berhasil',
                        'paid_at' => now(),
                        'cashier_id' => $cashier?->id,
                        'cashier_name' => $cashier?->name,
                    ]);
                } else {
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_method' => $confirmedMethod,
                        'amount' => $order->total,
                        'status' => 'Berhasil',
                        'paid_at' => now(),
                        'cashier_id' => $cashier?->id,
                        'cashier_name' => $cashier?->name,
                    ]);
                }

                $order->update([
                    'payment_status' => 'Dibayar',
                    'status' => 'Menunggu',
                    'kitchen_confirmed_at' => now(),
                ]);
            });

            return redirect()
                ->route('kasir.orders')
                ->with('success', 'Pembayaran berhasil dikonfirmasi dan pesanan dikirim ke Kitchen.');
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

    public function createCashierQris(Request $request, $id, MidtransSnapService $midtrans)
    {
        $order = Order::with('payment.attempts')->findOrFail($id);
        $payment = $order->payment;

        if (!$payment || $payment->payment_method !== 'QRIS') {
            return response()->json(['message' => 'Pesanan ini tidak menggunakan QRIS.'], 422);
        }
        if ($order->payment_status === 'Dibayar' || $payment->status === 'Berhasil') {
            return response()->json(['status' => 'Dibayar', 'message' => 'Pembayaran sudah lunas.']);
        }
        if ($order->status === 'Dibatalkan') {
            return response()->json(['message' => 'Pesanan yang dibatalkan tidak dapat dibayar.'], 422);
        }
        if (!$midtrans->isConfigured()) {
            return response()->json(['message' => 'QRIS Midtrans belum dikonfigurasi. Periksa Server Key Midtrans.'], 503);
        }

        try {
            $attempt = $payment->attempts()->latest('id')->first();
            if ($attempt && $attempt->status === 'Pending' && $attempt->expires_at?->isFuture() && filled($attempt->qr_code_url)) {
                // Reuse an active attempt so reopening the cashier dialog does not charge twice.
            } else {
                if ($attempt && $attempt->status === 'Pending') {
                    $attempt->update(['status' => 'Gagal']);
                }
                $payment->update(['status' => 'Pending']);
                $attempt = $midtrans->createSession($payment->refresh());
            }

            $qrUrl = URL::temporarySignedRoute(
                'customer.orders.payment.qr',
                $attempt->expires_at,
                ['order' => $order, 'attempt' => $attempt->id],
                false,
            );

            return response()->json([
                'status' => 'Menunggu',
                'qr_url' => $qrUrl,
                'expires_at' => $attempt->expires_at->toIso8601String(),
                'message' => 'Pindai QRIS dengan aplikasi pembayaran pelanggan.',
            ])->header('Cache-Control', 'no-store, private');
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => $exception->getMessage() ?: 'QRIS belum dapat dibuat. Coba kembali.',
            ], 503);
        }
    }

    public function checkGatewayPayment(Request $request, $id, MidtransSnapService $midtrans, MidtransPaymentStatusService $statusService)
    {
        $order = Order::with('payment.attempts')->findOrFail($id);
        if ($order->payment_status === 'Dibayar' || $order->payment?->status === 'Berhasil') {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'Dibayar', 'message' => 'Pembayaran sudah lunas.']);
            }
            return redirect()->route('kasir.orders')->with('success', 'Pembayaran pesanan ini sudah lunas.');
        }

        $attempt = $order->payment?->attempts()->latest('id')->first();
        if (!$attempt || $attempt->status !== 'Pending') {
            if ($request->expectsJson()) {
                return response()->json(['status' => $attempt?->status === 'Gagal' ? 'Kedaluwarsa' : 'TidakAda', 'message' => 'Tidak ada pembayaran QRIS yang masih menunggu.']);
            }
            return redirect()->route('kasir.orders')->with('error', 'Tidak ada pembayaran QRIS Midtrans yang masih menunggu.');
        }

        try {
            $status = $midtrans->transactionStatus($attempt);
            $result = $statusService->apply($attempt, $status);
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => $result,
                    'message' => $result === 'Dibayar'
                        ? 'Pembayaran QRIS berhasil diverifikasi.'
                        : ($result === 'Menunggu' ? 'Menunggu pembayaran pelanggan.' : 'QRIS kedaluwarsa atau gagal.'),
                ]);
            }
            return redirect()->route('kasir.orders')->with(
                $result === 'Dibayar' ? 'success' : 'error',
                $result === 'Dibayar'
                    ? 'Pembayaran QRIS berhasil diverifikasi dari Midtrans.'
                    : ($result === 'Menunggu' ? 'Pembayaran belum terdeteksi oleh Midtrans.' : 'QRIS kedaluwarsa atau gagal. Minta pelanggan membuat QR baru.')
            );
        } catch (\Throwable $exception) {
            report($exception);
            if ($request->expectsJson()) {
                return response()->json(['status' => 'Menunggu', 'message' => 'Status belum dapat diperiksa. Mencoba kembali otomatis.'], 503);
            }
            return redirect()->route('kasir.orders')->with('error', 'Status pembayaran belum dapat diperiksa. Coba kembali sebentar lagi.');
        }
    }

    public function releaseToKitchen($id)
    {
        $order = Order::with('payment.attempts')->findOrFail($id);

        if ($order->payment_status !== 'Dibayar' && $order->payment?->status !== 'Berhasil') {
            return redirect()->route('kasir.orders')->with('error', 'Pembayaran belum lunas. Pesanan belum bisa dikirim ke Kitchen.');
        }

        if ($order->status !== 'Menunggu') {
            return redirect()->route('kasir.orders')->with('error', 'Pesanan ini tidak lagi menunggu konfirmasi Kitchen.');
        }

        if ($order->kitchen_confirmed_at) {
            return redirect()->route('kasir.orders')->with('success', 'Pesanan ini sudah dikirim ke Kitchen.');
        }

        $order->update(['kitchen_confirmed_at' => now()]);

        return redirect()->route('kasir.orders')->with('success', 'Pesanan berhasil dikirim ke Kitchen.');
    }


    /**
 * =========================================================
 * RIWAYAT TRANSAKSI
 * =========================================================
 */
public function history()
{
    $request = request();
    $validated = $request->validate([
        'from_date' => ['nullable', 'date_format:Y-m-d'],
        'to_date' => ['nullable', 'date_format:Y-m-d'],
    ]);

    $fromDate = $validated['from_date'] ?? now()->startOfMonth()->toDateString();
    $toDate = $validated['to_date'] ?? now()->toDateString();
    [$periodStart, $periodEnd] = FinancialReportService::resolvePeriod(null, $fromDate, $toDate);
    $fromDate = $periodStart->toDateString();
    $toDate = $periodEnd->toDateString();

    $orders = Order::with([
        'items',
        'payment'
    ])
    ->where(function ($query) {
        $query->where('status', 'Sudah Diambil')
              ->orWhere('status', 'Dibatalkan');
    })
    ->whereBetween('created_at', [$periodStart, $periodEnd])
    ->latest()
    ->get();

    return view('kasir.history', compact('orders', 'fromDate', 'toDate'));
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
            'payment_method' => ['required', 'in:Cash,QRIS,Bank Transfer'],
            'items' => ['required', 'json'],
        ]);

        $paymentMethod = $request->input('payment_method');

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
            if (!preg_match('/^A(?:0[1-9]|1[0-9]|20)$/', $table)) {
                return back()->withErrors(['table' => 'Nomor meja tidak valid. Pilih meja A01-A20 atau Take Away.'])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request, $quantities, $table, $paymentMethod) {
                if ($table !== 'Take Away') {
                    $tableTaken = Order::where('table_number', $table)
                        ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai', 'Sudah Diambil'])
            ->whereNull('table_finished_at')
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
                    $availableStock = max(0, (int) $product->stock - $this->pendingCustomerReservation((int) $id));
                    if ($availableStock < $quantity) {
                        throw new \RuntimeException("Stok {$product->name} yang belum dipesan hanya {$availableStock}. Diminta: {$quantity}.");
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

                }

                $order->update(['subtotal' => $subtotal, 'tax' => 0, 'total' => $subtotal]);

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethod,
                    'amount' => $subtotal,
                    'status' => 'Pending',
                ]);
            });

            return redirect()->route('kasir.orders')->with('success', 'Pesanan tersimpan dengan metode pembayaran ' . $paymentMethod . '. Stok dicadangkan sampai pembayaran dikonfirmasi.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['order' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['order' => 'Pesanan gagal disimpan. Periksa log Laravel untuk detail kesalahan.'])->withInput();
        }
    }
}









