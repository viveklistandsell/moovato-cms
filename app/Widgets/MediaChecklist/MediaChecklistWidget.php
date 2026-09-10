<?php

declare(strict_types=1);

namespace App\Widgets\MediaChecklist;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class MediaChecklistWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'media_checklist';
    }

    public static function label(): string
    {
        return 'Media + Checklist';
    }

    public static function icon(): string
    {
        return 'ListChecks';
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
            'card' => true,
            'bg' => 'none', // none | tinted | dark
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Schritte zu einem stressfreien Umzug',
            'body' => '<p>Persönliche Beratung und ein durchdachter Ablauf nehmen Ihnen den Stress – damit Sie Ihren Umzug in Berlin von Anfang bis Ende entspannt angehen.</p><ul><li><strong>Persönlicher Ansprechpartner für Ihren Umzug</strong> – Sie haben während des gesamten Umzugs eine feste Kontaktperson an Ihrer Seite.</li><li><strong>Termine, die zu Ihrem Zeitplan passen</strong> – Wir stimmen den Umzugstermin flexibel auf Ihre Bedürfnisse ab.</li><li><strong>Faire Festpreise, transparent kalkuliert</strong> – Ihr Angebot zeigt jede Position einzeln – klar und nachvollziehbar.</li><li><strong>Rücksichtsvolles, geschultes Team</strong> – Unsere Mitarbeiter gehen sorgfältig mit Ihrem Eigentum und Ihrer Wohnung um.</li></ul>',
            'image_alt' => 'Moovato Beraterin plant einen Umzug in Berlin',
            'button_label' => 'Beratung anfragen',
            'button_url' => '#kontakt',
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
