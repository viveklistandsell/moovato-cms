<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Directory\State;

use App\Actions\Admin\Directory\State\ImportStatesFromCsv;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the CSV upload. Per-row content is validated inside
 * {@see ImportStatesFromCsv} so a bad
 * row doesn't reject the whole batch.
 */
final class ImportStatesRequest extends FormRequest
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
