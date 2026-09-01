<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the CSV upload used by the "Import Pages" admin screen.
 *
 * `chunk_size` is enforced within a safe range so a rogue payload can't
 * hand the importer a value that would either process one page at a
 * time (too slow) or blow through memory limits (too many pages held
 * open in one transaction).
 */
final class ImportPagesRequest extends FormRequest
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
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'lang' => ['required', Rule::in(['de', 'en'])],
            'chunk_size' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }
}
