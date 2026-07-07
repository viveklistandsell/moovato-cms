<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\City;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderCitiesRequest extends FormRequest
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
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'exists:cities,id'],
        ];
    }
}
