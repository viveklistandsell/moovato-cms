<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

final class StorageFallbackController extends Controller
{
    public function __invoke(string $path): Response
    {
        if (str_contains($path, '..') || str_contains($path, "\0")) {
            abort(404);
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        $fullPath = $disk->path($path);

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
