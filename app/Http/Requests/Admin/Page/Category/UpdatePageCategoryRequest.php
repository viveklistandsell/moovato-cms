<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Page\Category;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdatePageCategoryRequest extends FormRequest
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
        $categoryId = (int) ($this->route('category')?->id ?? 0);

        $languages = Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get(['code', 'lang_is_default']);

        $rules = [
            'parent_id' => [
                'nullable',
                'integer',
                'exists:page_categories,id',
                Rule::notIn([$categoryId]),
            ],
            'is_default' => ['boolean'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
            'translations' => ['required', 'array', 'min:1'],
        ];

        foreach ($languages as $language) {
            $code = $language->code;
            $isDefault = (bool) $language->lang_is_default;

            $titleRules = $isDefault
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255', "required_with:translations.{$code}.permalink"];

            $permalinkRules = [
                $isDefault ? 'required' : 'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('page_category_translation', 'permalink')
                    ->where('lang', $code)
                    ->ignore($categoryId, 'category_id'),
            ];

            if (! $isDefault) {
                $permalinkRules[] = "required_with:translations.{$code}.title";
            }

            $rules["translations.{$code}.title"] = $titleRules;
            $rules["translations.{$code}.permalink"] = $permalinkRules;
        }

        return $rules;
    }
}
