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

test('bulk delete moves multiple files to trash', function (): void {
    $files = MediaFile::factory()->count(3)->create();

    $this->post(route('admin.media.files.bulk-delete'), [
        'ids' => $files->pluck('id')->all(),
    ])->assertRedirect();

    expect(MediaFile::onlyTrashed()->count())->toBe(3)
        ->and(MediaFile::query()->count())->toBe(0);
});

test('bulk delete validates ids array', function (): void {
    $this->post(route('admin.media.files.bulk-delete'), [])
        ->assertSessionHasErrors('ids');
});

test('bulk move puts multiple files into a folder', function (): void {
    $folder = MediaFolder::factory()->create(['name' => 'Target', 'path' => 'Target']);
    $files = MediaFile::factory()->count(2)->create(['folder_id' => null]);

    $this->post(route('admin.media.files.bulk-move'), [
        'ids' => $files->pluck('id')->all(),
        'folder_id' => $folder->id,
    ])->assertRedirect();

    expect(MediaFile::query()->where('folder_id', $folder->id)->count())->toBe(2);
});

test('bulk move with null folder moves files to root', function (): void {
    $folder = MediaFolder::factory()->create();
    $files = MediaFile::factory()->count(2)->create(['folder_id' => $folder->id]);

    $this->post(route('admin.media.files.bulk-move'), [
        'ids' => $files->pluck('id')->all(),
        'folder_id' => null,
    ])->assertRedirect();

    expect(MediaFile::query()->whereNull('folder_id')->count())->toBe(2);
});

test('search returns matching files across folders', function (): void {
    $folderA = MediaFolder::factory()->create();
    $folderB = MediaFolder::factory()->create();

    MediaFile::factory()->create(['name' => 'invoice-january.pdf', 'folder_id' => $folderA->id]);
    MediaFile::factory()->create(['name' => 'photo-vacation.jpg', 'folder_id' => $folderB->id]);
    MediaFile::factory()->create(['name' => 'invoice-feb.pdf', 'folder_id' => null]);

    $response = $this->get(route('admin.media.index', ['q' => 'invoice']));

    $response->assertSuccessful();

    $data = $response->viewData('page')['props']['files']['data'];

    expect(count($data))->toBe(2)
        ->and(collect($data)->pluck('name')->all())->each(
            fn ($name) => $name->toContain('invoice'),
        );
});

test('search hides folders from results', function (): void {
    $folder = MediaFolder::factory()->create();
    MediaFile::factory()->create(['name' => 'something.txt', 'folder_id' => $folder->id]);

    $response = $this->get(route('admin.media.index', ['q' => 'something']));

    $response->assertSuccessful();
    $folders = $response->viewData('page')['props']['folders'];

    expect($folders)->toBe([]);
});
