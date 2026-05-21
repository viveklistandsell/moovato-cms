<?php

declare(strict_types=1);

namespace App\Widgets\Contracts;

/**
 * A widget describes a single block type that admins can drop onto a page.
 *
 * - `settings` is non-translatable structural data (image paths, layout flags,
 *   colors, alignments) shared across every locale.
 * - `data` is the translatable payload (titles, body text, button labels, list
 *   items) stored once per active language.
 *
 * Adding a new widget = create a class implementing this contract + a matching
 * editor/renderer pair on the Vue side. No core changes required.
 */
interface WidgetContract
{
    /** Stable slug used as DB `type` value and frontend lookup key. */
    public static function type(): string;

    /** Human label shown in the picker. */
    public static function label(): string;

    /** Lucide icon name used in the picker tile. */
    public static function icon(): string;

    /** Category bucket: `layout`, `content`, `media`, `marketing`. */
    public static function category(): string;

    /**
     * Default non-translatable settings used when a widget is first inserted.
     *
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array;

    /**
     * Default translatable payload — one shape, reused per language tab.
     *
     * @return array<string, mixed>
     */
    public static function defaultData(): array;

    /**
     * Laravel validation rules for the `settings` array, keys WITHOUT the
     * `widgets.*.settings.` prefix (the action prepends it).
     *
     * @return array<string, mixed>
     */
    public static function settingsRules(): array;

    /**
     * Laravel validation rules for one translation `data` array. Keys are
     * relative to the translation; the action wraps them under
     * `widgets.*.translations.{lang}.data.`.
     *
     * @return array<string, mixed>
     */
    public static function dataRules(): array;

    /**
     * Flat metadata bundle shipped to the admin frontend picker.
     *
     * @return array{type: string, label: string, icon: string, category: string, default_settings: array<string, mixed>, default_data: array<string, mixed>}
     */
    public static function toArray(): array;
}
