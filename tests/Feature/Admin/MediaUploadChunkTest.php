<?php

declare(strict_types=1);

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($this->admin);
});

test('the chunk store endpoint accepts a single-chunk upload and creates a media file', function (): void {
    $payload = [
        'file' => UploadedFile::fake()->createWithContent('chunk-1', 'hello world'),
        'resumableChunkNumber' => 1,
        'resumableTotalChunks' => 1,
        'resumableChunkSize' => 11,
        'resumableTotalSize' => 11,
        'resumableIdentifier' => 'test-id-001',
        'resumableFilename' => 'note.txt',
        'resumableType' => 'text/plain',
    ];

    $this->post(route('admin.media.files.store'), $payload)
        ->assertOk()
        ->assertJsonPath('complete', true)
        ->assertJsonPath('file.name', 'note.txt');

    $file = MediaFile::query()->firstWhere('original_name', 'note.txt');

    expect($file)->not->toBeNull()
        ->and($file->size)->toBe(11);

    Storage::disk('public')->assertExists($file->path);
});

test('blocked extensions are rejected', function (): void {
    $payload = [
        'file' => UploadedFile::fake()->createWithContent('chunk-1', '<?php echo 1;'),
        'resumableChunkNumber' => 1,
        'resumableTotalChunks' => 1,
        'resumableChunkSize' => 13,
        'resumableTotalSize' => 13,
        'resumableIdentifier' => 'test-id-002',
        'resumableFilename' => 'shell.php',
        'resumableType' => 'text/plain',
    ];

    $this->post(route('admin.media.files.store'), $payload)
        ->assertSessionHasErrors('resumableFilename');
});

test('the chunk-check endpoint returns 204 when chunk is missing', function (): void {
    $this->get(route('admin.media.files.check', [
        'resumableIdentifier' => 'never-uploaded',
        'resumableChunkNumber' => 1,
    ]))->assertNoContent(204);
});
