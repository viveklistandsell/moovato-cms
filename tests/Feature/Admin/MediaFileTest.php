<?php

declare(strict_types=1);

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $role = Role::findOrCreate(User::SUPER_ADMIN_ROLE, 'web');
    $admin = User::factory()->create();
    $admin->assignRole($role);
    $this->actingAs($admin);
});

test('an admin can rename a file', function (): void {
    $file = MediaFile::factory()->create(['name' => 'old.jpg']);

    $this->patch(route('admin.media.files.update', $file), [
        'name' => 'new.jpg',
    ])->assertRedirect();

    expect($file->refresh()->name)->toBe('new.jpg');
});

test('an admin can move a file to another folder', function (): void {
    $folder = MediaFolder::factory()->create(['name' => 'Target', 'path' => 'Target']);
    $file = MediaFile::factory()->create(['folder_id' => null]);

    $this->patch(route('admin.media.files.update', $file), [
        'folder_id' => $folder->id,
    ])->assertRedirect();

    expect($file->refresh()->folder_id)->toBe($folder->id);
});

test('an admin can soft-delete a file', function (): void {
    $file = MediaFile::factory()->create();

    $this->delete(route('admin.media.files.destroy', $file))
        ->assertRedirect();

    expect(MediaFile::onlyTrashed()->whereKey($file->id)->exists())->toBeTrue();
});

test('a file name with slashes is rejected', function (): void {
    $file = MediaFile::factory()->create();

    $this->patch(route('admin.media.files.update', $file), [
        'name' => 'bad/name.jpg',
    ])->assertSessionHasErrors('name');
});
