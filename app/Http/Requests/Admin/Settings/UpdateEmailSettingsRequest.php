<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the admin-supplied mail/SMTP config. The password is treated
 * as optional — a blank submit means "keep what's stored", so admins
 * can't accidentally wipe credentials by hitting Save after just editing
 * the host.
 */
final class UpdateEmailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.email') ?? false;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'mail_transport' => ['required', 'string', 'in:smtp,log,sendmail,ses,mailgun'],
            'mail_host' => ['nullable', 'required_if:mail_transport,smtp', 'string', 'max:255'],
            'mail_port' => ['nullable', 'required_if:mail_transport,smtp', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'in:tls,ssl'],
            'mail_from_address' => ['required', 'email:rfc', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ];
    }
}
