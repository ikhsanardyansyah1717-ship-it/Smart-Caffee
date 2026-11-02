<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductAvailabilityController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('name')->get();
        $totalProducts = $products->count();
        $lowStockCount = $products->filter(fn ($product) => (int) $product->stock > 0 && (int) $product->stock <= 5)->count();
        $outOfStockCount = $products->filter(fn ($product) => (int) $product->stock <= 0)->count();

        return view('kasir.products.availability', compact(
            'products', 'totalProducts', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ]);

        DB::transaction(function () use ($product, $data): void {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);
            $reservedQuantity = OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('payments', 'payments.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $lockedProduct->id)
                ->where('payments.status', 'Pending')
                ->where('orders.payment_status', 'Belum Dibayar')
                ->where('orders.status', '!=', 'Dibatalkan')
                ->sum('order_items.quantity');

            if ((int) $data['stock'] < (int) $reservedQuantity) {
                throw ValidationException::withMessages([
                    'stock' => "Stok tidak boleh kurang dari {$reservedQuantity} karena jumlah tersebut sedang dipesan pelanggan.",
                ]);
            }

            $lockedProduct->stock = (int) $data['stock'];
            $lockedProduct->is_available = $lockedProduct->stock > 0;
            $lockedProduct->save();
        });

        return redirect()->route('kasir.products.availability')
            ->with('success', "Stok {$product->name} berhasil diperbarui menjadi {$data['stock']}.");
    }
}
