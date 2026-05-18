<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Page\Page;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePageRequest extends FormRequest
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
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:page_categories,id'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_path' => ['nullable', 'string', 'max:1000'],
            'template' => ['required', Rule::in(['default', 'fullwidth', 'nolayout'])],
            'is_home' => ['boolean'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
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
                Rule::unique('page_translation', 'permalink')->where('lang', $code),
            ];

            if (! $isDefault) {
                $permalinkRules[] = "required_with:translations.{$code}.title";
            }

            $rules["translations.{$code}.title"] = $titleRules;
            $rules["translations.{$code}.permalink"] = $permalinkRules;
            $rules["translations.{$code}.content"] = ['nullable', 'string'];
        }

        return $rules;
    }
}
