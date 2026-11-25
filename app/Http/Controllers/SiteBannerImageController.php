<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class SiteBannerImageController extends Controller
{
    public function __invoke(string $filename)
    {
        $path = 'site-banners/' . basename($filename);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, basename($path), [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}