<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MediaFileFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

final class MediaFile extends Model
{
    /** @use HasFactory<MediaFileFactory> */
    use HasFactory, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'folder_id',
        'user_id',
        'name',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'disk',
        'path',
        'thumb_path',
        'medium_path',
        'metadata',
    ];

    /** @return BelongsTo<MediaFolder, $this> */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'metadata' => 'array',
        ];
    }

    /** @return Attribute<string, never> */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Storage::disk($this->disk)->url($this->path),
        );
    }

    /** @return Attribute<?string, never> */
    protected function thumbUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->thumb_path
                ? Storage::disk($this->disk)->url($this->thumb_path)
                : null,
        );
    }

    /** @return Attribute<?string, never> */
    protected function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->medium_path
                ? Storage::disk($this->disk)->url($this->medium_path)
                : null,
        );
    }
}
