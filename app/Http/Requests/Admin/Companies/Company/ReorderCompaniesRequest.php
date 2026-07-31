<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Companies\Company;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderCompaniesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('companies.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'exists:companies,id'],
        ];
    }
}
