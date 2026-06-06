<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class MediaFile extends Model
{
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

    /**
     * A relative URL resolves against whatever 
     * host the page was served from and matches
     * what MediaPickerController does.
     *
     * @return Attribute<string, never>
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->relativeUrl($this->path),
        );
    }

    /**
     * Thumb URL with a sensible fallback. For images the resized variant may
     * never have been generated (uploads from the legacy chunked endpoint
     * don't run the variant pipeline) — in that case fall back to the
     * original so the grid still shows a preview instead of a broken icon.
     * For non-images (PDF, video, etc.) we keep returning null so the UI can
     * render a file-type icon instead.
     *
     * @return Attribute<?string, never>
     */
    protected function thumbUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->thumb_path) {
                    return $this->relativeUrl($this->thumb_path);
                }

                return $this->isImage() && $this->path
                    ? $this->relativeUrl($this->path)
                    : null;
            },
        );
    }

    /** @return Attribute<?string, never> */
    protected function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->medium_path
                ? $this->relativeUrl($this->medium_path)
                : null,
        );
    }
    private function relativeUrl(string $path): string
    {
        return '/storage/'.mb_ltrim($path, '/');
    }
}
