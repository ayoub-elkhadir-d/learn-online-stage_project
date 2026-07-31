<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageController extends Controller
{
    /**
     * Fallback for the "public" disk's Storage::url() links.
     *
     * On hosts where `php artisan storage:link` never ran (or the symlink
     * doesn't survive a deploy — common on shared hosting where symlink()
     * is disabled), Apache's `RewriteCond %{REQUEST_FILENAME} !-f` never
     * matches an existing file, so every request falls through to Laravel.
     * This route catches that case and streams the file straight from the
     * disk, so uploaded images work whether or not the symlink exists.
     */
    public function show(string $path): StreamedResponse
    {
        if (Str::contains($path, '..')) {
            abort(404);
        }

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
