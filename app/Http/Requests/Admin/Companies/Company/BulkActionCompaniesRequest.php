<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Companies\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BulkActionCompaniesRequest extends FormRequest
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
            'action' => ['required', Rule::in([
                'publish', 'draft', 'inactive',
                'mark_verified', 'unmark_verified',
                'mark_top_rated', 'unmark_top_rated',
                'delete',
            ])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:companies,id'],
        ];
    }
}
