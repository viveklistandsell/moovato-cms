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
     * Both match the runtime clamps in AppServiceProvider and
     * FortifyServiceProvider, so saving an out-of-range value here
     * surfaces an inline error rather than silently being ignored
     * during request handling.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'require_2fa_for_admins' => ['boolean'],
            'session_lifetime_minutes' => ['nullable', 'integer', 'min:5', 'max:10080'],
            'login_throttle_attempts' => ['nullable', 'integer', 'min:1', 'max:60'],
        ];
    }

    /**
     * Friendlier error messages so the user sees what range to enter
     * instead of generic "must be between X and Y" without context.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'session_lifetime_minutes.min' => 'Session lifetime must be at least 5 minutes.',
            'session_lifetime_minutes.max' => 'Session lifetime cannot exceed 10 080 minutes (1 week).',
            'login_throttle_attempts.min' => 'Login throttle must allow at least 1 attempt per minute.',
            'login_throttle_attempts.max' => 'Login throttle cannot exceed 60 attempts per minute.',
        ];
    }
}
