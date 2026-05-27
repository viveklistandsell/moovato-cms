<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Extends Spatie's Permission with:
 * - `display_name` for human-readable labels in the role form
 * - `group` to bucket related permissions (e.g. all `users.*` under "Users")
 */
#[Fillable([
    'name', 'guard_name', 'display_name', 'group',
])]
final class Permission extends SpatiePermission {}
