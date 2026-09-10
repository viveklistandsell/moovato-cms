<?php

declare(strict_types=1);

namespace App\Widgets\Concerns;

/**
 * Shared defaults for WidgetContract implementations. Concrete widgets `use`
 * this trait and override only what they need — keeping each widget's class
 * focused on its own type/label and field schema.
 *
 * We use a trait (not an abstract base class) so concrete widgets are free to
 * override any method without tripping Pint's `final_public_method_for_abstract_class`.
 */
trait ProvidesWidgetDefaults
{
    abstract public static function type(): string;

    abstract public static function label(): string;

    public static function category(): string
    {
        return 'content';
    }

    public static function icon(): string
    {
        return 'Square';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [];
    }

    /**
     * Locale-specific defaults for the translatable payload, keyed by language
     * code (e.g. `['de' => [...], 'en' => [...]]`). Widgets that need different
     * initial copy per language override this; everyone else keeps returning
     * an empty map and the frontend falls back to {@see defaultData()} for
     * every language tab. Only defined here (not on the interface) so the
     * addition stays backward compatible — every concrete widget uses this
     * trait and therefore inherits the empty default automatically.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function defaultDataByLocale(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [];
    }

    /**
     * Metadata shape consumed by the admin frontend picker.
     *
     * @return array{type: string, label: string, icon: string, category: string, default_settings: array<string, mixed>, default_data: array<string, mixed>, default_data_by_locale: array<string, array<string, mixed>>}
     */
    public static function toArray(): array
    {
        return [
            'type' => static::type(),
            'label' => static::label(),
            'icon' => static::icon(),
            'category' => static::category(),
            'default_settings' => static::defaultSettings(),
            'default_data' => static::defaultData(),
            'default_data_by_locale' => static::defaultDataByLocale(),
        ];
    }
}
