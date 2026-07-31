<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\City;

use App\Actions\Admin\Directory\City\ImportCitiesFromCsv;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the CSV upload. Per-row content is validated inside
 * {@see ImportCitiesFromCsv} so a bad row doesn't reject the whole batch.
 */
final class ImportCitiesRequest extends FormRequest
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
            'file' => [
                'required', 'file',
                'mimes:csv,txt',
                'max:512',
            ],
        ];
    }
}
