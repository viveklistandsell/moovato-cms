<?php

declare(strict_types=1);

namespace App\Http\Requests\Partner\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the public /partner/register submission.
 *
 * NO auth required — this IS the entry point for company owners.
 * Anti-spam is layered separately at the route (rate limit) and
 * via reCAPTCHA (added in Phase 3 once the site key is configured).
 *
 * Password is intentionally NOT collected here — the user only
 * gets a password once the admin approves the application and the
 * "set your password" email link is clicked.
 */
final class StorePartnerRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Contact person
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:190', Rule::unique('company_users', 'email')],
            'phone' => ['nullable', 'string', 'max:50'],

            // Company details
            'company_name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')],

            // Docs — up to 5 files, 5 MB each. Optional.
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],

            // Legal — must be accepted to proceed.
            'accept_terms' => ['accepted'],

            // Honeypot — bots fill hidden fields; humans don't. The
            // Vue form renders this off-screen with aria-hidden.
            'website_url' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'accept_terms.accepted' => __('partner.register.errors.accept_terms'),
            'email.unique' => __('partner.register.errors.email_unique'),
            'website_url.max' => __('partner.register.errors.spam_rejected'),
        ];
    }
}
