<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.site') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $langs = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        return [
            'default_meta_title_template' => ['nullable', 'string', 'max:255'],
            'default_og_image_path' => ['nullable', 'string', 'max:500'],
            'robots_index' => ['boolean'],
            'translations' => ['required', 'array'],
            'translations.*.lang' => ['required', 'string', Rule::in($langs)],
            'translations.*.default_meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
