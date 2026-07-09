<?php

declare(strict_types=1);

namespace App\Widgets\GetInTouch;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * "Let's get in touch" CTA: a dark rounded card on faint concentric rings with
 * an eyebrow, a large heading, a short lead, two action buttons (a gradient
 * primary and an outline secondary) and a decorative megaphone illustration on
 * the right. All copy and both button targets are editable.
 */
final class GetInTouchWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'get_in_touch';
    }

    public static function label(): string
    {
        return 'Get In Touch';
    }

    public static function icon(): string
    {
        return 'Megaphone';
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
            'image_path' => null,
            'image_url' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Ihr Umzugsunternehmen in Berlin',
            'heading' => 'Lassen Sie uns sprechen.',
            'lead' => 'Ihr Umzug sollte für Sie arbeiten – nicht umgekehrt. Wir helfen Ihnen gerne weiter.',
            'image_alt' => 'Moovato Umzugsteam in Berlin',
            'primary_label' => 'Jetzt anfragen',
            'primary_url' => '#kontakt',
            'secondary_label' => 'Termin buchen',
            'secondary_url' => '#angebot',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:160'],
            'lead' => ['nullable', 'string', 'max:400'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'primary_label' => ['nullable', 'string', 'max:60'],
            'primary_url' => ['nullable', 'string', 'max:255'],
            'secondary_label' => ['nullable', 'string', 'max:60'],
            'secondary_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
