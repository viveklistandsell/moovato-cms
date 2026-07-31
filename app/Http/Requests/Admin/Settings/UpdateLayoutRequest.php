<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateLayoutRequest extends FormRequest
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
            'header_sticky' => ['boolean'],
            'show_language_switcher' => ['boolean'],
            'show_back_to_top' => ['boolean'],
            'footer_copyright_auto_year' => ['boolean'],
        ];
    }
}
