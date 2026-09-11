<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('guests are redirected to login when visiting media', function (): void {
    $response = $this->get(route('admin.media.index'));

    $response->assertRedirect(route('login'));
});

test('non-admin users get a 403 when visiting media', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.media.index'))
        ->assertForbidden();
});

test('admin users can visit media', function (): void {
    $role = Role::findOrCreate(User::SUPER_ADMIN_ROLE, 'web');
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.media.index'))
        ->assertSuccessful();
});
