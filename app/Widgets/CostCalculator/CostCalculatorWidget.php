<?php

declare(strict_types=1);

namespace App\Widgets\CostCalculator;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Moovato moving cost calculator: start / destination / living space inputs and
 * a calculate button. The estimate card, the follow-up copy and the CTA button
 * only appear once a price has been calculated. The estimate is driven purely by
 * distance and living space — `km × price_per_km + m² × price_per_sqm`, widened
 * by `spread_percent` into a range. `base_price` stays available as an optional
 * flat surcharge and defaults to zero.
 */
final class CostCalculatorWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'cost_calculator';
    }

    public static function label(): string
    {
        return 'Cost Calculator';
    }

    public static function icon(): string
    {
        return 'Calculator';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'currency' => '€',
            'base_price' => 0,
            'price_per_sqm' => 9.60,
            'price_per_km' => 0.70,
            'spread_percent' => 5,
            'locations' => self::defaultLocations(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'title' => 'Umzugskostenrechner',
            'from_label' => 'Umzug von',
            'from_placeholder' => 'Berlin, Deutschland',
            'to_label' => 'Umzug nach',
            'to_placeholder' => 'München, Deutschland',
            'area_label' => 'Fläche des aktuellen Wohnorts',
            'area_placeholder' => '80',
            'area_unit' => 'm²',
            'calculate_label' => 'Preis berechnen',
            'recalculate_label' => 'Erneut berechnen',
            'result_title' => 'Geschätzte Kosten',
            'volume_label' => 'Volumen',
            'distance_label' => 'Entfernung',
            'empty_text' => 'Geben Sie Start, Ziel und Wohnfläche ein – Ihre unverbindliche Kostenschätzung erscheint direkt hier.',
            'error_text' => 'Bitte wählen Sie Start- und Zielort aus der Liste und geben Sie Ihre Wohnfläche in m² an.',
            'cta_text' => '<p>Möchten Sie einen detaillierten Preis, der genau auf Ihren Umzug zugeschnitten ist?</p>',
            'cta_label' => 'Kostenloses Angebot anfordern',
            'cta_url' => '#',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'currency' => ['nullable', 'string', 'max:8'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'price_per_sqm' => ['nullable', 'numeric', 'min:0'],
            'price_per_km' => ['nullable', 'numeric', 'min:0'],
            'spread_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'locations' => ['nullable', 'array', 'max:200'],
            'locations.*.name' => ['nullable', 'string', 'max:160'],
            'locations.*.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'locations.*.lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:160'],
            'from_label' => ['nullable', 'string', 'max:120'],
            'from_placeholder' => ['nullable', 'string', 'max:160'],
            'to_label' => ['nullable', 'string', 'max:120'],
            'to_placeholder' => ['nullable', 'string', 'max:160'],
            'area_label' => ['nullable', 'string', 'max:120'],
            'area_placeholder' => ['nullable', 'string', 'max:60'],
            'area_unit' => ['nullable', 'string', 'max:16'],
            'calculate_label' => ['nullable', 'string', 'max:80'],
            'recalculate_label' => ['nullable', 'string', 'max:80'],
            'result_title' => ['nullable', 'string', 'max:120'],
            'volume_label' => ['nullable', 'string', 'max:80'],
            'distance_label' => ['nullable', 'string', 'max:80'],
            'empty_text' => ['nullable', 'string', 'max:400'],
            'error_text' => ['nullable', 'string', 'max:400'],
            'cta_text' => ['nullable', 'string', 'max:2000'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Offline fallback used when no Google Places key is configured, and as the
     * instant suggestion list while the API request is still in flight. Covers
     * every Bundesland plus the largest cities and the Berlin commuter belt.
     *
     * @return array<int, array{name: string, lat: float, lng: float}>
     */
    public static function defaultLocations(): array
    {
        return [
            ['name' => 'Berlin, Deutschland', 'lat' => 52.5200, 'lng' => 13.4050],
            ['name' => 'Baden-Württemberg, Deutschland', 'lat' => 48.6616, 'lng' => 9.3501],
            ['name' => 'Bayern, Deutschland', 'lat' => 48.7904, 'lng' => 11.4979],
            ['name' => 'Brandenburg, Deutschland', 'lat' => 52.4125, 'lng' => 12.5316],
            ['name' => 'Bremen, Deutschland', 'lat' => 53.0793, 'lng' => 8.8017],
            ['name' => 'Hamburg, Deutschland', 'lat' => 53.5511, 'lng' => 9.9937],
            ['name' => 'Hessen, Deutschland', 'lat' => 50.6521, 'lng' => 9.1624],
            ['name' => 'Mecklenburg-Vorpommern, Deutschland', 'lat' => 53.6127, 'lng' => 12.4296],
            ['name' => 'Niedersachsen, Deutschland', 'lat' => 52.6367, 'lng' => 9.8451],
            ['name' => 'Nordrhein-Westfalen, Deutschland', 'lat' => 51.4332, 'lng' => 7.6616],
            ['name' => 'Rheinland-Pfalz, Deutschland', 'lat' => 50.1183, 'lng' => 7.3090],
            ['name' => 'Saarland, Deutschland', 'lat' => 49.3964, 'lng' => 7.0230],
            ['name' => 'Sachsen, Deutschland', 'lat' => 51.1045, 'lng' => 13.2017],
            ['name' => 'Sachsen-Anhalt, Deutschland', 'lat' => 51.9503, 'lng' => 11.6923],
            ['name' => 'Schleswig-Holstein, Deutschland', 'lat' => 54.2194, 'lng' => 9.6961],
            ['name' => 'Thüringen, Deutschland', 'lat' => 50.9013, 'lng' => 11.0177],
            ['name' => 'Potsdam, Deutschland', 'lat' => 52.3906, 'lng' => 13.0645],
            ['name' => 'Oranienburg, Deutschland', 'lat' => 52.7548, 'lng' => 13.2361],
            ['name' => 'Bernau bei Berlin, Deutschland', 'lat' => 52.6790, 'lng' => 13.5876],
            ['name' => 'Falkensee, Deutschland', 'lat' => 52.5606, 'lng' => 13.0925],
            ['name' => 'Strausberg, Deutschland', 'lat' => 52.5786, 'lng' => 13.8869],
            ['name' => 'Königs Wusterhausen, Deutschland', 'lat' => 52.2977, 'lng' => 13.6244],
            ['name' => 'Frankfurt (Oder), Deutschland', 'lat' => 52.3412, 'lng' => 14.5487],
            ['name' => 'Cottbus, Deutschland', 'lat' => 51.7563, 'lng' => 14.3329],
            ['name' => 'München, Deutschland', 'lat' => 48.1351, 'lng' => 11.5820],
            ['name' => 'Dachau, Deutschland', 'lat' => 48.2603, 'lng' => 11.4342],
            ['name' => 'Augsburg, Deutschland', 'lat' => 48.3705, 'lng' => 10.8978],
            ['name' => 'Ingolstadt, Deutschland', 'lat' => 48.7665, 'lng' => 11.4258],
            ['name' => 'Regensburg, Deutschland', 'lat' => 49.0134, 'lng' => 12.1016],
            ['name' => 'Würzburg, Deutschland', 'lat' => 49.7913, 'lng' => 9.9534],
            ['name' => 'Nürnberg, Deutschland', 'lat' => 49.4521, 'lng' => 11.0767],
            ['name' => 'Köln, Deutschland', 'lat' => 50.9375, 'lng' => 6.9603],
            ['name' => 'Düsseldorf, Deutschland', 'lat' => 51.2277, 'lng' => 6.7735],
            ['name' => 'Dortmund, Deutschland', 'lat' => 51.5136, 'lng' => 7.4653],
            ['name' => 'Essen, Deutschland', 'lat' => 51.4556, 'lng' => 7.0116],
            ['name' => 'Duisburg, Deutschland', 'lat' => 51.4344, 'lng' => 6.7623],
            ['name' => 'Bochum, Deutschland', 'lat' => 51.4818, 'lng' => 7.2162],
            ['name' => 'Wuppertal, Deutschland', 'lat' => 51.2562, 'lng' => 7.1508],
            ['name' => 'Bonn, Deutschland', 'lat' => 50.7374, 'lng' => 7.0982],
            ['name' => 'Münster, Deutschland', 'lat' => 51.9607, 'lng' => 7.6261],
            ['name' => 'Bielefeld, Deutschland', 'lat' => 52.0302, 'lng' => 8.5325],
            ['name' => 'Frankfurt am Main, Deutschland', 'lat' => 50.1109, 'lng' => 8.6821],
            ['name' => 'Wiesbaden, Deutschland', 'lat' => 50.0782, 'lng' => 8.2398],
            ['name' => 'Mainz, Deutschland', 'lat' => 49.9929, 'lng' => 8.2473],
            ['name' => 'Kassel, Deutschland', 'lat' => 51.3127, 'lng' => 9.4797],
            ['name' => 'Stuttgart, Deutschland', 'lat' => 48.7758, 'lng' => 9.1829],
            ['name' => 'Karlsruhe, Deutschland', 'lat' => 49.0069, 'lng' => 8.4037],
            ['name' => 'Mannheim, Deutschland', 'lat' => 49.4875, 'lng' => 8.4660],
            ['name' => 'Heidelberg, Deutschland', 'lat' => 49.3988, 'lng' => 8.6724],
            ['name' => 'Freiburg im Breisgau, Deutschland', 'lat' => 47.9990, 'lng' => 7.8421],
            ['name' => 'Ulm, Deutschland', 'lat' => 48.4011, 'lng' => 9.9876],
            ['name' => 'Saarbrücken, Deutschland', 'lat' => 49.2402, 'lng' => 6.9969],
            ['name' => 'Koblenz, Deutschland', 'lat' => 50.3569, 'lng' => 7.5890],
            ['name' => 'Trier, Deutschland', 'lat' => 49.7490, 'lng' => 6.6371],
            ['name' => 'Hannover, Deutschland', 'lat' => 52.3759, 'lng' => 9.7320],
            ['name' => 'Braunschweig, Deutschland', 'lat' => 52.2689, 'lng' => 10.5268],
            ['name' => 'Osnabrück, Deutschland', 'lat' => 52.2799, 'lng' => 8.0472],
            ['name' => 'Oldenburg, Deutschland', 'lat' => 53.1435, 'lng' => 8.2146],
            ['name' => 'Göttingen, Deutschland', 'lat' => 51.5413, 'lng' => 9.9158],
            ['name' => 'Kiel, Deutschland', 'lat' => 54.3233, 'lng' => 10.1394],
            ['name' => 'Lübeck, Deutschland', 'lat' => 53.8655, 'lng' => 10.6866],
            ['name' => 'Flensburg, Deutschland', 'lat' => 54.7937, 'lng' => 9.4469],
            ['name' => 'Rostock, Deutschland', 'lat' => 54.0924, 'lng' => 12.0991],
            ['name' => 'Schwerin, Deutschland', 'lat' => 53.6355, 'lng' => 11.4012],
            ['name' => 'Greifswald, Deutschland', 'lat' => 54.0865, 'lng' => 13.3923],
            ['name' => 'Leipzig, Deutschland', 'lat' => 51.3397, 'lng' => 12.3731],
            ['name' => 'Dresden, Deutschland', 'lat' => 51.0504, 'lng' => 13.7373],
            ['name' => 'Chemnitz, Deutschland', 'lat' => 50.8278, 'lng' => 12.9214],
            ['name' => 'Magdeburg, Deutschland', 'lat' => 52.1205, 'lng' => 11.6276],
            ['name' => 'Halle (Saale), Deutschland', 'lat' => 51.4825, 'lng' => 11.9705],
            ['name' => 'Erfurt, Deutschland', 'lat' => 50.9848, 'lng' => 11.0299],
            ['name' => 'Jena, Deutschland', 'lat' => 50.9271, 'lng' => 11.5892],
            ['name' => 'Weimar, Deutschland', 'lat' => 50.9795, 'lng' => 11.3235],
            ['name' => 'Gera, Deutschland', 'lat' => 50.8829, 'lng' => 12.0819],
        ];
    }
}
