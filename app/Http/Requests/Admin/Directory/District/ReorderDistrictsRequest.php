<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\District;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderDistrictsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('locations.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'exists:districts,id'],
        ];
    }
}
