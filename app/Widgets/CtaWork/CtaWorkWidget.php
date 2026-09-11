<?php

declare(strict_types=1);

namespace App\Widgets\CtaWork;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Work CTA: a dark two-column call-to-action card with a headline, supporting
 * copy, a pill button, and a decorative illustration inside glowing rings.
 */
final class CtaWorkWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'cta_work';
    }

    public static function label(): string
    {
        return 'Work CTA';
    }

    public static function icon(): string
    {
        return 'Megaphone';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    public static function defaultSettings(): array
    {
        return [
            'media_image_path' => null,
            'media_image_url' => '/images/boxes-dolly.png',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Bereit für Ihren stressfreien Umzug mit Moovato?',
            'subtext' => 'Erhalten Sie in wenigen Minuten ein unverbindliches Angebot und starten Sie entspannt in Ihr neues Zuhause in Berlin.',
            'media_image_alt' => 'Umzugskartons und Sackkarre für Ihren Umzug mit Moovato',
            'button_label' => 'Jetzt Angebot anfordern',
            'button_url' => '/kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'media_image_path' => ['nullable', 'string'],
            'media_image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string'],
            'subtext' => ['nullable', 'string'],
            'media_image_alt' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
