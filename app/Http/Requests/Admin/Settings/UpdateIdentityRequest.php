<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateIdentityRequest extends FormRequest
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
            'site_name' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:64', Rule::in(timezone_identifiers_list())],
            'date_format' => ['nullable', 'string', 'max:32'],
            'translations' => ['required', 'array'],
            'translations.*.lang' => ['required', 'string', Rule::in($langs)],
            'translations.*.site_tagline' => ['nullable', 'string', 'max:255'],
        ];
    }
}
