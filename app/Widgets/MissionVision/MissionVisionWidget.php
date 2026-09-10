<?php

declare(strict_types=1);

namespace App\Widgets\MissionVision;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class MissionVisionWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'mission_vision';
    }

    public static function label(): string
    {
        return 'Mission & Vision';
    }

    public static function icon(): string
    {
        return 'Target';
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
        ];
    }

    public static function defaultData(): array
    {
        return [
            'image_alt' => 'Moovato Team beim Handschlag nach einem erfolgreichen Umzug in Berlin',
            'heading' => 'Umzüge, auf die Sie sich verlassen können',
            'subheading' => 'Sicher, pünktlich und ohne Überraschungen',
            'body' => 'Wir sind spezialisiert auf zuverlässige, sichere und planbare Umzugsdienstleistungen für Privatpersonen und Unternehmen in Berlin und Umgebung.',
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
            'panel_one_heading' => 'Unser Auftrag',
            'panel_one_body' => '<p>Unser Auftrag ist es, jeden Umzug so stressfrei wie möglich zu gestalten – mit erfahrenem Personal, transparenten Festpreisen und einem Service, der wirklich hält, was er verspricht.</p><ul><li><strong>Maßgeschneiderte Umzugslösungen</strong> – Jeder Umzug wird individuell geplant, abgestimmt auf Wohnungsgröße, Zeitplan und persönliche Wünsche.</li><li><strong>Verlässliche Partnerschaft</strong> – Von der ersten Anfrage bis zum letzten Karton sind wir ein fester Ansprechpartner an Ihrer Seite.</li><li><strong>Höchster Anspruch an Qualität</strong> – Geschultes Personal und eine sorgfältige Arbeitsweise sichern ein Ergebnis, auf das Sie sich verlassen können.</li></ul>',
            'panel_two_heading' => 'Unsere Vision',
            'panel_two_body' => '<p>Wir wollen der Umzugspartner sein, dem Berlin vertraut – mit einem Service, der Umzüge so einfach macht, wie sie sein sollten.</p><ul><li><strong>Innovativer Ansatz</strong> – Wir entwickeln unseren Service stetig weiter, um Umzüge in Berlin einfacher und transparenter zu machen.</li><li><strong>Kundenorientierung</strong> – Ihre Zufriedenheit steht im Mittelpunkt jeder Entscheidung, die wir treffen.</li><li><strong>Regionale Verwurzelung</strong> – Als Berliner Unternehmen kennen wir die Stadt, ihre Bezirke und die Herausforderungen vor Ort.</li></ul>',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'image_alt' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'panel_one_heading' => ['nullable', 'string'],
            'panel_one_body' => ['nullable', 'string'],
            'panel_two_heading' => ['nullable', 'string'],
            'panel_two_body' => ['nullable', 'string'],
        ];
    }
}
