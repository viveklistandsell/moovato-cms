<?php

declare(strict_types=1);

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($this->admin);
});

test('the trash page shows soft-deleted folders and files', function (): void {
    $folder = MediaFolder::factory()->create();
    $file = MediaFile::factory()->create();

    $folder->delete();
    $file->delete();

    $this->get(route('admin.media.trash.index'))->assertSuccessful();

    expect(MediaFolder::onlyTrashed()->count())->toBe(1)
        ->and(MediaFile::onlyTrashed()->count())->toBe(1);
});

test('an admin can restore a soft-deleted folder', function (): void {
    $folder = MediaFolder::factory()->create();
    $folder->delete();

    $this->post(route('admin.media.trash.folders.restore', $folder->id))
        ->assertRedirect();

    expect(MediaFolder::query()->whereKey($folder->id)->exists())->toBeTrue();
});

test('an admin can restore a soft-deleted file', function (): void {
    $file = MediaFile::factory()->create();
    $file->delete();

    $this->post(route('admin.media.trash.files.restore', $file->id))
        ->assertRedirect();

    expect(MediaFile::query()->whereKey($file->id)->exists())->toBeTrue();
});

test('an admin can permanently delete a trashed file', function (): void {
    $file = MediaFile::factory()->create();
    $file->delete();

    $this->delete(route('admin.media.trash.files.force-destroy', $file->id))
        ->assertRedirect();

    expect(MediaFile::withTrashed()->whereKey($file->id)->exists())->toBeFalse();
});

test('an admin can permanently delete a trashed folder along with its files', function (): void {
    $folder = MediaFolder::factory()->create();
    $file = MediaFile::factory()->create(['folder_id' => $folder->id]);
    $file->delete();
    $folder->delete();

    $this->delete(route('admin.media.trash.folders.force-destroy', $folder->id))
        ->assertRedirect();

    expect(MediaFolder::withTrashed()->whereKey($folder->id)->exists())->toBeFalse()
        ->and(MediaFile::withTrashed()->whereKey($file->id)->exists())->toBeFalse();
});
