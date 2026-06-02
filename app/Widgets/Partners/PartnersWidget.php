<?php

declare(strict_types=1);

namespace App\Widgets\Partners;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class PartnersWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'partners';
    }

    public static function label(): string
    {
        return 'Partners & Certificates';
    }

    public static function icon(): string
    {
        return 'Handshake';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'partners' => [
                ['path' => null, 'url' => null, 'name' => 'AMÖ Bundesverband', 'link' => ''],
                ['path' => null, 'url' => null, 'name' => 'DEKRA', 'link' => ''],
                ['path' => null, 'url' => null, 'name' => 'TÜV Rheinland', 'link' => ''],
                ['path' => null, 'url' => null, 'name' => 'Berlin Partner', 'link' => ''],
                ['path' => null, 'url' => null, 'name' => 'Deutsche Umzugslogistik', 'link' => ''],
            ],
            'certificates' => [
                ['icon' => 'ShieldCheck', 'title' => 'DEKRA geprüft'],
                ['icon' => 'Award', 'title' => 'AMÖ Mitglied'],
                ['icon' => 'Leaf', 'title' => 'Klimaneutraler Umzug'],
                ['icon' => 'Lock', 'title' => 'Datenschutz-zertifiziert'],
            ],
            'cards' => [
                [
                    'path' => null,
                    'url' => null,
                    'title' => 'Persönliche Beratung',
                    'description' => 'Wir planen Ihren Umzug in Berlin gemeinsam – mit kostenloser Besichtigung und transparentem Festpreis.',
                ],
                [
                    'path' => null,
                    'url' => null,
                    'title' => 'Sorgfältige Verpackung',
                    'description' => 'Profimaterial und geschultes Team schützen Ihr Hab und Gut vom ersten Karton bis zum Aufbau.',
                ],
                [
                    'path' => null,
                    'url' => null,
                    'title' => 'Pünktliche Lieferung',
                    'description' => 'Termintreue und ein eingespieltes Team sorgen für einen reibungslosen Umzugstag.',
                ],
            ],
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Vertrauen & Qualität',
            'heading' => 'Unsere Partner & Zertifikate',
            'subheading' => 'Moovato arbeitet mit etablierten Partnern zusammen und erfüllt höchste Qualitätsstandards für Ihren Umzug in Berlin.',
            'partners_title' => 'Starke Partner an unserer Seite',
            'certificates_title' => 'Geprüft & zertifiziert',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'partners' => ['nullable', 'array', 'max:24'],
            'partners.*.path' => ['nullable', 'string', 'max:1000'],
            'partners.*.url' => ['nullable', 'string', 'max:2000'],
            'partners.*.name' => ['nullable', 'string', 'max:120'],
            'partners.*.link' => ['nullable', 'string', 'max:2000'],
            'certificates' => ['nullable', 'array', 'max:12'],
            'certificates.*.icon' => ['nullable', 'string', 'max:64'],
            'certificates.*.title' => ['nullable', 'string', 'max:120'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:500'],
            'partners_title' => ['nullable', 'string', 'max:120'],
            'certificates_title' => ['nullable', 'string', 'max:120'],
        ];
    }
}
