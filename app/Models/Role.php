<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Extends Spatie's Role with project-specific UI metadata:
 * - `display_name` shown in admin lists instead of the raw `name`
 * - `description` for the role form
 * - `color` drives the Tailwind badge in user tables
 * - `is_system` locks seeded roles (Super Admin, Admin, …) against destructive
 *   edits in the UI / Delete action.
 */
#[Fillable([
    'name', 'guard_name', 'display_name', 'description', 'color', 'is_system',
])]
final class Role extends SpatieRole
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }
}
