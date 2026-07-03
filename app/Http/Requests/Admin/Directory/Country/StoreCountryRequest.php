<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('locations.create') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'iso_code' => [
                'required', 'string', 'size:2',
                Rule::unique('countries', 'iso_code'),
            ],
            'phone_code' => ['nullable', 'string', 'max:8'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $iso = $this->input('iso_code');
        if (is_string($iso)) {
            $this->merge(['iso_code' => mb_strtoupper(mb_trim($iso))]);
        }
    }
}
