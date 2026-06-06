<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateBrandingRequest extends FormRequest
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
            'logo_light_path' => ['nullable', 'string', 'max:500'],
            'logo_dark_path' => ['nullable', 'string', 'max:500'],
            'favicon_path' => ['nullable', 'string', 'max:500'],
            'theme_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
