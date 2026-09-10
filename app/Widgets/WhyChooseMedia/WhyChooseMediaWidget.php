<?php

declare(strict_types=1);

namespace App\Widgets\WhyChooseMedia;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class WhyChooseMediaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'why_choose_media';
    }

    public static function label(): string
    {
        return 'Why Choose (Media)';
    }

    public static function icon(): string
    {
        return 'BadgeCheck';
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
            'image_side' => 'right',
            'card' => false,
            'bg' => 'none',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Warum wir',
            'heading' => 'Warum Moovato die richtige Wahl ist',
            'body' => '<p>Wir verbinden Sie mit erfahrenen Umzugsprofis aus Berlin – zuverlässig, sicher und zu fairen Festpreisen, ganz bequem von zu Hause aus geplant.</p><ul><li><strong>Der passende Umzugsexperte für Sie</strong> – Wir vermitteln den Umzugspartner, der am besten zu Ihrem Vorhaben passt.</li><li><strong>Termine rund um Ihren Zeitplan</strong> – Ihr Umzugstermin richtet sich nach Ihrem Alltag, nicht umgekehrt.</li><li><strong>Faire Preise, transparent kalkuliert</strong> – Sie erhalten ein Angebot, das jede Leistung einzeln aufschlüsselt.</li><li><strong>Freundlicher, aufmerksamer Service</strong> – Von der ersten Anfrage an begleiten wir Sie zuvorkommend durch den Umzug.</li></ul>',
            'image_alt' => 'Moovato Berater im Gespräch mit einem Kunden',
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
