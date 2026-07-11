<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Service\ParentCategory;

use App\Actions\Admin\Service\ParentCategory\ImportParentCategoriesFromCsv;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the CSV upload. Per-row content is validated inside
 * {@see ImportParentCategoriesFromCsv} so a bad row doesn't reject the
 * whole batch.
 */
final class ImportParentCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('service_categories.create') === true;
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
