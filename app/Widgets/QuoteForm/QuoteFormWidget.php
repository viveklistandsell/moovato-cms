<?php

declare(strict_types=1);

namespace App\Widgets\QuoteForm;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Multi-step quote form: a left marketing aside (eyebrow, heading, lead,
 * benefits) and a right 3-step form card (move details → addresses → contact)
 * with a progress bar and a success state. Ported from the index-v7 template
 * and recolored to the theme. The marketing copy + benefits + success message
 * are editable; the form fields themselves are fixed.
 */
final class QuoteFormWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'quote_form';
    }

    public static function label(): string
    {
        return 'Quote Form';
    }

    public static function icon(): string
    {
        return 'ClipboardList';
    }

    public static function category(): string
    {
        return 'content';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Umzugsformular',
            'heading' => 'Kostenloses Angebot in 60 Sekunden',
            'lead' => 'Ein paar Angaben genügen. Wir berechnen Ihren Festpreis und melden uns innerhalb von 24 Stunden.',
            'benefits' => [
                'Kostenlos & unverbindlich',
                'Festpreisgarantie ohne versteckte Kosten',
                'Antwort innerhalb von 24 Stunden',
                'Versicherte Transporte in ganz Berlin',
            ],
            'form_title' => 'Jetzt Festpreis anfordern',
            'form_subtitle' => 'Schnell, kostenlos und unverbindlich.',
            'success_title' => 'Vielen Dank!',
            'success_text' => 'Ihre Anfrage ist bei uns eingegangen. Wir melden uns innerhalb von 24 Stunden mit Ihrem kostenlosen Festpreis-Angebot.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'lead' => ['nullable', 'string'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string'],
            'form_title' => ['nullable', 'string'],
            'form_subtitle' => ['nullable', 'string'],
            'success_title' => ['nullable', 'string'],
            'success_text' => ['nullable', 'string'],
        ];
    }
}
