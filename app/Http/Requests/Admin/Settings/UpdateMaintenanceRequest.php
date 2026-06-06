<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateMaintenanceRequest extends FormRequest
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
            'maintenance_enabled' => ['boolean'],
            'maintenance_bypass_ips' => ['nullable', 'string', 'max:500'],
            'translations' => ['required', 'array'],
            'translations.*.lang' => ['required', 'string', Rule::in($langs)],
            'translations.*.maintenance_heading' => ['nullable', 'string', 'max:255'],
            'translations.*.maintenance_message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
