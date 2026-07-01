<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\State;

use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('locations.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var State|null $state */
        $state = $this->route('state');
        $ignoreId = $state?->id;
        $countryId = (int) $this->input('country_id');

        return [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 'string', 'max:8',
                Rule::unique('states', 'code')->ignore($ignoreId)->where('country_id', $countryId),
            ],
            'permalink' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('states', 'permalink')->ignore($ignoreId)->where('country_id', $countryId),
            ],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');
        if (is_string($code)) {
            $this->merge(['code' => mb_strtoupper(mb_trim($code))]);
        }
    }
}
