<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KasirBannerController extends Controller
{
    public function edit(SiteSettingsService $settings): View
    {
        return $this->showEditor($settings, 'kasir');
    }

    public function ownerEdit(SiteSettingsService $settings): View
    {
        return $this->showEditor($settings, 'owner');
    }

    private function showEditor(SiteSettingsService $settings, string $area): View
    {
        return view('kasir.banners', [
            'banners' => $settings->homeBanners(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'price', 'image']),
            'layout' => 'layouts.' . $area,
            'routePrefix' => $area,
        ]);
    }

    public function update(Request $request, SiteSettingsService $settings): RedirectResponse
    {
        return $this->saveBanners($request, $settings, 'kasir');
    }

    public function ownerUpdate(Request $request, SiteSettingsService $settings): RedirectResponse
    {
        return $this->saveBanners($request, $settings, 'owner');
    }

    private function saveBanners(Request $request, SiteSettingsService $settings, string $area): RedirectResponse
    {
        $validated = $request->validate([
            'banners' => ['required', 'array', 'size:4'],
            'banners.*.badge' => ['required', 'string', 'max:40'],
            'banners.*.title' => ['required', 'string', 'max:80'],
            'banners.*.description' => ['required', 'string', 'max:180'],
            'banners.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'banners.*.discount_percent' => ['required', 'integer', 'min:0', 'max:90'],
            'banners.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banners.*.image_source' => ['nullable', 'in:product,custom,banner'],
        ]);

        $selectedProducts = collect($validated['banners'])
            ->pluck('product_id')->filter()->map(fn ($id) => (int) $id);
        if ($selectedProducts->duplicates()->isNotEmpty()) {
            return back()->withErrors(['banners' => 'Pilih produk yang berbeda untuk setiap promo.'])->withInput();
        }

        $existing = $settings->homeBanners();
        $updated = [];
        foreach ($validated['banners'] as $index => $banner) {
            $imagePath = $existing[$index]['image'] ?? '';
            $upload = $request->file("banners.{$index}.image");
            $productId = filled($banner['product_id'] ?? null) ? (int) $banner['product_id'] : null;
            $product = $productId ? Product::find($productId) : null;
            $imageSource = $upload ? 'custom' : ($banner['image_source'] ?? 'banner');

            if ($upload) {
                $newPath = $upload->store('site-banners', 'public');
                if (str_starts_with($imagePath, 'site-banners/')) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $newPath;
            }

            if ($imageSource === 'product' && (!$product || !$product->image)) {
                $imageSource = str_starts_with($imagePath, 'site-banners/') ? 'custom' : 'banner';
            }
            if ($imageSource === 'custom' && !str_starts_with($imagePath, 'site-banners/')) {
                $imageSource = 'banner';
            }
            if ($imageSource === 'product' && str_starts_with($imagePath, 'site-banners/')) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = '';
            }

            $updated[] = [
                'badge' => trim($banner['badge']),
                'title' => $product?->name ?? trim($banner['title']),
                'description' => trim($banner['description']),
                'image' => $imagePath,
                'image_source' => $imageSource,
                'product_id' => $productId,
                'discount_percent' => $productId ? (int) $banner['discount_percent'] : 0,
            ];
        }

        $settings->updateHomeBanners($updated);

        return redirect()->route($area . '.banners.edit')->with('success', 'Banner dan promo berhasil diperbarui. Harga promo otomatis tampil di Home Customer dan keranjang.');
    }
}
