<?php

declare(strict_types=1);

namespace App\Widgets\LocationSearch;

use App\Http\Controllers\Frontend\LocationSearchController;
use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Standalone location search widget.
 *
 * The admin drops it onto any page; the visitor types the name of a
 * city, district, state or country and — on Enter, click, or picking a
 * suggestion — is redirected to the Companies list scoped to that
 * location. The widget itself only carries copy (heading, subtitle,
 * placeholder, CTA label); all matching logic lives server-side in
 * {@see LocationSearchController}, and
 * the Vue renderer just talks to `/location-search/suggest` and
 * `/location-search/go`.
 */
final class LocationSearchWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'location_search';
    }

    public static function label(): string
    {
        return 'Standort-Suche';
    }

    public static function icon(): string
    {
        return 'Search';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'alignment' => 'center',
            'background' => 'orange-soft',
        ];
    }

    public static function defaultData(): array
    {
        // German is the primary locale — the map returned from
        // defaultDataByLocale() reuses these values for the `de` slot and
        // provides an English variant for any bilingual site.
        return [
            'eyebrow' => 'Finden Sie Ihre Region',
            'title' => 'Umzugsunternehmen in Ihrer Nähe',
            'subtitle' => 'Stadt, Bezirk, Bundesland oder Land eingeben — wir zeigen Ihnen sofort passende Umzugsunternehmen.',
            'placeholder' => 'z. B. Berlin, Neckarstadt, Bayern, Deutschland …',
            'button_label' => 'Suchen',
            'no_match_hint' => 'Keinen Treffer gefunden? Wir zeigen Ihnen trotzdem passende Umzugsunternehmen.',
        ];
    }

    public static function defaultDataByLocale(): array
    {
        return [
            'de' => self::defaultData(),
            'en' => [
                'eyebrow' => 'Find your area',
                'title' => 'Moving companies near you',
                'subtitle' => 'Enter a city, district, state or country — we\'ll show you matching moving companies right away.',
                'placeholder' => 'e.g. Berlin, Neckarstadt, Bavaria, Germany …',
                'button_label' => 'Search',
                'no_match_hint' => 'No exact match? We\'ll still show you moving companies that can help.',
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'alignment' => ['required', 'in:left,center'],
            'background' => ['required', 'in:orange-soft,midnight,paper,none'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'placeholder' => ['nullable', 'string', 'max:120'],
            'button_label' => ['nullable', 'string', 'max:40'],
            'no_match_hint' => ['nullable', 'string', 'max:255'],
        ];
    }
}
