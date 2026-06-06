<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateAnalyticsRequest extends FormRequest
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
        return [
            'google_analytics_id' => ['nullable', 'string', 'max:64'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:64'],
            'meta_pixel_id' => ['nullable', 'string', 'max:64'],
            'custom_head_code' => ['nullable', 'string', 'max:8000'],
            'cookie_consent_required' => ['boolean'],
        ];
    }
}
