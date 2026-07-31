<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateLegalRequest extends FormRequest
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
            'privacy_page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'terms_page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'imprint_page_id' => ['nullable', 'integer', 'exists:pages,id'],
        ];
    }
}
