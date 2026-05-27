<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user') instanceof User
            ? (int) $this->route('user')->getKey()
            : 0;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email:filter', 'max:255',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            // Password is optional on update. Only validated + hashed when present.
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'status' => ['required', Rule::in(UserStatus::values())],
            'role' => ['nullable', 'string', Rule::in(Role::query()->pluck('name')->all())],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }
}
