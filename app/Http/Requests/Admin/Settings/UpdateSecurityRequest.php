<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateSecurityRequest extends FormRequest
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
            'require_2fa_for_admins' => ['boolean'],
            'session_lifetime_minutes' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'login_throttle_attempts' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
