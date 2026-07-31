<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\State;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BulkActionStatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['publish', 'draft', 'inactive', 'delete'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:states,id'],
        ];
    }
}
