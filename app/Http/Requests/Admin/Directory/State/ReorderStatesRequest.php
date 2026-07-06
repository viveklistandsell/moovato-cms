<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\State;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderStatesRequest extends FormRequest
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
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'exists:states,id'],
        ];
    }
}
