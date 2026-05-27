<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Role;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreRoleRequest extends FormRequest
{
    private const COLORS = ['rose', 'amber', 'emerald', 'sky', 'violet', 'neutral'];

    public function authorize(): bool
    {
        return $this->user()?->can('roles.create') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:64', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'display_name' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', Rule::in(self::COLORS)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists((new Permission())->getTable(), 'id')],
        ];
    }
}
