<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Import;

/**
 * Maps the widget slugs used in the import CSV to the widget types that
 * actually exist in this project (registered in WidgetServiceProvider).
 *
 * The importer intentionally REUSES existing Moovato widgets — no new
 * widget types are added for the import format. When a mapping needs
 * to influence the widget's shape (e.g. `zig_zag_content` renders with
 * the media on the left, `zig_zag_content_two` with it on the right),
 * the extra behaviour is expressed as `settings_overrides` here rather
 * than as a new widget class.
 *
 * Supported CSV slugs (see the "sections" column of the import CSV):
 *   - service_hero        → hero
 *   - content_image       → split_media (media on the right)
 *   - device_showcase     → media_checklist (media on the right)
 *   - zig_zag_content     → split_media (media on the left)
 *   - zig_zag_content_two → split_media (media on the right)
 *   - faq                 → faq
 *
 * Unknown slugs return null so the importer can skip the row and report
 * it rather than silently swallowing bad input.
 */
final class WidgetAliasMap
{
    /**
     * @var array<string, array{type: string, settings_overrides: array<string, mixed>}>
     */
    private const MAP = [
        'service_hero' => [
            'type' => 'hero',
            'settings_overrides' => [
                'alignment' => 'center',
                'overlay' => true,
                'height' => 'lg',
            ],
        ],
        'content_image' => [
            'type' => 'split_media',
            'settings_overrides' => [
                'image_side' => 'right',
            ],
        ],
        'device_showcase' => [
            'type' => 'media_checklist',
            'settings_overrides' => [
                'image_side' => 'right',
            ],
        ],
        'zig_zag_content' => [
            'type' => 'split_media',
            'settings_overrides' => [
                'image_side' => 'left',
            ],
        ],
        'zig_zag_content_two' => [
            'type' => 'split_media',
            'settings_overrides' => [
                'image_side' => 'right',
            ],
        ],
        'faq' => [
            'type' => 'faq',
            'settings_overrides' => [],
        ],
    ];

    public function has(string $csvSlug): bool
    {
        return isset(self::MAP[mb_strtolower(mb_trim($csvSlug))]);
    }

    /**
     * @return array{type: string, settings_overrides: array<string, mixed>}|null
     */
    public function resolve(string $csvSlug): ?array
    {
        return self::MAP[mb_strtolower(mb_trim($csvSlug))] ?? null;
    }

    /**
     * @return list<string>
     */
    public function csvSlugs(): array
    {
        return array_keys(self::MAP);
    }
}
