<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Import\Support;

use App\Models\MediaFile;

/**
 * Turns a filename cell from the import CSV (e.g. "computer-webdesign.webp")
 * into the storage path we drop into `page_widgets.settings.image_path`
 * or `pages.image`.
 *
 * The importer resolves images by matching the value against the media
 * library, trying progressively more forgiving strategies before giving
 * up. Each strategy is only tried when the previous one didn't hit:
 *
 *   1. Exact case-sensitive match on `original_name` or `name`.
 *   2. Case-insensitive match (Excel-style capitalisation drift).
 *   3. If the cell looks like a full path (`media/2026/foo.webp`), take
 *      its basename and try #1 + #2 again.
 *   4. Match on the stored `path` column directly — supports users who
 *      pasted the /storage/ URL or the raw storage path.
 *
 * A missing image is never a fatal error — callers get `null` back and
 * a warning is surfaced in the import report so the row still lands.
 *
 * Lookups are memoised per instance so an import processing dozens of
 * rows only hits the DB once per unique filename.
 */
final class ResolveMedia
{
    /** @var array<string, string|null> */
    private array $cache = [];

    public function pathFor(?string $filename): ?string
    {
        if ($filename === null) {
            return null;
        }
        $needle = mb_trim($filename);
        if ($needle === '') {
            return null;
        }

        if (str_starts_with($needle, '/storage/')) {
            $needle = mb_substr($needle, 9);
        }

        $key = mb_strtolower($needle);
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $path = $this->resolvePath($needle);
        $this->cache[$key] = $path;

        return $path;
    }

    private function resolvePath(string $needle): ?string
    {
        $path = MediaFile::query()
            ->where(function ($q) use ($needle): void {
                $q->where('original_name', $needle)
                    ->orWhere('name', $needle);
            })
            ->value('path');
        if (is_string($path) && $path !== '') {
            return $path;
        }

        $path = MediaFile::query()
            ->where(function ($q) use ($needle): void {
                $q->whereRaw('LOWER(original_name) = ?', [mb_strtolower($needle)])
                    ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($needle)]);
            })
            ->value('path');
        if (is_string($path) && $path !== '') {
            return $path;
        }  
        
        // Strategy 3 — user pasted a full path (e.g. `media/2026/foo.webp`).
        $basename = basename($needle);
        if ($basename !== $needle && $basename !== '') {
            $path = MediaFile::query()
                ->where(function ($q) use ($basename): void {
                    $q->where('original_name', $basename)
                        ->orWhere('name', $basename)
                        ->orWhereRaw('LOWER(original_name) = ?', [mb_strtolower($basename)])
                        ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($basename)]);
                })
                ->value('path');
            if (is_string($path) && $path !== '') {
                return $path;
            }
        }

        // Strategy 4 — user pasted the storage path itself. Match on `path`.
        $path = MediaFile::query()
            ->where('path', $needle)
            ->orWhere('path', 'like', '%/'.$basename)
            ->value('path');
        if (is_string($path) && $path !== '') {
            return $path;
        }

        return null;
    }
}
