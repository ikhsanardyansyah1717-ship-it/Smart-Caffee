<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    public function show(Product $product)
    {
        $storedPath = trim((string) $product->image);
        abort_if($storedPath === '' || Str::startsWith($storedPath, ['http://', 'https://']), 404);

        $storedPath = str_replace('\\', '/', $storedPath);
        $storedPath = preg_replace('#^(public/|storage/)#', '', ltrim($storedPath, '/'));
        abort_if(collect(explode('/', $storedPath))->contains('..'), 404);
        abort_unless(Storage::disk('public')->exists($storedPath), 404);

        return Storage::disk('public')->response($storedPath, null, [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
