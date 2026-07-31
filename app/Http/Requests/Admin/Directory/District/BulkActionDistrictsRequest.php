<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\District;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BulkActionDistrictsRequest extends FormRequest
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
            'action' => [
                'required',
                Rule::in(['publish', 'draft', 'inactive', 'mark_popular', 'unmark_popular', 'delete']),
            ],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:districts,id'],
        ];
    }
}
