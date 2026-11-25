<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

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

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_available', true);
            }

            if ($request->status === 'inactive') {
                $query->where('is_available', false);
            }
        }

        $products = $query->get()->sort(function ($a, $b) {
            $rank = collect(config('product_categories'))
                ->pluck('value')
                ->flip();
            $categoryA = $rank[$a->category] ?? count($rank);
            $categoryB = $rank[$b->category] ?? count($rank);

            return ($categoryA <=> $categoryB) ?: strcasecmp($a->name, $b->name);
        })->values();

        return view('owner.products.index', compact('products'));
    }


    /**
     * Menampilkan form tambah produk
     */
    public function create(Request $request)
    {
        $iconOptions = [
            'fa-mug-hot' => 'Coffee',
            'fa-mug-saucer' => 'Mug',
            'fa-glass-water' => 'Minuman',
            'fa-bread-slice' => 'Roti',
            'fa-cake-candles' => 'Cake',
        ];

        $validCategories = collect(config('product_categories'))->pluck('value')->all();
        $selectedCategory = in_array($request->query('category'), $validCategories, true)
            ? $request->query('category')
            : null;

        return view('owner.products.create', compact('iconOptions', 'selectedCategory'));
    }


    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        $this->normalizePrices($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'category' => ['required', 'in:' . implode(',', array_column(config('product_categories'), 'value'))],

            'price' => 'required|numeric|min:0',

            'cost_price' => 'required|numeric|min:0',

            'icon' => 'nullable|string|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'description' => 'nullable|string',
        ]);

        // Checkbox is_available
        $validated['is_available'] = $request->has('is_available');


        // Simpan gambar sebelum membuat produk agar kegagalan upload tidak
        // menghasilkan produk dengan path gambar kosong.
        if ($request->file('image') instanceof UploadedFile) {
            $validated['image'] = $this->storeProductImage($request->file('image'));
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
        $this->normalizePrices($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'category' => ['required', 'in:' . implode(',', array_column(config('product_categories'), 'value'))],

            'price' => 'required|numeric|min:0',

            'cost_price' => 'required|numeric|min:0',

            'icon' => 'nullable|string|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'description' => 'nullable|string',
        ]);

        // Checkbox is_available
        $validated['is_available'] = $request->has('is_available');


        $previousImage = $product->image;
        if ($request->file('image') instanceof UploadedFile) {
            $validated['image'] = $this->storeProductImage($request->file('image'));
        }

        $product->update($validated);

        if (!empty($validated['image']) && $previousImage && $previousImage !== $validated['image']
            && !str_starts_with($previousImage, 'http://') && !str_starts_with($previousImage, 'https://')) {
            Storage::disk('public')->delete($previousImage);
        }


        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }


    /**
     * Menghapus produk.
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

    private function normalizePrices(Request $request): void
    {
        foreach (['price', 'cost_price'] as $field) {
            $value = $request->input($field);
            if (is_string($value) && trim($value) !== '') {
                $request->merge([$field => preg_replace('/\D+/', '', $value)]);
            }
        }
    }

    private function storeProductImage(UploadedFile $image): string
    {
        if (!$image->isValid()) {
            throw ValidationException::withMessages([
                'image' => 'Upload gambar gagal. Silakan pilih file JPG, PNG, atau WEBP yang valid, lalu coba lagi.',
            ]);
        }

        try {
            $disk = Storage::disk('public');
            $disk->makeDirectory('products');
            $path = $image->storePublicly('products', 'public');

            if (!$path || !$disk->exists($path)) {
                throw new \RuntimeException('Product image was not persisted to the public disk.');
            }

            return $path;
        } catch (Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'image' => 'Gambar tidak berhasil disimpan. Periksa ruang penyimpanan server, lalu coba unggah kembali.',
            ]);
        }
    }
}
