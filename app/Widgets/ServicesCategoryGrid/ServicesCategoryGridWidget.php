<?php

declare(strict_types=1);

namespace App\Widgets\ServicesCategoryGrid;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * There is intentionally no parent picker: the widget always renders the
 * full list of parents (subject to the featured / popular filters below).
 * Content editors manage which parents exist via
 * Admin → Dienstleistungen → Elternkategorien.
 */
final class ServicesCategoryGridWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'services_category_grid';
    }

    public static function label(): string
    {
        return 'Parent Categories Grid';
    }

    public static function icon(): string
    {
        return 'LayoutGrid';
    }

    public static function category(): string
    {
        return 'services';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'only_featured' => false,
            'only_popular' => false,
            'max_items' => 12,
            'columns' => 5,
            'show_description' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Unsere Services',
            'heading' => 'Alle Dienstleistungen im Überblick',
            'subheading' => 'Wähle eine Kategorie, um passende Anbieter in deiner Stadt zu sehen.',
            'description' => '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'only_featured' => ['boolean'],
            'only_popular' => ['boolean'],
            'max_items' => ['required', 'integer', 'min:0', 'max:100'],
            'columns' => ['required', 'integer', 'in:2,3,4,5,6'],
            'show_description' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}
