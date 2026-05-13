<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Blog\Category;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreBlogCategoryRequest extends FormRequest
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
        $languages = Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get(['code', 'lang_is_default']);

        $rules = [
            'parent_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'is_featured' => ['boolean'],
            'is_default' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
            'translations' => ['required', 'array', 'min:1'],
        ];

        foreach ($languages as $language) {
            $code = $language->code;
            $isDefault = (bool) $language->lang_is_default;

            $nameRules = $isDefault
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255', "required_with:translations.{$code}.permalink"];

            $permalinkRules = [
                $isDefault ? 'required' : 'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_category_translation', 'permalink')->where('lang', $code),
            ];

            if (! $isDefault) {
                $permalinkRules[] = "required_with:translations.{$code}.name";
            }

            $rules["translations.{$code}.name"] = $nameRules;
            $rules["translations.{$code}.permalink"] = $permalinkRules;
            $rules["translations.{$code}.short_description"] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }
}
