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
    public function storeOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_number' => [
                'nullable',
                'string',
                'max:50'
            ],

            'items' => [
                'required',
                'array',
                'min:1'
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id'
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        DB::transaction(function () use ($data) {

            $subtotal = 0;

            /*
            |--------------------------------------------------------------------------
            | Hitung subtotal dari database
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                if (!$product->is_available) {
                    abort(
                        422,
                        "Produk {$product->name} sedang tidak tersedia."
                    );
                }

                $subtotal +=
                    $product->price * $item['quantity'];
            }

            /*
            |--------------------------------------------------------------------------
            | Pajak 10%
            |--------------------------------------------------------------------------
            */

            $tax = $subtotal * 0.10;

            $total = $subtotal + $tax;

            /*
            |--------------------------------------------------------------------------
            | Buat nomor order
            |--------------------------------------------------------------------------
            */

            $orderNumber =
                'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999);

            /*
            |--------------------------------------------------------------------------
            | Simpan Order
            |--------------------------------------------------------------------------
            */

            $order = Order::create([
                'order_number' => $orderNumber,

                'user_id' => Auth::id(),

                'customer_name' =>
                    Auth::user()->name,

                'table_number' =>
                    $data['table_number'] ?? null,

                'subtotal' => $subtotal,

                'tax' => $tax,

                'total' => $total,

                'status' => 'Menunggu',

                'payment_status' => 'Belum Dibayar',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Simpan Order Items
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                $quantity = $item['quantity'];

                $itemSubtotal =
                    $product->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,

                    'product_id' => $product->id,

                    'product_name' => $product->name,

                    'quantity' => $quantity,

                    'price' => $product->price,

                    'subtotal' => $itemSubtotal,
                ]);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Kembali ke halaman orders
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('customer.orders')
            ->with(
                'success',
                'Pesanan berhasil dibuat dan sedang menunggu diproses.'
            );
    }
}