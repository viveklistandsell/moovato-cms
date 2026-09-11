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
            'card' => false,
            'bg' => 'none',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Unterstützung bei Firmen- & Büroumzügen',
            'body' => '<p>Unternehmen stehen beim Umzug vor besonderen Herausforderungen: enge Zeitfenster, sensible Technik und ein laufender Betrieb, der möglichst wenig gestört werden soll.</p><p>Moovato plant Ihren Büroumzug in Berlin sorgfältig durch – mit klarer Logistik, geschultem Personal und einem Ablauf, der Ausfallzeiten auf ein Minimum reduziert.</p><ul><li><strong>Klare Kommunikation ohne Fachjargon</strong> – Wir erklären jeden Schritt verständlich, ohne komplizierte Fachbegriffe.</li><li><strong>Sorgfältige Planung jedes Schritts</strong> – Jede Phase Ihres Büroumzugs wird im Voraus durchdacht und abgestimmt.</li><li><strong>Sensibler Umgang mit Technik & Akten</strong> – Empfindliche Geräte und wichtige Unterlagen werden besonders geschützt transportiert.</li><li><strong>Minimale Ausfallzeiten für Ihr Team</strong> – Wir planen den Umzug so, dass Ihr Betrieb so wenig wie möglich unterbrochen wird.</li></ul>',
            'image_alt' => 'Moovato Mitarbeiterin bei der Umzugsberatung',
            'button_label' => '',
            'button_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
            'card' => ['boolean'],
            'bg' => ['nullable', 'in:none,tinted,dark'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
