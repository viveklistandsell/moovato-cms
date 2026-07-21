<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\District;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreDistrictRequest extends FormRequest
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
        $cityId = (int) $this->input('city_id');

        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:120'],
            'code' => [
                'nullable', 'string', 'max:8',
                Rule::unique('districts', 'code')->where('city_id', $cityId),
            ],
            'permalink' => [
                'required', 'string', 'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('districts', 'permalink')->where('city_id', $cityId),
            ],
            'postal_code_prefix' => ['nullable', 'string', 'max:5'],
            'is_popular' => ['boolean'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
