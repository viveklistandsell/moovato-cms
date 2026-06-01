<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\SiteSetting;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateSiteSettingRequest extends FormRequest
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
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'translations' => ['required', 'array'],
            'translations.*.lang' => ['required', 'string', Rule::in($langs)],
            'translations.*.about_text' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
