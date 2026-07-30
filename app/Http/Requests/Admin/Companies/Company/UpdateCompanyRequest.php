<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Companies\Company;

use App\Models\Language;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCompanyRequest extends FormRequest
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
            'primary_city_id' => ['required', 'integer', 'exists:cities,id'],
            'primary_district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],

            'logo' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'string', 'max:255'],

            'verified' => ['boolean'],
            'is_top_rated' => ['boolean'],
            'plan_tier' => ['required', Rule::in(['free', 'silver', 'gold'])],
            'rating_avg' => ['nullable', 'numeric', 'between:0,10'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            'recommend_pct' => ['nullable', 'integer', 'between:0,100'],
            'rating_breakdown' => ['nullable', 'array'],
            'rating_breakdown.1' => ['nullable', 'integer', 'min:0'],
            'rating_breakdown.2' => ['nullable', 'integer', 'min:0'],
            'rating_breakdown.3' => ['nullable', 'integer', 'min:0'],
            'rating_breakdown.4' => ['nullable', 'integer', 'min:0'],
            'rating_breakdown.5' => ['nullable', 'integer', 'min:0'],

            'google_rating' => ['nullable', 'numeric', 'between:0,10'],
            'google_review_count' => ['nullable', 'integer', 'min:0'],

            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'employee_count' => ['nullable', 'integer', 'min:0'],

            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang' => ['required', 'string', 'max:10'],
            'translations.*.name' => ['nullable', 'string', 'max:200'],
            'translations.*.permalink' => [
                'nullable', 'string', 'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'translations.*.short_description' => ['nullable', 'string', 'max:1000'],
            'translations.*.about' => ['nullable', 'string'],

            'contacts' => ['nullable', 'array'],
            'contacts.*.type' => ['required_with:contacts.*.value', Rule::in(['phone', 'email', 'website', 'whatsapp'])],
            'contacts.*.value' => ['required_with:contacts.*.type', 'string', 'max:255'],
            'contacts.*.label' => ['nullable', 'string', 'max:80'],
            'contacts.*.is_primary' => ['boolean'],

            'services' => ['nullable', 'array'],
            'services.*.service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'services.*.is_primary' => ['boolean'],
            'services.*.price_from' => ['nullable', 'numeric', 'min:0'],
            'services.*.price_unit' => ['nullable', 'string', 'max:40'],

            'service_areas' => ['nullable', 'array'],
            'service_areas.*.district_id' => ['required', 'integer', 'exists:districts,id'],
            'service_areas.*.is_home_base' => ['boolean'],
            'service_areas.*.response_hours' => ['nullable', 'integer', 'min:0', 'max:720'],

            'media' => ['nullable', 'array'],
            'media.*.media_file_id' => ['required', 'integer', 'exists:media_files,id'],
            'media.*.kind' => ['required', Rule::in(['logo', 'cover', 'gallery'])],

            'faqs' => ['nullable', 'array'],
            'faqs.*.status' => ['nullable', Rule::in(['published', 'draft'])],
            'faqs.*.translations' => ['required', 'array', 'min:1'],
            'faqs.*.translations.*.lang' => ['required', 'string', 'max:10'],
            'faqs.*.translations.*.question' => ['required', 'string', 'max:500'],
            'faqs.*.translations.*.answer' => ['required', 'string'],
        ];
    }

    /**
     * Enforce that only the default-language translation row has name +
     * permalink filled in. Non-default rows may be left blank so admins
     * aren't forced to translate everything before saving.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $defaultLang = Language::query()
                ->where('lang_is_default', true)
                ->value('code') ?? 'de';

            $translations = $this->input('translations', []);
            $seenDefault = false;

            foreach ($translations as $i => $t) {
                if (($t['lang'] ?? null) !== $defaultLang) {
                    continue;
                }
                $seenDefault = true;
                if (mb_trim((string) ($t['name'] ?? '')) === '') {
                    $v->errors()->add("translations.{$i}.name", __('validation.required', ['attribute' => 'name']));
                }
                if (mb_trim((string) ($t['permalink'] ?? '')) === '') {
                    $v->errors()->add("translations.{$i}.permalink", __('validation.required', ['attribute' => 'permalink']));
                }
            }

            if (! $seenDefault) {
                $v->errors()->add('translations', __('validation.required', ['attribute' => 'default language translation']));
            }
        });
    }
}
