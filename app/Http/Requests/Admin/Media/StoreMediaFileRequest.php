<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Media;

use App\Models\MediaFile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreMediaFileRequest extends FormRequest
{
    /** Extensions never allowed for upload, even when "any file" is permitted. */
    public const BLOCKED_EXTENSIONS = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8',
        'phps', 'pht', 'htaccess', 'htpasswd', 'cgi', 'pl', 'sh', 'exe',
    ];

    public function authorize(): bool
    {
        return $this->user()?->can('create', MediaFile::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:524288'],
            'folder_id' => ['nullable', 'integer', Rule::exists('media_folders', 'id')->whereNull('deleted_at')],
            'resumableChunkNumber' => ['required', 'integer', 'min:1'],
            'resumableTotalChunks' => ['required', 'integer', 'min:1'],
            'resumableChunkSize' => ['required', 'integer', 'min:1'],
            'resumableTotalSize' => ['required', 'integer', 'min:1', 'max:524288000'],
            'resumableIdentifier' => ['required', 'string', 'max:128', 'regex:/^[A-Za-z0-9_\-]+$/'],
            'resumableFilename' => ['required', 'string', 'max:255'],
            'resumableType' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            $filename = (string) $this->input('resumableFilename');
            $extension = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($extension, self::BLOCKED_EXTENSIONS, true)) {
                $validator->errors()->add('resumableFilename', 'This file type is not allowed.');
            }
        });
    }
}
