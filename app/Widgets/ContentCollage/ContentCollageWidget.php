<?php

declare(strict_types=1);

namespace App\Widgets\ContentCollage;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ContentCollageWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'content_collage';
    }

    public static function label(): string
    {
        return 'Content + Image Collage';
    }

    public static function icon(): string
    {
        return 'LayoutGrid';
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
            'image_side' => 'left', // left | right
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Zuverlässige Umzugslösungen für Privat & Gewerbe',
            'body' => '<p>Umzüge können stressig sein – müssen es aber nicht. Wir nehmen Ihnen die Last ab: mit erfahrenen Teams, klarer Planung und einem Service, der genau auf Ihre Bedürfnisse zugeschnitten ist.</p><ul><li><strong>Privatumzug</strong> – Ihr Wohnungswechsel in Berlin – von der Einzimmerwohnung bis zum Familienhaus.</li><li><strong>Gewerbeumzug</strong> – Büro- und Firmenumzüge mit minimaler Ausfallzeit für Ihr Unternehmen.</li><li><strong>Fernumzug</strong> – Zuverlässiger Transport deutschlandweit, egal wie weit der Weg ist.</li><li><strong>Spezialtransport</strong> – Sicherer Transport für Klaviere, Kunstwerke und andere empfindliche Gegenstände.</li></ul>',
            'image_alt' => 'Moovato Team belädt einen Umzugswagen in Berlin',
            'button_label' => 'Angebot anfordern',
            'button_url' => '#kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
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
