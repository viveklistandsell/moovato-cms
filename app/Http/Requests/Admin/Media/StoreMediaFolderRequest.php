<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Media;

use App\Models\MediaFolder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreMediaFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', MediaFolder::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[^\/\\\\]+$/'],
            'parent_id' => ['nullable', 'integer', Rule::exists('media_folders', 'id')->whereNull('deleted_at')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'Folder name cannot contain slashes.',
        ];
    }
}
