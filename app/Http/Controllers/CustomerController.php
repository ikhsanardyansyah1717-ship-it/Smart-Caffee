<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Payment;

class CustomerController extends Controller
{
    /**
     * Halaman Home Customer
     */
    public function home(): View
    {
        $products = Product::where('is_available', true)
            ->get();

        return view('customer.home', compact('products'));
    }

    /**
     * Halaman Orders Customer
     */
    public function orders(): View
    {
        $orders = Order::with(['items', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.orders', compact('orders'));
    }

    /**
     * Halaman Favorites
     */
    public function favorites(): View
    {
        return view('customer.favorites');
    }

    /**
     * Halaman Profile
     */
    public function profile(): View
    {
        return view('customer.profile');
    }

    /**
     * Simpan pesanan Customer
     */
    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'table_number' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:Cash,QRIS,Debit'],
            'debit_bank' => ['nullable', 'required_if:payment_method,Debit', 'in:BCA,Mandiri,BNI,BRI'],
            'cash_received' => ['nullable', 'required_if:payment_method,Cash', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if (!$product->is_available) {
                    abort(422, "Produk {$product->name} sedang tidak tersedia.");
                }

                $subtotal += $product->price * $item['quantity'];
            }

            $tax = round($subtotal * 0.10, 2);
            $total = $subtotal + $tax;

            if (
                $data['payment_method'] === 'Cash' &&
                (float) $data['cash_received'] < $total
            ) {
                abort(422, 'Uang cash belum mencukupi total pembayaran.');
            }

            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999);

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'customer_name' => Auth::user()->name,
                'table_number' => $data['table_number'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'Menunggu',
                'payment_status' => 'Belum Dibayar',
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $product->price * $quantity,
                ]);
            }

            // Menggunakan Payment model + tabel payments yang SUDAH ADA.
            // Untuk Debit, bank disimpan sebagai bagian dari payment_method
            // karena tabel payments saat ini belum memiliki kolom bank.
            $paymentMethod = $data['payment_method'];

            if ($paymentMethod === 'Debit') {
                $paymentMethod .= ' - ' . $data['debit_bank'];
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $total,
                'status' => 'Pending',
                'paid_at' => null,
            ]);

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat.',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ], 201);
    }
}