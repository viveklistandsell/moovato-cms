<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Role;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateRoleRequest extends FormRequest
{
    private const COLORS = ['rose', 'amber', 'emerald', 'sky', 'violet', 'neutral'];

    public function authorize(): bool
    {
        return $this->user()?->can('roles.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $role = $this->route('role');
        $roleId = $role instanceof Role ? (int) $role->getKey() : 0;
        $isSystem = $role instanceof Role && $role->is_system === true;

        return [
            // System roles lock the `name` (so we can't break the
            // hard-coded references like User::SUPER_ADMIN_ROLE).
            'name' => $isSystem
                ? ['nullable']
                : ['required', 'string', 'max:64', Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($roleId)],
            'display_name' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', Rule::in(self::COLORS)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists((new Permission())->getTable(), 'id')],
        ];
    }
}
