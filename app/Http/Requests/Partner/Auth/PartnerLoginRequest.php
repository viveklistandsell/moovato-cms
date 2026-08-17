<?php

declare(strict_types=1);

namespace App\Http\Requests\Partner\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Partner login form input.
 *
 * Rate limiting lives on the route (throttle:10,1) — we do not need
 * a second wall of per-request logic here. Real auth failure vs.
 * invalid input is decided in the controller so the response can
 * pick the correct localized message.
 */
final class PartnerLoginRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->input('email'))
                ? mb_strtolower(mb_trim($this->input('email')))
                : $this->input('email'),
            'remember' => $this->boolean('remember'),
        ]);
    }
}
