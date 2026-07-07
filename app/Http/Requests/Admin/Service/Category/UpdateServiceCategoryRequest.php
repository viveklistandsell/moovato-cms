<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Service\Category;

use App\Models\Language;
use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateServiceCategoryRequest extends FormRequest
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
        /** @var ServiceCategory|null $category */
        $category = $this->route('category');
        $categoryId = $category?->id;

        $languages = Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get(['code', 'lang_is_default']);

        $rules = [
            'parent_category_id' => ['required', 'integer', 'exists:service_parent_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'is_featured' => ['boolean'],
            'is_popular' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'translations' => ['required', 'array', 'min:1'],
        ];

        foreach ($languages as $language) {
            $code = $language->code;
            $isDefault = (bool) $language->lang_is_default;

            $nameRules = $isDefault
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255', "required_with:translations.{$code}.permalink"];

            $permalinkRule = Rule::unique('service_category_translation', 'permalink')
                ->where('lang', $code)
                ->ignore(
                    optional(
                        ServiceCategoryTranslation::query()
                            ->where('category_id', $categoryId)
                            ->where('lang', $code)
                            ->first()
                    )->id,
                );

            $permalinkRules = [
                $isDefault ? 'required' : 'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $permalinkRule,
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
