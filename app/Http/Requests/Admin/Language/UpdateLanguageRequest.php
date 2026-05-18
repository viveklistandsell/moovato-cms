<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Language;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $languageId = (int) ($this->route('language')?->id ?? 0);

        return [
            'code' => [
                'required',
                'string',
                'min:2',
                'max:10',
                'regex:/^[a-z]{2,3}(?:[_-][a-zA-Z]{2,4})?$/',
                Rule::unique('languages', 'code')->ignore($languageId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'native_name' => ['required', 'string', 'max:100'],
            'flag' => ['nullable', 'string', 'max:20'],
            'lang_locale' => ['nullable', 'string', 'max:20'],
            'lang_is_default' => ['boolean'],
            'status' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
