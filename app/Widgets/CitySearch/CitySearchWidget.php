<?php

declare(strict_types=1);

namespace App\Widgets\CitySearch;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Moovato city directory: centered heading + intro on a white band, a row of
 * city image cards on a soft band, and a search field. City card images are
 * non-translatable settings; the copy lives in the translatable data payload.
 */
final class CitySearchWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'city_search';
    }

    public static function label(): string
    {
        return 'City Directory Search';
    }

    public static function icon(): string
    {
        return 'MapPin';
    }

    public static function category(): string
    {
        return 'content';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'cities' => [
                ['path' => null, 'url' => null, 'alt' => 'Berlin', 'name' => 'Berlin', 'link' => '#'],
                ['path' => null, 'url' => null, 'alt' => 'Hamburg', 'name' => 'Hamburg', 'link' => '#'],
                ['path' => null, 'url' => null, 'alt' => 'München', 'name' => 'München', 'link' => '#'],
                ['path' => null, 'url' => null, 'alt' => 'Köln', 'name' => 'Köln', 'link' => '#'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'title' => 'Finden Sie die besten Umzugsunternehmen in Ihrer Nähe',
            'subtitle' => 'Auf der Suche nach den besten Umzugsunternehmen in Deutschland? Suchen & vergleichen Sie ganz einfach Umzugsunternehmen in Ihrer Region, bevor Sie eine Wahl treffen.',
            'card_prefix' => 'Top 10 Umzugsunternehmen in',
            'search_placeholder' => 'Stadt oder Umzugsfirma suchen',
            'search_url' => '#',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'cities' => ['nullable', 'array', 'max:12'],
            'cities.*.path' => ['nullable', 'string', 'max:1000'],
            'cities.*.url' => ['nullable', 'string', 'max:2000'],
            'cities.*.alt' => ['nullable', 'string', 'max:160'],
            'cities.*.name' => ['nullable', 'string', 'max:120'],
            'cities.*.link' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'card_prefix' => ['nullable', 'string', 'max:120'],
            'search_placeholder' => ['nullable', 'string', 'max:120'],
            'search_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
