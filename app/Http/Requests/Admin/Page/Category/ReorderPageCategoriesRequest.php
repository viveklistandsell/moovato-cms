<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Page\Category;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderPageCategoriesRequest extends FormRequest
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
            'parent_id' => ['nullable', 'integer', 'exists:page_categories,id'],
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'distinct', 'exists:page_categories,id'],
        ];
    }
}
