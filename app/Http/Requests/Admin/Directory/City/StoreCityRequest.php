<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('locations.create') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $stateId = (int) $this->input('state_id');

        return [
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'name' => ['required', 'string', 'max:255'],
            'permalink' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('cities', 'permalink')->where('state_id', $stateId),
            ],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'is_popular' => ['boolean'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
