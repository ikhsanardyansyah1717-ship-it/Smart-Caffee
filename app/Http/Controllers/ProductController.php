<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Pencarian nama produk
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_available', true);
            }

            if ($request->status === 'inactive') {
                $query->where('is_available', false);
            }
        }

        $products = $query->latest()->get();

        return view('owner.products.index', compact('products'));
    }


    /**
     * Menampilkan form tambah produk
     */
    public function create()
    {
        $iconOptions = [
            'fa-mug-hot' => 'Coffee',
            'fa-mug-saucer' => 'Mug',
            'fa-glass-water' => 'Minuman',
            'fa-bread-slice' => 'Roti',
            'fa-cake-candles' => 'Cake',
        ];

        return view(
            'owner.products.create',
            compact('iconOptions')
        );
    }


    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'category' => 'required|in:Coffee,Non Coffee,Food',

            'price' => 'required|numeric|min:0',

            'icon' => 'nullable|string|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'description' => 'nullable|string',
        ]);

        // Checkbox is_available
        $validated['is_available'] = $request->has('is_available');


        // Upload gambar
        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('products', 'public');
        }


        Product::create($validated);


        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }


    /**
     * Menampilkan detail produk
     */
    public function show(Product $product)
    {
        return view(
            'owner.products.show',
            compact('product')
        );
    }


    /**
     * Menampilkan form edit
     */
    public function edit(Product $product)
    {
        $iconOptions = [
            'fa-mug-hot' => 'Coffee',
            'fa-mug-saucer' => 'Mug',
            'fa-glass-water' => 'Minuman',
            'fa-bread-slice' => 'Roti',
            'fa-cake-candles' => 'Cake',
        ];

        return view(
            'owner.products.edit',
            compact('product', 'iconOptions')
        );
    }


    /**
     * Update produk
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'category' => 'required|in:Coffee,Non Coffee,Food',

            'price' => 'required|numeric|min:0',

            'icon' => 'nullable|string|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'description' => 'nullable|string',
        ]);

        // Checkbox is_available
        $validated['is_available'] = $request->has('is_available');


        // Jika upload gambar baru
        if ($request->hasFile('image')) {

            // Hapus gambar lama
            if (
                $product->image &&
                Storage::disk('public')->exists($product->image)
            ) {
                Storage::disk('public')->delete($product->image);
            }

            // Simpan gambar baru
            $validated['image'] = $request
                ->file('image')
                ->store('products', 'public');
        }


        $product->update($validated);


        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }


    /**
     * Menghapus produk
     */
    public function destroy(Product $product)
    {
        // Hapus gambar produk
        if (
            $product->image &&
            Storage::disk('public')->exists($product->image)
        ) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();


        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}