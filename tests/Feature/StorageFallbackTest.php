<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Storage fallback
|--------------------------------------------------------------------------
|
| Uploaded media is referenced as relative "/storage/<path>" URLs. On hosting
| where the public/storage symlink cannot exist (FTP deploys, most shared
| hosting), Apache forwards the request to index.php and this route must serve
| the file from the public disk instead. Without it every CMS image is blank.
|
*/

it('serves a file from the public disk when the storage symlink is missing', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('media/example.png', 'fake-png-bytes');

    $this->get('/storage/media/example.png')
        ->assertOk()
        ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');
});

it('serves nested paths containing slashes', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('company-portal/gallery/berlin/umzug.webp', 'fake-webp-bytes');

    $this->get('/storage/company-portal/gallery/berlin/umzug.webp')->assertOk();
});

it('returns 404 for a file that does not exist on the public disk', function (): void {
    Storage::fake('public');

    $this->get('/storage/media/missing.png')->assertNotFound();
});

it('rejects directory traversal attempts', function (): void {
    Storage::fake('public');

    $this->get('/storage/../../.env')->assertNotFound();
    $this->get('/storage/media/..%2F..%2F.env')->assertNotFound();
});
