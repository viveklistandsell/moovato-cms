<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Service\ParentCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BulkActionServiceParentCategoriesRequest extends FormRequest
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
            'action' => ['required', Rule::in([
                'publish', 'draft', 'inactive', 'delete',
                'mark_featured', 'unmark_featured',
                'mark_popular', 'unmark_popular',
            ])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:service_parent_categories,id'],
        ];
    }
}
