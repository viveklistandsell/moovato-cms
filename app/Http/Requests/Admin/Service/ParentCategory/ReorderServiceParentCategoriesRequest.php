<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Service\ParentCategory;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderServiceParentCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('service_categories.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'exists:service_parent_categories,id'],
        ];
    }
}
