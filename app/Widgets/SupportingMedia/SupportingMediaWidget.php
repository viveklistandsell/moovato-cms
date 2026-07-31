<?php

declare(strict_types=1);

namespace App\Widgets\SupportingMedia;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class SupportingMediaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'supporting_media';
    }

    public static function label(): string
    {
        return 'Media + Lead Text';
    }

    public static function icon(): string
    {
        return 'TextSearch';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
            'image_side' => 'left',
            'marker_style' => 'check',
            'card' => false,
            'heading_style' => 'bold',
            'bg' => 'none',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Unterstützung bei Firmen- & Büroumzügen',
            'body' => "Unternehmen stehen beim Umzug vor besonderen Herausforderungen: enge Zeitfenster, sensible Technik und ein laufender Betrieb, der möglichst wenig gestört werden soll.\n\nMoovato plant Ihren Büroumzug in Berlin sorgfältig durch – mit klarer Logistik, geschultem Personal und einem Ablauf, der Ausfallzeiten auf ein Minimum reduziert.",
            'image_alt' => 'Moovato Mitarbeiterin bei der Umzugsberatung',
            'points' => [
                'Klare Kommunikation ohne Fachjargon',
                'Sorgfältige Planung jedes Schritts',
                'Sensibler Umgang mit Technik & Akten',
                'Minimale Ausfallzeiten für Ihr Team',
            ],
            'button_label' => '',
            'button_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image_side' => ['required', 'in:left,right'],
            'marker_style' => ['required', 'in:check,chevron'],
            'card' => ['boolean'],
            'heading_style' => ['nullable', 'in:bold,italic'],
            'bg' => ['nullable', 'in:none,tinted,dark'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1200'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
