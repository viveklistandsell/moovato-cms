<?php

declare(strict_types=1);

namespace App\Widgets\CtaWork;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Work CTA: a full-width dark call-to-action with a background image + overlay,
 * a large uppercase headline that wraps around an inline image, and a button.
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
            'bg_image_path' => null,
            'bg_image_url' => null,
            'inline_image_path' => null,
            'inline_image_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'title_before' => 'Jetzt',
            'title_after' => 'Umzug starten',
            'inline_image_alt' => 'Moovato Umzugsteam in Berlin',
            'button_label' => 'Kontakt aufnehmen',
            'button_url' => '/kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'bg_image_path' => ['nullable', 'string'],
            'bg_image_url' => ['nullable', 'string'],
            'inline_image_path' => ['nullable', 'string'],
            'inline_image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'title_before' => ['nullable', 'string'],
            'title_after' => ['nullable', 'string'],
            'inline_image_alt' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
