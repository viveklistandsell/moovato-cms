<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Blog\Tag;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderBlogTagsRequest extends FormRequest
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
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'distinct', 'exists:blog_tags,id'],
        ];
    }
}
