<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $image = $request->file('image');
        unset($data['image']);

        $data['stock'] = (int) $data['stock'];
        $data['icon'] = $this->iconForCategory($data['category']);
        $data['image'] = $image ? $image->store('products', 'public') : null;
        $data['is_available'] = $data['stock'] > 0;

        Product::create($data);

        return redirect()->route('kasir.products.availability')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $data = $this->validatedData($request);
        $image = $request->file('image');
        unset($data['image']);

        $data['stock'] = (int) $data['stock'];
        $data['icon'] = $this->iconForCategory($data['category']);

        if ($image) {
            $oldImage = $product->image;
            $data['image'] = $image->store('products', 'public');

            if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                $oldPath = preg_replace('#^(public/|storage/)#', '', ltrim($oldImage, '/'));
                Storage::disk('public')->delete($oldPath);
            }
        } else {
            // Pertahankan foto lama jika tidak ada foto baru yang dipilih.
            $data['image'] = $product->image;
        }

        $data['is_available'] = $data['stock'] > 0;
        $product->update($data);

        return redirect()->route('kasir.products.availability')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        if ($product->orderItems()->exists()) {
            return redirect()->route('kasir.products.availability')
                ->with('error', 'Produk tidak bisa dihapus karena sudah digunakan dalam riwayat pesanan. Ubah stok menjadi 0 jika produk tidak dijual lagi.');
        }

        if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
            $imagePath = preg_replace('#^(public/|storage/)#', '', ltrim($product->image, '/'));
            Storage::disk('public')->delete($imagePath);
        }

        $product->delete();

        return redirect()->route('kasir.products.availability')->with('success', 'Produk berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Coffee,Non Coffee,Snack,Makanan Berat'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function iconForCategory(string $category): string
    {
        return match ($category) {
            'Coffee' => 'fa-solid fa-mug-hot',
            'Non Coffee' => 'fa-solid fa-glass-water',
            'Snack' => 'fa-solid fa-cookie-bite',
            'Makanan Berat' => 'fa-solid fa-utensils',
            default => 'fa-solid fa-mug-hot',
        };
    }
}
