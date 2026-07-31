<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User;

use App\Enums\UserStatus;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'status' => ['required', Rule::in(UserStatus::values())],
            // Role: stored on `roles.name` (Spatie). Optional; an unassigned
            // user simply gets no permissions and is gated out of the admin
            // area by EnsureUserIsAdmin.
            'role' => ['nullable', 'string', Rule::in(Role::query()->pluck('name')->all())],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_path' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
